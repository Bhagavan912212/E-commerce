<?php
session_start();
include '../includes/db.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch Cart Items
$cart_query = "SELECT cart.id, products.id AS product_id, products.name, products.price, cart.quantity 
               FROM cart 
               JOIN products ON cart.product_id = products.id 
               WHERE cart.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total amount
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Store total amount in session
$_SESSION['total_amount'] = $total_amount;

// Handle checkout form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_payment'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $payment_method = $_POST['payment_method'];

    if (empty($name) || empty($address)) {
        $error = "All fields are required!";
    } else {
        // Store order details in session
        $_SESSION['order_details'] = [
            'name' => htmlspecialchars($name),
            'address' => htmlspecialchars($address),
            'payment_method' => $payment_method,
            'total_amount' => $_SESSION['total_amount']
        ];

        // Redirect to process payment
        header("Location: process_payment.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
</head>
<body>
    <div class="container">
        <h2>Checkout</h2>

        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="name">Full Name:</label>
            <input type="text" name="name" required>

            <label for="address">Address:</label>
            <textarea name="address" required></textarea>

            <label for="payment_method">Payment Method:</label>
            <select name="payment_method" required>
                <option value="COD">Cash on Delivery</option>
                <option value="Card">Credit/Debit Card</option>
            </select>

            <!-- Display Total Amount -->
            <h3>Total Amount: ₹<?= number_format($_SESSION['total_amount'], 2) ?></h3>

            <button type="submit" name="confirm_payment">Confirm Payment</button>
        </form>
    </div>
</body>
</html>
<style>
/* General Page Styling */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f8f9fa;
    color: #333;
    padding: 20px;
}

/* Container */
.container {
    width: 80%;
    max-width: 600px;
    margin: auto;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

/* Headings */
h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #007bff;
}

/* Form Styling */
label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
}

input[type="text"], 
textarea, 
select {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
}

/* Buttons */
button {
    width: 100%;
    padding: 12px;
    border: none;
    background-color: #28a745;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 10px;
}

button:hover {
    background-color: #218838;
}

/* Error Messages */
p.error {
    color: red;
    font-weight: bold;
    text-align: center;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .container {
        width: 95%;
    }

    button {
        font-size: 14px;
        padding: 10px;
    }
}
