<?php
$pageTitle   = 'Newsfeed';
$pageStyles  = ['assets/css/posts.css'];
$pageScripts = ['assets/js/posts.js'];
require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/navbar.php';

?>

<div class="page-layout">

  <!-- Left Sidebar -->
  <aside class="sidebar-left">
    <div class="sidebar-label">Menu</div>
    <a href="index.php?action=newsfeed" class="sidebar-item active">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
        <polyline points="9 22 9 12 15 12 15 22"></polyline>
      </svg>
      Newsfeed
    </a>
    <a href="index.php?action=profile" class="sidebar-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
        <circle cx="12" cy="7" r="4"></circle>
      </svg>
      My Profile
    </a>
    <a href="index.php?action=create_post" class="sidebar-item">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16">
        <path d="M12 5v14M5 12h14"></path>
      </svg>
      New Post
    </a>

    <?php if (!empty($userFandoms)): ?>
    <div class="sidebar-label" style="margin-top:20px;">Your Fandoms</div>
    <?php foreach ($userFandoms as $fandom): ?>
      <div class="sidebar-item">
        <span class="badge badge-<?= strtolower(str_replace(['-',' '],['',''],$fandom)) ?>">
          <?= htmlspecialchars($fandom) ?>
        </span>
      </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </aside>

  <!-- Main Feed -->
  <div class="feed-main">

    <!-- Quick Create Post -->
    <div class="create-post">
      <div class="create-post-top">
        <div class="post-avatar">
          <?php if (!empty($_SESSION['profile_image']) && $_SESSION['profile_image'] !== 'default.png'): ?>
            <img src="<?= htmlspecialchars($_SESSION['profile_image']) ?>" alt="Profile" />
          <?php else: ?>
            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 2)) ?>
          <?php endif; ?>
        </div>
        <textarea class="create-post-input" id="postContent"
                  placeholder="What's your hot take today?" rows="2"></textarea>
      </div>
      <div class="create-post-actions">
        <?php foreach (['Anime','Games','Movies','Manga','K-Drama'] as $f): ?>
          <button type="button" class="tag-btn <?= $f === 'Anime' ? 'active' : '' ?>"
                  data-fandom="<?= $f ?>"><?= $f ?></button>
        <?php endforeach; ?>
        <button type="button" class="btn-post" onclick="submitPost()">Post</button>
      </div>
    </div>

    <!-- Posts Feed -->
    <div id="posts-container">
      <?php if (empty($posts)): ?>
        <div class="empty-state">
          <p>No posts yet. Be the first to share something!</p>
          <a href="index.php?action=create_post" class="btn-primary">Create Post</a>
        </div>
      <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <article class="post-card" data-post-id="<?= $post['id'] ?>">
          <div class="post-header">
            <div class="post-avatar">
              <?php if (!empty($post['user_avatar']) && $post['user_avatar'] !== 'default.png'): ?>
                <img src="<?= htmlspecialchars($post['user_avatar']) ?>"
                     alt="<?= htmlspecialchars($post['username']) ?>" />
              <?php else: ?>
                <?= strtoupper(substr($post['username'], 0, 2)) ?>
              <?php endif; ?>
            </div>
            <div class="post-meta">
              <div class="post-username"><?= htmlspecialchars($post['full_name']) ?></div>
              <div class="post-time"><?= timeAgo($post['created_at']) ?></div>
            </div>
            <?php if (!empty($post['fandom_tag'])): ?>
            <span class="badge badge-<?= strtolower(str_replace(['-',' '],['',''],$post['fandom_tag'])) ?>">
              <?= htmlspecialchars($post['fandom_tag']) ?>
            </span>
            <?php endif; ?>
            <?php if ($post['user_id'] == $_SESSION['user_id']): ?>
            <div class="post-owner-actions">
              <a href="index.php?action=edit_post&id=<?= $post['id'] ?>" class="action-link">Edit</a>
              <form method="POST" action="index.php?action=delete_post" style="display:inline;"
                    onsubmit="return confirm('Delete this post?')">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>" />
                <button type="submit" class="action-link danger">Delete</button>
              </form>
            </div>
            <?php endif; ?>
          </div>

          <div class="post-body"><?= nl2br(htmlspecialchars($post['content'])) ?></div>

          <?php if (!empty($post['image'])): ?>
            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Post image" class="post-image" />
          <?php endif; ?>

          <div class="post-actions">
            <button class="action-btn <?= $post['user_liked'] ? 'liked' : '' ?>"
                    onclick="toggleLike(this, <?= $post['id'] ?>)">
              <svg viewBox="0 0 24 24" width="16" height="16"
                   fill="<?= $post['user_liked'] ? 'currentColor' : 'none' ?>"
                   stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
              </svg>
              <span class="like-count"><?= $post['like_count'] ?></span>
            </button>
            <button class="action-btn" onclick="toggleComments(<?= $post['id'] ?>)">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                   stroke="currentColor" stroke-width="2">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
              <span class="comment-count"><?= $post['comment_count'] ?></span>
            </button>
          </div>

          <!-- Comments -->
          <div class="comments-section" id="comments-<?= $post['id'] ?>" style="display:none;">
            <div class="comments-list" id="comments-list-<?= $post['id'] ?>"></div>
            <div class="comment-form">
              <input type="text" class="comment-input"
                     id="comment-input-<?= $post['id'] ?>"
                     placeholder="Write a comment..."
                     onkeypress="handleCommentKeypress(event, <?= $post['id'] ?>)" />
              <button class="comment-submit"
                      onclick="submitComment(<?= $post['id'] ?>)">Comment</button>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Right Sidebar -->
  <aside class="sidebar-right">
    <div class="widget">
      <div class="widget-title">Trending Now</div>
      <div class="trend-item"><span class="trend-rank">1</span><span class="trend-name">#DemonSlayer</span><span class="trend-count">2.4k</span></div>
      <div class="trend-item"><span class="trend-rank">2</span><span class="trend-name">#GenshinImpact</span><span class="trend-count">1.8k</span></div>
      <div class="trend-item"><span class="trend-rank">3</span><span class="trend-name">#OnePiece</span><span class="trend-count">1.2k</span></div>
      <div class="trend-item"><span class="trend-rank">4</span><span class="trend-name">#BlueLock</span><span class="trend-count">856</span></div>
      <div class="trend-item"><span class="trend-rank">5</span><span class="trend-name">#SoloLeveling</span><span class="trend-count">642</span></div>
    </div>
    <div class="widget">
      <div class="widget-title">Popular Fandoms</div>
      <div class="fan-item"><span class="badge badge-anime">Anime</span><span class="fan-name">12.5k fans</span></div>
      <div class="fan-item"><span class="badge badge-games">Games</span><span class="fan-name">8.2k fans</span></div>
      <div class="fan-item"><span class="badge badge-movies">Movies</span><span class="fan-name">6.1k fans</span></div>
      <div class="fan-item"><span class="badge badge-manga">Manga</span><span class="fan-name">4.8k fans</span></div>
    </div>
  </aside>

</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>