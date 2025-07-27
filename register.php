<?php

session_start();


if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}

include('db.php');

$message = '';
$message_type = 'danger';


if (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
    $message = "✅ Your account has been deleted successfully.";
    $message_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);


    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "❗ Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❗ Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $message = "❗ Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $message = "❗ Password must be at least 6 characters long.";
    } else {

        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $message = "❌ Email already exists. Please use a different email.";
        } else {

            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
           $default_photo = 'default.jpg';
$stmt = $conn->prepare("INSERT INTO users (name, email, password, profile_photo) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashed_password, $default_photo);


            if ($stmt->execute()) {
                $message = "✅ Registration successful! You can now login.";
                $message_type = 'success';
            } else {
                $message = "❌ Registration failed: " . $conn->error;
            }
            $stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<?php include('header.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">Register... and BE HAPPY</h4>
                </div>
                <div class="card-body">
                    <?php if ($message): ?>
                        <div class="alert alert-<?= $message_type ?>"><?= $message ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" onsubmit="return validateRegisterForm();">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <small class="form-text text-muted">Minimum 6 characters</small>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Register</button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/validation.js"></script>

<?php include('footer.php'); ?>