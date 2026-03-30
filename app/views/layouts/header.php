<?php
if (!defined('BASE_PATH') && basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    exit('No direct script access allowed');
}
$pageStyles  = $pageStyles  ?? [];
$pageScripts = $pageScripts ?? [];

// Build correct base URL for assets
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
         . '://' . $_SERVER['HTTP_HOST']
         . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/';
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | FanZone' : 'FanZone' ?></title>
  <link rel="icon" type="image/jpeg" href="<?= $baseUrl ?>assets/fanzone.jpg" />
  <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css" />
  <?php foreach ($pageStyles as $style): ?>
    <link rel="stylesheet" href="<?= $baseUrl . htmlspecialchars($style) ?>" />
  <?php endforeach; ?>
  <script src="<?= $baseUrl ?>assets/js/theme.js"></script>
</head>
<body>
<div id="toast-container" class="toast-container"></div>