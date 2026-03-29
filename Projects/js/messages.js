// ============================================================
// messages.js — FanZone | Messages page interactions
// ============================================================

/**
 * Send a new message from the input bar.
 * Appends the bubble to the chat UI immediately (optimistic update).
 * TODO: Also POST to your MessageController (e.g. /message/send).
 */
function sendMessage() {
    const input     = document.getElementById('msgInput');
    const container = document.getElementById('chatMessages');
    const text      = input.value.trim();
    if (!text || !container) return;

    const now = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    const row       = document.createElement('div');
    row.className   = 'msg-row me';
    row.innerHTML   = `
        <div class="msg-col">
            <div class="bubble">${escapeHtml(text)}</div>
            <div class="msg-ts">${now}</div>
        </div>`;
    container.appendChild(row);
    container.scrollTop = container.scrollHeight;
    input.value = '';

    // TODO: POST to your MessageController
    // const convId = new URLSearchParams(window.location.search).get('conv');
    // fetch('/message/send', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken() },
    //     body: JSON.stringify({ conversation_id: convId, text })
    // });
}

/**
 * Allow sending a message by pressing Enter in the input field.
 * @param {KeyboardEvent} event
 */
function handleMsgEnter(event) {
    if (event.key === 'Enter') sendMessage();
}

/**
 * Escape HTML special characters to prevent XSS in dynamic content.
 * @param {string} str
 * @returns {string}
 */
function escapeHtml(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

// Auto-scroll to the latest message on page load
document.addEventListener('DOMContentLoaded', () => {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) chatMessages.scrollTop = chatMessages.scrollHeight;
});
