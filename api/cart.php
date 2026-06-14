<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'] ?? '/taiba-store/';
    header('Location: /taiba-store/login.php');
    exit;
}

verifyCsrf();

$action    = $_POST['action']     ?? '';
$productId = (int)($_POST['product_id'] ?? 0);
$qty       = (int)($_POST['quantity']   ?? 1);
$redirect  = $_POST['redirect']   ?? '/taiba-store/cart.php';
$userId    = $_SESSION['user_id'];

if ($action === 'add' && $productId) {
    $product = queryOne('SELECT id, stock FROM products WHERE id=? AND active=1', [$productId]);
    if ($product && $product['stock'] > 0) {
        execute(
            'INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?) ON DUPLICATE KEY UPDATE quantity=quantity+?',
            [$userId, $productId, $qty, $qty]
        );
        $_SESSION['flash'] = 'Added to cart!';
    }
} elseif ($action === 'remove' && $productId) {
    execute('DELETE FROM cart WHERE user_id=? AND product_id=?', [$userId, $productId]);
} elseif ($action === 'update' && $productId) {
    if ($qty <= 0) {
        execute('DELETE FROM cart WHERE user_id=? AND product_id=?', [$userId, $productId]);
    } else {
        execute('UPDATE cart SET quantity=? WHERE user_id=? AND product_id=?', [$qty, $userId, $productId]);
    }
} elseif ($action === 'clear') {
    execute('DELETE FROM cart WHERE user_id=?', [$userId]);
}

header('Location: ' . $redirect);
exit;
