<?php
// furniture/search.php
$pageTitle = 'Search';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/db_connection.php';

$q = sanitize_input($_GET['q'] ?? '');

?>
<div class="my-4">
  <h3>Search results for: <?php echo e($q); ?></h3>
  <p class="small-muted">Showing sample placeholder results. Implement DB search with LIKE or full-text for production.</p>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
