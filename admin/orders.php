<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

// Handle POST before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    execute('UPDATE orders SET status=? WHERE id=?', [$_POST['status'], (int)$_POST['id']]);
    $_SESSION['admin_flash'] = 'Order status updated.';
    header('Location: /taiba-store/admin/orders.php' . (!empty($_GET['status']) ? '?status='.$_GET['status'] : ''));
    exit;
}

$flash    = $_SESSION['admin_flash'] ?? '';
unset($_SESSION['admin_flash']);
$filter   = $_GET['status'] ?? '';
$where    = $filter ? 'WHERE o.status=?' : '';
$params   = $filter ? [$filter] : [];
$orders   = query("SELECT o.*,u.name as user_name,u.email as user_email FROM orders o JOIN users u ON u.id=o.user_id $where ORDER BY o.created_at DESC", $params);
$statuses = ['PENDING','CONFIRMED','PROCESSING','SHIPPED','DELIVERED','CANCELLED'];

$adminPage = 'Orders';
require_once 'header.php';
?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-bold text-[#5c4b3e] playfair">Orders</h1>
    <p class="text-[#85766a] mt-1"><?= count($orders) ?> orders</p>
  </div>
  <div class="flex gap-2 flex-wrap">
    <a href="?" class="text-xs px-3 py-1.5 rounded-full border <?= !$filter?'border-[#8d9d4f] text-[#8d9d4f]':'border-[#b19681] text-[#85766a] hover:border-[#8d9d4f] hover:text-[#8d9d4f]' ?> transition-all">All</a>
    <?php foreach ($statuses as $s): ?>
    <a href="?status=<?= $s ?>" class="text-xs px-3 py-1.5 rounded-full border <?= $filter===$s?'border-[#8d9d4f] text-[#8d9d4f]':'border-[#b19681] text-[#85766a] hover:border-[#8d9d4f] hover:text-[#8d9d4f]' ?> transition-all"><?= $s ?></a>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($flash): ?><div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm px-4 py-3 rounded-xl"><?= e($flash) ?></div><?php endif; ?>

<div class="space-y-3">
  <?php foreach ($orders as $o): ?>
  <?php $items = query('SELECT * FROM order_items WHERE order_id=?', [$o['id']]); ?>
  <div class="card-dark rounded-2xl overflow-hidden">
    <div class="p-5 flex flex-wrap items-center gap-4 cursor-pointer" onclick="this.nextElementSibling.classList.toggle('hidden')">
      <div class="flex-1 min-w-0">
        <div class="flex items-center gap-3 flex-wrap">
          <span class="font-mono text-[#8d9d4f] font-bold text-sm"><?= e($o['order_number']) ?></span>
          <?= statusBadge($o['status']) ?>
        </div>
        <p class="text-sm text-[#85766a] mt-1"><?= e($o['user_name']) ?> · <?= e($o['user_email']) ?></p>
      </div>
      <div class="text-right">
        <p class="font-bold text-[#5c4b3e]"><?= formatPrice($o['total']) ?></p>
        <p class="text-xs text-[#a08878]"><?= date('d M Y H:i', strtotime($o['created_at'])) ?></p>
      </div>
    </div>
    <div class="hidden border-t border-[#b19681] p-5 space-y-4">
      <div>
        <p class="text-xs text-[#85766a] uppercase tracking-wider mb-2">Items</p>
        <?php foreach ($items as $item): ?>
        <div class="flex justify-between text-sm py-1">
          <span class="text-[#85766a]"><?= e($item['product_name']) ?> × <?= $item['quantity'] ?></span>
          <span class="text-[#85766a]"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
        </div>
        <?php endforeach; ?>
        <div class="mt-2 pt-2 border-t border-[#dbc894] grid grid-cols-3 gap-2 text-xs text-[#85766a]">
          <div>Subtotal: <?= formatPrice($o['subtotal']) ?></div>
          <div>VAT: <?= formatPrice($o['tax']) ?></div>
          <div>Shipping: <?= $o['shipping']>0?formatPrice($o['shipping']):'Free' ?></div>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div><p class="text-xs text-[#85766a] mb-1">Address</p><p class="text-[#85766a]"><?= e($o['address']) ?></p></div>
        <div><p class="text-xs text-[#85766a] mb-1">Phone</p><p class="text-[#85766a]"><?= e($o['phone']) ?></p></div>
        <?php if (!empty($o['notes'])): ?>
        <div class="col-span-2"><p class="text-xs text-[#85766a] mb-1">Notes</p><p class="text-[#85766a]"><?= e($o['notes']) ?></p></div>
        <?php endif; ?>
      </div>
      <div>
        <p class="text-xs text-[#85766a] uppercase tracking-wider mb-2">Update Status</p>
        <form method="post" class="flex flex-wrap gap-2">
          <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
          <input type="hidden" name="id" value="<?= $o['id'] ?>">
          <?php foreach ($statuses as $s): ?>
          <button name="status" value="<?= $s ?>" type="submit" class="text-xs px-3 py-1.5 rounded-full border transition-all <?= $o['status']===$s?'border-[#8d9d4f] text-[#8d9d4f] bg-[#8d9d4f]/10':'border-[#b19681] text-[#85766a] hover:border-[#8d9d4f] hover:text-[#8d9d4f]' ?>"><?= $s ?></button>
          <?php endforeach; ?>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <?php if (empty($orders)): ?><div class="text-center py-20 text-[#a08878]">No orders found.</div><?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
