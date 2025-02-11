<?php
session_start();
include '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details (only first name, last name, and email are mandatory)
$query = "SELECT first_name, last_name, email, phone, address, city, state, zip_code, country, date_of_birth, gender FROM users WHERE id = :user_id";
$stmt = $conn->prepare($query);
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Optional Fields (Allow NULL)
    $phone = !empty($_POST['phone']) ? $_POST['phone'] : NULL;
    $address = !empty($_POST['address']) ? $_POST['address'] : NULL;
    $city = !empty($_POST['city']) ? $_POST['city'] : NULL;
    $state = !empty($_POST['state']) ? $_POST['state'] : NULL;
    $zip_code = !empty($_POST['zip_code']) ? $_POST['zip_code'] : NULL;
    $country = !empty($_POST['country']) ? $_POST['country'] : NULL;
    $date_of_birth = !empty($_POST['date_of_birth']) ? $_POST['date_of_birth'] : NULL;
    $gender = !empty($_POST['gender']) ? $_POST['gender'] : NULL;

    $update_query = "UPDATE users SET 
        first_name = :first_name, 
        last_name = :last_name, 
        email = :email, 
        phone = :phone, 
        address = :address, 
        city = :city, 
        state = :state, 
        zip_code = :zip_code, 
        country = :country, 
        date_of_birth = :date_of_birth, 
        gender = :gender
        WHERE id = :user_id";

    $stmt = $conn->prepare($update_query);
    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':phone' => $phone,
        ':address' => $address,
        ':city' => $city,
        ':state' => $state,
        ':zip_code' => $zip_code,
        ':country' => $country,
        ':date_of_birth' => $date_of_birth,
        ':gender' => $gender,
        ':user_id' => $user_id
    ]);

    $_SESSION['success_message'] = "Profile updated successfully!";
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <div class="header-container">
        <a href="../index.php" style="text-decoration: none; color: inherit;">
            <h1>Welcome to Our Store</h1>
        </a>
        <nav>
            <a href="cart.php" class="cart-link">
                <img src="../images/cart-icon.png" alt="Cart" class="cart-icon"> Cart
            </a>
            <form method="POST" action="../pages/logout.php" style="display: inline;">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </nav>
    </div>
</header>

<div class="container">
    <h2>Your Profile</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="success-message"><?= $_SESSION['success_message']; ?></p>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="phone">Phone :</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">

        <label for="address">Address :</label>
        <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">

        <label for="city">City: </label>
        <input type="text" name="city" value="<?= htmlspecialchars($user['city'] ?? '') ?>">

        <label for="state">State :</label>
        <input type="text" name="state" value="<?= htmlspecialchars($user['state'] ?? '') ?>">

        <label for="zip_code">Zip Code :</label>
        <input type="text" name="zip_code" value="<?= htmlspecialchars($user['zip_code'] ?? '') ?>">

        <label for="country">Country :</label>
        <input type="text" name="country" value="<?= htmlspecialchars($user['country'] ?? '') ?>">

        <label for="date_of_birth">Date of Birth :</label>
        <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">

        <label for="gender">Gender :</label>
        <select name="gender">
            <option value="" <?= empty($user['gender']) ? 'selected' : '' ?>>Select</option>
            <option value="Male" <?= ($user['gender'] == 'Male') ? 'selected' : '' ?>>Male</option>
            <option value="Female" <?= ($user['gender'] == 'Female') ? 'selected' : '' ?>>Female</option>
            <option value="Other" <?= ($user['gender'] == 'Other') ? 'selected' : '' ?>>Other</option>
        </select>

        <button type="submit" name="update_profile" class="update-btn">Update Profile</button>
    </form>
</div>

</body>
</html>
<style>
    /* Global Styles */
/* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Arial', sans-serif;
}

/* Body Styling */
body {
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Profile Container */
.profile-container {
    background: #fff;
    width: 400px;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Profile Image */
.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #3498db;
    margin-bottom: 15px;
}

/* Profile Title */
.profile-title {
    font-size: 22px;
    font-weight: bold;
    color: #333;
}

/* Profile Info */
.profile-info {
    margin-top: 10px;
    text-align: left;
}

.profile-info label {
    font-weight: bold;
    color: #555;
    display: block;
    margin-bottom: 5px;
}

.profile-info input,
.profile-info textarea {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

/* Save Button */
.save-btn {
    width: 100%;
    background: #3498db;
    color: #fff;
    padding: 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.save-btn:hover {
    background: #2980b9;
}

    </style>