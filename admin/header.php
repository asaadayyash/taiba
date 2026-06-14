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
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<style>
  body{font-family:'Inter',sans-serif;background:#0D0D0D;color:#F5F0E8;}
  .gold-text{background:linear-gradient(135deg,#C9A840,#E2C97E);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  .gold-gradient{background:linear-gradient(135deg,#C9A840 0%,#E2C97E 100%);}
  .btn-gold{background:linear-gradient(135deg,#C9A840,#E2C97E);color:#0D0D0D;font-weight:600;transition:all .2s;}
  .btn-gold:hover{opacity:.9;}
  .card-dark{background:#161616;border:1px solid #2A2A2A;}
  .input-dark{background:#1E1E1E;border:1px solid #2A2A2A;color:#F5F0E8;transition:border-color .2s;}
  .input-dark:focus{outline:none;border-color:#C9A840;}
  .input-dark::placeholder{color:#666;}
  .playfair{font-family:'Playfair Display',serif;}
  ::-webkit-scrollbar{width:5px;} ::-webkit-scrollbar-track{background:#0D0D0D;} ::-webkit-scrollbar-thumb{background:#C9A840;border-radius:3px;}
</style>
</head>
<body class="flex min-h-screen">

<!-- Sidebar -->
<aside class="w-60 min-h-screen bg-[#0A0A0A] border-r border-[#2A2A2A] flex flex-col flex-shrink-0">
  <div class="p-6 border-b border-[#2A2A2A]">
    <a href="/taiba-store/admin/" class="flex items-center gap-2">
      <div class="w-8 h-8 rounded-full gold-gradient flex items-center justify-center"><span class="text-black font-bold text-sm">T</span></div>
      <div>
        <p class="font-bold text-[#F5F0E8] text-sm leading-tight">Taiba Gold</p>
        <p class="text-[#C9A840] text-xs">Admin Panel</p>
      </div>
    </a>
  </div>
  <nav class="flex-1 p-4 space-y-1">
    <?php foreach ($navItems as $href => [$label, $icon]): ?>
    <?php $active = str_contains($currentUri, $href) && ($href !== '/taiba-store/admin/' || str_ends_with($currentUri, '/admin/')); ?>
    <a href="<?= $href ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $active ? 'bg-[#C9A840]/10 text-[#C9A840] border border-[#C9A840]/20' : 'text-[#666] hover:text-[#A0998A] hover:bg-[#161616]' ?>">
      <span><?= $icon ?></span><?= $label ?>
    </a>
    <?php endforeach; ?>
  </nav>
  <div class="p-4 border-t border-[#2A2A2A] space-y-1">
    <a href="/taiba-store/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-[#666] hover:text-[#A0998A] hover:bg-[#161616]">🏪 View Store</a>
    <a href="/taiba-store/logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm text-red-400/70 hover:text-red-400 hover:bg-[#161616]">🚪 Sign Out</a>
  </div>
</aside>

<!-- Main -->
<main class="flex-1 overflow-auto">
<div class="p-8">
