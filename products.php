<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$category = trim($_GET['category'] ?? '');
$search   = trim($_GET['search']   ?? '');
$featured = !empty($_GET['featured']);

$where  = ['p.active=1'];
$params = [];

if ($category) { $where[] = 'c.slug=?'; $params[] = $category; }
if ($featured)  { $where[] = 'p.featured=1'; }
if ($search)    { $where[] = 'p.name LIKE ?'; $params[] = "%$search%"; }

$sql = 'SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE ' . implode(' AND ',$where) . ' ORDER BY p.created_at DESC';
$products = query($sql, $params);

$title = $category ? ucfirst($category) : ($search ? "Search: \"$search\"" : 'All Jewelry');
$pageTitle = $title;
require_once 'includes/header.php';
?>

<div class="py-8 pb-20 max-w-7xl mx-auto px-6">
  <div class="mb-10">
    <p class="text-[#8d9d4f] text-xs tracking-[.3em] uppercase mb-2">Taiba Gold Collection</p>
    <h1 class="text-3xl md:text-4xl font-bold text-[#5c4b3e] playfair"><?= e($title) ?></h1>
    <p class="text-[#85766a] mt-2 text-sm"><?= count($products) ?> items</p>
  </div>

  <div class="flex flex-col lg:flex-row gap-8">
    <!-- Sidebar -->
    <aside class="w-full lg:w-56 flex-shrink-0">
      <div class="card-dark rounded-2xl p-5 sticky top-24">
        <h3 class="text-[#8d9d4f] font-semibold text-sm uppercase tracking-wider mb-4">Categories</h3>
        <ul class="space-y-1">
          <li><a href="/taiba-store/products.php" class="block px-3 py-2 rounded-lg text-sm transition-colors <?= !$category ? 'bg-[#8d9d4f]/10 text-[#8d9d4f]' : 'text-[#85766a] hover:text-[#8d9d4f]' ?>">All Jewelry</a></li>
          <?php foreach ($categories as $cat): ?>
          <li><a href="/taiba-store/products.php?category=<?= e($cat['slug']) ?>" class="block px-3 py-2 rounded-lg text-sm transition-colors <?= $category===$cat['slug'] ? 'bg-[#8d9d4f]/10 text-[#8d9d4f]' : 'text-[#85766a] hover:text-[#8d9d4f]' ?>"><?= e($cat['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
        <div class="mt-6 pt-6 border-t border-[#b19681]">
          <h3 class="text-[#8d9d4f] font-semibold text-sm uppercase tracking-wider mb-4">Search</h3>
          <form method="get">
            <input name="search" value="<?= e($search) ?>" placeholder="Search..." class="input-dark w-full px-3 py-2 rounded-lg text-sm"/>
          </form>
        </div>
      </div>
    </aside>

    <!-- Products -->
    <div class="flex-1">
      <?php if (empty($products)): ?>
      <div class="text-center py-20">
        <div class="text-5xl mb-4">✨</div>
        <p class="text-[#85766a] text-lg">No products found</p>
        <a href="/taiba-store/products.php" class="inline-block mt-4 text-[#8d9d4f] text-sm hover:underline">View all jewelry</a>
      </div>
      <?php else: ?>
      <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
        <?php foreach ($products as $p): ?>
        <?php include 'includes/product_card.php'; ?>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
