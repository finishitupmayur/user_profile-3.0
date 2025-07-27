<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php');
$user_id = $_SESSION['user_id'];


if (isset($_GET['toggle_complete'])) {
    $task_id = intval($_GET['toggle_complete']);
    

    $stmt = $conn->prepare("SELECT completed FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $task = $result->fetch_assoc();
        $new_status = $task['completed'] ? 0 : 1; 
        
        
        $update_stmt = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ? AND user_id = ?");
        $update_stmt->bind_param("iii", $new_status, $task_id, $user_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
    $stmt->close();
    
    
    header("Location: tasks.php");
    exit();
}


$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY completed ASC, created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


$count_stmt = $conn->prepare("SELECT 
    SUM(CASE WHEN completed = 1 THEN 1 ELSE 0 END) as completed_count,
    SUM(CASE WHEN completed = 0 THEN 1 ELSE 0 END) as pending_count,
    COUNT(*) as total_count
    FROM tasks WHERE user_id = ?");
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$counts = $count_stmt->get_result()->fetch_assoc();
$count_stmt->close();
?>

<?php include('header.php'); ?>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-4">📝 Your Tasks</h2>
            

            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3><?= $counts['total_count'] ?></h3>
                            <p>Total Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3><?= $counts['pending_count'] ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3><?= $counts['completed_count'] ?></h3>
                            <p>Completed</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3><?= $counts['total_count'] > 0 ? round(($counts['completed_count'] / $counts['total_count']) * 100) : 0 ?>%</h3>
                            <p>Progress</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <a href="create_task.php" class="btn btn-primary mb-3">+ Add New Task</a>
        </div>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Status</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Due Date</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($task = $result->fetch_assoc()): ?>
                        <tr class="<?= $task['completed'] ? 'table-success' : '' ?>">
                            <td class="text-center">
                                <a href="tasks.php?toggle_complete=<?= $task['id'] ?>" 
                                   class="btn btn-sm <?= $task['completed'] ? 'btn-success' : 'btn-outline-secondary' ?>"
                                   title="<?= $task['completed'] ? 'Mark as Pending' : 'Mark as Complete' ?>">
                                    <?= $task['completed'] ? '✓' : '○' ?>
                                </a>
                            </td>
                            <td class="<?= $task['completed'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                <?= htmlspecialchars($task['title']) ?>
                            </td>
                            <td class="<?= $task['completed'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                <?= nl2br(htmlspecialchars(substr($task['description'], 0, 100))) ?>
                                <?= strlen($task['description']) > 100 ? '...' : '' ?>
                            </td>
                            <td class="<?= $task['completed'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                <?= $task['due_date'] ? date('d M Y', strtotime($task['due_date'])) : 'No due date' ?>
                            </td>
                            <td><?= date('d M Y', strtotime($task['created_at'])) ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="edit_task.php?id=<?= $task['id'] ?>" 
                                       class="btn btn-sm btn-warning" title="Edit Task">Edit</a>
                                    <a href="delete_task.php?id=<?= $task['id'] ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this task?');" 
                                       title="Delete Task">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <h4 class="text-muted">No tasks found</h4>
            <p class="text-muted">Click "Add New Task" to create your first task.</p>
            <a href="create_task.php" class="btn btn-primary">Add New Task</a>
        </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>