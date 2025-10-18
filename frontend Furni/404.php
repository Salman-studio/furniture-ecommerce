<?php
// furniture/404.php
http_response_code(404);
$pageTitle = 'Page Not Found';
require_once __DIR__ . '/includes/header.php';
?>
<div class="my-5 text-center">
  <h1>404</h1>
  <p>Sorry, the page you requested was not found.</p>
  <a href="index.php" class="btn btn-primary-custom">Go Home</a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
