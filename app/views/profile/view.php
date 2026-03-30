<?php
// app/views/profile/view.php
$pageTitle = htmlspecialchars($user['full_name']);
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
        <a href="index.php?action=profile" class="sidebar-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            My Profile
        </a>
    </aside>

    <!-- Main Profile Content -->
    <main class="feed-main">
        <!-- Profile Header -->
        <div class="profile-cover"></div>
        
        <div class="profile-info-row">
            <div class="profile-big-avatar">
                <?php if (!empty($user['profile_image']) && $user['profile_image'] !== 'default.png'): ?>
                    <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="<?= htmlspecialchars($user['username']) ?>" />
                <?php else: ?>
                    <?= strtoupper(substr($user['username'], 0, 2)) ?>
                <?php endif; ?>
            </div>
            
            <div class="profile-details">
                <div class="profile-name"><?= htmlspecialchars($user['full_name']) ?></div>
                <div class="profile-handle">@<?= htmlspecialchars($user['username']) ?></div>
                <?php if (!empty($user['bio'])): ?>
                    <div class="profile-bio"><?= htmlspecialchars($user['bio']) ?></div>
                <?php endif; ?>
            </div>
            
            <?php if ($isOwnProfile): ?>
                <button class="btn-edit-profile" onclick="location.href='index.php?action=edit_profile'">
                    Edit Profile
                </button>
            <?php endif; ?>
        </div>

        <!-- Profile Stats (Posts Only) -->
        <div class="profile-stats">
            <div class="stat-box">
                <div class="stat-num"><?= $stats['posts'] ?></div>
                <div class="stat-label">Posts</div>
            </div>
        </div>

        <!-- User Fandoms -->
        <?php if (!empty($fandoms)): ?>
        <div class="profile-fandoms">
            <?php foreach ($fandoms as $fandom): ?>
                <span class="badge badge-<?= strtolower(str_replace(['-', ' '], ['', ''], $fandom)) ?>">
                    <?= htmlspecialchars($fandom) ?>
                </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- User Posts -->
        <div class="profile-posts-section">
            <h3 class="section-title">Posts</h3>
            
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <p>No posts yet.</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-card" data-post-id="<?= $post['id'] ?>">
                        <div class="post-header">
                            <div class="post-avatar">
                                <?php if (!empty($post['user_avatar']) && $post['user_avatar'] !== 'default.png'): ?>
                                    <img src="<?= htmlspecialchars($post['user_avatar']) ?>" alt="<?= htmlspecialchars($post['username']) ?>" />
                                <?php else: ?>
                                    <?= strtoupper(substr($post['username'], 0, 2)) ?>
                                <?php endif; ?>
                            </div>
                            <div class="post-meta">
                                <div class="post-username"><?= htmlspecialchars($post['full_name']) ?></div>
                                <div class="post-time"><?= timeAgo($post['created_at']) ?></div>
                            </div>
                            <span class="badge badge-<?= strtolower(str_replace(['-', ' '], ['', ''], $post['fandom_tag'])) ?>">
                                <?= htmlspecialchars($post['fandom_tag']) ?>
                            </span>
                        </div>
                        
                        <div class="post-body"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
                        
                        <?php if (!empty($post['image'])): ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post image" class="post-image" />
                        <?php endif; ?>
                        
                        <div class="post-actions">
                            <button class="action-btn <?= $post['user_liked'] ? 'liked' : '' ?>" onclick="toggleLike(this, <?= $post['id'] ?>)">
                                <svg viewBox="0 0 24 24" fill="<?= $post['user_liked'] ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                                <span class="like-count"><?= $post['like_count'] ?></span>
                            </button>
                            <button class="action-btn" onclick="toggleComments(<?= $post['id'] ?>)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <span class="comment-count"><?= $post['comment_count'] ?></span>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Right Sidebar -->
    <aside class="sidebar-right">
        <div class="widget">
            <div class="widget-title">About</div>
            <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.6;">
                Member since <?= date('F Y', strtotime($user['created_at'])) ?>
            </p>
        </div>
    </aside>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>