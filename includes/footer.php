  <!-- Footer -->
  <footer class="uoc-footer">
    <div class="uoc-container">

      <!-- Footer Top -->
      <div class="uoc-footer-top">
        <div class="uoc-footer-brand">
          <div class="uoc-footer-logo">UOC</div>
          <p>Premium streetwear, ethnic fusion & contemporary fashion. Handcrafted in India.</p>
          <div class="uoc-footer-social">
            <a href="#" aria-label="Instagram">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
            </a>
            <a href="#" aria-label="Twitter">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4 4l11.733 16h4.267l-11.733-16zM4 20l6.768-6.768M20 4l-6.768 6.768"/></svg>
            </a>
            <a href="#" aria-label="YouTube">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
            </a>
          </div>
        </div>

        <div class="uoc-footer-col">
          <h4>Shop</h4>
          <ul>
            <li><a href="<?= BASE_URL ?>/shop.php?category=new-arrivals">New Arrivals</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?category=men">Men</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?category=women">Women</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion">Ethnic Fusion</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?category=sale">Sale</a></li>
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

  <script>
    // Header scroll
    const header = document.getElementById('siteHeader');
    if (header) {
      window.addEventListener('scroll', () => {
        header.classList.toggle('scrolled', window.scrollY > 20);
      });
    }

    // Mobile Drawer
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');
    const drawerBackdrop = document.getElementById('drawerBackdrop');

    if (mobileMenuBtn && mobileDrawer) {
      mobileMenuBtn.addEventListener('click', () => {
        mobileDrawer.classList.add('open');
        document.body.style.overflow = 'hidden';
      });
      if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', () => { mobileDrawer.classList.remove('open'); document.body.style.overflow = ''; });
      if (drawerBackdrop) drawerBackdrop.addEventListener('click', () => { mobileDrawer.classList.remove('open'); document.body.style.overflow = ''; });
    }

    // Search Modal
    const searchBtn = document.getElementById('searchBtn');
    const searchModal = document.getElementById('searchModal');
    const closeSearchBtn = document.getElementById('closeSearchBtn');
    const searchBackdrop = document.getElementById('searchBackdrop');
    const searchInput = document.getElementById('searchInput');

    if (searchBtn && searchModal) {
      searchBtn.addEventListener('click', () => { searchModal.classList.add('active'); if (searchInput) searchInput.focus(); });
      if (closeSearchBtn) closeSearchBtn.addEventListener('click', () => searchModal.classList.remove('active'));
      if (searchBackdrop) searchBackdrop.addEventListener('click', () => searchModal.classList.remove('active'));
    }

    // Ctrl+K shortcut
    document.addEventListener('keydown', (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        if (searchModal) { searchModal.classList.toggle('active'); if (searchInput) searchInput.focus(); }
      }
    });
  </script>
</body>
</html>