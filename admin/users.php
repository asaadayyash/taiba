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
    $id     = (int)$_POST['id'];
    if ($action === 'role') {
        execute('UPDATE users SET role=? WHERE id=?', [$_POST['role'], $id]);
        $_SESSION['admin_flash'] = 'User role updated.';
    } elseif ($action === 'delete') {
        $me = currentUser();
        if ($id !== (int)$me['id']) {
            execute('DELETE FROM users WHERE id=?', [$id]);
            $_SESSION['admin_flash'] = 'User deleted.';
        }
    }
    header('Location: /taiba-store/admin/users.php');
    exit;
}

$flash     = $_SESSION['admin_flash'] ?? '';
unset($_SESSION['admin_flash']);
$users     = query('SELECT u.*,(SELECT COUNT(*) FROM orders WHERE user_id=u.id) as order_count FROM users u ORDER BY u.created_at DESC');
$adminUser = currentUser();

$adminPage = 'Users';
require_once 'header.php';
?>

<div class="mb-8">
  <h1 class="text-3xl font-bold text-[#F5F0E8] playfair">Users</h1>
  <p class="text-[#666] mt-1"><?= count($users) ?> registered users</p>
</div>
<?php if ($flash): ?><div class="mb-4 bg-green-500/10 border border-green-500/30 text-green-400 text-sm px-4 py-3 rounded-xl"><?= e($flash) ?></div><?php endif; ?>

<div class="card-dark rounded-2xl overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full">
      <thead><tr class="border-b border-[#2A2A2A]">
        <?php foreach (['User','Email','Phone','Role','Orders','Joined','Actions'] as $h): ?>
        <th class="text-left px-6 py-3 text-xs text-[#666] font-medium uppercase tracking-wider"><?= $h ?></th>
        <?php endforeach; ?>
      </tr></thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr class="border-b border-[#1A1A1A] hover:bg-[#161616]">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-[#2A2A2A] flex items-center justify-center">
                <span class="text-[#C9A840] text-sm font-bold"><?= strtoupper($u['name'][0]) ?></span>
              </div>
              <span class="font-medium text-[#F5F0E8] text-sm"><?= e($u['name']) ?></span>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-[#A0998A]"><?= e($u['email']) ?></td>
          <td class="px-6 py-4 text-sm text-[#666]"><?= e($u['phone'] ?? '—') ?></td>
          <td class="px-6 py-4">
            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full <?= $u['role']==='ADMIN'?'bg-[#C9A840]/20 text-[#C9A840]':'bg-[#2A2A2A] text-[#666]' ?>"><?= $u['role'] ?></span>
          </td>
          <td class="px-6 py-4 text-sm text-[#666]"><?= $u['order_count'] ?></td>
          <td class="px-6 py-4 text-xs text-[#555]"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <input type="hidden" name="action" value="role">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <input type="hidden" name="role" value="<?= $u['role']==='ADMIN'?'USER':'ADMIN' ?>">
                <button type="submit" title="<?= $u['role']==='ADMIN'?'Remove admin':'Make admin' ?>" class="text-sm <?= $u['role']==='ADMIN'?'text-[#C9A840]':'text-[#666] hover:text-[#C9A840]' ?>">🛡</button>
              </form>
              <?php if ($u['id'] != $adminUser['id']): ?>
              <form method="post" onsubmit="return confirm('Delete user <?= e($u['name']) ?>?')">
                <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <button type="submit" class="text-[#666] hover:text-red-400 text-sm">🗑</button>
              </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once 'footer.php'; ?>
