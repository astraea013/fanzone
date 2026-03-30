<?php
if (!defined('BASE_PATH') && basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    exit('No direct script access allowed');
}
$pageScripts = $pageScripts ?? [];
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/index.php';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
         . '://' . $_SERVER['HTTP_HOST']
         . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>
</main>

<footer class="site-footer">
  <div class="footer-content">
    <div class="footer-brand">
      <div class="footer-logo">FAN<span>ZONE</span></div>
      <p class="footer-tagline">Where fandoms unite</p>
    </div>
    <div class="footer-links">
      <div class="footer-section">
        <h4>Platform</h4>
        <a href="<?= $base ?>?action=newsfeed">Newsfeed</a>
        <a href="<?= $base ?>?action=profile">Profile</a>
      </div>
      <div class="footer-section">
        <h4>Fandoms</h4>
        <a>Anime</a>
        <a>Games</a>
        <a>Movies</a>
        <a>Manga</a>
      </div>
      <div class="footer-section">
        <h4>Account</h4>
        <a href="<?= $base ?>?action=edit_profile">Settings</a>
        <a href="<?= $base ?>?action=logout">Logout</a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; <?= date('Y') ?> FanZone. All rights reserved.</p>
  </div>
</footer>

<script>
function toggleNavDropdown() {
  const menu   = document.getElementById('dropdown-menu');
  const isHide = menu.getAttribute('aria-hidden') !== 'false';
  menu.setAttribute('aria-hidden', isHide ? 'false' : 'true');
  menu.classList.toggle('show', isHide);
  if (isHide) setTimeout(() => document.addEventListener('click', closeNavDropdown), 0);
}
function closeNavDropdown(e) {
  const menu   = document.getElementById('dropdown-menu');
  const avatar = document.querySelector('.nav-avatar');
  if (menu && avatar && !menu.contains(e.target) && !avatar.contains(e.target)) {
    menu.setAttribute('aria-hidden', 'true');
    menu.classList.remove('show');
    document.removeEventListener('click', closeNavDropdown);
  }
}
function showToast(message, type = 'success', duration = 3000) {
  const c = document.getElementById('toast-container');
  const t = document.createElement('div');
  t.className   = `toast ${type}`;
  t.textContent = message;
  c.appendChild(t);
  requestAnimationFrame(() => t.classList.add('show'));
  setTimeout(() => { t.classList.remove('show'); setTimeout(() => t.remove(), 300); }, duration);
}
</script>

<?php foreach ($pageScripts as $script): ?>
  <script src="<?= $baseUrl . htmlspecialchars($script) ?>"></script>
<?php endforeach; ?>

</body>
</html>