<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

$featured   = query('SELECT p.*, c.name as cat_name, c.slug as cat_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.featured=1 AND p.active=1 ORDER BY p.created_at DESC LIMIT 8');
$cats       = getCategories();
$catIcons   = ['rings'=>'💍','bracelets'=>'📿','necklaces'=>'✨','earrings'=>'🌟','sets'=>'👑','ingots'=>'🏆'];
?>

<!-- Hero -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
  <div class="absolute inset-0" style="background:radial-gradient(circle at 30% 50%,rgba(201,168,64,.12) 0%,transparent 60%),radial-gradient(circle at 70% 50%,rgba(155,125,40,.08) 0%,transparent 60%),#0D0D0D"></div>
  <div class="relative z-10 text-center px-6 max-w-4xl mx-auto">
    <p class="text-[#C9A840] text-sm font-semibold tracking-[.3em] uppercase mb-6">Est. 1995 · Al Madinah, Saudi Arabia</p>
    <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight playfair">
      <span class="text-[#F5F0E8]">The Finest</span><br>
      <span class="gold-text">Gold Jewelry</span>
    </h1>
    <p class="text-[#A0998A] text-lg max-w-2xl mx-auto leading-relaxed mb-10">
      Discover our handcrafted collection of 18K &amp; 21K gold jewelry. Each piece tells a story of craftsmanship, heritage, and timeless elegance.
    </p>
    <div class="flex flex-col sm:flex-row gap-4 justify-center">
      <a href="/taiba-store/products.php" class="btn-gold inline-flex items-center gap-2 px-8 py-4 rounded-full text-base">Shop Collection →</a>
      <a href="/taiba-store/products.php?featured=1" class="inline-flex items-center gap-2 px-8 py-4 rounded-full border border-[#C9A840]/40 text-[#C9A840] hover:bg-[#C9A840]/10 transition-colors text-base">View Featured</a>
    </div>
  </div>
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 text-[#666]">
    <span class="text-xs tracking-widest">SCROLL</span>
    <div class="w-px h-12 bg-gradient-to-b from-[#C9A840] to-transparent"></div>
  </div>
</section>

<!-- Trust badges -->
<section class="py-10 border-y border-[#2A2A2A] bg-[#0A0A0A]">
  <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ([
      ['🛡️','Certified Authentic','100% genuine gold, certified by Saudi standards'],
      ['🚚','Free Shipping','Free delivery on orders over SAR 500'],
      ['🔄','Easy Returns','30-day hassle-free return policy'],
    ] as [$icon,$title,$desc]): ?>
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-full border border-[#C9A840]/30 flex items-center justify-center flex-shrink-0 text-xl"><?= $icon ?></div>
      <div>
        <p class="font-semibold text-[#F5F0E8] text-sm"><?= $title ?></p>
        <p class="text-[#666] text-xs mt-0.5"><?= $desc ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Categories -->
<?php if ($cats): ?>
<section class="py-20 px-6 max-w-7xl mx-auto">
  <div class="text-center mb-12">
    <p class="text-[#C9A840] text-xs tracking-[.3em] uppercase mb-3">Browse By</p>
    <h2 class="text-3xl md:text-4xl font-bold text-[#F5F0E8] playfair">Collections</h2>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    <?php foreach ($cats as $cat): ?>
    <a href="/taiba-store/products.php?category=<?= e($cat['slug']) ?>" class="card-dark card-hover rounded-2xl p-6 flex flex-col items-center text-center gap-3">
      <span class="text-3xl"><?= $catIcons[$cat['slug']] ?? '✨' ?></span>
      <div>
        <p class="font-medium text-[#F5F0E8] text-sm"><?= e($cat['name']) ?></p>
        <p class="text-[#666] text-xs mt-0.5"><?= $cat['product_count'] ?> items</p>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- Featured Products -->
<?php if ($featured): ?>
<section class="py-20 px-6 max-w-7xl mx-auto">
  <div class="flex items-end justify-between mb-12">
    <div>
      <p class="text-[#C9A840] text-xs tracking-[.3em] uppercase mb-3">Hand-picked</p>
      <h2 class="text-3xl md:text-4xl font-bold text-[#F5F0E8] playfair">Featured Pieces</h2>
    </div>
    <a href="/taiba-store/products.php" class="hidden md:flex items-center gap-2 text-[#C9A840] text-sm hover:underline">View All →</a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
    <?php foreach ($featured as $p): ?>
    <?php include 'includes/product_card.php'; ?>
    <?php endforeach; ?>
  </div>
  <div class="text-center mt-10">
    <a href="/taiba-store/products.php" class="inline-flex items-center gap-2 btn-gold px-8 py-3 rounded-full">View Full Collection →</a>
  </div>
</section>
<?php endif; ?>

<!-- Testimonials -->
<section class="py-20 bg-[#0A0A0A] border-y border-[#2A2A2A]">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-12">
      <p class="text-[#C9A840] text-xs tracking-[.3em] uppercase mb-3">Reviews</p>
      <h2 class="text-3xl md:text-4xl font-bold text-[#F5F0E8] playfair">What Our Customers Say</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ([
        ['Sarah K.','Absolutely stunning bracelet! The quality is exceptional and delivery was fast.'],
        ['Mohammed A.','Bought a ring for my wife\'s anniversary. She was speechless. Remarkable craftsmanship.'],
        ['Fatima H.','Every piece is perfect. Highly recommend Taiba Gold!'],
      ] as [$name,$text]): ?>
      <div class="card-dark rounded-2xl p-6">
        <div class="flex gap-1 mb-4"><?= str_repeat('<span class="text-[#C9A840]">★</span>',5) ?></div>
        <p class="text-[#A0998A] text-sm leading-relaxed mb-4">"<?= e($text) ?>"</p>
        <p class="text-[#C9A840] font-semibold text-sm"><?= e($name) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
