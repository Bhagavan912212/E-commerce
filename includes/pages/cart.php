<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ecommerce");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];

    // Check if product already exists in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $cart_item = $result->fetch_assoc();

    if ($cart_item) {
        // Update quantity if product exists
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $stmt->bind_param("i", $cart_item['id']);
        $stmt->execute();
    } else {
        // Insert new product into cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
    }
    header("Location: cart.php");
    exit();
}

// Handle Increment Quantity
if (isset($_GET['action']) && $_GET['action'] == "increment" && isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Handle Decrement Quantity
if (isset($_GET['action']) && $_GET['action'] == "decrement" && isset($_GET['cart_id'])) {
    $cart_id = $_GET['cart_id'];
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result['quantity'] > 1) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE id = ?");
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
    } else {
        // Remove item if quantity is 1
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();
    }
    header("Location: cart.php");
    exit();
}

// Handle Remove Item
if (isset($_GET['remove'])) {
    $cart_id = $_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Fetch Products
$product_query = "SELECT * FROM products";
$product_result = $conn->query($product_query);

// Fetch Cart Items
$cart_items_query = "SELECT cart.id, products.name, products.price, products.image, cart.quantity 
                     FROM cart 
                     JOIN products ON cart.product_id = products.id 
                     WHERE cart.user_id = ?";
$stmt = $conn->prepare($cart_items_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

$total_amount = 0;
while ($item = $cart_items->fetch_assoc()) {
    $total_amount += $item['price'] * $item['quantity'];
}

$_SESSION['total_amount'] = $total_amount;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <div class="header-container">
        <a href="../" style="text-decoration: none; color: inherit;">
            <h1>Welcome to Our Store</h1>
        </a>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">Profile</a>
                <a href="cart.php" class="cart-link">
                    <img src="../images/cart-icon.png" alt="Cart" class="cart-icon"> Cart
                </a>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="logout-button">Logout</button>
                </form>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="container">
    <h2>Available Products</h2>
    <div class="products-container">
        <?php while ($product = $product_result->fetch_assoc()): ?>
            <div class="product-card">
                <img src="../images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                <h3><?= htmlspecialchars($product['name']) ?></h3>
                <p>₹<?= number_format($product['price'], 2) ?></p>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" name="add_to_cart" class="add-to-cart-button">Add to Cart</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>

    <h2>Your Cart</h2>
    <div class="cart-container">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $cart_items->data_seek(0); // Reset pointer
                while ($item = $cart_items->fetch_assoc()): 
                    $item_total = $item['price'] * $item['quantity'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td>₹<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <a href="cart.php?action=decrement&cart_id=<?= $item['id'] ?>" class="qty-btn">-</a>
                            <?= htmlspecialchars($item['quantity']) ?>
                            <a href="cart.php?action=increment&cart_id=<?= $item['id'] ?>" class="qty-btn">+</a>
                        </td>
                        <td>₹<?= number_format($item_total, 2) ?></td>
                        <td><a href="cart.php?remove=<?= $item['id'] ?>" class="remove-btn">Remove</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <h3>Total Amount: ₹<?= number_format($total_amount, 2) ?></h3>
        <form method="POST" action="../payments/checkout.php">
            <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
            <button type="submit" name="proceed_to_payment" class="proceed-btn">Proceed to Payment</button>
        </form>
    </div>
</div>
</body>
</html>

<style>
        body { font-family: Arial, sans-serif; background-color: #f7f7f7; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); }
        .products-container, .cart-container { margin: 20px 0; }
        .product-card { display: inline-block; background: white; border-radius: 8px; padding: 15px; width: 250px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); text-align: center; margin-right: 10px; }
        .product-card img { width: 100%; height: 150px; object-fit: cover; border-radius: 8px; }
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table th, .cart-table td { padding: 15px; border-bottom: 1px solid #eee; text-align: center; }
        .cart-table th { background-color: #2c3e50; color: white; }
        /* Proceed Button */
     .proceed-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            width: 100%;
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }

        .proceed-btn:hover {
            background-color: #27ae60;
        }
        .qty-btn, .remove-btn, .add-to-cart-button { cursor: pointer; padding: 5px 10px; border-radius: 4px; text-decoration: none; color: white; }
        .qty-btn { background-color: #3498db; }
        .remove-btn { background-color: #e74c3c; }
        .add-to-cart-button { background-color: #2ecc71; padding: 10px 20px; }
        header {
    background-color:rgb(131, 156, 181);
    padding: 10px;
     /* Proceed Button */
     .proceed-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            width: 100%;
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }

        .proceed-btn:hover {
            background-color: #27ae60;
        }
}

.header-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

nav {
    display: flex;
    gap: 20px;
}

nav a {
    color: white;
    text-decoration: none;
    padding: 10px;
    font-weight: bold;
}

.logout-button {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    transition: all 0.3s;
}

.logout-button:hover {
    background-color: #c0392b;
}
    </style>