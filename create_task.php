<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
    $user_id = $_SESSION['user_id'];

    if ($title !== '') {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, due_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $title, $description, $due_date);
        
        if ($stmt->execute()) {
            header("Location: tasks.php");
            exit();
        } else {
            $message = "❌ Failed to add task.";
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
                    <h4>Add New Task</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-warning"><?= $message ?></div>
                    <?php endif; ?>

                    <form method="POST" onsubmit="return validateTaskForm()">
                        <div class="mb-3">
                            <label for="title" class="form-label">Task Title *</label>
                            <input type="text" name="title" id="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4" placeholder="Enter task description..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="due_date" class="form-control" min="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="tasks.php" class="btn btn-secondary me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-success">Create Task</button>
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