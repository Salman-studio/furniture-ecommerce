<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Fetch products for dropdown
$products = [];
$result = mysqli_query($conn, "SELECT product_id, product_name FROM products ORDER BY product_name");
if (!$result) {
    error_log("Error fetching products: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
    die("Error fetching products. Please try again.");
}
while ($row = mysqli_fetch_assoc($result)) {
    $products[$row['product_id']] = $row['product_name'];
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Validate product_id
    if ($product_id <= 0 || !array_key_exists($product_id, $products)) {
        $error = "Invalid product selected.";
    } elseif (!empty($_FILES['images']['name'][0])) {
        $upload_dir = __DIR__ . '/Uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_file_size = 5 * 1024 * 1024; // 5MB

        foreach ($_FILES['images']['name'] as $key => $image_name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $image_tmp = $_FILES['images']['tmp_name'][$key];
                $image_type = mime_content_type($image_tmp);
                $image_size = $_FILES['images']['size'][$key];
                $ext = pathinfo($image_name, PATHINFO_EXTENSION);
                $image_new_name = uniqid('img_') . '.' . strtolower($ext);
                $image_path = $upload_dir . $image_new_name;

                if (!in_array($image_type, $allowed_types)) {
                    $error = "Invalid file type for '$image_name'. Only JPG, PNG, GIF, WebP allowed.";
                    break;
                } elseif ($image_size > $max_file_size) {
                    $error = "Image '$image_name' exceeds 5MB.";
                    break;
                } elseif (move_uploaded_file($image_tmp, $image_path)) {
                    $relative_path = 'Uploads/' . $image_new_name;
                    $stmt = mysqli_prepare($conn, "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                    mysqli_stmt_bind_param($stmt, "is", $product_id, $relative_path);
                    if (!mysqli_stmt_execute($stmt)) {
                        $error = "Failed to save image '$image_name' to database.";
                        unlink($image_path); // Remove uploaded file on DB error
                        break;
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Failed to upload image '$image_name'.";
                    break;
                }
            } elseif ($_FILES['images']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                $error = "Error uploading '$image_name'.";
                break;
            }
        }
        if (!$error) {
            $success = "Images uploaded successfully!";
        }
    } else {
        $error = "No images selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Images</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .main-content { padding: 30px; max-width: 800px; margin: 0 auto; }
        .form-control, .form-select { border-radius: 10px; transition: border-color 0.3s; }
        .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 8px rgba(13, 110, 253, 0.2); }
        .btn-success { border-radius: 10px; transition: transform 0.3s; }
        .btn-success:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .btn-secondary { border-radius: 10px; }
        .alert { border-radius: 10px; }
        .form-group { margin-bottom: 1.5rem; }
        label { font-weight: 600; color: #333; }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="animate__animated animate__fadeInDown">Add Images to Product</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="animate__animated animate__fadeInUp">
        <div class="form-group">
            <label for="product_id">Select Product *</label>
            <select name="product_id" id="product_id" class="form-select" required>
                <option value="">Select Product</option>
                <?php foreach ($products as $id => $name): ?>
                    <option value="<?= $id ?>" <?= (isset($_POST['product_id']) && $_POST['product_id'] == $id) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="images">Upload Images *</label>
            <input type="file" name="images[]" id="images" multiple accept="image/jpeg,image/png,image/gif,image/webp" class="form-control" required>
            <small class="form-text text-muted">Allowed formats: JPG, PNG, GIF, WebP. Max size: 5MB each. Multiple files allowed.</small>
        </div>

        <button type="submit" class="btn btn-success"><i class="fa fa-upload"></i> Upload Images</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php ob_end_flush(); ?>
</body>
</html>