<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();

$csrf       = csrfToken();
$adminUser  = currentUser();
$adminPage  = $adminPage ?? 'Dashboard';
$currentUri = $_SERVER['REQUEST_URI'];

$navItems = [
    '/taiba-store/admin/'              => ['Dashboard','📊'],
    '/taiba-store/admin/products.php'  => ['Products','📦'],
    '/taiba-store/admin/orders.php'    => ['Orders','🛍'],
    '/taiba-store/admin/users.php'     => ['Users','👥'],
    '/taiba-store/admin/categories.php'=> ['Categories','🏷'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Admin: <?= e($adminPage) ?> — Taiba Gold</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet"/>
<style>
  body{font-family:'Merriweather',serif;background:#e4d7b0;color:#5c4b3e;}
  .gold-text{background:linear-gradient(135deg,#8d9d4f,#b5c97a);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  .gold-gradient{background:linear-gradient(135deg,#8d9d4f 0%,#a8b86a 100%);}
  .btn-gold{background:linear-gradient(135deg,#8d9d4f,#a8b86a);color:#fdfbf6;font-weight:600;transition:all .2s;}
  .btn-gold:hover{opacity:.9;box-shadow:0 4px 16px rgba(141,157,79,.4);}
  .card-dark{background:#ede9d8;border:1px solid #c9b99a;}
  .input-dark{background:#f0e9d2;border:1px solid #c9b99a;color:#5c4b3e;transition:border-color .2s;}
  .input-dark:focus{outline:none;border-color:#8d9d4f;}
  .input-dark::placeholder{color:#a09080;}
  .playfair{font-family:'Playfair Display',serif;}
  ::-webkit-scrollbar{width:5px;} ::-webkit-scrollbar-track{background:#e4d7b0;} ::-webkit-scrollbar-thumb{background:#8d9d4f;border-radius:3px;}
</style>
</head>
<body class="flex min-h-screen">

<!-- Sidebar -->
<aside class="w-60 min-h-screen bg-[#ddd0a8] border-r border-[#b19681] flex flex-col flex-shrink-0">
  <div class="p-6 border-b border-[#b19681]">
    <a href="/taiba-store/admin/" class="flex items-center gap-2">
      <div class="w-8 h-8 rounded-full gold-gradient flex items-center justify-center"><span class="text-black font-bold text-sm">T</span></div>
      <div>
        <p class="font-bold text-[#5c4b3e] text-sm leading-tight">Taiba Gold</p>
        <p class="text-[#8d9d4f] text-xs">Admin Panel</p>
      </div>
    </a>
  </div>
  <nav class="flex-1 p-4 space-y-1">
    <?php foreach ($navItems as $href => [$label, $icon]): ?>
    <?php $active = str_contains($currentUri, $href) && ($href !== '/taiba-store/admin/' || str_ends_with($currentUri, '/admin/')); ?>
    <a href="<?= $href ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $active ? 'bg-[#8d9d4f]/15 text-[#5e7230] border border-[#8d9d4f]/30' : 'text-[#85766a] hover:text-[#5c4b3e] hover:bg-[#dbc894]' ?>">
      <span><?= $icon ?></span><?= $label ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="p-4 border-t border-[#b19681] space-y-1">
    <a href="/taiba-store/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-[#85766a] hover:text-[#5c4b3e] hover:bg-[#dbc894]">🏪 View Store</a>
    <a href="/taiba-store/logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-500/70 hover:text-red-600 hover:bg-[#dbc894]">🚪 Sign Out</a>
  </div>
</aside>

<!-- Main -->
<main class="flex-1 overflow-auto">
<div class="p-8">
