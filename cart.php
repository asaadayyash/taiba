<?php
$pageTitle = 'Shopping Cart';
require_once 'includes/header.php';
requireLogin();

$userId = $_SESSION['user_id'];
$items  = query(
    'SELECT c.*, p.name, p.price, p.images, p.stock, cat.name as cat_name FROM cart c
     JOIN products p ON p.id=c.product_id
     JOIN categories cat ON cat.id=p.category_id
     WHERE c.user_id=? AND p.active=1',
    [$userId]
);

$subtotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
$tax      = $subtotal * VAT_RATE;
$shipping = $subtotal >= FREE_SHIPPING_THRESHOLD || empty($items) ? 0 : SHIPPING_COST;
$total    = $subtotal + $tax + $shipping;

$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);
$orderError = $_SESSION['order_error'] ?? '';
unset($_SESSION['order_error']);
?>

<div class="py-8 pb-20 max-w-6xl mx-auto px-6">
  <h1 class="text-3xl font-bold text-[#5c4b3e] mb-8 playfair">Shopping Cart</h1>

  <?php if ($flash): ?>
  <div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm px-4 py-3 rounded-xl"><?= e($flash) ?></div>
  <?php endif; ?>
  <?php if ($orderError): ?>
  <div class="mb-4 bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-3 rounded-xl"><?= e($orderError) ?></div>
  <?php endif; ?>

  <?php if (empty($items)): ?>
  <div class="text-center py-20">
    <div class="text-5xl mb-4">🛒</div>
    <p class="text-[#85766a] text-lg mb-6">Your cart is empty</p>
    <a href="/taiba-store/products.php" class="btn-gold inline-flex items-center gap-2 px-8 py-3 rounded-full">Start Shopping →</a>
  </div>
  <?php else: ?>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Items -->
    <div class="lg:col-span-2 space-y-4">
      <?php foreach ($items as $item): ?>
      <?php $imgs = parseImages($item['images']); ?>
      <div class="card-dark rounded-2xl p-4 flex gap-4">
        <a href="/taiba-store/product.php?id=<?= $item['product_id'] ?>">
          <div class="w-24 h-24 rounded-xl overflow-hidden bg-[#dbc894] flex-shrink-0">
            <?php if (!empty($imgs[0])): ?>
              <img src="<?= e($imgs[0]) ?>" alt="<?= e($item['name']) ?>" class="w-full h-full object-cover"/>
            <?php else: ?>
              <div class="w-full h-full flex items-center justify-center text-[#c4b49a] text-2xl playfair">T</div>
            <?php endif; ?>
          </div>
        </a>
        <div class="flex-1 min-w-0">
          <p class="text-xs text-[#8d9d4f] mb-1"><?= e($item['cat_name']) ?></p>
          <a href="/taiba-store/product.php?id=<?= $item['product_id'] ?>"><p class="font-medium text-[#5c4b3e] text-sm mb-2"><?= e($item['name']) ?></p></a>
          <p class="text-[#8d9d4f] font-bold"><?= formatPrice($item['price']) ?></p>
        </div>
        <div class="flex flex-col items-end justify-between">
          <!-- Remove -->
          <form method="post" action="/taiba-store/api/cart.php">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
            <input type="hidden" name="redirect" value="/taiba-store/cart.php">
            <button type="submit" class="text-[#a08878] hover:text-red-400 transition-colors text-sm">🗑</button>
          </form>
          <!-- Quantity -->
          <form method="post" action="/taiba-store/api/cart.php" class="flex items-center gap-2 bg-[#dbc894] rounded-full px-3 py-1.5">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
            <input type="hidden" name="redirect" value="/taiba-store/cart.php">
            <button name="quantity" value="<?= $item['quantity'] - 1 ?>" class="text-[#85766a] hover:text-[#8d9d4f] w-5 text-center">−</button>
            <span class="text-[#5c4b3e] text-sm w-5 text-center"><?= $item['quantity'] ?></span>
            <button name="quantity" value="<?= $item['quantity'] + 1 ?>" class="text-[#85766a] hover:text-[#8d9d4f] w-5 text-center">+</button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Summary + Checkout -->
    <div>
      <div class="card-dark rounded-2xl p-6 sticky top-24">
        <h2 class="font-bold text-[#5c4b3e] mb-5 text-lg">Order Summary</h2>
        <div class="space-y-3 text-sm mb-5">
          <div class="flex justify-between"><span class="text-[#85766a]">Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
          <div class="flex justify-between"><span class="text-[#85766a]">VAT (15%)</span><span><?= formatPrice($tax) ?></span></div>
          <div class="flex justify-between"><span class="text-[#85766a]">Shipping</span><span class="<?= $shipping===0?'text-green-400':'' ?>"><?= $shipping===0?'Free':formatPrice($shipping) ?></span></div>
          <div class="border-t border-[#b19681] pt-3 flex justify-between font-bold text-base">
            <span>Total</span><span class="text-[#8d9d4f]"><?= formatPrice($total) ?></span>
          </div>
        </div>

        <!-- Checkout form -->
        <form method="post" action="/taiba-store/api/order.php" id="checkoutForm">
          <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
          <div id="checkoutFields" class="hidden space-y-3 mb-4">
            <h3 class="font-semibold text-[#5c4b3e] text-sm">Delivery Details</h3>
            <input name="address" required placeholder="Full delivery address *" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm"/>
            <input name="phone" type="tel" required placeholder="Phone number *" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm"/>
            <textarea name="notes" placeholder="Order notes (optional)" rows="2" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm resize-none"></textarea>
            <button type="submit" class="btn-gold w-full py-3 rounded-xl font-semibold">Place Order</button>
            <button type="button" onclick="toggleCheckout()" class="w-full py-2 text-sm text-[#85766a] hover:text-[#85766a]">← Back to Cart</button>
          </div>
          <button type="button" onclick="toggleCheckout()" id="proceedBtn" class="btn-gold w-full py-3 rounded-xl font-semibold">
            Proceed to Checkout
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<script>
function toggleCheckout() {
  const fields = document.getElementById('checkoutFields');
  const btn    = document.getElementById('proceedBtn');
  const hidden = fields.classList.contains('hidden');
  fields.classList.toggle('hidden', !hidden);
  btn.classList.toggle('hidden', hidden);
}
</script>

<?php require_once 'includes/footer.php'; ?>
