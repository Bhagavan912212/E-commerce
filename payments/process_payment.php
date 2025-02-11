<?php 
session_start();
include '../includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure order details exist
if (!isset($_SESSION['order_details'])) {
    die("Order details are missing. Please go back and try again.");
}

$order_details = $_SESSION['order_details'];

// Fetch cart items from the database instead of relying on session
$cart_query = "SELECT cart.product_id, cart.quantity, products.price 
               FROM cart 
               JOIN products ON cart.product_id = products.id 
               WHERE cart.user_id = :user_id";
$stmt = $conn->prepare($cart_query);
$stmt->execute([':user_id' => $user_id]);

$cart_items = [];
$total_amount = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $row['total_price'] = $row['price'] * $row['quantity'];
    $total_amount += $row['total_price'];
    $cart_items[] = $row;
}

// If cart is still empty, prevent order processing
if (empty($cart_items)) {
    die("Your cart is empty. Please add items before proceeding.");
}

// Insert order into the `orders` table
$order_query = "INSERT INTO orders (user_id, name, address, payment_method, total_amount) 
                VALUES (:user_id, :name, :address, :payment_method, :total_amount)";
$stmt = $conn->prepare($order_query);
$stmt->execute([
    ':user_id' => $user_id,
    ':name' => $order_details['name'],
    ':address' => $order_details['address'],
    ':payment_method' => $order_details['payment_method'],
    ':total_amount' => $total_amount
]);

$order_id = $conn->lastInsertId(); // Get the last inserted order ID

// Insert each cart item into `order_items`
$item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
               VALUES (:order_id, :product_id, :quantity, :price)";
$stmt = $conn->prepare($item_query);

foreach ($cart_items as $item) {
    $stmt->execute([
        ':order_id' => $order_id,
        ':product_id' => $item['product_id'],
        ':quantity' => $item['quantity'],
        ':price' => $item['price']
    ]);
}

// Clear the cart after successful order
$clear_cart_query = "DELETE FROM cart WHERE user_id = :user_id";
$stmt = $conn->prepare($clear_cart_query);
$stmt->execute([':user_id' => $user_id]);

// Unset session variables
unset($_SESSION['cart_items'], $_SESSION['order_details']);

// Redirect to success page
header("Location: success.php?order_id=$order_id");
exit();
?>
