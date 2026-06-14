<?php
$pageTitle = 'Home';
require_once 'includes/header.php';

$featured = query(
    'SELECT p.*, c.name as cat_name, c.slug as cat_slug
     FROM products p JOIN categories c ON p.category_id = c.id
     WHERE p.featured=1 AND p.active=1
     ORDER BY p.created_at DESC LIMIT 8'
);
$cats = getCategories();

$base       = 'https://cdn.files.salla.network/homepage/2019323384/';
$heroSlides = [
    $base . '87833948-4244-4d27-aeb3-c18684589f5d.webp',
    $base . 'c5760a63-660c-4466-8614-d5a947a2490c.webp',
    $base . '3daa6fe4-fb7a-4524-b257-312bb38bb921.webp',
    $base . '0f75b8a6-ed07-456b-8f8d-ce28522ff4a0.webp',
    $base . 'f91129a9-b132-49b0-8db7-2bea1026a665.webp',
    $base . '1686c2bd-1c85-40cd-915b-0c15195308b7.webp',
];
$catImages = [
    'rings'     => $base . 'fabd25a7-0b84-47ca-8455-8a2703d42cd7.webp',
    'bracelets' => $base . 'be7d470d-ff02-4e49-aadc-e4bf8f6d5f24.webp',
    'necklaces' => $base . 'b6109662-b4e9-4066-a305-fec657970f2e.webp',
    'earrings'  => $base . '098e3859-26c6-47fa-b95b-0572b4feef52.webp',
    'sets'      => $base . '94992020-08ab-4a21-b2d4-a798b33dae23.webp',
    'ingots'    => $base . '75175604-d748-4512-9b0b-f67a3a44d3e5.webp',
];
?>

<!-- ═══════════════════════ HERO SLIDER ═══════════════════════ -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden" id="hero">

  <!-- Slide backgrounds -->
  <?php foreach ($heroSlides as $i => $slide): ?>
  <div id="slide-<?= $i ?>"
       class="absolute inset-0 transition-opacity duration-1000 ease-in-out <?= $i === 0 ? 'opacity-100' : 'opacity-0' ?>"
       aria-hidden="<?= $i === 0 ? 'false' : 'true' ?>">
    <img src="<?= e($slide) ?>"
         class="w-full h-full object-cover"
         alt="Taiba Gold Collection"
         <?= $i > 0 ? 'loading="lazy"' : 'fetchpriority="high"' ?>/>
    <div class="absolute inset-0"
         style="background:linear-gradient(105deg,rgba(0,0,0,.78) 0%,rgba(0,0,0,.45) 55%,rgba(0,0,0,.20) 100%),linear-gradient(to top,rgba(0,0,0,.55) 0%,transparent 45%)">
    </div>
  </div>
  <?php endforeach; ?>

  <!-- Content overlay -->
  <div class="relative z-10 px-6 max-w-5xl mx-auto w-full">
    <div class="max-w-2xl">
      <p class="text-[#b5c97a] text-xs font-semibold tracking-[.35em] uppercase mb-5">Est. 1995 · Al Madinah, Saudi Arabia</p>
      <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-[1.1] playfair">
        <span class="text-white">The Finest</span><br>
        <span class="gold-text">Gold Jewelry</span>
      </h1>
      <p class="text-white/70 text-lg leading-relaxed mb-10 max-w-lg">
        Discover our handcrafted collection of 18K &amp; 21K gold jewelry. Each piece tells a story of craftsmanship, heritage, and timeless elegance.
      </p>
      <div class="flex flex-col sm:flex-row gap-4">
        <a href="/taiba-store/products.php"
           class="btn-gold inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full text-base font-semibold">
          Shop Collection →
        </a>
        <a href="/taiba-store/products.php?featured=1"
           class="inline-flex items-center justify-center gap-2 px-8 py-4 rounded-full border border-[#8d9d4f]/50 text-[#8d9d4f] hover:bg-[#8d9d4f]/10 transition-colors text-base">
          View Featured
        </a>
      </div>
    </div>
  </div>

  <!-- Slide dots -->
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2 z-10">
    <?php foreach ($heroSlides as $i => $_): ?>
    <button onclick="heroGoSlide(<?= $i ?>)"
            id="dot-<?= $i ?>"
            aria-label="Slide <?= $i+1 ?>"
            class="transition-all duration-300 rounded-full <?= $i===0 ? 'w-8 h-1.5 bg-[#8d9d4f]' : 'w-2 h-1.5 bg-white/30 hover:bg-white/60' ?>">
    </button>
    <?php endforeach; ?>
  </div>

  <!-- Scroll indicator -->
  <div class="absolute bottom-8 right-8 hidden md:flex flex-col items-center gap-2 text-white/35 z-10">
    <div class="w-px h-14 bg-gradient-to-b from-[#8d9d4f]/60 to-transparent"></div>
    <span class="text-[10px] tracking-[.25em] uppercase">Scroll</span>
  </div>
