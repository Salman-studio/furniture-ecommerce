<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Fetch dropdown data
$brands = mysqli_query($conn, "SELECT brand_id, brand_name FROM brands");
$categories = mysqli_query($conn, "SELECT category_id, category_name FROM categories");
$subcategories = mysqli_query($conn, "SELECT subcategory_id, subcategory_name FROM subcategories");

if (!$brands || !$categories || !$subcategories) {
    die("Database error: " . mysqli_error($conn));
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'product_name' => trim($_POST['product_name']),
        'sku' => trim($_POST['sku']),
        'description' => trim($_POST['description']),
        'category_id' => intval($_POST['category_id']),
        'subcategory_id' => intval($_POST['subcategory_id']),
        'brand_id' => intval($_POST['brand_id']),
        'price' => floatval($_POST['price']),
        'cost_price' => floatval($_POST['cost_price']),
        'quantity' => intval($_POST['quantity']),
        'min_stock_level' => intval($_POST['min_stock_level']),
        'weight' => floatval($_POST['weight']),
        'dimensions' => trim($_POST['dimensions']),
        'status' => isset($_POST['status']) ? 1 : 0,
        'featured' => isset($_POST['featured']) ? 1 : 0,
    ];

    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_type = mime_content_type($file_tmp);
        $file_size = $_FILES['image']['size'];

        if (!in_array($file_type, $allowed_types)) {
            $error = "Only JPG, PNG, GIF, and WebP images are allowed.";
        } elseif ($file_size > 5 * 1024 * 1024) {
            $error = "Image must be smaller than 5MB.";
        } else {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_name = uniqid('product_') . '.' . strtolower($ext);
            $upload_dir = __DIR__ . '/uploads/';
            if (!is_dir($upload_dir) && !mkdir($upload_dir, 0777, true)) {
                $error = "Failed to create uploads directory.";
            } else {
                $destination = $upload_dir . $new_name;
                if (move_uploaded_file($file_tmp, $destination)) {
                    $image_path = 'uploads/' . $new_name;
                } else {
                    $error = "Failed to move uploaded file.";
                }
            }
        }
    }

    if (!$error) {
        $stmt_check = mysqli_prepare($conn, "SELECT product_id FROM products WHERE sku = ?");
        mysqli_stmt_bind_param($stmt_check, 's', $data['sku']);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "SKU '{$data['sku']}' already exists.";
        } else {
            $image_path = $image_path ?? '';
            $stmt = mysqli_prepare($conn, "INSERT INTO products (product_name, sku, description, category_id, subcategory_id, brand_id, price, cost_price, quantity, min_stock_level, weight, dimensions, image, status, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sssiiiddiidssii', $data['product_name'], $data['sku'], $data['description'], $data['category_id'], $data['subcategory_id'], $data['brand_id'], $data['price'], $data['cost_price'], $data['quantity'], $data['min_stock_level'], $data['weight'], $data['dimensions'], $image_path, $data['status'], $data['featured']);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Product added successfully!";
                // Reset form data
                $data = array_map(function($value) { return ''; }, $data);
                $data['min_stock_level'] = 5;
            } else {
                $error = "Database error: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($stmt_check);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .main-content { padding: 30px; max-width: 900px; margin: 0 auto; }
        .form-control, .form-select { border-radius: 10px; transition: border-color 0.3s; }
        .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 8px rgba(13, 110, 253, 0.2); }
        .btn-primary { border-radius: 10px; transition: transform 0.3s; }
        .btn-primary:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { border-radius: 10px; }
        .form-group { margin-bottom: 1.5rem; }
        label { font-weight: 600; color: #333; }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="animate__animated animate__fadeInDown">Add Product</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="animate__animated animate__fadeInUp">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product_name">Product Name *</label>
                    <input name="product_name" id="product_name" required class="form-control" value="<?php echo htmlspecialchars($data['product_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="sku">SKU *</label>
                    <input name="sku" id="sku" required class="form-control" value="<?php echo htmlspecialchars($data['sku'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $c['category_id']; ?>" <?php echo (($data['category_id'] ?? '') == $c['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['category_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subcategory_id">Subcategory</label>
                    <select name="subcategory_id" id="subcategory_id" class="form-select">
                        <option value="">Select Subcategory</option>
                        <?php mysqli_data_seek($subcategories, 0); while ($s = mysqli_fetch_assoc($subcategories)): ?>
                            <option value="<?php echo $s['subcategory_id']; ?>" <?php echo (($data['subcategory_id'] ?? '') == $s['subcategory_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['subcategory_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="brand_id">Brand *</label>
                    <select name="brand_id" id="brand_id" class="form-select" required>
                        <option value="">Select Brand</option>
                        <?php mysqli_data_seek($brands, 0); while ($b = mysqli_fetch_assoc($brands)): ?>
                            <option value="<?php echo $b['brand_id']; ?>" <?php echo (($data['brand_id'] ?? '') == $b['brand_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['brand_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price">Price *</label>
                    <input name="price" id="price" type="number" step="0.01" required class="form-control" value="<?php echo htmlspecialchars($data['price'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="cost_price">Cost Price</label>
                    <input name="cost_price" id="cost_price" type="number" step="0.01" class="form-control" value="<?php echo htmlspecialchars($data['cost_price'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity *</label>
                    <input name="quantity" id="quantity" type="number" required class="form-control" value="<?php echo htmlspecialchars($data['quantity'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="min_stock_level">Minimum Stock Level</label>
                    <input name="min_stock_level" id="min_stock_level" type="number" class="form-control" value="<?php echo htmlspecialchars($data['min_stock_level'] ?? '5'); ?>">
                </div>
                <div class="form-group">
                    <label for="weight">Weight (kg)</label>
                    <input name="weight" id="weight" type="number" step="0.01" class="form-control" value="<?php echo htmlspecialchars($data['weight'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="dimensions">Dimensions (LxWxH)</label>
                    <input name="dimensions" id="dimensions" class="form-control" placeholder="e.g., 10x5x3" value="<?php echo htmlspecialchars($data['dimensions'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input name="image" id="image" type="file" class="form-control" accept="image/*">
                </div>
                <div class="form-check mb-2">
                    <input name="status" id="status" type="checkbox" class="form-check-input" <?php echo (!isset($data['status']) || $data['status']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="status">Active</label>
                </div>
                <div class="form-check mb-3">
                    <input name="featured" id="featured" type="checkbox" class="form-check-input" <?php echo (isset($data['featured']) && $data['featured']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="featured">Featured Product</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Save Product</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php ob_end_flush(); ?>
</body>
</html>