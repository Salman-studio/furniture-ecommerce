<?php
// furniture/contact.php
$pageTitle = 'Contact';
require_once __DIR__ . '/includes/header.php';
?>
<div class="my-4">
  <h3>Contact Us</h3>
  <p>Please use any of the channels below to reach us.</p>
  <?php include __DIR__ . '/includes/chat_integrations.php'; ?>

  <form class="card p-3 mt-3" method="post" action="contact.php">
    <?php echo csrf_field(); ?>
    <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Message</label><textarea name="message" class="form-control" required></textarea></div>
    <button class="btn btn-primary-custom">Send</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
