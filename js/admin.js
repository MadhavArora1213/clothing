document.addEventListener('DOMContentLoaded', () => {
  // Sidebar toggle
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('adminSidebar');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (sidebar && sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== sidebarToggle) {
      sidebar.classList.remove('open');
    }
  });

  // Form enhancements
  document.querySelectorAll('.form-group input[type="checkbox"]').forEach(cb => {
    cb.addEventListener('change', function() {
      this.nextElementSibling?.remove();
    });
  });

  // Confirm delete actions
  document.querySelectorAll('a[href*="delete.php"]').forEach(link => {
    if (!link.getAttribute('onclick')) {
      link.setAttribute('onclick', 'return confirm("Are you sure?")');
    }
  });
});
