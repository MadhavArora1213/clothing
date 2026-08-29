document.addEventListener('DOMContentLoaded', () => {
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('adminSidebar');
  const overlay = document.getElementById('sidebarOverlay');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      sidebar.classList.toggle('open');
      if (overlay) overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    });
  }

  if (overlay) {
    overlay.addEventListener('click', () => {
      sidebar.classList.remove('open');
      overlay.style.display = 'none';
    });
  }

  document.addEventListener('click', (e) => {
    if (sidebar && sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== sidebarToggle && !sidebarToggle.contains(e.target)) {
      sidebar.classList.remove('open');
      if (overlay) overlay.style.display = 'none';
    }
  });

  document.querySelectorAll('.form-group input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', function() {
      this.nextElementSibling?.remove();
    });
  });

  document.querySelectorAll('a[href*="delete.php"]').forEach(link => {
    if (!link.getAttribute('onclick')) {
      link.setAttribute('onclick', 'return confirm("Are you sure?")');
    }
  });
});
