<?php
// A protected page: only signed-in users can see it.
require __DIR__ . '/../includes/bootstrap.php';
require_login();
$user = current_user();

page_start('Home', 'page-login');
?>
<main class="center-layout">
  <section class="card card-success">
    <div class="card-logo"><?php logo(); ?></div>
    <h1 class="success-title">Hi, <?= e($user['first_name']) ?>!</h1>
    <p class="success-text">You're signed in as <strong><?= e($user['email']) ?></strong>.</p>
    <form method="post" action="logout.php" class="full">
      <?= csrf_field() ?>
      <button type="submit" class="btn btn-sub">Log out</button>
    </form>
  </section>
</main>
<?php page_end(); ?>
