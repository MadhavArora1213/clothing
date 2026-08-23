  <!-- Main Footer -->
  <footer class="aura-footer">
    <div class="aura-container">
      
      <!-- Value Proposition Strip -->
      <div class="footer-features">
        <div class="footer-feature-item">
          <div class="feature-icon-box">✈️</div>
          <div>
            <h4>Free Air Shipping</h4>
            <p>On all domestic prepaid &amp; COD orders above ₹999.</p>
          </div>
        </div>

        <div class="footer-feature-item">
          <div class="feature-icon-box">🔄</div>
          <div>
            <h4>7-Day Easy Exchange</h4>
            <p>Hassle-free doorstep reverse pickup service.</p>
          </div>
        </div>

        <div class="footer-feature-item">
          <div class="feature-icon-box">🛡️</div>
          <div>
            <h4>100% Genuine Fabrics</h4>
            <p>Pure organic linen &amp; 260+ GSM combed French Terry.</p>
          </div>
        </div>

        <div class="footer-feature-item">
          <div class="feature-icon-box">💬</div>
          <div>
            <h4>24/7 Dedicated Care</h4>
            <p>WhatsApp concierge support at +91 90237 21805.</p>
          </div>
        </div>
      </div>

      <!-- Footer Grid -->
      <div class="footer-grid">
        <div class="footer-brand-col">
          <div class="aura-brand">
            <span class="aura-logo-name" style="color: #FFFFFF;">AURA</span>
            <span class="aura-logo-sub">&amp; CO.</span>
          </div>
          <p>
            Crafting luxury streetwear drops and artisanal Chikankari heritage fusion for the modern aesthetic wardrobe. Designed &amp; manufactured in India.
          </p>
          <div style="margin-top: 18px; display: flex; gap: 8px;">
            <a href="<?= BASE_URL ?>/admin/login.php" class="aura-admin-btn">
              <span>⚙️ Dynamic Admin Portal</span>
            </a>
          </div>
        </div>

        <div class="footer-col">
          <h4>Collections</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_URL ?>/shop.php?category=oversized">Oversized Drop Tees</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?category=ethnic-fusion">Arya Ethnic Fusion</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?category=co-ords">Resort Co-Ords</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?category=men">Men's Streetwear</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?category=women">Women's Edit</a></li>
            <li><a href="<?= BASE_URL ?>/shop.php?sale=1">50% OFF Flash Sale</a></li>
          </ul>
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
          <h4>VIP Club</h4>
          <p style="font-size: 12px; color: #94A3B8; margin-bottom: 14px;">
            Subscribe to receive private drops, early access to sales, and 10% OFF your first order.
          </p>
          <form onsubmit="event.preventDefault(); alert('🎉 Welcome to the AURA VIP Club! Your 10% coupon code is WELCOME10');" style="display: flex; gap: 6px;">
            <input type="email" placeholder="Enter your email" required style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); padding: 10px 14px; border-radius: 9999px; color: #FFFFFF; font-size: 12px; flex: 1; outline: none;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 18px; font-size: 12px;">Join</button>
          </form>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> AURA &amp; CO. Apparel Studio. All rights reserved.</p>
        <p>100% Secure Checkout &bull; UPI &bull; Cards &bull; NetBanking &bull; COD</p>
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
