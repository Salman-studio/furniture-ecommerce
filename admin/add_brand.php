
<?php
ob_start(); // Start output buffering
require_once 'includes/header.php';
require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand_name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    // Check for existing brand
    $sql_check = "SELECT brand_id FROM brands WHERE brand_name = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, 's', $brand_name);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    
    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $error = "Error: Brand '$brand_name' already exists.";
    } else {
        $sql = "INSERT INTO brands (brand_name, description) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $brand_name, $description);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: brands.php');
                exit();
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            $error = "Error preparing statement: " . mysqli_error($conn);
        }
    }
    mysqli_stmt_close($stmt_check);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .main-content {
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.2);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-success:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        .alert {
            border-radius: 10px;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        label {
            font-weight: 600;
            color: #333;
        }
    </style>
</head>
<body>
<div class="main-content">
    <h2 class="animate__animated animate__fadeInDown">Add Brand</h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__fadeIn"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST" class="animate__animated animate__fadeInUp">
        <div class="form-group">
            <label for="name">Brand Name</label>
            <input name="name" id="name" required class="form-control" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" />
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
<?php ob_end_flush(); // Flush output buffer ?>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>
