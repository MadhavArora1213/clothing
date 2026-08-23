document.addEventListener('DOMContentLoaded', () => {
  // === Header Scroll Effect ===
  const header = document.getElementById('siteHeader');
  window.addEventListener('scroll', () => {
    if (window.pageYOffset > 50) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  });

  // === Mobile Menu ===
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileNav = document.getElementById('mobileNav');

  if (mobileMenuBtn && mobileNav) {
    mobileMenuBtn.addEventListener('click', () => {
      mobileMenuBtn.classList.toggle('active');
      mobileNav.classList.toggle('active');
      document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
    });

    mobileNav.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        mobileMenuBtn.classList.remove('active');
        mobileNav.classList.remove('active');
        document.body.style.overflow = '';
      });
    });
  }

  // === Search Overlay ===
  const searchBtn = document.getElementById('searchBtn');
  const searchOverlay = document.getElementById('searchOverlay');
  const closeSearch = document.getElementById('closeSearch');
  const searchInput = document.getElementById('searchInput');
  const searchResults = document.getElementById('searchResults');

  if (searchBtn && searchOverlay) {
    const openSearch = () => {
      searchOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      setTimeout(() => searchInput.focus(), 100);
    };

    const closeSearchOverlay = () => {
      searchOverlay.classList.remove('active');
      document.body.style.overflow = '';
    };

    searchBtn.addEventListener('click', openSearch);
    closeSearch.addEventListener('click', closeSearchOverlay);
    searchOverlay.addEventListener('click', (e) => {
      if (e.target === searchOverlay) closeSearchOverlay();
    });

    document.addEventListener('keydown', (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        openSearch();
      }
      if (e.key === 'Escape' && searchOverlay.classList.contains('active')) {
        closeSearchOverlay();
      }
    });

    if (searchInput && searchResults) {
      searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        if (query.length < 2) {
          searchResults.innerHTML = '';
          return;
        }

        fetch('/api/search.php?q=' + encodeURIComponent(query))
          .then(res => res.json())
          .then(data => {
            if (data.length === 0) {
              searchResults.innerHTML = '<div style="padding: 24px; text-align: center; color: var(--color-text-tertiary);">No products found</div>';
              return;
            }
            searchResults.innerHTML = data.map(product => `
              <a href="/product.php?slug=${product.slug}" class="search-result-item">
                <img src="${product.image}" alt="${product.name}" loading="lazy">
                <div class="search-result-info">
                  <h4>${product.name}</h4>
                  <p>₹${parseFloat(product.price).toLocaleString('en-IN')}</p>
                </div>
              </a>
            `).join('');
          })
          .catch(() => {
            searchResults.innerHTML = '<div style="padding: 24px; text-align: center; color: var(--color-text-tertiary);">Something went wrong</div>';
          });
    }
  }

  // === Toast Notifications ===
  window.showToast = (message, type = 'success') => {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    container.appendChild(toast);

    setTimeout(() => {
      toast.classList.add('removing');
      setTimeout(() => toast.remove(), 300);
    }, 3000);
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
        setTimeout(() => {
          this.textContent = 'ADD TO CART';
        }, 2000);
      }, 800);
    });
  });

  // === Wishlist ===
  document.querySelectorAll('.wishlist-btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      this.classList.toggle('active');
      if (this.classList.contains('active')) {
        showToast('Added to wishlist', 'success');
      } else {
        showToast('Removed from wishlist', 'success');
      }
    });
  });

  // === Quantity Stepper ===
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const valueEl = this.parentElement.querySelector('.qty-value');
      let value = parseInt(valueEl.textContent);
      if (this.textContent === '+') {
        value = Math.min(value + 1, 10);
      } else {
        value = Math.max(value - 1, 1);
      }
      valueEl.textContent = value;
    });
  });

  // === Accordion ===
  document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', function() {
      const item = this.parentElement;
      const isActive = item.classList.contains('active');
      document.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));
      if (!isActive) {
        item.classList.add('active');
      }
    });
  });

  // === Intersection Observer for Animations ===
  const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('fade-in');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);

  document.querySelectorAll('.product-card, .category-card, .section-header').forEach(el => {
    observer.observe(el);
  });

  // === Product Detail Page Interactions ===
  if (document.querySelector('.pdp-layout')) {
    document.querySelectorAll('.pdp-thumb').forEach(thumb => {
      thumb.addEventListener('click', function() {
        document.querySelectorAll('.pdp-thumb').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const mainImg = document.getElementById('mainProductImage');
        if (mainImg) {
          mainImg.style.opacity = '0';
          setTimeout(() => {
            mainImg.src = this.dataset.image;
            mainImg.style.opacity = '1';
          }, 150);
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
        setTimeout(() => {
          this.classList.remove('loading');
          showToast('Added to cart successfully', 'success');
        }, 800);
      });
    }
  }

  // === Search Input Handler ===
  document.getElementById('searchInput')?.addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    if (query.length < 2) {
      searchResults.innerHTML = '';
      return;
    }

    fetch('/api/search.php?q=' + encodeURIComponent(query))
      .then(res => res.json())
      .then(data => {
        if (data.length === 0) {
          searchResults.innerHTML = '<div style="padding: 24px; text-align: center; color: var(--color-text-tertiary);">No products found</div>';
          return;
        }
        searchResults.innerHTML = data.map(product => `
          <a href="/product.php?slug=${product.slug}" class="search-result-item">
            <img src="${product.image}" alt="${product.name}" loading="lazy">
            <div class="search-result-info">
              <h4>${product.name}</h4>
              <p>₹${parseFloat(product.price).toLocaleString('en-IN')}</p>
            </div>
          </a>
        `).join('');
      })
      .catch(() => {
        searchResults.innerHTML = '<div style="padding: 24px; text-align: center; color: var(--color-text-tertiary);">Something went wrong</div>';
      });
  });
});
