<?php 


require_once 'includes/sidebar.php';
require_once 'includes/db_connection.php';

// Initialize default profile values
$profile = [
    'company_id' => 0,
    'company_name' => '',
    'email' => '',
    'phone' => '',
    'address' => '',
    'city' => '',
    'state' => '',
    'zip_code' => '',
    'country' => '',
    'logo' => '',
    'website' => '',
    'tax_id' => '',
    'currency' => 'USD',
    'created_at' => '',
    'updated_at' => ''
];

// Fetch company info from company_profile table
$sql = "SELECT * FROM company_profile LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $profile = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company_name = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $state = trim($_POST['state']);
    $zip_code = trim($_POST['zip_code']);
    $country = trim($_POST['country']);
    $website = trim($_POST['website']);
    $tax_id = trim($_POST['tax_id']);
    $currency = trim($_POST['currency']);
    
    if ($profile['company_id'] > 0) {
        // Update existing record
        $sql = "UPDATE company_profile SET 
                company_name=?, email=?, phone=?, address=?, city=?, state=?, 
                zip_code=?, country=?, website=?, tax_id=?, currency=?, updated_at=NOW() 
                WHERE company_id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssssssssssi', 
            $company_name, $email, $phone, $address, $city, $state, 
            $zip_code, $country, $website, $tax_id, $currency, $profile['company_id']
        );
    } else {
        // Insert new record
        $sql = "INSERT INTO company_profile 
                (company_name, email, phone, address, city, state, zip_code, country, website, tax_id, currency) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssssssssss', 
            $company_name, $email, $phone, $address, $city, $state, 
            $zip_code, $country, $website, $tax_id, $currency
        );
    }
     if (mysqli_stmt_execute($stmt)) {
        header('Location: company_profile.php');
        exit();
    } else {
        $error = "Error saving profile: " . mysqli_error($conn);
    }
   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Profile</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #6f42c1;
            --success-color: #1cc88a;
            --light-bg: #f8f9fc;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fb;
            color: #4e4e4e;
        }
        
        h2, h3, h4, h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .main-content {
            padding: 20px;
            animation: fadeIn 0.8s ease;
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 20px;
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .card-header {
            background: linear-gradient(120deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 500;
        }
        
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #ddd;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.3rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-success {
            background: linear-gradient(120deg, var(--success-color), #17a673);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .profile-info {
            background: linear-gradient(120deg, #f8f9fc, #e9ecef);
            border-radius: 10px;
            padding: 20px;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .animated {
            animation-duration: 1s;
            animation-fill-mode: both;
        }
        
        .fadeIn {
            animation-name: fadeIn;
        }
        
        .pulse {
            animation-name: pulse;
            animation-duration: 2s;
            animation-iteration-count: infinite;
        }
        
        .delay-1 {
            animation-delay: 0.2s;
        }
        
        .delay-2 {
            animation-delay: 0.4s;
        }
        
        .delay-3 {
            animation-delay: 0.6s;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #4a4a4a;
        }

         .main-content {
            padding: 20px;
            margin-left: 250px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
         }
    </style>
</head>
<body>
<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="animated fadeInDown mb-0"><i class="fas fa-building me-2"></i>Company Profile</h2>
                <p class="text-muted animated fadeIn delay-1">Manage your company information and settings</p>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger animated fadeIn delay-1"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card animated fadeIn delay-1">
                    <div class="card-header py-3">
                        <h5 class="m-0"><i class="fas fa-edit me-2"></i>Company Details</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Company Name *</label>
                                        <input name="company_name" value="<?= htmlspecialchars($profile['company_name']) ?>" required class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input name="email" value="<?= htmlspecialchars($profile['email']) ?>" type="email" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tax ID</label>
                                        <input name="tax_id" value="<?= htmlspecialchars($profile['tax_id']) ?>" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <input name="address" value="<?= htmlspecialchars($profile['address']) ?>" class="form-control" />
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City</label>
                                        <input name="city" value="<?= htmlspecialchars($profile['city']) ?>" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>State</label>
                                        <input name="state" value="<?= htmlspecialchars($profile['state']) ?>" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ZIP Code</label>
                                        <input name="zip_code" value="<?= htmlspecialchars($profile['zip_code']) ?>" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Country</label>
                                        <input name="country" value="<?= htmlspecialchars($profile['country']) ?>" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Currency</label>
                                        <select name="currency" class="form-control">
                                            <option value="INR" <?= $profile['currency'] == 'INR' ? 'selected' : '' ?>>INR - Indian Rupee</option>
                                            <option value="USD" <?= $profile['currency'] == 'USD' ? 'selected' : '' ?>>USD - US Dollar</option>
                                            <option value="EUR" <?= $profile['currency'] == 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                                            <option value="GBP" <?= $profile['currency'] == 'GBP' ? 'selected' : '' ?>>GBP - British Pound</option>
                                            <option value="JPY" <?= $profile['currency'] == 'JPY' ? 'selected' : '' ?>>JPY - Japanese Yen</option>
                                            <option value="CAD" <?= $profile['currency'] == 'CAD' ? 'selected' : '' ?>>CAD - Canadian Dollar</option>
                                            <option value="AUD" <?= $profile['currency'] == 'AUD' ? 'selected' : '' ?>>AUD - Australian Dollar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Website</label>
                                <input name="website" value="<?= htmlspecialchars($profile['website']) ?>" type="url" class="form-control" placeholder="https://..." />
                            </div>

                            <button type="submit" class="btn btn-success pulse animated"><i class="fa fa-save me-2"></i> Update Company Profile</button>
                            
                            <?php if ($profile['company_id'] == 0): ?>
                                <div class="alert alert-info mt-3 animated fadeIn">
                                    <i class="fa fa-info-circle me-2"></i> No company profile found. Fill out the form to create one.
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Display current profile info -->
                <?php if ($profile['company_id'] > 0): ?>
                <div class="card animated fadeIn delay-2">
                    <div class="card-header py-3">
                        <h5 class="m-0"><i class="fas fa-info-circle me-2"></i>Profile Information</h5>
                    </div>
                    <div class="card-body profile-info">
                        <div class="mb-3 text-center">
                            <i class="fas fa-building fa-3x text-primary mb-2"></i>
                            <h4><?= htmlspecialchars($profile['company_name']) ?></h4>
                        </div>
                        <div class="mb-3">
                            <p><strong><i class="fas fa-calendar-alt me-2"></i>Last Updated:</strong><br>
                            <?= date('M j, Y g:i A', strtotime($profile['updated_at'])) ?></p>
                            
                            <p><strong><i class="fas fa-calendar-plus me-2"></i>Created:</strong><br>
                            <?= date('M j, Y g:i A', strtotime($profile['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="card animated fadeIn delay-2">
                    <div class="card-header py-3">
                        <h5 class="m-0"><i class="fas fa-lightbulb me-2"></i>Quick Tips</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Fill all required fields marked with *</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Keep your information up to date</li>
                            <li class="list-group-item"><i class="fas fa-check-circle text-success me-2"></i>Double-check your tax ID before saving</li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    // Add some interactive animations
    document.addEventListener('DOMContentLoaded', function() {
        const formInputs = document.querySelectorAll('.form-control');
        
        formInputs.forEach(input => {
            // Add focus effect
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('pulse');
            });
            
            // Remove effect when focus is lost
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('pulse');
            });
        });
    });
</script>
</body>
</html>
<?php require_once 'includes/footer.php'; ?>