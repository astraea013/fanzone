
// ── FANDOM TAG SELECTION
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.tag-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.tag-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
    });
  });
});

// ── GET BASE URL (absolute, works from any folder depth)
function getBaseUrl() {
  // Walk script tags to find our assets path
  const scripts = document.querySelectorAll('script[src]');
  for (const s of scripts) {
    if (s.src.includes('/assets/js/')) {
      return s.src.split('/assets/js/')[0] + '/';
    }
  }
  
  return window.location.href.split('/index.php')[0] + '/';
}

// ── SUBMIT QUICK POST FROM NEWSFEED
function submitPost() {
  const content   = document.getElementById('postContent')?.value.trim();
  const activeTag = document.querySelector('.tag-btn.active');
  const fandom    = activeTag ? activeTag.dataset.fandom : 'Anime';

  if (!content) { alert('Please write something first!'); return; }

  const form   = document.createElement('form');
  form.method  = 'POST';
  form.action  = getBaseUrl() + 'index.php?action=create_post';

  appendField(form, 'content',    content);
  appendField(form, 'fandom_tag', fandom);
  document.body.appendChild(form);
  form.submit();
}

function appendField(form, name, value) {
  const input = document.createElement('input');
  input.type  = 'hidden';
  input.name  = name;
  input.value = value;
  form.appendChild(input);
}

// ── TOGGLE LIKE (AJAX)
function toggleLike(btn, postId) {
  const formData = new FormData();
  formData.append('post_id', postId);

  fetch(getBaseUrl() + 'index.php?action=like_post', {
    method: 'POST',
    body:   formData
  })
  .then(r => r.json())
  .then(data => {
    const countEl = btn.querySelector('.like-count');
    let count = parseInt(countEl?.textContent) || 0;
    const svg = btn.querySelector('svg');

    if (data.liked) {
      btn.classList.add('liked');
      if (svg) svg.setAttribute('fill', 'currentColor');
      if (countEl) countEl.textContent = count + 1;
    } else {
      btn.classList.remove('liked');
      if (svg) svg.setAttribute('fill', 'none');
      if (countEl) countEl.textContent = Math.max(0, count - 1);
    }
  })
  .catch(err => console.error('Like error:', err));
}

// ── TOGGLE COMMENTS SECTION
function toggleComments(postId) {
  const section = document.getElementById('comments-' + postId);
  if (!section) return;

  const isHidden = section.style.display === 'none' || section.style.display === '';
  if (isHidden) {
    section.style.display = 'block';
    loadComments(postId);
  } else {
    section.style.display = 'none';
  }
}

// ── LOAD COMMENTS VIA AJAX

function loadComments(postId) {
  fetch(getBaseUrl() + 'index.php?action=get_comments&post_id=' + postId)
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('comments-list-' + postId);
      if (!list) return;
      list.innerHTML = '';

      // FIX: handle both plain array and {success, comments:[]} format
      const comments = Array.isArray(data) ? data : (data.comments ?? []);

      if (comments.length === 0) {
        list.innerHTML = '<p style="font-size:12px;color:var(--text-muted);padding:8px 0;">No comments yet. Be the first!</p>';
        return;
      }

      comments.forEach(c => {
        const initials  = (c.username || 'U').substring(0, 2).toUpperCase();
        const hasAvatar = c.profile_image && c.profile_image !== 'default.png';
        const avatarHtml = hasAvatar
          ? `<img src="${escHtml(c.profile_image)}" alt="${escHtml(c.username)}"
               style="width:100%;height:100%;object-fit:cover;border-radius:50%;" />`
          : initials;

        const div = document.createElement('div');
        div.className = 'comment-item';
        div.innerHTML = `
          <div class="post-avatar"
               style="width:30px;height:30px;font-size:11px;flex-shrink:0;">
            ${avatarHtml}
          </div>
          <div class="comment-body">
            <div class="comment-user">${escHtml(c.full_name)}</div>
            <div class="comment-text">${escHtml(c.content)}</div>
          </div>`;
        list.appendChild(div);
      });
    })
    .catch(err => console.error('Load comments error:', err));
}

// ── SUBMIT COMMENT
// FIX: After success, reload comments so the new one appears immediately
function submitComment(postId) {
  const input   = document.getElementById('comment-input-' + postId);
  const content = input?.value.trim();
  if (!content) return;

  const formData = new FormData();
  formData.append('post_id', postId);
  formData.append('content', content);

  fetch(getBaseUrl() + 'index.php?action=add_comment', {
    method: 'POST',
    body:   formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      input.value = '';
      // Reload comments list so new comment appears
      loadComments(postId);
      // Update comment counter badge
      const card    = document.querySelector(`[data-post-id="${postId}"]`);
      const countEl = card?.querySelector('.comment-count');
      if (countEl) countEl.textContent = parseInt(countEl.textContent || 0) + 1;
    } else {
      alert(data.message || 'Failed to post comment');
    }
  })
  .catch(err => console.error('Comment submit error:', err));
}

// ── ENTER KEY SUBMITS COMMENT
function handleCommentKeypress(event, postId) {
  if (event.key === 'Enter') submitComment(postId);
}

// ── CONFIRM & DELETE POST
function confirmDelete(postId) {
  if (!confirm('Delete this post? This cannot be undone.')) return;
  const form  = document.createElement('form');
  form.method = 'POST';
  form.action = getBaseUrl() + 'index.php?action=delete_post';
  appendField(form, 'post_id', postId);
  document.body.appendChild(form);
  form.submit();
}

// ── SAFE HTML ESCAPE
function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}