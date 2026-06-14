<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Handle POST before any HTML output
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name     = trim($_POST['name']             ?? '');
    $email    = trim($_POST['email']            ?? '');
    $password = trim($_POST['password']         ?? '');
    $confirm  = trim($_POST['confirm_password'] ?? '');
    $phone    = trim($_POST['phone']            ?? '');

    if (!$name || !$email || !$password) {
        $error = 'All fields are required.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (!registerUser($name, $email, $password, $phone)) {
        $error = 'Email already registered. <a href="/taiba-store/login.php" class="underline">Login instead?</a>';
    } else {
        loginUser($email, $password);
        header('Location: /taiba-store/');
        exit;
    }
}

$pageTitle = 'Sign Up';
require_once 'includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12 relative">
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 w-96 h-96 rounded-full blur-3xl" style="background:rgba(141,157,79,.05)"></div>
  </div>
  <div class="w-full max-w-md">
    <div class="text-center mb-8">
      <a href="/taiba-store/" class="inline-flex items-center gap-2">
        <div class="w-10 h-10 rounded-full gold-gradient flex items-center justify-center"><span class="text-black font-bold">T</span></div>
        <span class="text-2xl font-bold gold-text playfair">Taiba Gold</span>
      </a>
      <h1 class="text-2xl font-bold text-[#5c4b3e] mt-6 mb-2">Create Account</h1>
      <p class="text-[#85766a] text-sm">Join our luxury jewelry family</p>
    </div>

    <form method="post" class="card-dark rounded-2xl p-8 space-y-5">
      <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">

      <?php if ($error): ?>
      <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-sm px-4 py-3 rounded-xl"><?= $error ?></div>
      <?php endif; ?>

      <div>
        <label class="block text-[#85766a] text-sm mb-2">Full Name</label>
        <input name="name" type="text" required value="<?= e($_POST['name'] ?? '') ?>" placeholder="Your full name" class="input-dark w-full px-4 py-3 rounded-xl text-sm" autocomplete="name"/>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-2">Email Address</label>
        <input name="email" type="email" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="you@example.com" class="input-dark w-full px-4 py-3 rounded-xl text-sm" autocomplete="email"/>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-2">Phone <span class="text-[#a08878]">(optional)</span></label>
        <input name="phone" type="tel" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="+966 5X XXX XXXX" class="input-dark w-full px-4 py-3 rounded-xl text-sm" autocomplete="tel"/>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-2">Password</label>
        <div class="relative">
          <input id="pw" name="password" type="password" required placeholder="Min 8 characters" class="input-dark w-full px-4 py-3 pr-12 rounded-xl text-sm" autocomplete="new-password" oninput="updateStrength(this.value)"/>
          <button type="button" onclick="const f=document.getElementById('pw');f.type=f.type==='password'?'text':'password'" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#85766a] text-sm">👁</button>
        </div>
        <div id="strength" class="mt-2 hidden">
          <div class="flex gap-1">
            <div id="s1" class="h-1 flex-1 rounded-full bg-[#b19681]"></div>
            <div id="s2" class="h-1 flex-1 rounded-full bg-[#b19681]"></div>
            <div id="s3" class="h-1 flex-1 rounded-full bg-[#b19681]"></div>
          </div>
        </div>
      </div>
      <div>
        <label class="block text-[#85766a] text-sm mb-2">Confirm Password</label>
        <input name="confirm_password" type="password" required placeholder="Repeat password" class="input-dark w-full px-4 py-3 rounded-xl text-sm" autocomplete="new-password"/>
      </div>

      <button type="submit" class="btn-gold w-full py-3 rounded-xl font-semibold mt-2">Create Account</button>
      <p class="text-center text-[#85766a] text-sm">
        Already have an account? <a href="/taiba-store/login.php" class="text-[#8d9d4f] hover:underline">Sign in</a>
      </p>
    </form>
  </div>
</div>

<script>
function updateStrength(pw) {
  document.getElementById('strength').classList.remove('hidden');
  const bars = [document.getElementById('s1'), document.getElementById('s2'), document.getElementById('s3')];
  let score = 0;
  if (pw.length >= 8) score++;
  if (/[A-Z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  const colors = ['bg-red-500','bg-yellow-500','bg-green-500'];
  bars.forEach((b,i) => b.className = 'h-1 flex-1 rounded-full ' + (i < score ? colors[score-1] : 'bg-[#b19681]'));
}
</script>
<?php require_once 'includes/footer.php'; ?>
