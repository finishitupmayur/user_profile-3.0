<?php
// login.php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

include('db.php');

$message = '';
$message_type = 'danger';

// Check for logout message
if (isset($_GET['message']) && $_GET['message'] === 'logged_out') {
    $message = "✅ You have been logged out successfully.";
    $message_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "❗ Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                // Login success
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                header("Location: profile.php");
                exit();
            } else {
                $message = "❌ Incorrect password.";
            }
        } else {
            $message = "❌ No user found with this email.";
        }
        $stmt->close();
    }
}
?>

<?php include('header.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Login</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" onsubmit="return validateLoginForm();">
                        <div class="mb-3">
                            <label for="login_email" class="form-label">Email</label>
                            <input type="email" name="email" id="login_email" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="login_password" class="form-label">Password</label>
                            <input type="password" name="password" id="login_password" class="form-control" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">Login</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/validation.js"></script>

<?php include('footer.php'); ?>