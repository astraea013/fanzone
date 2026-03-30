<?php
if (!defined('BASE_PATH') && basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    exit('No direct script access allowed');
}
$currentAction = $_GET['action'] ?? 'newsfeed';
$username      = $_SESSION['username']      ?? '';
$fullName      = $_SESSION['full_name']     ?? '';
$profileImage  = $_SESSION['profile_image'] ?? '';
$userInitials  = strtoupper(substr($username, 0, 2));

// Get current theme for icon
$currentTheme = $_SESSION['theme'] ?? 'dark';
$themeIcon = $currentTheme === 'dark' ? '☀️' : '🌙';
$themeText = $currentTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
?>

<!-- REMOVED: Fixed theme toggle button -->

<nav class="navbar">
  <a href="index.php?action=newsfeed" class="logo">FAN<span>ZONE</span></a>

  <div class="navbar-links">
    <a href="index.php?action=newsfeed"
       class="<?= $currentAction === 'newsfeed' ? 'active' : '' ?>">Newsfeed</a>
    <a href="index.php?action=profile"
       class="<?= in_array($currentAction, ['profile','edit_profile']) ? 'active' : '' ?>">My Profile</a>
  </div>

  <div class="navbar-right">
    <form class="nav-search" action="index.php" method="GET">
      <input type="hidden" name="action" value="search" />
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
           stroke="currentColor" stroke-width="2">
        <circle cx="11" cy="11" r="8"></circle>
        <path d="m21 21-4.35-4.35"></path>
      </svg>
      <input type="text" name="q" placeholder="Search fans, posts..."
             value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" autocomplete="off" />
    </form>

    <div class="nav-profile-dropdown" id="nav-dropdown">
      <button class="nav-avatar" onclick="toggleNavDropdown()">
        <?php if (!empty($profileImage) && $profileImage !== 'default.png'): ?>
          <img src="<?= htmlspecialchars($profileImage) ?>"
               alt="<?= htmlspecialchars($username) ?>" />
        <?php else: ?>
          <?= $userInitials ?>
        <?php endif; ?>
      </button>
      <div class="dropdown-menu" id="dropdown-menu" aria-hidden="true">
        <div class="dropdown-header">
          <strong><?= htmlspecialchars($fullName) ?></strong>
          <span>@<?= htmlspecialchars($username) ?></span>
        </div>
        <div class="dropdown-divider"></div>
        <a href="index.php?action=profile">Profile</a>
        <a href="index.php?action=edit_profile">Edit Profile</a>
        
        <!-- THEME TOGGLE ADDED INSIDE DROPDOWN -->
        <button class="dropdown-theme-toggle" onclick="toggleTheme()">
          <span id="dropdown-theme-icon"><?= $themeIcon ?></span>
          <span id="dropdown-theme-text"><?= $themeText ?></span>
        </button>
        
        <div class="dropdown-divider"></div>
        <a href="index.php?action=logout" class="dropdown-logout">Logout</a>
      </div>
    </div>
  </div>
</nav>

<main id="main-content">