<?php

session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('db.php');
if (!isset($conn) || !$conn) {
    die("Database connection failed.");
}

$user_id = $_SESSION['user_id'];
$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $upload_dir = 'uploads/';


    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $file = $_FILES['profile_photo'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; 

    if ($file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $file_name = time() . '_' . basename($file['name']);
            $upload_path = $upload_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                
                $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
                if (!$stmt) {
                    die("Prepare failed (UPDATE): " . $conn->error);
                }
                $stmt->bind_param("si", $file_name, $user_id);

                if ($stmt->execute()) {
                    $message = "✅ Profile photo updated successfully!";        
                } else {
                    $message = "❌ Failed to update profile photo in database.";
                }
                $stmt->close();
            } else {
                $message = "❌ Failed to upload file.";
            }
        } else {
            $message = "❌ Invalid file type or size too large (max 5MB). Allowed: JPG, PNG, GIF";
        }
    } else {
        $message = "❌ File upload error.";
    }
}


$stmt = $conn->prepare("SELECT name, email, profile_photo, created_at FROM users WHERE id = ?");
if (!$stmt) {
    die("Prepare failed (SELECT): " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    $user = [
        'name' => 'Unknown',
        'email' => 'Unknown',
        'profile_photo' => 'default.jpg',
        'created_at' => date('Y-m-d')
    ];
}
$stmt->close();
?>

<?php include('header.php'); ?>

<div class="container mt-5">
    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <div class="row">
       <div class="col-md-4">
    <div class="card">
        <div class="card-body text-center">
            <?php 
            $photo_path = 'uploads/' . $user['profile_photo'];
            if (empty($user['profile_photo']) || !file_exists($photo_path) || $user['profile_photo'] === 'default.jpg') {
                $photo_path = 'https://via.placeholder.com/150x150/6c757d/ffffff?text=No+Photo';
            }
            ?>
            <img src="<?= $photo_path ?>" alt="Profile Photo" class="profile-photo mb-3 rounded-circle" width="150" height="150">
            <h5><?= htmlspecialchars($user['name']) ?></h5>
            <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
            <p class="text-muted">Joined: <?= date('d M Y', strtotime($user['created_at'])) ?></p>


            <form method="POST" enctype="multipart/form-data" class="mt-3">
                <div class="mb-3">
                    <input type="file" class="form-control" name="profile_photo" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Update Photo</button>
            </form>
        </div>
    </div>
</div>

                    <h5><?= htmlspecialchars($user['name']) ?></h5>
                    <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                    <p class="text-muted">Joined: <?= date('d M Y', strtotime($user['created_at'])) ?></p>


                    <form method="POST" enctype="multipart/form-data" class="mt-3">
                        <div class="mb-3">
                            <input type="file" class="form-control" name="profile_photo" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Update Photo</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>👤 Welcome, <?= htmlspecialchars($user['name']) ?></h4>
                </div>
                <div class="card-body">
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                    <p><strong>Joined:</strong> <?= date('d M Y', strtotime($user['created_at'])) ?></p>

                    <div class="mt-4">
                        <a href="edit_profile.php" class="btn btn-warning me-2">Edit Profile</a>
                        <a href="delete_profile.php" class="btn btn-danger me-2" onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone!');">Delete Account</a>
                        <a href="tasks.php" class="btn btn-primary">Manage Tasks</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>
