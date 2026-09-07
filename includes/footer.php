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
    // Search Modal
    const searchBtn = document.getElementById('searchBtn');
    const searchModal = document.getElementById('searchModal');
    const closeSearchBtn = document.getElementById('closeSearchBtn');
    const searchBackdrop = document.getElementById('searchBackdrop');
    const modalSearchInput = document.getElementById('modalSearchInput');
    const modalSearchResults = document.getElementById('searchResults');

    if (searchBtn && searchModal) {
      searchBtn.addEventListener('click', () => { searchModal.classList.add('active'); document.body.style.overflow = 'hidden'; if (modalSearchInput) setTimeout(() => modalSearchInput.focus(), 100); });
      if (closeSearchBtn) closeSearchBtn.addEventListener('click', () => { searchModal.classList.remove('active'); document.body.style.overflow = ''; });
      if (searchBackdrop) searchBackdrop.addEventListener('click', () => { searchModal.classList.remove('active'); document.body.style.overflow = ''; });
    }

    // Ctrl+K shortcut
    document.addEventListener('keydown', (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        if (searchModal) { searchModal.classList.toggle('active'); document.body.style.overflow = searchModal.classList.contains('active') ? 'hidden' : ''; if (modalSearchInput) modalSearchInput.focus(); }
      }
      if (e.key === 'Escape') {
        if (searchModal && searchModal.classList.contains('active')) {
          searchModal.classList.remove('active');
          document.body.style.overflow = '';
        }
        const dd = document.getElementById('navSearchDropdown');
        if (dd) dd.style.display = 'none';
      }
    });

    // Modal search on keyup
    if (modalSearchInput && modalSearchResults) {
      let modalTimer;
      modalSearchInput.addEventListener('input', function() {
        clearTimeout(modalTimer);
        const q = this.value.trim();
        if (q.length < 2) { modalSearchResults.innerHTML = ''; return; }
        modalTimer = setTimeout(() => {
          fetch('<?= BASE_URL ?>/api/search.php?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
              let html = '';
              if (data.categories && data.categories.length) {
                html += '<div style="padding:10px 14px 6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#999;">Categories</div>';
                data.categories.forEach(c => {
                  const url = c.parent_id > 0
                    ? '<?= BASE_URL ?>/shop.php?category=' + c.department + '&subcategory=' + c.slug
                    : '<?= BASE_URL ?>/shop.php?category=' + c.slug;
                  html += '<a href="' + url + '" style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;color:#333;transition:background 0.15s;" onmouseover="this.style.background=\'#f5f5f5\'" onmouseout="this.style.background=\'transparent\'"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg><span style="font-size:13px;font-weight:500;">' + c.name + '</span><span style="font-size:11px;color:#aaa;margin-left:auto;">' + (c.parent_id > 0 ? c.department : 'Category') + '</span></a>';
                });
              }
              if (data.products && data.products.length) {
                html += '<div style="padding:10px 14px 6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#999;border-top:1px solid #eee;">Products</div>';
                data.products.forEach(p => {
                  html += '<a href="<?= BASE_URL ?>/product.php?slug=' + p.slug + '" style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;color:#333;transition:background 0.15s;" onmouseover="this.style.background=\'#f5f5f5\'" onmouseout="this.style.background=\'transparent\'"><img src="' + (p.image || '') + '" style="width:40px;height:50px;object-fit:cover;border-radius:6px;background:#f5f5f5;" onerror="this.style.display=\'none\'"><div style="flex:1;min-width:0;"><div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + p.name + '</div><div style="font-size:12px;color:#888;">₹' + Number(p.price).toLocaleString() + (p.discount_percent ? ' <span style="color:#16a34a;font-weight:600;">' + p.discount_percent + '% OFF</span>' : '') + '</div></div></a>';
                });
              }
              if (!html) html = '<div style="padding:24px;text-align:center;color:#999;font-size:13px;">No results found</div>';
              html += '<a href="<?= BASE_URL ?>/shop.php?search=' + encodeURIComponent(q) + '" style="display:block;padding:12px;text-align:center;font-size:12px;font-weight:600;color:#000;border-top:1px solid #eee;text-decoration:none;">View all results →</a>';
              modalSearchResults.innerHTML = html;
            });
        }, 300);
      });
    }

    // ── Navbar Live Search ──
    const navInput = document.getElementById('navSearchInput');
    const navDropdown = document.getElementById('navSearchDropdown');
    if (navInput && navDropdown) {
      let navTimer;
      navInput.addEventListener('input', function() {
        clearTimeout(navTimer);
        const q = this.value.trim();
        if (q.length < 2) { navDropdown.style.display = 'none'; return; }
        navTimer = setTimeout(() => {
          fetch('<?= BASE_URL ?>/api/search.php?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
              let html = '';
              if (data.categories && data.categories.length) {
                html += '<div style="padding:10px 14px 6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#999;">Categories</div>';
                data.categories.forEach(c => {
                  const url = c.parent_id > 0
                    ? '<?= BASE_URL ?>/shop.php?category=' + c.department + '&subcategory=' + c.slug
                    : '<?= BASE_URL ?>/shop.php?category=' + c.slug;
                  html += '<a href="' + url + '" style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;color:#333;transition:background 0.15s;" onmouseover="this.style.background=\'#f5f5f5\'" onmouseout="this.style.background=\'transparent\'"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/></svg><span style="font-size:13px;font-weight:500;">' + c.name + '</span></a>';
                });
              }
              if (data.products && data.products.length) {
                html += '<div style="padding:10px 14px 6px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#999;border-top:1px solid #eee;">Products</div>';
                data.products.forEach(p => {
                  html += '<a href="<?= BASE_URL ?>/product.php?slug=' + p.slug + '" style="display:flex;align-items:center;gap:10px;padding:10px 14px;text-decoration:none;color:#333;transition:background 0.15s;" onmouseover="this.style.background=\'#f5f5f5\'" onmouseout="this.style.background=\'transparent\'"><img src="' + (p.image || '') + '" style="width:40px;height:50px;object-fit:cover;border-radius:6px;background:#f5f5f5;" onerror="this.style.display=\'none\'"><div style="flex:1;min-width:0;"><div style="font-size:13px;font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + p.name + '</div><div style="font-size:12px;color:#888;">₹' + Number(p.price).toLocaleString() + (p.discount_percent ? ' <span style="color:#16a34a;font-weight:600;">' + p.discount_percent + '% OFF</span>' : '') + '</div></div></a>';
                });
              }
              if (!html) {
                html = '<div style="padding:20px;text-align:center;color:#999;font-size:13px;">No results found</div>';
              } else {
                html += '<a href="<?= BASE_URL ?>/shop.php?search=' + encodeURIComponent(q) + '" style="display:block;padding:12px;text-align:center;font-size:12px;font-weight:600;color:#000;border-top:1px solid #eee;text-decoration:none;">View all results →</a>';
              }
              navDropdown.innerHTML = html;
              navDropdown.style.display = 'block';
            });
        }, 300);
      });

      // Enter key on navbar search
      navInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && this.value.trim()) {
          navDropdown.style.display = 'none';
          window.location = '<?= BASE_URL ?>/shop.php?search=' + encodeURIComponent(this.value.trim());
        }
      });

      // Close dropdown on outside click
      document.addEventListener('click', function(e) {
        if (!navInput.contains(e.target) && !navDropdown.contains(e.target)) {
          navDropdown.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html>