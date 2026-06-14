<?php
$pageTitle = 'My Orders';
require_once 'includes/header.php';
requireLogin();

$orders = query(
    'SELECT o.*, COUNT(oi.id) as item_count FROM orders o LEFT JOIN order_items oi ON oi.order_id=o.id WHERE o.user_id=? GROUP BY o.id ORDER BY o.created_at DESC',
    [$_SESSION['user_id']]
);

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<div class="py-8 pb-20 max-w-4xl mx-auto px-6">
  <h1 class="text-3xl font-bold text-[#5c4b3e] mb-8 playfair">My Orders</h1>

  <?php if ($flash): ?>
  <div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm px-4 py-3 rounded-xl"><?= e($flash) ?></div>
  <?php endif; ?>

  <?php if (empty($orders)): ?>
  <div class="text-center py-20">
    <div class="text-5xl mb-4">📦</div>
    <p class="text-[#85766a] text-lg">No orders yet</p>
    <a href="/taiba-store/products.php" class="inline-block mt-4 btn-gold px-8 py-3 rounded-full">Start Shopping</a>
  </div>
  <?php else: ?>
  <div class="space-y-4">
    <?php foreach ($orders as $order): ?>
    <?php $orderItems = query('SELECT oi.*, p.images FROM order_items oi LEFT JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?', [$order['id']]); ?>
    <div class="card-dark rounded-2xl overflow-hidden">
      <div class="p-5 flex flex-wrap items-start justify-between gap-3 cursor-pointer" onclick="this.nextElementSibling.classList.toggle('hidden')">
        <div>
          <div class="flex items-center gap-3 flex-wrap">
            <span class="font-mono text-[#8d9d4f] font-bold text-sm"><?= e($order['order_number']) ?></span>
            <?= statusBadge($order['status']) ?>
          </div>
          <p class="text-[#85766a] text-xs mt-1"><?= date('d M Y', strtotime($order['created_at'])) ?> · <?= $order['item_count'] ?> items</p>
        </div>
        <span class="font-bold text-[#5c4b3e]"><?= formatPrice($order['total']) ?></span>
      </div>
      <div class="hidden border-t border-[#b19681] p-5 space-y-2">
        <?php foreach ($orderItems as $item): ?>
        <div class="flex justify-between text-sm">
          <span class="text-[#85766a]"><?= e($item['product_name']) ?> × <?= $item['quantity'] ?></span>
          <span class="text-[#85766a]"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
        </div>
        <?php endforeach; ?>
        <div class="pt-2 border-t border-[#ede4d4] text-xs text-[#a08878]">
          <div>📍 <?= e($order['address']) ?></div>
          <div>📞 <?= e($order['phone']) ?></div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
