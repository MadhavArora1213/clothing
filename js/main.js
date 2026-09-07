document.addEventListener('DOMContentLoaded', () => {
  // === Header Scroll Effect ===
  const header = document.getElementById('siteHeader');
  window.addEventListener('scroll', () => {
    header && header.classList.toggle('scrolled', window.pageYOffset > 50);
  });

  // === Mobile Drawer (Sidebar) ===
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const drawer = document.getElementById('mobileDrawer');
  const drawerBackdrop = document.getElementById('drawerBackdrop');
  const closeDrawerBtn = document.getElementById('closeDrawerBtn');

  function openDrawer() {
    if (drawer) { drawer.classList.add('open'); document.body.style.overflow = 'hidden'; }
  }
  function closeDrawer() {
    if (drawer) { drawer.classList.remove('open'); document.body.style.overflow = ''; }
  }

  if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openDrawer);
  if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);
  if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);

  // Drawer: close on link click
  if (drawer) {
    drawer.querySelectorAll('.drawer-links-list a, .drawer-track-link').forEach(link => {
      link.addEventListener('click', closeDrawer);
    });
  }

  // === Drawer: Accordion Toggle ===
  document.querySelectorAll('.drawer-accordion-header').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.target;
      const body = document.getElementById(targetId);
      if (!body) return;
      const isOpen = btn.classList.contains('open');
      const scroll = btn.closest('.drawer-scroll');

      // Close other accordions
      if (scroll) {
        scroll.querySelectorAll('.drawer-accordion-header.open').forEach(h => {
          if (h !== btn) {
            h.classList.remove('open');
            const b = document.getElementById(h.dataset.target);
            if (b) b.classList.remove('open');
          }
        });
      }

      btn.classList.toggle('open', !isOpen);
      body.classList.toggle('open', !isOpen);
    });
  });

  // === Drawer: Department Tabs ===
  document.querySelectorAll('.drawer-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.drawer-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const dept = tab.dataset.dept;
      document.querySelectorAll('.drawer-dept-content').forEach(panel => {
        panel.style.display = panel.id === 'drawerDept' + dept.charAt(0).toUpperCase() + dept.slice(1) ? '' : 'none';
      });
    });
  });

  // === Drawer: Carousel — Swipe, Drag, Auto-scroll ===
  document.querySelectorAll('.drawer-carousel').forEach(carousel => {
    const track = carousel.querySelector('.drawer-carousel-track');
    const dots = carousel.querySelectorAll('.drawer-carousel-dots .dot');
    if (!track) return;

    let isDown = false, startX, scrollLeft, autoTimer;

    // Mouse drag
    track.addEventListener('mousedown', (e) => {
      isDown = true;
      track.style.cursor = 'grabbing';
      startX = e.pageX - track.offsetLeft;
      scrollLeft = track.scrollLeft;
      stopAuto();
    });
    track.addEventListener('mouseleave', () => { isDown = false; track.style.cursor = ''; });
    track.addEventListener('mouseup', () => { isDown = false; track.style.cursor = ''; startAuto(); });
    track.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - track.offsetLeft;
      track.scrollLeft = scrollLeft - (x - startX) * 1.5;
    });

    // Touch swipe
    let touchStartX, touchScrollLeft;
    track.addEventListener('touchstart', (e) => {
      touchStartX = e.touches[0].pageX;
      touchScrollLeft = track.scrollLeft;
      stopAuto();
    }, { passive: true });
    track.addEventListener('touchend', () => startAuto());

    // Dots update on scroll
    const cardW = 92;
    track.addEventListener('scroll', () => {
      if (!dots.length) return;
      const idx = Math.round(track.scrollLeft / cardW);
      dots.forEach((d, i) => d.classList.toggle('active', i === Math.min(idx, dots.length - 1)));
    });

    // Auto-scroll
    function startAuto() {
      stopAuto();
      autoTimer = setInterval(() => {
        if (track.scrollLeft + track.clientWidth >= track.scrollWidth - 5) {
          track.scrollTo({ left: 0, behavior: 'smooth' });
        } else {
          track.scrollBy({ left: cardW, behavior: 'smooth' });
        }
      }, 2500);
    }
    function stopAuto() { clearInterval(autoTimer); }

    startAuto();
    carousel.addEventListener('mouseenter', stopAuto);
    carousel.addEventListener('mouseleave', startAuto);
  });

  // === Search Modal — Live Results ===
  const searchInput = document.getElementById('searchInput');
  const searchResults = document.getElementById('searchResults');
  const searchModal = document.getElementById('searchModal');

  if (searchInput && searchResults) {
    let debounceTimer;
    searchInput.addEventListener('input', (e) => {
      clearTimeout(debounceTimer);
      const query = e.target.value.toLowerCase().trim();
      if (query.length < 2) { searchResults.innerHTML = ''; return; }
      debounceTimer = setTimeout(() => {
        fetch('/api/search.php?q=' + encodeURIComponent(query))
          .then(r => r.json())
          .then(data => {
            if (!data.length) { searchResults.innerHTML = '<div style="padding:24px;text-align:center;color:#999;">No products found</div>'; return; }
            searchResults.innerHTML = data.map(p =>
              '<a href="/product.php?slug=' + p.slug + '" class="search-result-item"><img src="' + p.image + '" alt="' + p.name + '" loading="lazy"><div class="search-result-info"><h4>' + p.name + '</h4><p>&#8377;' + parseFloat(p.price).toLocaleString('en-IN') + '</p></div></a>'
            ).join('');
          })
          .catch(() => { searchResults.innerHTML = '<div style="padding:24px;text-align:center;color:#999;">Something went wrong</div>'; });
      }, 300);
    });
  }

  // === Toast Notifications ===
  window.showToast = (message, type) => {
    type = type || 'success';
    const old = document.querySelectorAll('.uoc-toast');
    old.forEach(el => el.remove());
    const icons = {
      success: '<svg width="14" height="14" fill="none" stroke="#22c55e" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
      error: '<svg width="14" height="14" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      info: '<svg width="14" height="14" fill="none" stroke="#111" stroke-width="2.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'
    };
    const t = document.createElement('div');
    t.className = 'uoc-toast ' + type;
    t.innerHTML = '<span class="uoc-toast-icon">' + (icons[type] || icons.success) + '</span><span class="uoc-toast-text">' + message + '</span><button class="uoc-toast-close" onclick="this.parentElement.remove()"><svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
    document.body.appendChild(t);
    requestAnimationFrame(() => requestAnimationFrame(() => t.classList.add('show')));
    setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 400); }, 3500);
  };

  // === Add to Cart ===
  document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      if (this.classList.contains('loading')) return;
      this.classList.add('loading');
      setTimeout(() => {
        this.classList.remove('loading');
        this.textContent = 'Added!';
        showToast('Added to cart successfully', 'success');
        setTimeout(() => { this.textContent = 'ADD TO CART'; }, 2000);
      }, 800);
    });
  });

  // === Wishlist ===
  document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      this.classList.toggle('active');
      showToast(this.classList.contains('active') ? 'Added to wishlist' : 'Removed from wishlist', 'success');
    });
  });

  // === Quantity Stepper ===
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const valueEl = this.parentElement.querySelector('.qty-value');
      let value = parseInt(valueEl.textContent);
      value = this.textContent === '+' ? Math.min(value + 1, 10) : Math.max(value - 1, 1);
      valueEl.textContent = value;
    });
  });

  // === Accordion ===
  document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', function() {
      const item = this.parentElement;
      const isActive = item.classList.contains('active');
      document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));
      if (!isActive) item.classList.add('active');
    });
  });

  // === Intersection Observer for Animations ===
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) { entry.target.classList.add('fade-in'); observer.unobserve(entry.target); }
    });
  }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

  document.querySelectorAll('.product-card, .category-card, .section-header').forEach(el => observer.observe(el));

  // === Product Detail Page Interactions ===
  if (document.querySelector('.pdp-layout')) {
    document.querySelectorAll('.pdp-thumb').forEach(thumb => {
      thumb.addEventListener('click', function() {
        document.querySelectorAll('.pdp-thumb').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const mainImg = document.getElementById('mainProductImage');
        if (mainImg) {
          mainImg.style.opacity = '0';
          setTimeout(() => { mainImg.src = this.dataset.image; mainImg.style.opacity = '1'; }, 150);
        }
      });
    });

    document.querySelectorAll('.color-option').forEach(option => {
      option.addEventListener('click', function() {
        document.querySelectorAll('.color-option').forEach(o => o.classList.remove('active'));
        this.classList.add('active');
        const colorName = document.getElementById('selectedColorName');
        if (colorName) colorName.textContent = this.dataset.color;
      });
    });

    document.querySelectorAll('.size-option').forEach(option => {
      option.addEventListener('click', function() {
        if (this.classList.contains('out-of-stock')) return;
        document.querySelectorAll('.size-option').forEach(o => o.classList.remove('active'));
        this.classList.add('active');
        const selectedSize = document.getElementById('selectedSize');
        if (selectedSize) selectedSize.textContent = this.dataset.size;
        const mobileSizeBtn = document.getElementById('mobileSizeBtn');
        if (mobileSizeBtn) mobileSizeBtn.textContent = this.dataset.size;
      });
    });

    const mobileAddToCart = document.getElementById('mobileAddToCart');
    if (mobileAddToCart) {
      mobileAddToCart.addEventListener('click', function() {
        this.classList.add('loading');
        setTimeout(() => { this.classList.remove('loading'); showToast('Added to cart successfully', 'success'); }, 800);
      });
    }
  }
});
