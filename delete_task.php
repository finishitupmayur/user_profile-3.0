<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header ("Location:register.php");
    exit();
}

include('db.php');

$user_id = $_SESSION['user_id'];
$task_id = $_GET['id'] ?? null;

if (!$task_id) {
    header("Location: tasks.php");
    exit();
}

// Delete task only if it belongs to this user
$stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);

if ($stmt->execute()) {
    header("Location: tasks.php");
    exit();
} else {
    echo "❌ Error deleting task: " . $stmt->error;
}

$stmt->close();
?>
