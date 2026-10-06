<?php
// Shared page pieces: <head>, the logo, the toast, and form fields.

function page_start(string $title, string $bodyClass): void
{
    $app = e(app_config()['app_name']);
    ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?> · <?= $app ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lobster&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="<?= e($bodyClass) ?>">
    <?php
}

function page_end(): void
{
    ?>
  <div class="toast" id="toast" role="status" aria-live="polite" hidden>
    <?= icon('info', 20) ?><span class="toast-text"></span>
  </div>
  <script src="assets/js/app.js"></script>
</body>
</html>
    <?php
}

/** The "Programming Languages by Hannah" wordmark from the card header. */
function logo(): void
{
    ?>
    <div class="logo" role="img" aria-label="Programming Languages by Hannah">
      <span class="logo-line logo-programming"><span>Programming</span></span>
      <span class="logo-line logo-languages"><span>Languages</span></span>
      <span class="logo-line logo-by"><span>by Hannah</span></span>
    </div>
    <?php
}

/**
 * One labelled input, styled like the Figma "Form Fields" component.
 * $opts: type, placeholder, value, error, autocomplete, password (bool)
 */
function field(string $name, string $label, array $opts = []): void
{
    $type       = $opts['type'] ?? 'text';
    $isPassword = $type === 'password';
    $error      = $opts['error'] ?? '';
    $invalid    = $error !== '' || !empty($opts['invalid']);
    $id         = 'f-' . $name;
    ?>
    <div class="field">
      <label class="field-label" for="<?= e($id) ?>"><?= e($label) ?></label>
      <div class="input<?= $invalid ? ' is-error' : '' ?>">
        <?php if ($isPassword): ?><span class="input-lead"><?= icon('lock', 16) ?></span><?php endif; ?>
        <input id="<?= e($id) ?>" name="<?= e($name) ?>" type="<?= e($type) ?>"
               placeholder="<?= e($opts['placeholder'] ?? '') ?>"
               value="<?= $isPassword ? '' : e($opts['value'] ?? '') ?>"
               autocomplete="<?= e($opts['autocomplete'] ?? 'off') ?>"
               <?= $invalid ? 'aria-invalid="true" aria-describedby="' . e($id) . '-err"' : '' ?>
               required>
        <span class="input-state input-state-error"><?= icon('error', 18) ?></span>
        <span class="input-state input-state-ok"><?= icon('check', 18) ?></span>
        <?php if ($isPassword): ?>
          <button type="button" class="input-eye" aria-label="Show password" data-toggle-password>
            <?= icon('eye', 20) ?><?= icon('eye-off', 20) ?>
          </button>
        <?php endif; ?>
      </div>
      <p class="field-error" id="<?= e($id) ?>-err"<?= $error ? '' : ' hidden' ?>><?= e($error) ?></p>
      <?= $opts['after'] ?? '' ?>
    </div>
    <?php
}

/** Checkbox styled like the Figma "Checkbox w" component. */
function checkbox(string $name, string $html, bool $checked = false, string $size = 'sm', bool $required = false): string
{
    return '<label class="check check-' . $size . '">'
         . '<input type="checkbox" name="' . e($name) . '" value="1"' . ($checked ? ' checked' : '') . ($required ? ' required' : '') . '>'
         . '<span class="check-box">' . icon('check', $size === 'sm' ? 12 : 16) . '</span>'
         . '<span class="check-text">' . $html . '</span></label>';
}
