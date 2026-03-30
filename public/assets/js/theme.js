(function () {
  const saved = localStorage.getItem('fz-theme') || 'dark';
  document.documentElement.setAttribute('data-theme', saved);
  updateIcon(saved);
})();

function toggleTheme() {
  const html = document.documentElement;
  const current = html.getAttribute('data-theme');
  const next = current === 'dark' ? 'light' : 'dark';
  
  html.setAttribute('data-theme', next);
  localStorage.setItem('fz-theme', next);
  updateIcon(next);
  
  // Update session via AJAX
  fetch('index.php?action=set_theme&theme=' + next, { method: 'POST' });
}

function updateIcon(theme) {
  // Update dropdown icon and text
  const iconSpan = document.getElementById('dropdown-theme-icon');
  const textSpan = document.getElementById('dropdown-theme-text');
  
  if (iconSpan) iconSpan.textContent = theme === 'dark' ? '☀️' : '🌙';
  if (textSpan) textSpan.textContent = theme === 'dark' ? 'Light Mode' : 'Dark Mode';
  
  // Keep old button updated if it exists
  const oldBtn = document.getElementById('ttoggle');
  if (oldBtn) oldBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
}