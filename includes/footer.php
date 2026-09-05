  <!-- Main Footer -->
  <footer class="aura-footer">
    <div class="aura-container">

      <!-- Footer Grid -->
      <div class="footer-grid">
        <div class="footer-brand-col">
          <div class="aura-brand" style="display:flex;align-items:center;gap:10px;">
            <img src="<?= BASE_URL ?>/src/Logo.png" alt="urban outfit" style="height: 70px; filter: brightness(0) invert(1);">
          </div>
          <p>
            Crafting luxury streetwear drops and artisanal Chikankari heritage fusion for the modern aesthetic wardrobe. Designed &amp; manufactured in India.
          </p>
          <div class="footer-social-links">
            <a href="#" title="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="5"/><circle cx="17.5" cy="6.5" r="1.5" fill="currentColor" stroke="none"/></svg>
            </a>
            <a href="#" title="Twitter/X">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4l11.733 16h4.267l-11.733-16zM4 20l6.768-6.768M20 4l-6.768 6.768"/></svg>
            </a>
            <a href="#" title="YouTube">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19.1c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.43z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
            </a>
            <a href="#" title="WhatsApp">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            </a>
          </div>
          <div class="footer-payments">
            <span>Secure Payments:</span>
            <span class="payment-icon">UPI</span>
            <span class="payment-icon">VISA</span>
            <span class="payment-icon">MC</span>
            <span class="payment-icon">RUPAY</span>
            <span class="payment-icon">COD</span>
          </div>
        </div>

        <div class="footer-col">
          <?php
          // Pull all active categories grouped by department
          $footerDepts = ['men' => [], 'women' => [], 'kids' => []];
          $deptLabels  = ['men' => 'Men', 'women' => 'Women', 'kids' => 'Kids'];
          if (!empty($mysqli)) {
            $fcResult = $mysqli->query(
              "SELECT name, slug, department, parent_id
               FROM categories
               WHERE is_active = 1 AND parent_id != 0
               AND department IN ('men','women','kids')
               ORDER BY department, sort_order ASC"
            );
            if ($fcResult) {
              while ($fc = $fcResult->fetch_assoc()) {
                $footerDepts[$fc['department']][] = $fc;
              }
            }
          }

          // Fallback if DB empty
          if (empty($footerDepts['men']) && empty($footerDepts['women'])) {
            $footerDepts = [
              'men'   => [['name'=>'Kurta Sets & Sherwanis','slug'=>'men-kurta-sets','department'=>'men'],['name'=>'Shirts','slug'=>'men-shirts','department'=>'men'],['name'=>'T-Shirts & Polos','slug'=>'men-tshirts','department'=>'men'],['name'=>'Trousers & Jeans','slug'=>'men-trousers-jeans','department'=>'men'],['name'=>'Jackets & Blazers','slug'=>'men-jackets-blazers','department'=>'men']],
              'women' => [['name'=>'Suits & Salwars','slug'=>'suits-salwars','department'=>'women'],['name'=>'Kurtis & Tunics','slug'=>'kurtis-tunics','department'=>'women'],['name'=>'Sarees','slug'=>'sarees','department'=>'women'],['name'=>'Lehengas','slug'=>'lehengas','department'=>'women'],['name'=>'Western Wear & Tops','slug'=>'women-western-tops','department'=>'women']],
              'kids'  => [['name'=>'Boys Ethnic Wear','slug'=>'boys-ethnic-wear','department'=>'kids'],['name'=>'Girls Ethnic & Frocks','slug'=>'girls-ethnic-frocks','department'=>'kids'],['name'=>'Boys Casuals','slug'=>'boys-casuals','department'=>'kids'],['name'=>'Girls Casuals','slug'=>'girls-casuals','department'=>'kids']],
            ];
          }

          foreach ($deptLabels as $dept => $label):
            if (empty($footerDepts[$dept])) continue;
            if ($dept === 'kids') continue;
          ?>
          <h4 style="margin-top: <?= $dept === 'men' ? '0' : '18px' ?>;"><?= $label ?></h4>
          <ul class="footer-links" style="margin-bottom: 0;">
            <?php foreach ($footerDepts[$dept] as $cat): ?>
              <li>
                <a href="<?= BASE_URL ?>/shop.php?category=<?= urlencode($cat['department']) ?>&subcategory=<?= urlencode($cat['slug']) ?>">
                  <?= htmlspecialchars($cat['name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php endforeach; ?>
        </div>

        <div class="footer-col">
          <h4>Customer Care</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/customer/order-tracking.php">Track Your Order</a></li>
            <li><a href="<?= BASE_URL ?>/pages/shipping.php">Shipping Policy</a></li>
            <li><a href="<?= BASE_URL ?>/pages/returns.php">Returns &amp; Exchanges</a></li>
            <li><a href="<?= BASE_URL ?>/pages/contact.php">Contact &amp; Concierge</a></li>
            <li><a href="<?= BASE_URL ?>/pages/privacy.php">Privacy Policy</a></li>
            <li><a href="<?= BASE_URL ?>/pages/terms.php">Terms of Service</a></li>
          </ul>
        </div>

        <div class="footer-col">
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> urban outfit Apparel Studio. All rights reserved.</p>
        <div class="footer-bottom-links">
          <a href="<?= BASE_URL ?>/pages/privacy.php">Privacy</a>
          <a href="<?= BASE_URL ?>/pages/terms.php">Terms</a>
          <a href="<?= BASE_URL ?>/pages/contact.php">Contact</a>
        </div>
      </div>

    </div>
  </footer>

  <script>
    // Header & Mobile Drawer Scripts
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const closeDrawerBtn = document.getElementById('closeDrawerBtn');
    const drawerBackdrop = document.getElementById('drawerBackdrop');

    if (mobileMenuBtn && mobileDrawer) {
      mobileMenuBtn.addEventListener('click', () => {
        mobileDrawer.classList.add('open');
        document.body.style.overflow = 'hidden';
      });
      if (closeDrawerBtn) {
        closeDrawerBtn.addEventListener('click', () => {
          mobileDrawer.classList.remove('open');
          document.body.style.overflow = '';
        });
      }
      if (drawerBackdrop) {
        drawerBackdrop.addEventListener('click', () => {
          mobileDrawer.classList.remove('open');
          document.body.style.overflow = '';
        });
      }
    }

    // Search Modal Scripts
    const searchBtn = document.getElementById('searchBtn');
    const searchModal = document.getElementById('searchModal');
    const closeSearchBtn = document.getElementById('closeSearchBtn');
    const searchBackdrop = document.getElementById('searchBackdrop');
    const searchInput = document.getElementById('searchInput');

    if (searchBtn && searchModal) {
      searchBtn.addEventListener('click', () => {
        searchModal.classList.add('active');
        if (searchInput) searchInput.focus();
      });
      if (closeSearchBtn) {
        closeSearchBtn.addEventListener('click', () => {
          searchModal.classList.remove('active');
        });
      }
      if (searchBackdrop) {
        searchBackdrop.addEventListener('click', () => {
          searchModal.classList.remove('active');
        });
      }
    }

    // Keyboard shortcut (Cmd+K / Ctrl+K)
    document.addEventListener('keydown', (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        if (searchModal) {
          searchModal.classList.toggle('active');
          if (searchInput) searchInput.focus();
        }
      }
    });
  </script>
</body>
</html>
