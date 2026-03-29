// ============================================================
// profile.js — FanZone | Profile page interactions
// ============================================================

/**
 * Switch between Posts / Media / Likes tabs.
 * @param {HTMLButtonElement} btn  - The clicked tab button
 * @param {string}            tabId - 'posts' | 'media' | 'likes'
 */
function switchTab(btn, tabId) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    ['posts', 'media', 'likes'].forEach(id => {
        const panel = document.getElementById('tab-' + id);
        if (panel) panel.style.display = id === tabId ? '' : 'none';
    });
}

/**
 * Toggle like / unlike on a post.
 * TODO: Wire up a POST request to your PostController (e.g. /post/like/{id}).
 * @param {HTMLButtonElement} btn
 */
function toggleLike(btn) {
    const span  = btn.querySelector('span');
    const base  = parseInt(btn.dataset.base, 10);
    const liked = btn.dataset.liked === '1';

    span.textContent    = (liked ? base : base + 1).toLocaleString();
    btn.style.color     = liked ? '' : '#ec4899';
    btn.dataset.liked   = liked ? '0' : '1';

    // TODO: POST to your PostController
    // const postId = btn.dataset.postId;
    // fetch(`/post/like/${postId}`, { method: 'POST', headers: { 'X-CSRF-Token': csrfToken() } });
}

/**
 * Toggle Follow / Following state in the sidebar widget.
 * TODO: Wire up a POST request to your UserController (e.g. /user/follow/{id}).
 * @param {HTMLButtonElement} btn
 */
function toggleFollow(btn) {
    const following = btn.classList.toggle('following');
    btn.textContent = following ? 'Following' : 'Follow';

    // TODO: POST to your UserController
    // const userId = btn.dataset.id;
    // fetch(`/user/follow/${userId}`, { method: 'POST', headers: { 'X-CSRF-Token': csrfToken() } });
}
