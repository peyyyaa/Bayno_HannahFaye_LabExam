<?php
require __DIR__ . '/../includes/bootstrap.php';
redirect_if_logged_in();

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = post('email');
    $password = $_POST['password'] ?? '';
    $remember = !empty($_POST['remember']);

    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Please try again.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } elseif ($password === '') {
        $errors['password'] = 'Enter your password.';
    } else {
        $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Always run password_verify, even for unknown emails, so response
        // time doesn't reveal which emails have accounts.
        $hash = $user['password_hash'] ?? '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG';
        if ($user && password_verify($password, $hash)) {
            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                    ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
            }
            login_user((int) $user['id'], $remember);
            $_SESSION['welcome'] = 'login';
            redirect('welcome.php');
        }
        // Same message for both cases: don't reveal whether the email exists.
        $errors['password'] = 'Incorrect email or password.';
        $errors['email_invalid'] = true;
    }
}

page_start('Sign in', 'page-login');
?>
<main class="login-layout">
  <section class="card card-login" aria-labelledby="login-title">
    <div class="card-logo"><?php logo(); ?></div>

    <form class="card-form" method="post" action="login.php" novalidate>
      <?= csrf_field() ?>
      <div class="card-heading">
        <h1 id="login-title">Welcome back</h1>
        <p>Sign in to continue to the app</p>
      </div>

      <?php if (!empty($errors['form'])): ?>
        <p class="form-alert"><?= icon('error', 18) ?><?= e($errors['form']) ?></p>
      <?php endif; ?>

      <?php field('email', 'Email address', [
          'type' => 'email', 'placeholder' => 'you@company.com',
          'value' => $email, 'autocomplete' => 'email',
          'error' => $errors['email'] ?? '',
          'invalid' => !empty($errors['email_invalid']),
      ]); ?>

      <?php field('password', 'Password', [
          'type' => 'password', 'placeholder' => 'Enter your password',
          'autocomplete' => 'current-password',
          'error' => $errors['password'] ?? '',
          'after' => '<div class="field-row">'
                   . checkbox('remember', 'Remember me', !empty($_POST['remember']))
                   . '<a class="link-sm" href="#" data-soon="Password reset is coming soon.">Forgot Password?</a>'
                   . '</div>',
      ]); ?>

      <div class="card-actions">
        <button type="submit" class="btn btn-primary">Sign In</button>
        <span class="or">or</span>
        <button type="button" class="btn btn-sub" data-soon="Email link sign-in is coming soon.">Sign in with email</button>
        <button type="button" class="btn btn-sub" data-soon="Google sign-in is coming soon.">Sign in with Google</button>
      </div>
    </form>

    <p class="card-foot">New to Programming Languages? <a href="register.php">Sign up here</a></p>
  </section>
</main>
<?php page_end(); ?>
