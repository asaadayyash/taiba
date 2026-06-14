<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: /taiba-store/products.php'); exit; }

$p = queryOne('SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id=c.id WHERE p.id=? AND p.active=1', [$id]);
if (!$p) { http_response_code(404); echo '<h1>Product not found</h1>'; exit; }

$imgs    = parseImages($p['images']);
$onSale  = !empty($p['compare_price']) && $p['compare_price'] > $p['price'];
$pageTitle = $p['name'];
require_once 'includes/header.php';

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
?>

<div class="py-8 pb-20 max-w-6xl mx-auto px-6">
  <a href="/taiba-store/products.php" class="inline-flex items-center gap-2 text-[#85766a] hover:text-[#8d9d4f] text-sm mb-8 transition-colors">← Back to Collection</a>

  <?php if ($flash): ?>
  <div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm px-4 py-3 rounded-xl"><?= e($flash) ?></div>
  <?php endif; ?>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
    <!-- Images -->
    <div>
      <div class="aspect-square rounded-2xl overflow-hidden bg-[#e7dbbf] border border-[#b19681] mb-4" id="mainImg">
        <?php if (!empty($imgs[0])): ?>
          <img src="<?= e($imgs[0]) ?>" alt="<?= e($p['name']) ?>" class="w-full h-full object-cover" id="mainImgEl"/>
        <?php else: ?>
          <div class="w-full h-full flex items-center justify-center text-[#c4b49a] text-8xl playfair">T</div>
        <?php endif; ?>
      </div>
      <?php if (count($imgs) > 1): ?>
      <div class="flex gap-3">
        <?php foreach ($imgs as $i => $img): ?>
        <button onclick="document.getElementById('mainImgEl').src='<?= e($img) ?>'" class="w-20 h-20 rounded-xl overflow-hidden border-2 <?= $i===0?'border-[#8d9d4f]':'border-[#b19681]' ?> transition-colors">
          <img src="<?= e($img) ?>" class="w-full h-full object-cover"/>
        </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div>
      <p class="text-[#8d9d4f] text-sm mb-2"><?= e($p['cat_name']) ?></p>
      <h1 class="text-3xl md:text-4xl font-bold text-[#5c4b3e] mb-4 leading-tight playfair"><?= e($p['name']) ?></h1>

      <div class="flex items-baseline gap-3 mb-6">
        <span class="text-3xl font-bold text-[#8d9d4f]"><?= formatPrice($p['price']) ?></span>
        <?php if ($onSale): ?>
          <span class="text-lg text-[#a08878] line-through"><?= formatPrice($p['compare_price']) ?></span>
          <span class="text-sm bg-red-500/20 text-red-400 px-2 py-0.5 rounded-full"><?= round((1-$p['price']/$p['compare_price'])*100) ?>% OFF</span>
        <?php endif; ?>
      </div>

      <?php if (!empty($p['description'])): ?>
        <p class="text-[#85766a] text-sm leading-relaxed mb-6"><?= nl2br(e($p['description'])) ?></p>
      <?php endif; ?>

      <div class="card-dark rounded-xl p-4 mb-6 space-y-3">
        <?php if (!empty($p['material'])): ?>
        <div class="flex justify-between text-sm"><span class="text-[#85766a]">Material</span><span class="text-[#5c4b3e]"><?= e($p['material']) ?></span></div>
        <?php endif; ?>
        <?php if (!empty($p['weight'])): ?>
        <div class="flex justify-between text-sm"><span class="text-[#85766a]">Weight</span><span class="text-[#5c4b3e]"><?= $p['weight'] ?>g</span></div>
        <?php endif; ?>
        <div class="flex justify-between text-sm">
          <span class="text-[#85766a]">Availability</span>
          <span class="<?= $p['stock']>0 ? 'text-green-400' : 'text-red-400' ?>">
            <?= $p['stock']>0 ? "In Stock ({$p['stock']})" : 'Out of Stock' ?>
          </span>
        </div>
      </div>

      <p class="text-xs text-[#a08878] mb-4">Price includes 15% VAT</p>

      <?php if ($p['stock'] > 0): ?>
      <form method="post" action="/taiba-store/api/cart.php">
        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
        <input type="hidden" name="redirect" value="/taiba-store/product.php?id=<?= $p['id'] ?>">
        <button type="submit" class="w-full btn-gold flex items-center justify-center gap-2 py-4 rounded-xl font-semibold text-base">
          🛒 Add to Cart
        </button>
      </form>
      <?php else: ?>
        <button class="w-full bg-[#b19681] text-[#a08878] py-4 rounded-xl font-semibold cursor-not-allowed">Out of Stock</button>
      <?php endif; ?>

      <div class="grid grid-cols-2 gap-3 mt-6">
        <div class="flex items-center gap-2 text-xs text-[#85766a]">📦 Free delivery over SAR 500</div>
        <div class="flex items-center gap-2 text-xs text-[#85766a]">🛡️ Certified authentic gold</div>
      </div>
    </div>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
