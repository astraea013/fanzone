<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FanZone – Login</title>
  <link rel="icon" type="image/jpeg" href="assets/fanzone.jpg" />
  <link rel="stylesheet" href="assets/css/form.css" />
</head>
<body>

  <div class="auth-container">

    <!-- LEFT PANEL -->
    <div class="auth-brand">
      <div class="brand-logo">FanZone</div>
      <p class="brand-tagline">
        Join the ultimate fandom universe. Connect with fellow fans,
        discover new content, and dive deep into your favorite worlds.
      </p>
      <ul class="brand-features">
        <li>Exclusive fan content & discussions</li>
        <li>Real-time event updates</li>
        <li>Community-driven recommendations</li>
        <li>Personalized fan experiences</li>
      </ul>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-forms">

      <!-- LOGIN PANEL -->
      <div id="login-panel" class="auth-panel">
        
        <div class="form-header">
          <h1 class="form-title">Welcome Back!</h1>
          <p class="form-subtitle">Log back in and catch up on everything happening in the fandom universe.</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login">
          <div class="form-group">
            <input type="text" name="username" class="form-input" placeholder="Username"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required />
          </div>
          
          <div class="form-group">
            <input type="password" name="password" class="form-input" placeholder="Password" required />
          </div>
          
          <button type="submit" class="btn-submit">Login</button>
        </form>

        <p class="form-switch">
          New to FanZone? <a data-switch="register">Join the community</a>
        </p>

      </div>

      <!-- REGISTER PANEL -->
      <div id="register-panel" class="auth-panel">

        <div class="form-header">
          <h1 class="form-title">Join the Fandom!</h1>
          <p class="form-subtitle">Create your account and start exploring the universe.</p>
        </div>

        <?php if (!empty($error)): ?>
          <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
          <div class="alert alert-success">
            <?= htmlspecialchars($success) ?> —
            <a href="index.php?action=login" style="color: inherit; text-decoration: underline;">Login →</a>
          </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=register">
          <div class="form-group">
            <input type="text" name="full_name" class="form-input" placeholder="Full Name"
              value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required />
          </div>
          
          <div class="form-group">
            <input type="text" name="username" class="form-input" placeholder="Username"
              value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required minlength="3" />
          </div>
          
          <div class="form-group">
            <input type="email" name="email" class="form-input" placeholder="Email Address"
              value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
          </div>
          
          <div class="form-group">
            <input type="password" name="password" class="form-input" placeholder="Password"
              required minlength="6" />
            <div class="password-strength">
              <div class="strength-bar"></div>
            </div>
            <span class="strength-text"></span>
          </div>
          
          <div class="form-group">
            <input type="password" name="confirm_password" class="form-input" placeholder="Confirm Password" required />
          </div>

          <div class="fandom-section">
            <span class="fandom-label">Pick your fandoms (at least one)</span>
            <div class="fandom-grid">
              <?php foreach (['Anime','Games','Movies','Manga','K-Drama'] as $f): ?>
              <label class="fandom-tag">
                <input type="checkbox" name="fandoms[]" value="<?= $f ?>"
                  <?= in_array($f, $_POST['fandoms'] ?? []) ? 'checked' : '' ?> />
                <span><?= $f ?></span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>

          <button type="submit" class="btn-submit">Create Account</button>
        </form>

        <p class="form-switch">
          Already a fan? <a data-switch="login">Log in here</a>
        </p>

      </div>

    </div>
  </div>

  <script src="assets/js/form.js"></script>

</body>
</html>