</section>

<script>
(function () {
  let cur = 0;
  const total = <?= count($heroSlides) ?>;
  const slides = Array.from({length: total}, (_, i) => document.getElementById('slide-' + i));
  const dots   = Array.from({length: total}, (_, i) => document.getElementById('dot-' + i));

  function activate(n) {
    slides[cur].classList.replace('opacity-100', 'opacity-0');
    slides[cur].setAttribute('aria-hidden', 'true');
    dots[cur].className = 'transition-all duration-300 rounded-full w-2 h-1.5 bg-white/30 hover:bg-white/60';
    cur = (n + total) % total;
    slides[cur].classList.replace('opacity-0', 'opacity-100');
    slides[cur].setAttribute('aria-hidden', 'false');
    dots[cur].className = 'transition-all duration-300 rounded-full w-8 h-1.5 bg-[#8d9d4f]';
  }

  window.heroGoSlide = activate;
  let timer = setInterval(() => activate(cur + 1), 5000);

  // Pause on hover
  document.getElementById('hero').addEventListener('mouseenter', () => clearInterval(timer));
  document.getElementById('hero').addEventListener('mouseleave', () => { timer = setInterval(() => activate(cur + 1), 5000); });
})();
</script>

<!-- ═══════════════════════ TRUST BADGES ═══════════════════════ -->
<section class="py-10 border-y border-[#b19681] bg-[#ddd0a8]">
  <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
    <?php foreach ([
      ['🛡️', 'Certified Authentic', '100% genuine gold, certified by Saudi standards'],
      ['🚚', 'Free Shipping',       'Free delivery on orders over SAR 500'],
      ['🔄', 'Easy Returns',        '30-day hassle-free return policy'],
    ] as [$icon, $title, $desc]): ?>
    <div class="flex items-center gap-5">
      <div class="w-12 h-12 rounded-full border border-[#8d9d4f]/30 flex items-center justify-center flex-shrink-0 text-xl"><?= $icon ?></div>
      <div>
        <p class="font-semibold text-[#5c4b3e] text-sm"><?= $title ?></p>
        <p class="text-[#85766a] text-xs mt-0.5"><?= $desc ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ═══════════════════════ CATEGORIES ═══════════════════════ -->
<?php if ($cats): ?>
<section class="py-20 px-6 max-w-7xl mx-auto">
  <div class="text-center mb-12">
    <p class="text-[#8d9d4f] text-xs tracking-[.3em] uppercase mb-3">Browse By</p>
    <h2 class="text-3xl md:text-4xl font-bold text-[#5c4b3e] playfair">Collections</h2>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
    <?php foreach ($cats as $cat):
      $img = $catImages[$cat['slug']] ?? null;
    ?>
    <a href="/taiba-store/products.php?category=<?= e($cat['slug']) ?>"
       class="group relative rounded-2xl overflow-hidden aspect-[3/4] block">
      <?php if ($img): ?>
        <img src="<?= e($img) ?>"
             alt="<?= e($cat['name']) ?>"
             class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
             loading="lazy"/>
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-black/10 group-hover:from-black/70 transition-colors"></div>
      <?php else: ?>
        <div class="absolute inset-0 bg-[#ede4d4] group-hover:bg-[#222] transition-colors"></div>
      <?php endif; ?>
      <div class="absolute inset-x-0 bottom-0 p-4">
        <p class="font-semibold text-white text-sm leading-tight"><?= e($cat['name']) ?></p>
        <p class="text-white/55 text-xs mt-0.5"><?= $cat['product_count'] ?> items</p>
      </div>
      <div class="absolute inset-0 ring-1 ring-inset ring-[#8d9d4f]/0 group-hover:ring-[#8d9d4f]/40 transition-all rounded-2xl"></div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════ FEATURED PRODUCTS ═══════════════════════ -->
