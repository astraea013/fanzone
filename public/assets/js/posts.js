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
  const scripts = document.querySelectorAll('script[src]');
  for (const s of scripts) {
    if (s.src.includes('/assets/js/')) {
      return s.src.split('/assets/js/')[0] + '/';
    }
  }
  return window.location.href.split('/index.php')[0] + '/';
}

// ── GET CURRENT USER ID FROM SESSION (add this meta tag in your HTML head)
function getCurrentUserId() {
  // Option 1: From meta tag (recommended - add to your header.php)
  const meta = document.querySelector('meta[name="user-id"]');
  if (meta) return parseInt(meta.content);
  
  // Option 2: From window object
  return window.currentUserId || 0;
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

// ── LOAD COMMENTS VIA AJAX (UPDATED with Edit/Delete buttons)
function loadComments(postId) {
  fetch(getBaseUrl() + 'index.php?action=get_comments&post_id=' + postId)
    .then(r => r.json())
    .then(data => {
      const list = document.getElementById('comments-list-' + postId);
      if (!list) return;
      list.innerHTML = '';

      const comments = Array.isArray(data) ? data : (data.comments ?? []);
      const currentUserId = getCurrentUserId();

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

        // Check if current user owns this comment
        const isOwner = parseInt(c.user_id) === currentUserId;
        
        // Build action buttons HTML if owner
        let actionsHtml = '';
        if (isOwner) {
          actionsHtml = `
            <div class="comment-actions" style="display:flex;gap:8px;margin-left:auto;">
              <button onclick="enableEditComment(${c.id}, ${postId})" 
                      class="btn-edit-comment" 
                      style="background:none;border:none;color:var(--accent);cursor:pointer;font-size:12px;padding:4px 8px;border-radius:4px;">
                Edit
              </button>
              <button onclick="deleteComment(${c.id}, ${postId})" 
                      class="btn-delete-comment" 
                      style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:12px;padding:4px 8px;border-radius:4px;">
                Delete
              </button>
            </div>
          `;
        }

        const div = document.createElement('div');
        div.className = 'comment-item';
        div.id = `comment-${c.id}`;
        div.dataset.commentId = c.id;
        div.innerHTML = `
          <div class="post-avatar"
               style="width:30px;height:30px;font-size:11px;flex-shrink:0;">
            ${avatarHtml}
          </div>
          <div class="comment-body" style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
              <div class="comment-user" style="font-weight:600;color:var(--text-primary);font-size:13px;">
                ${escHtml(c.full_name || c.username)}
              </div>
              ${actionsHtml}
            </div>
            <div class="comment-text" id="comment-text-${c.id}" style="color:var(--text-secondary);font-size:13px;margin-top:4px;word-break:break-word;">
              ${escHtml(c.content)}
            </div>
            <div class="comment-edit-form" id="comment-edit-form-${c.id}" style="display:none;margin-top:8px;">
              <input type="text" id="comment-edit-input-${c.id}" value="${escHtml(c.content)}" 
                     style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;background:var(--bg-input);color:var(--text-primary);font-size:13px;">
              <div style="display:flex;gap:8px;margin-top:8px;">
                <button onclick="submitEditComment(${c.id}, ${postId})" 
                        style="padding:6px 12px;background:var(--accent);color:white;border:none;border-radius:6px;font-size:12px;cursor:pointer;">Save</button>
                <button onclick="cancelEditComment(${c.id})" 
                        style="padding:6px 12px;background:transparent;border:1px solid var(--border);color:var(--text-secondary);border-radius:6px;font-size:12px;cursor:pointer;">Cancel</button>
              </div>
            </div>
          </div>`;
        list.appendChild(div);
      });
    })
    .catch(err => console.error('Load comments error:', err));
}

// ── ENABLE EDIT MODE FOR COMMENT
function enableEditComment(commentId, postId) {
  const textEl = document.getElementById(`comment-text-${commentId}`);
  const formEl = document.getElementById(`comment-edit-form-${commentId}`);
  const inputEl = document.getElementById(`comment-edit-input-${commentId}`);
  
  if (textEl && formEl) {
    textEl.style.display = 'none';
    formEl.style.display = 'block';
    inputEl.focus();
    inputEl.select();
  }
}

// ── CANCEL EDIT MODE
function cancelEditComment(commentId) {
  const textEl = document.getElementById(`comment-text-${commentId}`);
  const formEl = document.getElementById(`comment-edit-form-${commentId}`);
  
  if (textEl && formEl) {
    textEl.style.display = 'block';
    formEl.style.display = 'none';
  }
}

// ── SUBMIT EDIT COMMENT (AJAX)
function submitEditComment(commentId, postId) {
  const input = document.getElementById(`comment-edit-input-${commentId}`);
  const content = input?.value.trim();
  
  if (!content) {
    alert('Comment cannot be empty');
    return;
  }

  const formData = new FormData();
  formData.append('comment_id', commentId);
  formData.append('content', content);

  fetch(getBaseUrl() + 'index.php?action=edit_comment', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Update the text and exit edit mode
      const textEl = document.getElementById(`comment-text-${commentId}`);
      textEl.textContent = content;
      cancelEditComment(commentId);
    } else {
      alert(data.message || 'Failed to update comment');
    }
  })
  .catch(err => {
    console.error('Edit comment error:', err);
    alert('Error updating comment');
  });
}

// ── DELETE COMMENT (AJAX)
function deleteComment(commentId, postId) {
  if (!confirm('Delete this comment? This cannot be undone.')) return;

  const formData = new FormData();
  formData.append('comment_id', commentId);

  fetch(getBaseUrl() + 'index.php?action=delete_comment', {
    method: 'POST',
    body: formData
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // Remove comment from DOM
      const commentEl = document.getElementById(`comment-${commentId}`);
      if (commentEl) {
        commentEl.remove();
      }
      // Update comment counter
      const card = document.querySelector(`[data-post-id="${postId}"]`);
      const countEl = card?.querySelector('.comment-count');
      if (countEl) {
        const current = parseInt(countEl.textContent || 0);
        countEl.textContent = Math.max(0, current - 1);
      }
    } else {
      alert(data.message || 'Failed to delete comment');
    }
  })
  .catch(err => {
    console.error('Delete comment error:', err);
    alert('Error deleting comment');
  });
}

// ── SUBMIT COMMENT (UPDATED)
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
      loadComments(postId); // Reload to show new comment with actions
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