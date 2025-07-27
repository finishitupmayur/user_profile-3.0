<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php');

$user_id = $_SESSION['user_id'];
$message = '';
$task_id = $_GET['id'] ?? null;

if (!$task_id) {
    header("Location: tasks.php");
    exit();
}


$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header("Location: tasks.php");
    exit();
}

$task = $result->fetch_assoc();
$stmt->close();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

    if ($title !== '') {
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, due_date = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sssii", $title, $description, $due_date, $task_id, $user_id);

        if ($stmt->execute()) {
            header("Location: tasks.php");
            exit();
        } else {
            $message = "❌ Failed to update task.";
        }
        $stmt->close();
    } else {
        $message = "❗ Title is required.";
    }
}
?>

<?php include('header.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Edit Task</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-warning"><?= $message ?></div>
                    <?php endif; ?>

                    <form method="POST" onsubmit="return validateTaskForm()">
                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title *</label>
                            <input type="text" name="title" id="title" class="form-control" 
                                   value="<?= htmlspecialchars($task['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($task['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" 
                                   value="<?= $task['due_date'] ?>" min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" disabled 
                                       <?= $task['completed'] ? 'checked' : '' ?>>
                                <label class="form-check-label">
                                    Task Status: <?= $task['completed'] ? 'Completed ✓' : 'Pending ○' ?>
                                </label>
                                <small class="form-text text-muted d-block">
                                    To change completion status, go back to tasks list and click the status button.
                                </small>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="tasks.php" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validateTaskForm() {
    const title = document.getElementById('title').value.trim();
    
    if (!title) {
        alert('❗ Task title is required.');
        return false;
    }
    
    if (title.length > 255) {
        alert('❗ Task title must be less than 255 characters.');
        return false;
    }
    
    return true;
}
</script>

<?php include('footer.php'); ?>