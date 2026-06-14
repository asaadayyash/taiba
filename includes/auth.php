<?php
require_once __DIR__ . '/db.php';

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'ADMIN';
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['user_role'],
    ];
}

function requireLogin(string $redirect = '/taiba-store/login.php'): void {
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

function requireAdmin(): void {
    if (!isAdmin()) {
        header('Location: /taiba-store/login.php');
        exit;
    }
}

function loginUser(string $email, string $password): bool {
    $user = queryOne('SELECT * FROM users WHERE email = ?', [$email]);
    if (!$user || !password_verify($password, $user['password'])) return false;

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];
    return true;
}

function logoutUser(): void {
    $_SESSION = [];
    session_destroy();
}

function registerUser(string $name, string $email, string $password, string $phone = ''): bool {
    $exists = queryOne('SELECT id FROM users WHERE email = ?', [$email]);
    if ($exists) return false;

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    execute(
        'INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)',
        [$name, $email, $hash, $phone]
    );
    return true;
}

function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit('Invalid CSRF token');
    }
}
