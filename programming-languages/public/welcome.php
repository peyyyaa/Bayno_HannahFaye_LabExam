<?php
// The "Signed in successfully" card (Figma frame "Frame 321").
require __DIR__ . '/../includes/bootstrap.php';
require_login();

$kind = $_SESSION['welcome'] ?? null;
unset($_SESSION['welcome']);
if (!$kind) {
    redirect('dashboard.php');   // only shown once, right after signing in
}

$user = current_user();
$title   = $kind === 'register' ? 'Welcome, ' . $user['first_name'] . '!' : 'Welcome back!';
$message = $kind === 'register' ? 'Your account has been created.' : 'Signed in successfully.';

page_start('Welcome', 'page-login');
?>
<main class="center-layout">
  <section class="card card-success" aria-live="polite">
    <div class="card-logo"><?php logo(); ?></div>
    <h1 class="success-title"><?= e($title) ?></h1>
    <p class="success-text"><?= e($message) ?></p>
    <a class="btn btn-primary" href="dashboard.php">Continue</a>
  </section>
</main>
<?php page_end(); ?>
