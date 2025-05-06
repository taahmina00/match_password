<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

require 'db_connection.php'; // Include your database connection file

$username = $_SESSION['username'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$repeat_new_password = $_POST['repeat_new_password'];

// Validate new passwords match
if ($new_password !== $repeat_new_password) {
    echo "New passwords do not match.";
    exit();
}

// Fetch the current password from the database
$stmt = $pdo->prepare("SELECT password FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($current_password, $user['password'])) {
    // Hash the new password
    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
    $update_stmt->execute([$hashed_new_password, $username]);

    echo "Password changed successfully.";
} else {
    echo "Current password is incorrect.";
}
?>
