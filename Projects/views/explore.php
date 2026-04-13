<?php
// ============================================================
// views/explore.php — FanZone | Explore Fandoms view
// MVC: Data is passed in from your ExploreController.
// ============================================================

// -- Current logged-in user (from session, set by your Auth controller)
$currentUser  = $_SESSION['user'] ?? [];
$userInitials = strtoupper(substr($currentUser['username'] ?? '', 0, 2));

// -- Trending hashtags → from your TrendingModel
// Expected keys per row: tag (string), posts (int)
$trending = $trending ?? [];

// -- Fans to follow → from your UserModel
// Expected keys per row: id (int), initials, name, handle, avatar_color
$fansToFollow = $fansToFollow ?? [];

// -- Fandom cards → from your FandomModel
// Expected keys per row: id (int), name, category, members (int), is_hot (bool)
$fandoms = $fandoms ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FanZone – Explore</title>
    <link rel="stylesheet" href="/public/css/base.css">
    <link rel="stylesheet" href="/public/css/explore.css">
</head>
<body>

<!-- ── Navbar ── -->
<nav class="navbar">
    <a href="/index.php" class="navbar-logo">FAN<span>ZONE</span></a>
    <div class="nav-links">
        <a href="/index.php">Home</a>
        <a href="/views/explore.php" class="active">Explore</a>
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
        <a href="/views/profile.php" class="nav-avatar"><?= htmlspecialchars($userInitials) ?></a>
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
        <a href="/views/profile.php" class="sb-link">
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
        <a href="?category=anime" class="sb-link">
            <span class="sb-icon" style="background:rgba(168,85,247,0.18)">📺</span>
            Anime
        </a>
        <a href="?category=games" class="sb-link">
            <span class="sb-icon" style="background:rgba(34,197,94,0.14)">🎮</span>
            Games
        </a>
        <a href="?category=movies" class="sb-link">
            <span class="sb-icon" style="background:rgba(251,146,60,0.14)">🎬</span>
            Movies
        </a>
    </aside>

    <!-- ── Main Content ── -->
    <main class="main-content">

        <!-- Search -->
        <div class="card">
            <div class="page-title">Explore Fandoms</div>
            <div class="search-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input
                    type="text"
                    class="search-input"
                    id="searchInput"
                    placeholder="Search for anime, games, movies..."
                    oninput="filterFandoms(this.value)"
                />
            </div>
        </div>

        <!-- Fandoms Grid -->
        <div class="card">
            <div class="section-title">🔥 Trending Fandoms</div>

            <?php if (empty($fandoms)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                No fandoms available yet.
            </div>
            <?php else: ?>
            <div class="fandoms-grid" id="fandomsGrid">
                <?php foreach ($fandoms as $f):
                    $cat = strtolower(htmlspecialchars($f['category']));
                ?>
                <div class="fandom-card" data-name="<?= strtolower(htmlspecialchars($f['name'])) ?>">
                    <div class="fdom-top">
                        <div>
                            <div class="fdom-name"><?= htmlspecialchars($f['name']) ?></div>
                            <span class="fdom-badge badge-<?= $cat ?>"><?= ucfirst($cat) ?></span>
                        </div>
                        <?php if (!empty($f['is_hot'])): ?>
                        <div class="hot-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;">
                                <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                                <polyline points="17 6 23 6 23 12"/>
                            </svg>
                            Hot
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="fdom-bottom">
                        <div class="members-txt">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                            </svg>
                            <?= number_format($f['members']) ?> members
                        </div>
                        <button
                            class="btn-join"
                            data-id="<?= (int)$f['id'] ?>"
                            onclick="toggleJoin(this)"
                        >Join</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

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

<script src="/public/js/explore.js"></script>
</body>
</html>
