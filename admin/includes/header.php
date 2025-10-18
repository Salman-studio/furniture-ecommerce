<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | Furniture Store</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">

    <!-- Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f7f9fc;
            color: #333;
        }
        /* Header */
        .admin-header {
            background: linear-gradient(90deg, #2c3e50, #34495e);
            color: #fff;
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: fadeInDown 0.7s ease-in-out;
        }
        .admin-header h2 {
            font-weight: 600;
            font-size: 22px;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .logo {
            height: 42px;
            margin-right: 12px;
            transition: transform 0.4s ease-in-out;
        }
        .logo:hover {
            transform: rotate(-5deg) scale(1.05);
        }
        /* User info */
        .admin-user {
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-user i {
            font-size: 18px;
            margin-right: 6px;
        }
        .admin-user .btn {
            padding: 4px 12px;
            font-size: 13px;
            border-radius: 20px;
            transition: all 0.3s ease-in-out;
        }
        .admin-user .btn:hover {
            background: #f1c40f;
            color: #2c3e50;
            transform: scale(1.05);
        }
        /* Smooth fade animation */
        @keyframes fadeInDown {
            from {
                transform: translateY(-40px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Admin Header -->
    <div class="admin-header">
        <div class="d-flex align-items-center animate__animated animate__fadeInLeft">
            <img src="assets/images/logo.png" alt="Logo" class="logo">
            <h2 class="animate__animated animate__fadeIn">Furniture Admin Panel</h2>
        </div>
        <div class="admin-user animate__animated animate__fadeInRight">
            <i class="fa fa-user-circle"></i> 
            <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>
            <a href="logout.php" class="btn btn-light btn-sm">
                <i class="fa fa-sign-out"></i> Logout
            </a>
        </div>
    </div>
