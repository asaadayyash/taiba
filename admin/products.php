<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

// Handle POST before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'delete') {
        execute('DELETE FROM products WHERE id=?', [(int)$_POST['id']]);
        $_SESSION['admin_flash'] = 'Product deleted.';

    } elseif (in_array($action, ['create', 'update'])) {
        $data = [
            trim($_POST['name']        ?? ''),
            trim($_POST['description'] ?? ''),
            (float)($_POST['price']         ?? 0),
            !empty($_POST['compare_price']) ? (float)$_POST['compare_price'] : null,
            (int)($_POST['stock']           ?? 0),
            trim($_POST['material']    ?? ''),
            !empty($_POST['weight'])   ? (float)$_POST['weight'] : null,
            isset($_POST['featured'])  ? 1 : 0,
            isset($_POST['active'])    ? 1 : 0,
            (int)($_POST['category_id'] ?? 0),
            json_encode(array_filter(array_map('trim', explode("\n", $_POST['images'] ?? '')))),
        ];
        if ($action === 'create') {
            execute('INSERT INTO products (name,description,price,compare_price,stock,material,weight,featured,active,category_id,images) VALUES (?,?,?,?,?,?,?,?,?,?,?)', $data);
            $_SESSION['admin_flash'] = 'Product created.';
        } else {
            $data[] = (int)$_POST['id'];
            execute('UPDATE products SET name=?,description=?,price=?,compare_price=?,stock=?,material=?,weight=?,featured=?,active=?,category_id=?,images=? WHERE id=?', $data);
            $_SESSION['admin_flash'] = 'Product updated.';
        }
    }
    header('Location: /taiba-store/admin/products.php');
    exit;
}

$flash      = $_SESSION['admin_flash'] ?? '';
unset($_SESSION['admin_flash']);
$products   = query('SELECT p.*,c.name as cat_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC');
$categories = query('SELECT * FROM categories ORDER BY name');
$editId     = (int)($_GET['edit'] ?? 0);
$editP      = $editId ? queryOne('SELECT * FROM products WHERE id=?', [$editId]) : null;