<?php if ($featured): ?>
<section class="py-20 px-6 max-w-7xl mx-auto">
  <div class="flex items-end justify-between mb-12">
    <div>
      <p class="text-[#8d9d4f] text-xs tracking-[.3em] uppercase mb-3">Hand-picked</p>
      <h2 class="text-3xl md:text-4xl font-bold text-[#5c4b3e] playfair">Featured Pieces</h2>
    </div>
    <a href="/taiba-store/products.php" class="hidden md:flex items-center gap-2 text-[#8d9d4f] text-sm hover:underline">View All →</a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
    <?php foreach ($featured as $p): ?>
    <?php include 'includes/product_card.php'; ?>
    <?php endforeach; ?>
  </div>
  <div class="text-center mt-10">
    <a href="/taiba-store/products.php" class="inline-flex items-center gap-2 btn-gold px-8 py-3 rounded-full font-semibold">View Full Collection →</a>
  </div>
</section>
<?php endif; ?>

<!-- ═══════════════════════ BANNER PROMO ═══════════════════════ -->
<section class="py-0 relative overflow-hidden" style="min-height:380px">
  <img src="<?= e($base . '3557981d-63d2-42bf-b888-9a183db2030b.webp') ?>"
       class="absolute inset-0 w-full h-full object-cover" alt="Gold Collection" loading="lazy"/>
  <div class="absolute inset-0" style="background:linear-gradient(90deg,rgba(0,0,0,.80) 0%,rgba(0,0,0,.35) 60%,transparent 100%)"></div>
  <div class="relative z-10 max-w-7xl mx-auto px-6 py-20 flex items-center min-h-[380px]">
    <div class="max-w-md">
      <p class="text-[#8d9d4f] text-xs tracking-[.3em] uppercase mb-4">Limited Collection</p>
      <h2 class="text-4xl md:text-5xl font-bold text-white playfair leading-tight mb-4">Bridal<br>Gold Sets</h2>
      <p class="text-white/70 text-sm leading-relaxed mb-8">
        Complete 21K gold bridal jewelry sets, crafted to make your special day unforgettable.
      </p>
      <a href="/taiba-store/products.php?category=sets"
         class="inline-flex items-center gap-2 btn-gold px-7 py-3 rounded-full font-semibold text-sm">
        Shop Bridal →
      </a>
    </div>
  </div>
</section>

<!-- ═══════════════════════ TESTIMONIALS ═══════════════════════ -->
<section class="py-20 bg-[#ddd0a8] border-t border-[#b19681]">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-12">
      <p class="text-[#8d9d4f] text-xs tracking-[.3em] uppercase mb-3">Reviews</p>
      <h2 class="text-3xl md:text-4xl font-bold text-[#5c4b3e] playfair">What Our Customers Say</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ([
        ['Sarah K.',     'Absolutely stunning bracelet! The quality is exceptional and delivery was super fast.'],
        ['Mohammed A.',  "Bought a ring for my wife's anniversary. She was speechless. Remarkable craftsmanship."],
        ['Fatima H.',    'Every piece is perfect. The bridal set was beyond our expectations. Highly recommend!'],
      ] as [$name, $text]): ?>
      <div class="card-dark rounded-2xl p-7">
        <div class="flex gap-1 mb-4"><?= str_repeat('<span class="text-[#8d9d4f]">★</span>', 5) ?></div>
        <p class="text-[#85766a] text-sm leading-relaxed mb-5">"<?= e($text) ?>"</p>
        <p class="text-[#8d9d4f] font-semibold text-sm"><?= e($name) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
