<?php
$pageTitle = 'Edit Post';
$pageStyles = ['assets/css/posts.css'];
$pageScripts = ['assets/js/posts.js'];

// BUG 1: Missing session check - user might not be logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BUG 2: Missing BASE_PATH definition check
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 3)); // Adjust based on your structure
}

// BUG 3: $post validation should check if it's an array with required keys
if (!isset($post) || !is_array($post) || empty($post['id'])) {
    header('Location: index.php?action=newsfeed');
    exit;
}

// BUG 4: $error should be initialized before the check
$error = '';

// BUG 5: Check if required session variables exist
if (!isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['full_name'])) {
    header('Location: index.php?action=login');
    exit;
}

require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/navbar.php';
?>

<div class="page-layout">
    <!-- Left Sidebar -->
    <aside class="sidebar-left">
        <div class="sidebar-section-label">Menu</div>
        <a href="index.php?action=newsfeed" class="sidebar-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Newsfeed
        </a>
        <a href="index.php?action=profile" class="sidebar-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            My Profile
        </a>
    </aside>

    <!-- Main Content -->
    <main class="feed-main">
        <div class="create-post-container">
            <h1 class="page-title">Edit Post</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- BUG 6: Missing CSRF protection -->
            <form class="create-post-form" method="POST" action="index.php?action=edit_post&id=<?= (int)$post['id'] ?>">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>" />
                
                <!-- BUG 7: Duplicate post_id - already in URL, but also good to have in form for security -->
                <input type="hidden" name="post_id" value="<?= (int)$post['id'] ?>" />

                <!-- User Info -->
                <div class="form-user-info">
                    <div class="post-avatar">
                        <?php 
                        // BUG 8: Missing null check for session variables
                        $profileImage = $_SESSION['profile_image'] ?? '';
                        $username = $_SESSION['username'] ?? '';
                        if (!empty($profileImage) && $profileImage !== 'default.png'): 
                        ?>
                            <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile" />
                        <?php else: ?>
                            <?= strtoupper(substr($username, 0, 2)) ?>
                        <?php endif; ?>
                    </div>
                    <div class="form-user-meta">
                        <div class="form-username"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Unknown') ?></div>
                        <div class="form-handle">@<?= htmlspecialchars($username) ?></div>
                    </div>
                </div>

                <!-- Content Input -->
                <div class="form-group">
                    <textarea 
                        name="content" 
                        class="create-post-textarea" 
                        placeholder="What's your hot take today?" 
                        required
                        maxlength="2000" <!-- BUG 9: Missing length limit -->
                    ><?= htmlspecialchars($post['content'] ?? '') ?></textarea>
                    <!-- BUG 10: Missing character counter -->
                    <small class="char-counter"><span id="charCount">0</span>/2000</small>
                </div>

                <!-- Current Image (if exists) -->
                <?php if (!empty($post['image'])): ?>
                <div class="current-image-container">
                    <label class="form-label">Current Image</label>
                    <div class="current-image-wrapper">
                        <!-- BUG 11: Missing path validation for image -->
                        <img src="<?= htmlspecialchars($post['image']) ?>" alt="Current post image" onerror="this.style.display='none'" />
                        <label class="remove-image-checkbox">
                            <input type="checkbox" name="remove_image" value="1" />
                            <span>Remove image</span>
                        </label>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Fandom Display (Read-only) -->
                <div class="form-group">
                    <label class="form-label">Fandom</label>
                    <div class="fandom-display">
                        <?php 
                        // BUG 12: fandom_tag might not exist
                        $fandomTag = $post['fandom_tag'] ?? 'Anime';
                        $badgeClass = strtolower(str_replace(['-', ' '], ['', ''], $fandomTag));
                        ?>
                        <span class="badge badge-<?= htmlspecialchars($badgeClass) ?>">
                            <?= htmlspecialchars($fandomTag) ?>
                        </span>
                        <input type="hidden" name="fandom_tag" value="<?= htmlspecialchars($fandomTag) ?>" />
                        <small style="color: var(--text-muted); margin-left: 8px;">Fandom cannot be changed</small>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="index.php?action=newsfeed" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary btn-large">Save Changes</button>
                </div>
            </form>

            <!-- Delete Post Section -->
            <div class="delete-post-section">
                <h3>Danger Zone</h3>
                <p>Once you delete a post, it cannot be recovered.</p>
                <!-- BUG 13: confirmDelete should pass post ID properly -->
                <button type="button" class="btn-danger" onclick="return confirmDelete(<?= (int)$post['id'] ?>)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Delete Post
                </button>
            </div>
        </div>
    </main>

    <!-- Right Sidebar -->
    <aside class="sidebar-right">
        <div class="widget">
            <div class="widget-title">Post Info</div>
            <div class="post-meta-info">
                <!-- BUG 14: created_at might not exist, and date format should handle errors -->
                <?php 
                $createdAt = $post['created_at'] ?? null;
                if ($createdAt): 
                ?>
                    <p>Posted on <?= date('F j, Y \a\t g:i A', strtotime($createdAt)) ?></p>
                <?php endif; ?>
                
                <?php 
                // BUG 15: updated_at check should use strict comparison
                $updatedAt = $post['updated_at'] ?? null;
                if ($updatedAt && $createdAt && $updatedAt !== $createdAt): 
                ?>
                    <p>Last edited <?= date('F j, Y \a\t g:i A', strtotime($updatedAt)) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </aside>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>