<?php
$pageTitle = 'Create Post';
$pageStyles = ['assets/css/posts.css'];
$pageScripts = ['assets/js/posts.js'];

// Add session check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['full_name'])) {
    header('Location: index.php?action=login');
    exit;
}

// Initialize error variable
$error = $error ?? '';

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
        
        <div class="sidebar-section-label" style="margin-top: 24px;">Create</div>
        <a href="index.php?action=create_post" class="sidebar-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"></path>
            </svg>
            New Post
        </a>
    </aside>

    <!-- Main Content -->
    <main class="feed-main">
        <div class="create-post-container">
            <h1 class="page-title">Create Post</h1>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- REMOVED: enctype="multipart/form-data" - no longer needed -->
            <form class="create-post-form" method="POST" action="index.php?action=create_post">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? bin2hex(random_bytes(32))) ?>" />
                
                <!-- User Info -->
                <div class="form-user-info">
                    <div class="post-avatar">
                        <?php 
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
                        id="postContent"
                        class="create-post-textarea" 
                        placeholder="What's your hot take today?" 
                        required
                        maxlength="2000"
                        autofocus
                    ><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
                    <small class="char-counter"><span id="charCount">0</span>/2000</small>
                </div>

                <!-- REMOVED: Image Preview section -->

                <!-- Fandom Selection -->
                <div class="form-group">
                    <label class="form-label">Select Fandom</label>
                    <div class="fandom-selector">
                        <?php 
                        $fandoms = ['Anime', 'Games', 'Movies', 'Manga', 'K-Drama'];
                        $selectedFandom = $_POST['fandom_tag'] ?? 'Anime';
                        foreach ($fandoms as $fandom): 
                        ?>
                            <label class="fandom-radio">
                                <input 
                                    type="radio" 
                                    name="fandom_tag" 
                                    value="<?= $fandom ?>" 
                                    <?= $selectedFandom === $fandom ? 'checked' : '' ?>
                                    required
                                />
                                <span class="badge badge-<?= strtolower(str_replace(['-', ' '], ['', ''], $fandom)) ?>">
                                    <?= $fandom ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- REMOVED: Image Upload section completely -->

                <!-- Actions -->
                <div class="form-actions">
                    <a href="index.php?action=newsfeed" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary btn-large">Post</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Right Sidebar -->
    <aside class="sidebar-right">
        <div class="widget">
            <div class="widget-title">Tips</div>
            <div class="tip-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <p>Share your thoughts, theories, or hot takes with the community.</p>
            </div>
            <div class="tip-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <p>Select the right fandom tag to reach the right audience.</p>
            </div>
        </div>
    </aside>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>