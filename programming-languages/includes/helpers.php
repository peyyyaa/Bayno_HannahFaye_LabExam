<?php
// Small helpers used by every page.

/** Escape text before echoing it into HTML (stops XSS). */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $to): never
{
    header('Location: ' . $to);
    exit;
}

/** CSRF: one random token per session, put in every form. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $sent = $_POST['csrf'] ?? '';
    return is_string($sent) && hash_equals(csrf_token(), $sent);
}

/** Read a trimmed string from $_POST. */
function post(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? trim($value) : '';
}

/** Returns the list of password rules that are NOT met yet. */
function password_problems(string $password): array
{
    $problems = [];
    if (strlen($password) < 8)                  $problems[] = 'length';
    if (!preg_match('/[A-Z]/', $password))       $problems[] = 'upper';
    if (!preg_match('/[0-9]/', $password))       $problems[] = 'number';
    if (!preg_match('/[^A-Za-z0-9]/', $password)) $problems[] = 'special';
    return $problems;
}
