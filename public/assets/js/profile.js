

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initImagePreview();
    initBioCounter();
    initLivePreview();
});

// Profile image preview
function initImagePreview() {
    const imageInput = document.getElementById('profileImage');
    if (!imageInput) return;
    
    imageInput.addEventListener('change', handleImagePreview);
}

function handleImagePreview() {
    const input = document.getElementById('profileImage');
    if (!input.files || !input.files[0]) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        // Update main avatar preview
        const avatarPreview = document.getElementById('avatarPreview');
        if (avatarPreview) {
            avatarPreview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" />';
        }
        
        // Update sidebar preview
        const previewAvatar = document.getElementById('previewAvatar');
        if (previewAvatar) {
            previewAvatar.src = e.target.result;
        }
    };
    reader.readAsDataURL(input.files[0]);
}

function removeProfileImage() {
    if (!confirm('Remove your profile photo?')) return;
    
    const imageInput = document.getElementById('profileImage');
    const avatarPreview = document.getElementById('avatarPreview');
    const username = avatarPreview?.dataset?.username || '??';
    
    if (imageInput) imageInput.value = '';
    if (avatarPreview) {
        avatarPreview.innerHTML = username.substring(0, 2).toUpperCase();
    }
}

// Bio character counter
function initBioCounter() {
    const bioInput = document.getElementById('bio');
    const bioCount = document.getElementById('bioCount');
    
    if (!bioInput || !bioCount) return;
    
    // Initialize count
    bioCount.textContent = bioInput.value.length;
    
    bioInput.addEventListener('input', function() {
        bioCount.textContent = this.value.length;
    });
}

// Live preview updates
function initLivePreview() {
    const nameInput = document.getElementById('fullName');
    const bioInput = document.getElementById('bio');
    const previewName = document.getElementById('previewName');
    const previewBio = document.getElementById('previewBio');
    
    if (nameInput && previewName) {
        nameInput.addEventListener('input', function() {
            previewName.textContent = this.value || previewName.dataset.default || '';
        });
    }
    
    if (bioInput && previewBio) {
        bioInput.addEventListener('input', function() {
            previewBio.textContent = this.value;
        });
    }
}

// Toggle fandom selection visual feedback
function toggleFandom(checkbox) {
    const badge = checkbox.nextElementSibling;
    if (!badge) return;
    
    if (checkbox.checked) {
        badge.style.transform = 'scale(1.05)';
        badge.style.boxShadow = '0 0 0 2px var(--accent)';
    } else {
        badge.style.transform = 'scale(1)';
        badge.style.boxShadow = 'none';
    }
}

// Validate profile form
function validateProfileForm() {
    const fullName = document.getElementById('fullName')?.value.trim();
    
    if (!fullName) {
        showToast('Full name is required', 'error');
        return false;
    }
    
    // Check at least one fandom selected
    const fandoms = document.querySelectorAll('input[name="fandoms[]"]:checked');
    if (fandoms.length === 0) {
        showToast('Please select at least one fandom', 'error');
        return false;
    }
    
    return true;
}