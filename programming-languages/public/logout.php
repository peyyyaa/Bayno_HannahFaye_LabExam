<?php
require __DIR__ . '/../includes/bootstrap.php';

// Logging out only works via POST with a valid CSRF token, so another
// site can't log people out with a hidden link or image.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    logout_user();
}
redirect('login.php');
