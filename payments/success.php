<?php
if (!isset($_GET['order_id'])) {
    header("Location: ../index.php");
    exit();
}

$order_id = $_GET['order_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Success</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Thank You!</h2>
        <p>Your order (#<?= $order_id ?>) has been placed successfully.</p>
        <a href="../index.php">Continue Shopping</a>
    </div>
</body>
</html>
