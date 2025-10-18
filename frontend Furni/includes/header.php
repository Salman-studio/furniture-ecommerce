<?php
// furniture/includes/header.php
// Page header included on every page. Starts session and outputs <head> with meta tags, CSS/JS includes.
// Important: avoid sending output before session_start in other includes.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/functions.php';
$config = require __DIR__ . '/config.php';
$siteName = $config['site']['name'] ?? 'Furniture Shop';

?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
  <title><?php echo e($siteName); ?> - <?php echo e($pageTitle ?? ''); ?></title>
  <meta name="description" content="Premium handcrafted furniture and repair services.">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-Avb2QiuDEEvB4bZJYdft2mNjVShBftLdPG8FJ0V7irTLQ8Uo0qcPxh4Plq7G5tGm0rU+1SPhVotteLpBERwTkw==" crossorigin="anonymous" />
  
  <!-- Custom CSS -->
  <style>
    :root {
      --primary-color: #8B5A2B;
      --secondary-color: #D2B48C;
      --accent-color: #A52A2A;
      --light-color: #F5F5DC;
      --dark-color: #3E2723;
      --transition: all 0.3s ease;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      color: #333;
      background-color: #f8f9fa;
    }
    
    h1, h2, h3, h4, h5, h6, .navbar-brand {
      font-family: 'Playfair Display', serif;
    }
    
    /* Navbar styling */
    .navbar {
      background: linear-gradient(to right, var(--primary-color), var(--dark-color));
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      padding: 0.8rem 1rem;
      transition: var(--transition);
    }
    
    .navbar-brand {
      font-weight: 700;
      font-size: 1.8rem;
      color: var(--light-color) !important;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
      transition: var(--transition);
    }
    
    .navbar-brand:hover {
      transform: scale(1.05);
    }
    
    .nav-link {
      color: rgba(255, 255, 255, 0.85) !important;
      font-weight: 500;
      padding: 0.5rem 1rem !important;
      margin: 0 0.2rem;
      border-radius: 4px;
      transition: var(--transition);
      position: relative;
    }
    
    .nav-link:before {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      width: 0;
      height: 2px;
      background: var(--secondary-color);
      transition: var(--transition);
      transform: translateX(-50%);
    }
    
    .nav-link:hover {
      color: white !important;
      background-color: rgba(255, 255, 255, 0.1);
    }
    
    .nav-link:hover:before {
      width: 80%;
    }
    
    .navbar-toggler {
      border: none;
      color: white !important;
    }
    
    .navbar-toggler:focus {
      box-shadow: none;
    }
    
    /* Hero section styling (if used) */
    .hero-section {
      background: url('https://images.unsplash.com/photo-1556228453-efd6c1ff04f6?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
      background-size: cover;
      padding: 6rem 0;
      position: relative;
    }
    
    .hero-section:before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
    }
    
    .hero-content {
      position: relative;
      color: white;
    }
    
    /* Animation classes */
    .fade-in {
      animation: fadeIn 1s ease-in;
    }
    
    .slide-in-left {
      animation: slideInLeft 0.5s ease-out;
    }
    
    .slide-in-right {
      animation: slideInRight 0.5s ease-out;
    }
    
    .pulse {
      animation: pulse 2s infinite;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }
    
    @keyframes slideInLeft {
      from { 
        transform: translateX(-100px);
        opacity: 0;
      }
      to { 
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes slideInRight {
      from { 
        transform: translateX(100px);
        opacity: 0;
      }
      to { 
        transform: translateX(0);
        opacity: 1;
      }
    }
    
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    
    /* Button styling */
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      transition: var(--transition);
    }
    
    .btn-primary:hover {
      background-color: var(--dark-color);
      border-color: var(--dark-color);
      transform: translateY(-2px);
    }
    
    /* Card styling */
    .card {
      border: none;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      transition: var(--transition);
    }
    
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
      .navbar-brand {
        font-size: 1.5rem;
      }
      
      .hero-section {
        padding: 4rem 0;
      }
    }
  </style>
</head>
<body>
  <?php include __DIR__ . '/navbar.php'; ?>

  <main class="container-fluid px-0">
    <!-- Page content begins; remember to close container in footer -->