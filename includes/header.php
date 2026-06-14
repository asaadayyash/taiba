<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

$user       = currentUser();
$cartItems  = isLoggedIn() ? cartCount() : 0;
$categories = getCategories();
$csrf       = csrfToken();
$pageTitle  = $pageTitle ?? SITE_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title><?= e($pageTitle) ?> — Taiba Gold</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet"/>
<style>
  body { font-family:'Merriweather',serif; background:#e4d7b0; color:#5c4b3e; }
  .gold-text { background:linear-gradient(135deg,#8d9d4f,#b5c97a); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
  .gold-gradient { background:linear-gradient(135deg,#8d9d4f 0%,#a8b86a 100%); }
  .btn-gold { background:linear-gradient(135deg,#8d9d4f,#a8b86a); color:#fdfbf6; font-weight:600; transition:all .2s; }
  .btn-gold:hover { opacity:.9; transform:translateY(-1px); box-shadow:0 4px 20px rgba(141,157,79,.5); }
  .card-dark { background:#ede9d8; border:1px solid #c9b99a; }
  .card-hover { transition:all .3s; }
  .card-hover:hover { border-color:#8d9d4f; transform:translateY(-2px); box-shadow:0 8px 30px rgba(141,157,79,.2); }
  .input-dark { background:#f0e9d2; border:1px solid #c9b99a; color:#5c4b3e; transition:border-color .2s; }
  .input-dark:focus { outline:none; border-color:#8d9d4f; box-shadow:0 0 0 3px rgba(141,157,79,.15); }
  .input-dark::placeholder { color:#a09080; }
  .playfair { font-family:'Playfair Display',serif; }
  ::-webkit-scrollbar { width:6px; } ::-webkit-scrollbar-track { background:#e4d7b0; } ::-webkit-scrollbar-thumb { background:#8d9d4f; border-radius:3px; }
</style>
</head>
<body class="min-h-screen">

<!-- Navbar -->
<nav class="fixed top-0 left-0 right-0 z-50 bg-[#e4d7b0]/95 backdrop-blur-sm border-b border-[#b19681]">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="flex items-center justify-between h-16">

      <!-- Logo -->
      <a href="/taiba-store/" class="flex items-center gap-2">
        <div class="w-8 h-8 rounded-full gold-gradient flex items-center justify-center">
          <span class="text-black font-bold text-sm">T</span>
        </div>
        <span class="text-xl font-bold gold-text playfair">Taiba Gold</span>
      </a>

      <!-- Desktop links -->
      <div class="hidden md:flex items-center gap-6">
        <a href="/taiba-store/" class="text-sm text-[#85766a] hover:text-[#8d9d4f] transition-colors">Home</a>
        <a href="/taiba-store/products.php" class="text-sm text-[#85766a] hover:text-[#8d9d4f] transition-colors">All Jewelry</a>
        <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
        <a href="/taiba-store/products.php?category=<?= e($cat['slug']) ?>" class="text-sm text-[#85766a] hover:text-[#8d9d4f] transition-colors"><?= e($cat['name']) ?></a>
        <?php endforeach; ?>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-3">
        <!-- Search -->
        <button onclick="document.getElementById('searchBar').classList.toggle('hidden')" class="p-2 text-[#85766a] hover:text-[#8d9d4f] transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </button>

        <?php if ($user): ?>
        <!-- Cart -->
        <a href="/taiba-store/cart.php" class="relative p-2 text-[#85766a] hover:text-[#8d9d4f] transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
          <?php if ($cartItems > 0): ?>
          <span id="cartCount" class="absolute -top-1 -right-1 w-5 h-5 rounded-full gold-gradient text-black text-xs font-bold flex items-center justify-center"><?= $cartItems ?></span>
          <?php endif; ?>
        </a>
        <!-- User menu -->
        <div class="relative" x-data="{open:false}">
          <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="p-2 text-[#85766a] hover:text-[#8d9d4f] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          </button>
          <div class="hidden absolute right-0 top-10 w-48 bg-[#e7dbbf] border border-[#b19681] rounded-xl shadow-xl py-2 z-50">
            <p class="px-4 py-2 text-xs text-[#85766a] border-b border-[#b19681]"><?= e($user['email']) ?></p>
            <a href="/taiba-store/orders.php" class="flex items-center gap-2 px-4 py-2 text-sm text-[#85766a] hover:text-[#8d9d4f] hover:bg-[#dbc894]">My Orders</a>
            <?php if (isAdmin()): ?>
            <a href="/taiba-store/admin/" class="flex items-center gap-2 px-4 py-2 text-sm text-[#8d9d4f] hover:bg-[#dbc894]">Admin Panel</a>
            <?php endif; ?>
            <a href="/taiba-store/logout.php" class="flex items-center gap-2 px-4 py-2 text-sm text-red-400 hover:bg-[#dbc894]">Sign Out</a>
          </div>
        </div>
        <?php else: ?>
        <a href="/taiba-store/login.php" class="text-sm text-[#85766a] hover:text-[#8d9d4f] transition-colors">Login</a>
        <a href="/taiba-store/signup.php" class="btn-gold text-sm px-4 py-1.5 rounded-full">Sign Up</a>
        <?php endif; ?>

        <!-- Mobile toggle -->
        <button class="md:hidden p-2 text-[#85766a]" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
        </button>
      </div>
    </div>

    <!-- Search bar -->
    <div id="searchBar" class="hidden py-3 border-t border-[#b19681]">
      <form action="/taiba-store/products.php" method="get">
        <input name="search" type="text" placeholder="Search jewelry..." class="input-dark w-full px-4 py-2 rounded-lg text-sm" autofocus/>
      </form>
    </div>

    <!-- Mobile menu -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-[#b19681] py-4 flex flex-col gap-3">
      <a href="/taiba-store/" class="text-sm text-[#85766a] hover:text-[#8d9d4f]">Home</a>
      <a href="/taiba-store/products.php" class="text-sm text-[#85766a] hover:text-[#8d9d4f]">All Jewelry</a>
      <?php foreach ($categories as $cat): ?>
      <a href="/taiba-store/products.php?category=<?= e($cat['slug']) ?>" class="text-sm text-[#85766a] hover:text-[#8d9d4f]"><?= e($cat['name']) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</nav>
<div class="pt-16"></div>
