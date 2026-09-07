  <!-- Footer -->
  <footer class="uoc-footer">
    <div class="uoc-container">

      <!-- Footer Top -->
      <div class="uoc-footer-top">
        <div class="uoc-footer-brand">
          <div class="uoc-footer-logo">
            <img src="<?= BASE_URL ?>/src/Logo.png" alt="Urban Outfit Collection" style="height:70px; width:auto;">
          </div>
          <p>Fashion and clothing store for men, women and kids. Mukerian, Punjab.</p>
          <div class="uoc-footer-social">
            <a href="https://www.instagram.com/urban_0utfit_mukerian/" target="_blank" rel="noopener" aria-label="Instagram">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
            </a>
            <a href="https://www.facebook.com/urban.outfit.2025" target="_blank" rel="noopener" aria-label="Facebook">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
          </div>
        </div>

        <div class="uoc-footer-col">
          <h4>Men</h4>
          <ul>
            <?php
            $menCats = $mysqli ? $mysqli->query("SELECT name, slug FROM categories WHERE parent_id = 2 AND is_active = 1 ORDER BY sort_order")->fetch_all(MYSQLI_ASSOC) : [];
            foreach ($menCats as $mc): ?>
              <li><a href="<?= BASE_URL ?>/shop.php?category=<?= $mc['slug'] ?>"><?= $mc['name'] ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="uoc-footer-col">
          <h4>Help</h4>
          <ul>
            <li><a href="<?= BASE_URL ?>/pages/shipping.php">Shipping</a></li>
            <li><a href="<?= BASE_URL ?>/pages/returns.php">Returns & Exchanges</a></li>
            <li><a href="<?= BASE_URL ?>/pages/contact.php">Contact Us</a></li>
            <li><a href="<?= BASE_URL ?>/pages/privacy.php">Privacy Policy</a></li>
            <li><a href="<?= BASE_URL ?>/pages/terms.php">Terms of Service</a></li>
          </ul>
        </div>

        <div class="uoc-footer-col">
          <h4>Account</h4>
          <ul>
            <?php if (isset($_SESSION['customer_id'])): ?>
              <li><a href="<?= BASE_URL ?>/customer/account.php">My Account</a></li>
              <li><a href="<?= BASE_URL ?>/customer/orders.php">Orders</a></li>
              <li><a href="<?= BASE_URL ?>/customer/wishlist.php">Wishlist</a></li>
            <?php else: ?>
              <li><a href="<?= BASE_URL ?>/customer/login.php">Sign In</a></li>
              <li><a href="<?= BASE_URL ?>/customer/register.php">Create Account</a></li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      <!-- Footer Bottom -->
      <div class="uoc-footer-bottom">
        <p>&copy; <?= date('Y') ?> UOC. All rights reserved.</p>
        <div class="uoc-footer-payments">
          <span>UPI</span>
          <span>VISA</span>
          <span>MC</span>
          <span>RuPay</span>
          <span>COD</span>
        </div>
      </div>

    </div>
  </footer>

  <script src="<?= BASE_URL ?>/js/main.js?v=<?= filemtime(__DIR__ . '/../js/main.js') ?>"></script>
  <script>
    // Search Modal (handled from footer to avoid duplicates with main.js)
    const searchBtn = document.getElementById('searchBtn');
    const searchModal = document.getElementById('searchModal');
    const closeSearchBtn = document.getElementById('closeSearchBtn');
    const searchBackdrop = document.getElementById('searchBackdrop');
    const searchInput = document.getElementById('searchInput');

    if (searchBtn && searchModal) {
      searchBtn.addEventListener('click', () => { searchModal.classList.add('active'); document.body.style.overflow = 'hidden'; if (searchInput) setTimeout(() => searchInput.focus(), 100); });
      if (closeSearchBtn) closeSearchBtn.addEventListener('click', () => { searchModal.classList.remove('active'); document.body.style.overflow = ''; });
      if (searchBackdrop) searchBackdrop.addEventListener('click', () => { searchModal.classList.remove('active'); document.body.style.overflow = ''; });
    }

    // Ctrl+K shortcut
    document.addEventListener('keydown', (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        if (searchModal) { searchModal.classList.toggle('active'); document.body.style.overflow = searchModal.classList.contains('active') ? 'hidden' : ''; if (searchInput) searchInput.focus(); }
      }
      if (e.key === 'Escape' && searchModal && searchModal.classList.contains('active')) {
        searchModal.classList.remove('active');
        document.body.style.overflow = '';
      }
    });
  </script>
</body>
</html>