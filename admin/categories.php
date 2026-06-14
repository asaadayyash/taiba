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
    if ($action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $slug = trim($_POST['slug'] ?? '') ?: strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
        $desc = trim($_POST['description'] ?? '');
        execute('INSERT IGNORE INTO categories (name,slug,description) VALUES (?,?,?)', [$name, $slug, $desc]);
        $_SESSION['admin_flash'] = 'Category created.';
    } elseif ($action === 'delete') {
        execute('DELETE FROM categories WHERE id=?', [(int)$_POST['id']]);
        $_SESSION['admin_flash'] = 'Category deleted.';
    }
    header('Location: /taiba-store/admin/categories.php');
    exit;
}

$flash      = $_SESSION['admin_flash'] ?? '';
unset($_SESSION['admin_flash']);
$categories = query('SELECT c.*,COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id ORDER BY c.name');

$adminPage = 'Categories';
require_once 'header.php';
?>

<div class="flex items-center justify-between mb-8">
  <div>
    <h1 class="text-3xl font-bold text-[#5c4b3e] playfair">Categories</h1>
    <p class="text-[#85766a] mt-1"><?= count($categories) ?> categories</p>
  </div>
  <a href="?add=1" class="btn-gold flex items-center gap-2 px-5 py-2.5 rounded-xl font-semibold text-sm">+ Add Category</a>
</div>

<?php if ($flash): ?><div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm px-4 py-3 rounded-xl"><?= e($flash) ?></div><?php endif; ?>

<?php if (isset($_GET['add'])): ?>
<div class="card-dark rounded-2xl p-6 mb-8">
  <h2 class="font-bold text-[#5c4b3e] mb-5">Add Category</h2>
  <form method="post" class="max-w-md space-y-4">
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <input type="hidden" name="action" value="create">
    <div>
      <label class="block text-[#85766a] text-sm mb-1.5">Name *</label>
      <input name="name" required class="input-dark w-full px-3 py-2.5 rounded-xl text-sm" placeholder="e.g. Rings"
        oninput="document.getElementById('slugField').value=this.value.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'')"/>
    </div>
    <div>
      <label class="block text-[#85766a] text-sm mb-1.5">Slug *</label>
      <input id="slugField" name="slug" required class="input-dark w-full px-3 py-2.5 rounded-xl text-sm font-mono" placeholder="e.g. rings"/>
    </div>
    <div>
      <label class="block text-[#85766a] text-sm mb-1.5">Description</label>
      <textarea name="description" rows="2" class="input-dark w-full px-3 py-2.5 rounded-xl text-sm resize-none"></textarea>
    </div>
    <div class="flex gap-3">
      <a href="/taiba-store/admin/categories.php" class="px-6 py-2.5 rounded-xl border border-[#b19681] text-[#85766a] text-sm">Cancel</a>
      <button type="submit" class="btn-gold px-6 py-2.5 rounded-xl text-sm font-semibold">Create</button>
    </div>
  </form>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
  <?php foreach ($categories as $cat): ?>
  <div class="card-dark rounded-2xl p-5 flex items-center gap-4">
    <div class="w-12 h-12 bg-[#8d9d4f]/10 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">🏷</div>
    <div class="flex-1 min-w-0">
      <p class="font-semibold text-[#5c4b3e] text-sm"><?= e($cat['name']) ?></p>
      <p class="text-xs text-[#a08878] mt-0.5">/<?= e($cat['slug']) ?></p>
      <p class="text-xs text-[#85766a] mt-1"><?= $cat['product_count'] ?> products</p>
    </div>
    <form method="post" onsubmit="return confirm('Delete <?= e($cat['name']) ?>?')">
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
      <input type="hidden" name="action" value="delete">
      <input type="hidden" name="id" value="<?= $cat['id'] ?>">
      <button type="submit" class="text-[#a08878] hover:text-red-400 transition-colors">🗑</button>
    </form>
  </div>
  <?php endforeach; ?>
</div>

<?php require_once 'footer.php'; ?>
