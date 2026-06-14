<footer class="bg-[#ddd0a8] border-t border-[#b19681] mt-20">
  <div class="max-w-7xl mx-auto px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
      <div>
        <div class="flex items-center gap-2 mb-4">
          <div class="w-8 h-8 rounded-full gold-gradient flex items-center justify-center">
            <span class="text-black font-bold text-sm">T</span>
          </div>
          <span class="text-xl font-bold gold-text playfair">Taiba Gold</span>
        </div>
        <p class="text-[#85766a] text-sm leading-relaxed">Crafting luxury gold jewelry with unmatched quality since 1995.</p>
        <div class="flex items-center gap-3 mt-4">
          <a href="#" class="p-2 text-[#85766a] hover:text-[#8d9d4f] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><rect width="20" height="20" x="2" y="2" rx="5"/><path d="M17.5 6.5h.01"/></svg>
          </a>
          <a href="#" class="p-2 text-[#85766a] hover:text-[#8d9d4f] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
          </a>
          <a href="#" class="p-2 text-[#85766a] hover:text-[#8d9d4f] transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
        </div>
      </div>
      <div>
        <h4 class="text-[#8d9d4f] font-semibold mb-4 text-sm uppercase tracking-wider">Shop</h4>
        <ul class="space-y-2">
          <?php foreach (getCategories() as $cat): ?>
          <li><a href="/taiba-store/products.php?category=<?= e($cat['slug']) ?>" class="text-[#85766a] text-sm hover:text-[#8d9d4f] transition-colors"><?= e($cat['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="text-[#8d9d4f] font-semibold mb-4 text-sm uppercase tracking-wider">Information</h4>
        <ul class="space-y-2">
          <?php foreach (['About Us','Shipping Policy','Return Policy','Privacy Policy'] as $item): ?>
          <li><a href="#" class="text-[#85766a] text-sm hover:text-[#8d9d4f] transition-colors"><?= $item ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <h4 class="text-[#8d9d4f] font-semibold mb-4 text-sm uppercase tracking-wider">Contact</h4>
        <ul class="space-y-3 text-sm text-[#85766a]">
          <li>+966 55 000 0000</li>
          <li>info@taibagold.com</li>
          <li>Al Madinah, Saudi Arabia</li>
        </ul>
      </div>
    </div>
    <div class="border-t border-[#b19681] mt-12 pt-6 flex flex-col md:flex-row justify-between items-center gap-4">
      <p class="text-[#85766a] text-xs">&copy; <?= date('Y') ?> Taiba Gold Store. All rights reserved.</p>
      <p class="text-[#85766a] text-xs">Prices include 15% VAT</p>
    </div>
  </div>
</footer>
</body>
</html>