$adminPage = 'Products';
require_once 'header.php';
?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-bold text-[#5c4b3e] playfair">Products</h1>
    <p class="text-[#85766a] mt-1"><?= count($products) ?> total</p>
  </div>
  <a href="?add=1" class="btn-gold flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm">+ Add Product</a>
</div>

<?php if ($flash): ?>
<div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm px-4 py-3 rounded-xl"><?= e($flash) ?></div>
<?php endif; ?>

<?php if (isset($_GET['add']) || $editId): ?>
<div class="card-dark rounded-2xl p-6 mb-8">
  <h2 class="font-bold text-[#5c4b3e] mb-5"><?= $editP ? 'Edit Product' : 'Add Product' ?></h2>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="<?= $editP ? 'update' : 'create' ?>">
    <?php if ($editP): ?><input type="hidden" name="id" value="<?= $editP['id'] ?>"><?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="md:col-span-2">
        <label class="block text-[#85766a] text-sm mb-1.5">Product Name *</label>
        <input name="name" required value="<?= e($editP['name'] ?? '') ?>" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm" placeholder="e.g. 21K Gold Ring"/>
      </div>
      <div class="md:col-span-2">
        <label class="block text-[#85766a] text-sm mb-1.5">Description</label>
        <textarea name="description" rows="3" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm resize-none"><?= e($editP['description'] ?? '') ?></textarea>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-1.5">Price (SAR) *</label>
        <input name="price" type="number" step="0.01" required value="<?= $editP['price'] ?? '' ?>" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm" placeholder="0.00"/>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-1.5">Compare Price</label>
        <input name="compare_price" type="number" step="0.01" value="<?= $editP['compare_price'] ?? '' ?>" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm" placeholder="0.00"/>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-1.5">Stock</label>
        <input name="stock" type="number" value="<?= $editP['stock'] ?? 0 ?>" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm"/>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-1.5">Weight (g)</label>
        <input name="weight" type="number" step="0.01" value="<?= $editP['weight'] ?? '' ?>" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm"/>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-1.5">Material</label>
        <input name="material" value="<?= e($editP['material'] ?? '') ?>" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm" placeholder="e.g. 21K Gold"/>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-1.5">Category *</label>
        <select name="category_id" required class="input-dark w-full px-3 py-2.5 rounded-xl text-sm">
          <option value="">Select...</option>
          <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= ($editP['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <label class="block text-[#85766a] text-sm mb-1.5">Image URLs <span class="text-[#a08878]">(one per line)</span></label>
        <textarea name="images" rows="3" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm resize-none font-mono text-xs" placeholder="https://example.com/image1.jpg"><?php
          if ($editP) { foreach (json_decode($editP['images'] ?? '[]') as $img) echo e($img)."\n"; }
        ?></textarea>
      </div>
      <div class="flex items-center gap-6">
        <label class="flex items-center gap-2 text-sm text-[#85766a] cursor-pointer">
          <input type="checkbox" name="featured" value="1" <?= !empty($editP['featured']) ? 'checked' : '' ?> class="accent-[#8d9d4f]"/> Featured
        </label>
        <label class="flex items-center gap-2 text-sm text-[#85766a] cursor-pointer">
          <input type="checkbox" name="active" value="1" <?= ($editP['active'] ?? 1) ? 'checked' : '' ?> class="accent-[#8d9d4f]"/> Active
        </label>
      </div>
    </div>

    <div class="flex gap-3 mt-6">
      <a href="/taiba-store/admin/products.php" class="px-6 py-2.5 rounded-xl border border-[#b19681] text-[#85766a] text-sm hover:text-[#85766a]">Cancel</a>
      <button type="submit" class="btn-gold px-6 py-2.5 rounded-xl text-sm font-semibold"><?= $editP ? 'Update Product' : 'Create Product' ?></button>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="card-dark rounded-2xl overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead><tr class="border-b border-[#b19681]">
        <?php foreach (['Product','Category','Price','Stock','Status','Actions'] as $h): ?>
        <th class="text-left px-6 py-3 text-xs text-[#85766a] font-medium uppercase tracking-wider"><?= $h ?></th>
        <?php endforeach; ?>
      </tr></thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr class="border-b border-[#ede4d4] hover:bg-[#e7dbbf]">
          <td class="px-6 py-4">
            <p class="font-medium text-[#5c4b3e] text-sm"><?= e($p['name']) ?></p>
            <?php if (!empty($p['material'])): ?><p class="text-xs text-[#a08878]"><?= e($p['material']) ?></p><?php endif; ?>
          </td>
          <td class="px-6 py-4 text-sm text-[#85766a]"><?= e($p['cat_name']) ?></td>
          <td class="px-6 py-4 text-sm font-semibold text-[#8d9d4f]"><?= formatPrice($p['price']) ?></td>
          <td class="px-6 py-4 text-sm <?= $p['stock']>0?'text-green-400':'text-red-400' ?>"><?= $p['stock'] ?></td>
          <td class="px-6 py-4">
            <div class="flex flex-col gap-1">
              <?php if ($p['featured']): ?><span class="text-xs bg-[#8d9d4f]/20 text-[#8d9d4f] px-2 py-0.5 rounded-full w-fit">Featured</span><?php endif; ?>
              <span class="text-xs px-2 py-0.5 rounded-full w-fit <?= $p['active']?'bg-green-500/20 text-green-400':'bg-red-500/20 text-red-400' ?>"><?= $p['active']?'Active':'Inactive' ?></span>
            </div>
          </td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <a href="?edit=<?= $p['id'] ?>" class="text-[#85766a] hover:text-[#8d9d4f]">✏️</a>
              <form method="post" onsubmit="return confirm('Delete this product?')">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit" class="text-[#85766a] hover:text-red-400">🗑</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($products)): ?>
        <tr><td colspan="6" class="px-6 py-12 text-center text-[#a08878]">No products yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'footer.php'; ?>
