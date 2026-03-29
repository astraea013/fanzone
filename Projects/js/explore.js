// ============================================================
// explore.js — FanZone | Explore page interactions
// ============================================================

/**
 * Live-filter fandom cards by name as the user types.
 * Called by the oninput event on the search input.
 * @param {string} query
 */
function filterFandoms(query) {
    const cards = document.querySelectorAll('#fandomsGrid .fandom-card');
    const q = query.toLowerCase().trim();
    cards.forEach(card => {
        card.style.display = card.dataset.name.includes(q) ? '' : 'none';
    });
}

/**
 * Toggle Join / Joined state on a fandom card button.
 * TODO: Wire up a POST request to your FandomController (e.g. /fandom/join/{id}).
 * @param {HTMLButtonElement} btn
 */
function toggleJoin(btn) {
    const joined = btn.classList.toggle('joined');
    btn.textContent = joined ? 'Joined' : 'Join';
    // TODO: POST to your FandomController
    // const fandomId = btn.dataset.id;
    // fetch(`/fandom/join/${fandomId}`, { method: 'POST', headers: { 'X-CSRF-Token': csrfToken() } });
}

/**
 * Toggle Follow / Following state on a fan suggestion.
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
