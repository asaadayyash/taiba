<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireLogin();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /taiba-store/cart.php'); exit; }
verifyCsrf();

$userId  = $_SESSION['user_id'];
$address = trim($_POST['address'] ?? '');
$phone   = trim($_POST['phone']   ?? '');
$notes   = trim($_POST['notes']   ?? '');

if (!$address || !$phone) {
    $_SESSION['order_error'] = 'Delivery address and phone are required.';
    header('Location: /taiba-store/cart.php');
    exit;
}

$items = query(
    'SELECT c.quantity, p.id as product_id, p.name, p.price, p.stock FROM cart c JOIN products p ON p.id=c.product_id WHERE c.user_id=? AND p.active=1',
    [$userId]
);
if (empty($items)) {
    $_SESSION['order_error'] = 'Your cart is empty.';
    header('Location: /taiba-store/cart.php');
    exit;
}

$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
$tax      = $subtotal * VAT_RATE;
$shipping = $subtotal >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_COST;
$total    = $subtotal + $tax + $shipping;
$orderNum = generateOrderNumber();

$db = getDB();
$db->beginTransaction();
try {
    execute(
        'INSERT INTO orders (order_number,user_id,address,phone,notes,subtotal,tax,shipping,total) VALUES (?,?,?,?,?,?,?,?,?)',
        [$orderNum, $userId, $address, $phone, $notes, $subtotal, $tax, $shipping, $total]
    );
    $orderId = (int)$db->lastInsertId();
    foreach ($items as $item) {
        execute(
            'INSERT INTO order_items (order_id,product_id,product_name,quantity,price) VALUES (?,?,?,?,?)',
            [$orderId, $item['product_id'], $item['name'], $item['quantity'], $item['price']]
        );
        execute('UPDATE products SET stock=stock-? WHERE id=?', [$item['quantity'], $item['product_id']]);
    }
    execute('DELETE FROM cart WHERE user_id=?', [$userId]);
    $db->commit();
    $_SESSION['flash'] = "Order $orderNum placed successfully!";
    header('Location: /taiba-store/orders.php');
} catch (Exception $e) {
    $db->rollBack();
    $_SESSION['order_error'] = 'Failed to place order. Please try again.';
    header('Location: /taiba-store/cart.php');
}
exit;
