<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Handle POST before any HTML output
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    if (loginUser($email, $password)) {
        $redirect = $_SESSION['redirect_after_login'] ?? '/taiba-store/';
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $redirect);
        exit;
    }
    $error = 'Invalid email or password. Please try again.';
}

$pageTitle = 'Login';
require_once 'includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12 relative">
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-96 h-96 rounded-full blur-3xl" style="background:rgba(201,168,64,.05)"></div>
  </div>
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <a href="/taiba-store/" class="inline-flex items-center gap-2">
        <div class="w-10 h-10 rounded-full gold-gradient flex items-center justify-center"><span class="text-black font-bold">T</span></div>
        <span class="text-2xl font-bold gold-text playfair">Taiba Gold</span>
      </a>
      <h1 class="text-2xl font-bold text-[#F5F0E8] mt-6 mb-2">Welcome Back</h1>
      <p class="text-[#666] text-sm">Sign in to your account</p>
    </div>

    <form method="post" class="card-dark rounded-2xl p-8 space-y-5">
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

      <?php if ($error): ?>
      <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-3 rounded-xl"><?= e($error) ?></div>
      <?php endif; ?>

      <div>
        <label class="block text-[#A0998A] text-sm mb-2" for="email">Email Address</label>
        <input id="email" name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@example.com" class="input-dark w-full px-4 py-3 rounded-xl text-sm" autocomplete="email"/>
      </div>

      <div>
        <label class="block text-[#A0998A] text-sm mb-2" for="password">Password</label>
        <div class="relative">
          <input id="password" name="password" type="password" required placeholder="••••••••" class="input-dark w-full px-4 py-3 pr-12 rounded-xl text-sm" autocomplete="current-password"/>
          <button type="button" onclick="const f=this.previousElementSibling;f.type=f.type==='password'?'text':'password';this.textContent=f.type==='password'?'👁':'🙈'" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#666] text-sm">👁</button>
        </div>
      </div>

      <button type="submit" class="btn-gold w-full py-3 rounded-xl font-semibold mt-2">Sign In</button>

      <p class="text-center text-[#666] text-sm">
        Don't have an account? <a href="/taiba-store/signup.php" class="text-[#C9A840] hover:underline">Create one</a>
      </p>
    </form>
  </div>
</div>

<?php require_once 'includes/footer.php'; ?>
