<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

$id = intval($_GET['id'] ?? 0);

// Fetch category
$sql = "SELECT * FROM categories WHERE category_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$cat = mysqli_fetch_assoc($result);

if (!$cat) {
    header('Location: categories.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);
    $image = $cat['image']; // keep old image by default

    // Handle image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/categories/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $image = $fileName;

                // delete old image if it exists
                if (!empty($cat['image']) && file_exists($targetDir . $cat['image'])) {
                    unlink($targetDir . $cat['image']);
                }
            } else {
                $error = "Error uploading image.";
            }
        } else {
            $error = "Invalid file type. Allowed: jpg, jpeg, png, gif, webp.";
        }
    }

    // Check for existing category name
    $sql_check = "SELECT category_id FROM categories WHERE category_name = ? AND category_id != ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, 'si', $name, $id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $error = "Error: Category '$name' already exists.";
    } elseif (empty($error)) {
        // Update category
        $sql = "UPDATE categories SET category_name=?, description=?, image=? WHERE category_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssi', $name, $desc, $image, $id);
        if (mysqli_stmt_execute($stmt)) {
            header('Location: categories.php');
            exit();
        } else {
            $error = "Error updating category: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_stmt_close($stmt_check);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .main-content { padding: 30px; max-width: 600px; margin: 0 auto; }
        .form-control { border-radius: 10px; }
        .btn-success { background: linear-gradient(135deg, #28a745, #20c997); border: none; border-radius: 10px; }
        .btn-secondary { background: linear-gradient(135deg, #6c757d, #adb5bd); border: none; border-radius: 10px; }
        .alert { border-radius: 10px; }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="animate__animated animate__fadeInDown">Edit Category</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data" class="animate__animated animate__fadeInUp">
        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input name="name" id="name" value="<?= htmlspecialchars($cat['category_name']) ?>" required class="form-control" />
        </div>
        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($cat['description']) ?></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="image">Image</label><br>
            <?php if (!empty($cat['image'])): ?>
                <img src="uploads/categories/<?= htmlspecialchars($cat['image']) ?>" alt="Category Image" width="100" class="mb-2 rounded">
            <?php endif; ?>
            <input type="file" name="image" id="image" class="form-control">
        </div>
        <button class="btn btn-success"><i class="fa fa-save"></i> Update</button>
        <a href="categories.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
<?php ob_end_flush(); ?>
<?php require_once 'includes/footer.php'; ?>
