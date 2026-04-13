<?php
// ============================================================
// views/profile.php — FanZone | My Profile view
// MVC: Data is passed in from your ProfileController.
// ============================================================

// -- Profile data → from your UserModel
// Expected keys: initials, name, handle, joined, bio,
//                posts (int), followers (int), following (int)
$profile = $profile ?? [];

// -- Fandom tags on profile → from your UserFandomModel
// Expected: flat array of strings e.g. ['Anime', 'Games', 'Movies']
$profileFandoms = $profileFandoms ?? [];

// -- User's own posts → from your PostModel
// Expected keys per row: id (int), fandom, time_ago, content,
//                        likes (int), comments (int), reposts (int)
$userPosts = $userPosts ?? [];

// -- Sidebar data → same models as other pages
$trending     = $trending    ?? [];
$fansToFollow = $fansToFollow ?? [];

// -- Fandom badge color map
$fandomColors = [
    'anime'  => ['bg' => 'rgba(168,85,247,0.18)', 'text' => '#c084fc'],
    'games'  => ['bg' => 'rgba(34,197,94,0.14)',  'text' => '#4ade80'],
    'movies' => ['bg' => 'rgba(251,146,60,0.14)', 'text' => '#fb923c'],
];

$userInitials = !empty($profile['initials']) ? htmlspecialchars($profile['initials']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FanZone – <?= htmlspecialchars($profile['name'] ?? 'My Profile') ?></title>
    <link rel="stylesheet" href="/public/css/base.css">
    <link rel="stylesheet" href="/public/css/profile.css">
</head>
<body>

<!-- ── Navbar ── -->
<nav class="navbar">
    <a href="/index.php" class="navbar-logo">FAN<span>ZONE</span></a>
    <div class="nav-links">
        <a href="/index.php">Home</a>
        <a href="/views/explore.php">Explore</a>
        <a href="/views/messages.php">Messages</a>
    </div>
    <div class="nav-right">
        <button class="icon-btn" title="Toggle theme">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="5"/>
                <line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/>
                <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                <line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/>
                <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
            </svg>
        </button>
        <a href="/views/profile.php" class="nav-avatar"><?= $userInitials ?></a>
    </div>
</nav>

<div class="page-body">

    <!-- ── Left Sidebar ── -->
    <aside class="sidebar">
        <div class="sb-label">Menu</div>
        <a href="/index.php" class="sb-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/>
                <path d="M9 21V12h6v9"/>
            </svg>
            Newsfeed
        </a>
        <a href="/views/profile.php" class="sb-link active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
            </svg>
            My Profile
        </a>
        <a href="/views/messages.php" class="sb-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
            Messages
        </a>
        <div class="sb-divider"></div>
        <div class="sb-label">Fandoms</div>
        <a href="/views/explore.php?category=anime" class="sb-link">
            <span class="sb-icon" style="background:rgba(168,85,247,0.18)">📺</span>
            Anime
        </a>
        <a href="/views/explore.php?category=games" class="sb-link">
            <span class="sb-icon" style="background:rgba(34,197,94,0.14)">🎮</span>
            Games
        </a>
        <a href="/views/explore.php?category=movies" class="sb-link">
            <span class="sb-icon" style="background:rgba(251,146,60,0.14)">🎬</span>
            Movies
        </a>
    </aside>

    <!-- ── Main Content ── -->
    <main class="main-content">

        <?php if (empty($profile)): ?>
        <div class="empty-state">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="8" r="4"/>
                <path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
            </svg>
            Profile not found.
        </div>
        <?php else: ?>

        <!-- Profile Card -->
        <div class="profile-card">
            <div class="profile-banner"></div>
            <div class="profile-body">
                <div class="profile-av-row">
                    <div class="profile-av"><?= htmlspecialchars($profile['initials']) ?></div>
                    <button class="btn-outline">Edit Profile</button>
                </div>
                <div class="profile-name"><?= htmlspecialchars($profile['name']) ?></div>
                <div class="profile-handle">
                    <?= htmlspecialchars($profile['handle']) ?>
                    &bull; Joined <?= htmlspecialchars($profile['joined']) ?>
                </div>
                <?php if (!empty($profile['bio'])): ?>
                <div class="profile-bio"><?= htmlspecialchars($profile['bio']) ?></div>
                <?php endif; ?>

                <?php if (!empty($profileFandoms)): ?>
                <div class="profile-fandom-tags">
                    <?php foreach ($profileFandoms as $fdom):
                        $key = strtolower($fdom);
                        $fc  = $fandomColors[$key] ?? ['bg' => 'rgba(255,255,255,0.08)', 'text' => '#aaa'];
                    ?>
                    <span class="profile-fdom-tag"
                          style="background:<?= $fc['bg'] ?>;color:<?= $fc['text'] ?>">
                        <?= htmlspecialchars($fdom) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="profile-stats">
                    <div class="stat-item">
                        <div class="stat-num"><?= number_format($profile['posts'] ?? 0) ?></div>
                        <div class="stat-label">Posts</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num"><?= number_format($profile['followers'] ?? 0) ?></div>
                        <div class="stat-label">Followers</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-num"><?= number_format($profile['following'] ?? 0) ?></div>
                        <div class="stat-label">Following</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="post-tabs">
            <button class="tab-btn active" onclick="switchTab(this,'posts')">Posts</button>
            <button class="tab-btn" onclick="switchTab(this,'media')">Media</button>
            <button class="tab-btn" onclick="switchTab(this,'likes')">Likes</button>
        </div>

        <!-- Posts Tab -->
        <div id="tab-posts">
            <?php if (empty($userPosts)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                No posts yet.
            </div>
            <?php else: ?>
                <?php foreach ($userPosts as $post):
                    $cat = strtolower($post['fandom'] ?? '');
                ?>
                <div class="post-card">
                    <div class="post-header">
                        <div class="post-av"><?= htmlspecialchars($profile['initials']) ?></div>
                        <div class="post-meta">
                            <div>
                                <span class="post-uname"><?= htmlspecialchars($profile['name']) ?></span>
                                <?php if (!empty($post['fandom'])): ?>
                                <span class="fandom-badge badge-<?= $cat ?>">
                                    <?= htmlspecialchars($post['fandom']) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="post-time"><?= htmlspecialchars($post['time_ago']) ?></div>
                        </div>
                    </div>
                    <div class="post-body"><?= htmlspecialchars($post['content']) ?></div>
                    <div class="post-actions">
                        <button
                            class="act-btn"
                            data-base="<?= (int)$post['likes'] ?>"
                            data-post-id="<?= (int)$post['id'] ?>"
                            onclick="toggleLike(this)"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                            </svg>
                            <span><?= number_format($post['likes']) ?></span>
                        </button>
                        <button class="act-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                            </svg>
                            <span><?= number_format($post['comments']) ?></span>
                        </button>
                        <button class="act-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="17 1 21 5 17 9"/>
                                <path d="M3 11V9a4 4 0 014-4h14"/>
                                <polyline points="7 23 3 19 7 15"/>
                                <path d="M21 13v2a4 4 0 01-4 4H3"/>
                            </svg>
                            <span><?= number_format($post['reposts']) ?></span>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Media Tab -->
        <div id="tab-media" style="display:none">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
                No media posts yet.
            </div>
        </div>

        <!-- Likes Tab -->
        <div id="tab-likes" style="display:none">
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>
                </svg>
                No liked posts yet.
            </div>
        </div>

        <?php endif; ?>
    </main>

    <!-- ── Right Sidebar ── -->
    <aside class="right-sidebar">

        <!-- Trending -->
        <div class="widget-card">
            <div class="widget-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                    <polyline points="17 6 23 6 23 12"/>
                </svg>
                Trending
            </div>
            <?php if (empty($trending)): ?>
                <p style="font-size:0.78rem;color:var(--text-muted)">No trending topics yet.</p>
            <?php else: ?>
                <?php foreach ($trending as $i => $t): ?>
                <div class="trend-row">
                    <span class="trend-n"><?= $i + 1 ?></span>
                    <span class="trend-tag"><?= htmlspecialchars($t['tag']) ?></span>
                    <span class="trend-posts"><?= number_format($t['posts']) ?> posts</span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Fans to Follow -->
        <div class="widget-card">
            <div class="widget-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                </svg>
                Fans to Follow
            </div>
            <?php if (empty($fansToFollow)): ?>
                <p style="font-size:0.78rem;color:var(--text-muted)">No suggestions right now.</p>
            <?php else: ?>
                <?php foreach ($fansToFollow as $fan): ?>
                <div class="fan-row">
                    <div class="fan-av" style="background:<?= htmlspecialchars($fan['avatar_color']) ?>">
                        <?= htmlspecialchars($fan['initials']) ?>
                    </div>
                    <div class="fan-info">
                        <div class="fan-name"><?= htmlspecialchars($fan['name']) ?></div>
                        <div class="fan-handle"><?= htmlspecialchars($fan['handle']) ?></div>
                    </div>
                    <button
                        class="btn-follow"
                        data-id="<?= (int)$fan['id'] ?>"
                        onclick="toggleFollow(this)"
                    >Follow</button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </aside>
</div>

<script src="/public/js/profile.js"></script>
</body>
</html>
