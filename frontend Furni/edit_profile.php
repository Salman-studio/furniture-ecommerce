<?php
// furniture/edit_profile.php
require_once __DIR__ . '/includes/middleware/auth.php';
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/header.php';

$pageTitle = 'Edit Profile';
$errors = [];

// Fetch user data
$stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, profile_image FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid CSRF token.';
    } else {
        $first_name = sanitize_input($_POST['first_name'] ?? '');
        $last_name  = sanitize_input($_POST['last_name'] ?? '');
        $email      = sanitize_input($_POST['email'] ?? '');
        
        // Handle profile image upload
        $profile_image = $user['profile_image'];
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $file_tmp  = $_FILES['profile_image']['tmp_name'];
            $file_name = uniqid() . '_' . basename($_FILES['profile_image']['name']);
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp, $file_path)) {
                $profile_image = $file_name;
            } else {
                $errors[] = 'Failed to upload profile image.';
            }
        }

        // Update user if no errors
        if (empty($errors)) {
            $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, profile_image = ? WHERE user_id = ?");
            $stmt->bind_param("ssssi", $first_name, $last_name, $email, $profile_image, $_SESSION['user_id']);
            $stmt->execute();

            flash_set('success', 'Profile updated.');
            redirect('profile.php');
        }
    }
}
?>

<div class="col-md-6 mx-auto my-4">
    <?php if ($msg = flash_get('success')): ?>
        <div class="alert alert-success"><?php echo e($msg); ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?php echo e(implode(', ', $errors)); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="card p-3">
        <?php echo csrf_field(); ?>

        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input name="first_name" class="form-control" value="<?php echo e($user['first_name'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input name="last_name" class="form-control" value="<?php echo e($user['last_name'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" value="<?php echo e($user['email'] ?? '') ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Profile Image</label>
            <?php if (!empty($user['profile_image'])): ?>
                <div class="mb-2">
                    <img src="uploads/<?php echo e($user['profile_image']); ?>" alt="Profile Image" style="max-width:100px;">
                </div>
            <?php endif; ?>
            <input type="file" name="profile_image" class="form-control">
        </div>

        <button class="btn btn-primary-custom">Save</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
