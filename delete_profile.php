<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location:login.php");
    exit();
}

include('db.php');

$user_id = $_SESSION['user_id'];

// Delete user from database
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    // Destroy session and logout
    session_destroy();
    header("Location: auth/register.php?deleted=1");
    exit();
} else {
    echo "❌ Failed to delete account: " . $stmt->error;
}

$stmt->close();
?>

