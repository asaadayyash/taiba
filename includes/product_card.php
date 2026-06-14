<?php
// $p = product row with cat_name, cat_slug
$imgs     = parseImages($p['images'] ?? '[]');
$imgSrc   = $imgs[0] ?? null;
$onSale   = !empty($p['compare_price']) && $p['compare_price'] > $p['price'];
$discount = $onSale ? round((1 - $p['price'] / $p['compare_price']) * 100) : 0;
?>
<div class="card-dark card-hover rounded-2xl overflow-hidden group">
  <a href="/taiba-store/product.php?id=<?= (int)$p['id'] ?>">
    <div class="relative aspect-square bg-[#1E1E1E] overflow-hidden">
      <?php if ($imgSrc): ?>
        <img src="<?= e($imgSrc) ?>" alt="<?= e($p['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy"/>
      <?php else: ?>
        <div class="w-full h-full flex items-center justify-center">
          <span class="text-[#333] text-6xl playfair">T</span>
        </div>
      <?php endif; ?>
      <?php if ($onSale): ?>
        <span class="absolute top-3 left-3 bg-red-500/90 text-white text-xs font-semibold px-2 py-1 rounded-full">-<?= $discount ?>%</span>
      <?php endif; ?>
    </div>
  </a>
  <div class="p-4">
    <p class="text-xs text-[#C9A840] mb-1"><?= e($p['cat_name'] ?? '') ?></p>
    <a href="/taiba-store/product.php?id=<?= (int)$p['id'] ?>">
      <h3 class="font-medium text-[#F5F0E8] hover:text-[#C9A840] transition-colors line-clamp-2 text-sm leading-snug mb-2"><?= e($p['name']) ?></h3>
    </a>
    <?php if (!empty($p['material'])): ?>
      <p class="text-xs text-[#666] mb-3"><?= e($p['material']) ?></p>
    <?php endif; ?>
    <div class="flex items-center justify-between mt-2">
      <div>
        <span class="font-bold text-[#C9A840]"><?= formatPrice($p['price']) ?></span>
        <?php if ($onSale): ?>
          <span class="text-xs text-[#555] line-through ml-2"><?= formatPrice($p['compare_price']) ?></span>
        <?php endif; ?>
      </div>
      <?php if ((int)$p['stock'] > 0): ?>
        <form method="post" action="/taiba-store/api/cart.php">
          <input type="hidden" name="csrf_token" value="<?= e($csrf ?? csrfToken()) ?>">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
          <button type="submit" class="btn-gold flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-full">
            🛒 Add
          </button>
        </form>
      <?php else: ?>
        <span class="bg-[#2A2A2A] text-[#555] text-xs px-3 py-1.5 rounded-full">Out</span>
      <?php endif; ?>
    </div>
  </div>
</div>
