<?php
require __DIR__ . '/../includes/bootstrap.php';
redirect_if_logged_in();

$errors = [];
$old    = ['first_name' => '', 'last_name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['first_name'] = post('first_name');
    $old['last_name']  = post('last_name');
    $old['email']      = post('email');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $password = is_string($password) ? $password : '';
    $confirm  = is_string($confirm) ? $confirm : '';

    if (!csrf_valid()) {
        $errors['form'] = 'Your session expired. Please try again.';
    }

    foreach (['first_name' => 'First name', 'last_name' => 'Last name'] as $key => $label) {
        if ($old[$key] === '') {
            $errors[$key] = "$label is required.";
        } elseif (mb_strlen($old[$key]) > 50) {
            $errors[$key] = "$label must be 50 characters or fewer.";
        }
    }

    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    } else {
        $stmt = db()->prepare('SELECT 1 FROM users WHERE email = ?');
        $stmt->execute([$old['email']]);
        if ($stmt->fetchColumn()) {
            $errors['email'] = 'An account with this email already exists.';
        }
    }

    if (password_problems($password)) {
        $errors['password'] = 'Password must be 8+ characters, with 1 uppercase letter, 1 number and 1 special character.';
    }
    if ($confirm === '' || $confirm !== $password) {
        $errors['confirm_password'] = 'Passwords do not match.';
    }
    if (empty($_POST['terms'])) {
        $errors['terms'] = 'Please agree to the Terms & Conditions and Privacy Policy.';
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $old['first_name'], $old['last_name'], $old['email'],
            password_hash($password, PASSWORD_DEFAULT),
        ]);

        login_user((int) db()->lastInsertId(), !empty($_POST['remember']));
        $_SESSION['welcome'] = 'register';
        redirect('welcome.php');
    }
}

// The pop-up shown above the password field while typing (Figma "Info pop up").
$rules = '<div class="info-pop" id="pw-rules" hidden>'
       . '<div class="info-pop-body">' . icon('info', 20)
       . '<div><p>Password must contain:</p><ul>'
       . '<li data-rule="length">At least 8 characters</li>'
       . '<li data-rule="upper">One uppercase letter</li>'
       . '<li data-rule="number">One number</li>'
       . '<li data-rule="special">One special character</li>'
       . '</ul></div></div></div>';

page_start('Create an account', 'page-register');
?>
<main class="modal-backdrop">
  <section class="signup-modal" role="dialog" aria-modal="true" aria-labelledby="signup-title">
    <a class="modal-close" href="login.php" aria-label="Back to sign in"><?= icon('x', 20) ?></a>

    <div class="card card-signup">
      <form class="card-form" method="post" action="register.php" novalidate data-register>
        <?= csrf_field() ?>
        <div class="card-heading">
          <h1 id="signup-title">Create an account</h1>
          <p>Sign up to continue to the app</p>
        </div>

        <?php if (!empty($errors['form'])): ?>
          <p class="form-alert"><?= icon('error', 18) ?><?= e($errors['form']) ?></p>
        <?php endif; ?>

        <?php field('first_name', 'First Name', [
            'placeholder' => 'e.g. Juan', 'value' => $old['first_name'],
            'autocomplete' => 'given-name', 'error' => $errors['first_name'] ?? '',
        ]); ?>
        <?php field('last_name', 'Last Name', [
            'placeholder' => 'e.g. Dela Cruz', 'value' => $old['last_name'],
            'autocomplete' => 'family-name', 'error' => $errors['last_name'] ?? '',
        ]); ?>
        <?php field('email', 'Email', [
            'type' => 'email', 'placeholder' => 'you@company.com', 'value' => $old['email'],
            'autocomplete' => 'email', 'error' => $errors['email'] ?? '',
        ]); ?>
        <?php field('password', 'Password', [
            'type' => 'password', 'placeholder' => 'Enter your password',
            'autocomplete' => 'new-password', 'error' => $errors['password'] ?? '',
            'after' => $rules,
        ]); ?>
        <?php field('confirm_password', 'Confirm Password', [
            'type' => 'password', 'placeholder' => 'Re-enter your password',
            'autocomplete' => 'new-password', 'error' => $errors['confirm_password'] ?? '',
            'after' => '<div class="field-row">' . checkbox('remember', 'Remember me', !empty($_POST['remember'])) . '</div>',
        ]); ?>

        <div class="terms">
          <?= checkbox('terms', 'I agree to the <a href="#" data-soon="Terms page coming soon.">Terms &amp; Conditions</a> and <a href="#" data-soon="Privacy page coming soon.">Privacy Policy</a>.', !empty($_POST['terms']), 'md', true) ?>
          <?php if (!empty($errors['terms'])): ?>
            <p class="field-error"><?= e($errors['terms']) ?></p>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-tall">Sign Up</button>
      </form>
    </div>
  </section>
</main>
<?php page_end(); ?>
