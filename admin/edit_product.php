<?php
ob_start();
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Get product_id from URL
$id = intval($_GET['id'] ?? 0);

// Fetch product by product_id
$sql = "SELECT * FROM products WHERE product_id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("Error preparing product query: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
    die("Error fetching product data. Please try again.");
}
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: products.php');
    exit();
}

// Fetch existing images
$images = [];
$sql_images = "SELECT id, image_path FROM product_images WHERE product_id = ?";
$stmt_images = mysqli_prepare($conn, $sql_images);
if (!$stmt_images) {
    error_log("Error preparing images query: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
    die("Error fetching images. Please try again.");
}
mysqli_stmt_bind_param($stmt_images, 'i', $id);
mysqli_stmt_execute($stmt_images);
$result_images = mysqli_stmt_get_result($stmt_images);
while ($row = mysqli_fetch_assoc($result_images)) {
    $images[] = $row;
}
mysqli_stmt_close($stmt_images);

// Fetch brands, categories, and subcategories
$brands = mysqli_query($conn, "SELECT brand_id, brand_name FROM brands");
$categories = mysqli_query($conn, "SELECT category_id, category_name FROM categories");
$subcategories = mysqli_query($conn, "SELECT subcategory_id, subcategory_name FROM subcategories");

if (!$brands || !$categories || !$subcategories) {
    error_log("Error fetching data: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
    die("Error fetching data. Please try again.");
}

$error = '';
$success = '';
$upload_dir = __DIR__ . '/Uploads/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_file_size = 5 * 1024 * 1024; // 5MB

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $subcategory_id = intval($_POST['subcategory_id'] ?? 0);
    $brand_id = intval($_POST['brand_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $cost_price = floatval($_POST['cost_price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $min_stock_level = intval($_POST['min_stock_level'] ?? 0);
    $weight = floatval($_POST['weight'] ?? 0);
    $dimensions = trim($_POST['dimensions'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;
    $featured = isset($_POST['featured']) ? 1 : 0;
    $delete_images = $_POST['delete_images'] ?? [];

    // Input validation
    if (empty($product_name) || empty($sku) || $category_id <= 0 || $brand_id <= 0 || $price <= 0 || $quantity < 0) {
        $error = "Please fill all required fields with valid values.";
    } else {
        // Check for duplicate SKU (excluding current product)
        $sql_check = "SELECT product_id FROM products WHERE sku = ? AND product_id != ?";
        $stmt_check = mysqli_prepare($conn, $sql_check);
        if (!$stmt_check) {
            error_log("Error preparing SKU check: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
            die("Error checking SKU. Please try again.");
        }
        mysqli_stmt_bind_param($stmt_check, 'si', $sku, $id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Error: SKU '$sku' already exists.";
        } else {
            // Update product
            $sql = "UPDATE products SET 
                product_name=?, sku=?, description=?, category_id=?, subcategory_id=?, 
                brand_id=?, price=?, cost_price=?, quantity=?, min_stock_level=?, 
                weight=?, dimensions=?, status=?, featured=?
                WHERE product_id=?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                error_log("Error preparing update query: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
                die("Error updating product. Please try again.");
            }
           $types = 'sssiiiddiidsiii';
mysqli_stmt_bind_param($stmt, $types,
    $product_name, $sku, $description, 
    $category_id, $subcategory_id, $brand_id,
    $price, $cost_price, $quantity, 
    $min_stock_level, $weight, $dimensions,
    $status, $featured, $id

            );

            if (mysqli_stmt_execute($stmt)) {
                // Handle image deletions
                if (!empty($delete_images)) {
                    $placeholders = implode(',', array_fill(0, count($delete_images), '?'));
                    $sql_delete = "DELETE FROM product_images WHERE id IN ($placeholders) AND product_id = ?";
                    $stmt_delete = mysqli_prepare($conn, $sql_delete);
                    if (!$stmt_delete) {
                        error_log("Error preparing delete query: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
                        die("Error deleting images. Please try again.");
                    }
                    $types_delete = str_repeat('i', count($delete_images)) . 'i';
                    $params = array_merge($delete_images, [$id]);
                    mysqli_stmt_bind_param($stmt_delete, $types_delete, ...$params);
                    mysqli_stmt_execute($stmt_delete);
                    mysqli_stmt_close($stmt_delete);

                    // Delete files from server
                    foreach ($delete_images as $image_id) {
                        foreach ($images as $img) {
                            if ($img['id'] == $image_id && file_exists($img['image_path'])) {
                                unlink($img['image_path']);
                            }
                        }
                    }
                }

                // Handle new image uploads
                if (!empty($_FILES['images']['name'][0])) {
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    foreach ($_FILES['images']['name'] as $key => $image_name) {
                        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                            $image_tmp = $_FILES['images']['tmp_name'][$key];
                            $image_type = mime_content_type($image_tmp);
                            $image_size = $_FILES['images']['size'][$key];
                            $ext = pathinfo($image_name, PATHINFO_EXTENSION);
                            $new_name = uniqid('img_') . '.' . strtolower($ext);
                            $destination = $upload_dir . $new_name;

                            if (!in_array($image_type, $allowed_types)) {
                                $error = "Invalid file type for '$image_name'. Only JPG, PNG, GIF, WebP allowed.";
                                break;
                            } elseif ($image_size > $max_file_size) {
                                $error = "Image '$image_name' exceeds 5MB.";
                                break;
                            } elseif (move_uploaded_file($image_tmp, $destination)) {
                                $image_path = 'Uploads/' . $new_name;
                                $stmt_img = mysqli_prepare($conn, "INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
                                if (!$stmt_img) {
                                    error_log("Error preparing image insert: " . mysqli_error($conn), 3, __DIR__ . '/logs/db_errors.log');
                                    die("Error saving image. Please try again.");
                                }
                                mysqli_stmt_bind_param($stmt_img, 'is', $id, $image_path);
                                if (!mysqli_stmt_execute($stmt_img)) {
                                    $error = "Failed to save image '$image_name' to database.";
                                    unlink($destination); // Remove uploaded file on DB error
                                    break;
                                }
                                mysqli_stmt_close($stmt_img);
                            } else {
                                $error = "Failed to upload image '$image_name'.";
                                break;
                            }
                        }
                    }
                }

                if (!$error) {
                    $success = "Product updated successfully!";
                    header('Location: products.php');
                    exit();
                }
            } else {
                $error = "Error updating product: " . mysqli_stmt_error($stmt);
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
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .main-content { padding: 30px; max-width: 900px; margin: 0 auto; }
        .form-control, .form-select { border-radius: 10px; border: 1px solid #ced4da; transition: 0.3s; }
        .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 8px rgba(13, 110, 253, 0.2); }
        .btn-success, .btn-secondary { border-radius: 10px; transition: 0.3s; }
        .btn-success:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .btn-secondary:hover { transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .alert { border-radius: 10px; }
        .form-group { margin-bottom: 1.5rem; }
        label { font-weight: 600; color: #333; }
        .product-image { max-width: 100px; height: auto; border-radius: 5px; margin: 5px; }
        .image-container { display: flex; flex-wrap: wrap; gap: 10px; }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="animate__animated animate__fadeInDown">Edit Product</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success animate__animated animate__fadeIn"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" class="animate__animated animate__fadeInUp" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="product_name">Product Name *</label>
                    <input name="product_name" id="product_name" required class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" />
                </div>
                <div class="form-group">
                    <label for="sku">SKU *</label>
                    <input name="sku" id="sku" required class="form-control" value="<?= htmlspecialchars($product['sku']) ?>" />
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label for="category_id">Category *</label>
                    <select name="category_id" id="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        <?php mysqli_data_seek($categories, 0); while($c = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $c['category_id'] ?>" <?= $c['category_id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['category_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subcategory_id">Subcategory</label>
                    <select name="subcategory_id" id="subcategory_id" class="form-select">
                        <option value="">Select Subcategory</option>
                        <?php mysqli_data_seek($subcategories, 0); while($s = mysqli_fetch_assoc($subcategories)): ?>
                            <option value="<?= $s['subcategory_id'] ?>" <?= $s['subcategory_id'] == $product['subcategory_id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['subcategory_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="brand_id">Brand *</label>
                    <select name="brand_id" id="brand_id" class="form-select" required>
                        <option value="">Select Brand</option>
                        <?php mysqli_data_seek($brands, 0); while($b = mysqli_fetch_assoc($brands)): ?>
                            <option value="<?= $b['brand_id'] ?>" <?= $b['brand_id'] == $product['brand_id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['brand_name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="price">Price *</label>
                    <input name="price" id="price" type="number" step="0.01" required class="form-control" value="<?= $product['price'] ?>" />
                </div>
                <div class="form-group">
                    <label for="cost_price">Cost Price</label>
                    <input name="cost_price" id="cost_price" type="number" step="0.01" class="form-control" value="<?= $product['cost_price'] ?>" />
                </div>
                <div class="form-group">
                    <label for="quantity">Quantity *</label>
                    <input name="quantity" id="quantity" type="number" required class="form-control" value="<?= $product['quantity'] ?>" />
                </div>
                <div class="form-group">
                    <label for="min_stock_level">Minimum Stock Level</label>
                    <input name="min_stock_level" id="min_stock_level" type="number" class="form-control" value="<?= $product['min_stock_level'] ?>" />
                </div>
                <div class="form-group">
                    <label for="weight">Weight (kg)</label>
                    <input name="weight" id="weight" type="number" step="0.01" class="form-control" value="<?= $product['weight'] ?>" />
                </div>
                <div class="form-group">
                    <label for="dimensions">Dimensions (LxWxH)</label>
                    <input name="dimensions" id="dimensions" class="form-control" placeholder="e.g., 10x5x3" value="<?= htmlspecialchars($product['dimensions']) ?>" />
                </div>
                <div class="form-group">
                    <label>Current Images</label>
                    <div class="image-container">
                        <?php foreach ($images as $img): ?>
                            <div>
                                <img src="<?= htmlspecialchars($img['image_path']) ?>" alt="Product Image" class="product-image">
                                <div>
                                    <input type="checkbox" name="delete_images[]" value="<?= $img['id'] ?>" id="delete_<?= $img['id'] ?>">
                                    <label for="delete_<?= $img['id'] ?>">Delete</label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($images)): ?>
                            <p>No images available.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="images">Upload New Images</label>
                    <input name="images[]" id="images" type="file" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp" multiple />
                    <small class="form-text text-muted">Allowed formats: JPG, PNG, GIF, WebP. Max size: 5MB each.</small>
                </div>
                <div class="form-check mb-2">
                    <input name="status" id="status" type="checkbox" class="form-check-input" <?= $product['status'] ? 'checked' : '' ?> />
                    <label class="form-check-label" for="status">Active</label>
                </div>
                <div class="form-check mb-3">
                    <input name="featured" id="featured" type="checkbox" class="form-check-input" <?= $product['featured'] ? 'checked' : '' ?> />
                    <label class="form-check-label" for="featured">Featured Product</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Update Product</button>
        <a href="products.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php ob_end_flush(); ?>
</body>
</html>