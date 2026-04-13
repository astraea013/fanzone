<?php
// ============================================================
// views/messages.php — FanZone | Messages view
// MVC: Data is passed in from your MessagesController.
// ============================================================

// -- Current logged-in user (from session)
$currentUser  = $_SESSION['user'] ?? [];
$userInitials = strtoupper(substr($currentUser['username'] ?? '', 0, 2));

// -- Conversation list → from your MessageModel
// Expected keys per row: id (int), name, initials, avatar_color,
//                        preview, time_ago, unread_count (int),
//                        is_online (bool), fandom
$conversations = $conversations ?? [];

// -- Active conversation ID (from query string, default to first)
$activeId   = isset($_GET['conv']) ? (int)$_GET['conv'] : ($conversations[0]['id'] ?? 0);
$activeConv = null;
foreach ($conversations as $c) {
    if ($c['id'] === $activeId) { $activeConv = $c; break; }
}
if (!$activeConv && !empty($conversations)) $activeConv = $conversations[0];

// -- Messages for active conversation → from your MessageModel
// Expected keys per row: from ('me' | 'other'), text, timestamp
$messages = $messages ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FanZone – Messages</title>
    <link rel="stylesheet" href="/public/css/base.css">
    <link rel="stylesheet" href="/public/css/messages.css">
</head>
<body>

<!-- ── Navbar ── -->
<nav class="navbar">
    <a href="/index.php" class="navbar-logo">FAN<span>ZONE</span></a>
    <div class="nav-links">
        <a href="/index.php">Home</a>
        <a href="/views/explore.php">Explore</a>
        <a href="/views/messages.php" class="active">Messages</a>
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

<div class="messages-layout">

    <!-- ── DM List ── -->
    <div class="dm-list">
        <div class="dm-list-header">
            <h2>Messages</h2>
            <button class="dm-new-btn" title="New message">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
            </button>
        </div>

        <div class="dm-search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" class="dm-search" placeholder="Search DMs" />
        </div>

        <div class="dm-items">
            <?php if (empty($conversations)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
                No conversations yet.
            </div>
            <?php else: ?>
                <?php foreach ($conversations as $conv): ?>
                <a
                    href="messages.php?conv=<?= (int)$conv['id'] ?>"
                    class="dm-item <?= $conv['id'] === $activeId ? 'active' : '' ?>"
                >
                    <div class="dm-av-wrap">
                        <div class="dm-av" style="background:<?= htmlspecialchars($conv['avatar_color']) ?>">
                            <?= htmlspecialchars($conv['initials']) ?>
                        </div>
                        <?php if (!empty($conv['is_online'])): ?>
                        <div class="online-dot"></div>
                        <?php endif; ?>
                    </div>
                    <div class="dm-meta">
                        <div class="dm-meta-top">
                            <span class="dm-name"><?= htmlspecialchars($conv['name']) ?></span>
                            <span class="dm-time"><?= htmlspecialchars($conv['time_ago']) ?></span>
                        </div>
                        <div class="dm-preview-row">
                            <span class="dm-preview"><?= htmlspecialchars($conv['preview']) ?></span>
                            <?php if (!empty($conv['unread_count'])): ?>
                            <span class="unread-badge"><?= (int)$conv['unread_count'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ── Chat Panel ── -->
    <div class="chat-panel">

        <?php if (!$activeConv): ?>
        <div class="chat-no-selection">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
            Select a conversation to start chatting.
        </div>

        <?php else: ?>

        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-av" style="background:<?= htmlspecialchars($activeConv['avatar_color']) ?>">
                <?= htmlspecialchars($activeConv['initials']) ?>
                <?php if (!empty($activeConv['is_online'])): ?>
                <div class="online-dot" style="border-color:var(--bg-secondary)"></div>
                <?php endif; ?>
            </div>
            <div class="chat-header-info">
                <div class="chat-header-name"><?= htmlspecialchars($activeConv['name']) ?></div>
                <div class="chat-badges">
                    <?php if (!empty($activeConv['fandom'])): ?>
                    <span class="fandom-badge badge-<?= strtolower(htmlspecialchars($activeConv['fandom'])) ?>">
                        <?= htmlspecialchars($activeConv['fandom']) ?>
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($activeConv['is_online'])): ?>
                    <span class="online-label">Online</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="chat-messages" id="chatMessages">
            <?php if (empty($messages)): ?>
            <div class="chat-no-selection">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
                </svg>
                No messages yet. Say hello!
            </div>
            <?php else: ?>
            <div class="chat-date">Today &bull; <?= date('F j, Y') ?></div>
            <?php foreach ($messages as $msg): ?>
            <div class="msg-row <?= $msg['from'] === 'me' ? 'me' : 'other' ?>">
                <?php if ($msg['from'] !== 'me'): ?>
                <div class="msg-av" style="background:<?= htmlspecialchars($activeConv['avatar_color']) ?>">
                    <?= htmlspecialchars($activeConv['initials']) ?>
                </div>
                <?php endif; ?>
                <div class="msg-col">
                    <div class="bubble"><?= htmlspecialchars($msg['text']) ?></div>
                    <div class="msg-ts"><?= htmlspecialchars($msg['timestamp']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Input Bar -->
        <div class="chat-input-bar">
            <button class="icon-action" title="Attach file">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                </svg>
            </button>
            <input
                type="text"
                class="chat-input"
                id="msgInput"
                placeholder="Message"
                onkeydown="handleMsgEnter(event)"
            />
            <button class="icon-action" title="Emoji">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                    <line x1="9" y1="9" x2="9.01" y2="9"/>
                    <line x1="15" y1="9" x2="15.01" y2="9"/>
                </svg>
            </button>
            <button class="send-btn" onclick="sendMessage()" title="Send">
                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                    <line x1="22" y1="2" x2="11" y2="13"/>
                    <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                </svg>
            </button>
        </div>

        <?php endif; ?>
    </div>

</div>

<script src="/public/js/messages.js"></script>
</body>
</html>
