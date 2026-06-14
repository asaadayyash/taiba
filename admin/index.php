<?php
$adminPage = 'Dashboard';
require_once 'header.php';

$stats = [
    'products' => queryOne('SELECT COUNT(*) as n FROM products WHERE active=1')['n'],
    'orders'   => queryOne('SELECT COUNT(*) as n FROM orders')['n'],
    'users'    => queryOne('SELECT COUNT(*) as n FROM users WHERE role="USER"')['n'],
    'revenue'  => queryOne('SELECT COALESCE(SUM(total),0) as n FROM orders WHERE status != "CANCELLED"')['n'],
];
$recentOrders = query('SELECT o.*,u.name as user_name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC LIMIT 8');
?>

<div class="mb-8">
  <h1 class="text-3xl font-bold text-[#F5F0E8] playfair">Dashboard</h1>
  <p class="text-[#666] mt-1">Welcome back, <?= e($adminUser['name']) ?></p>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-10">
  <?php foreach ([
    ['Total Products', $stats['products'],                   '📦', 'text-blue-400',   'bg-blue-400/10'],
    ['Total Orders',   $stats['orders'],                     '🛍', 'text-purple-400', 'bg-purple-400/10'],
    ['Customers',      $stats['users'],                      '👥', 'text-green-400',  'bg-green-400/10'],
    ['Revenue',        formatPrice($stats['revenue']),       '💰', 'text-[#C9A840]',  'bg-[#C9A840]/10'],
  ] as [$label, $value, $icon, $color, $bg]): ?>
  <div class="card-dark rounded-2xl p-6">
    <div class="flex items-center justify-between mb-4">
      <p class="text-[#666] text-sm"><?= $label ?></p>
      <div class="w-10 h-10 <?= $bg ?> rounded-xl flex items-center justify-center text-lg"><?= $icon ?></div>
    </div>
    <p class="text-2xl font-bold <?= $color ?>"><?= $value ?></p>
  </div>
  <?php endforeach; ?>
</div>

<!-- Recent orders -->
<div class="card-dark rounded-2xl overflow-hidden">
  <div class="p-6 border-b border-[#2A2A2A] flex justify-between items-center">
    <h2 class="font-bold text-[#F5F0E8]">Recent Orders</h2>
    <a href="/taiba-store/admin/orders.php" class="text-[#C9A840] text-sm hover:underline">View all →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead><tr class="border-b border-[#2A2A2A]">
        <?php foreach (['Order #','Customer','Total','Status','Date'] as $h): ?>
        <th class="text-left px-6 py-3 text-xs text-[#666] font-medium uppercase tracking-wider"><?= $h ?></th>
        <?php endforeach; ?>
      </tr></thead>
      <tbody>
        <?php foreach ($recentOrders as $o): ?>
        <tr class="border-b border-[#1A1A1A] hover:bg-[#161616]">
          <td class="px-6 py-4 text-sm font-mono text-[#C9A840]"><?= e($o['order_number']) ?></td>
          <td class="px-6 py-4 text-sm text-[#A0998A]"><?= e($o['user_name']) ?></td>
          <td class="px-6 py-4 text-sm font-semibold"><?= formatPrice($o['total']) ?></td>
          <td class="px-6 py-4"><?= statusBadge($o['status']) ?></td>
          <td class="px-6 py-4 text-xs text-[#555]"><?= date('d M Y', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($recentOrders)): ?>
        <tr><td colspan="5" class="px-6 py-10 text-center text-[#555]">No orders yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'footer.php'; ?>
