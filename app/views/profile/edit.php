<?php
$pageTitle = 'Edit Profile';
$pageStyles = ['assets/css/profile.css'];
$pageScripts = ['assets/js/profile.js'];

require_once BASE_PATH . '/app/views/layouts/header.php';
require_once BASE_PATH . '/app/views/layouts/navbar.php';
?>

<div class="page-layout">
    <!-- Left Sidebar -->
    <aside class="sidebar-left">
        <div class="sidebar-section-label">Menu</div>
        <a href="index.php?action=newsfeed" class="sidebar-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                <polyline points="9 22 9 12 15 12 15 22"></polyline>
            </svg>
            Newsfeed
        </a>
        <a href="index.php?action=profile" class="sidebar-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            My Profile
        </a>
        
        <div class="sidebar-section-label" style="margin-top: 24px;">Settings</div>
        <a href="index.php?action=edit_profile" class="sidebar-item active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            Edit Profile
        </a>
    </aside>

    <!-- Main Content -->
    <main class="feed-main">
        <div class="profile-edit-container">
            <h1 class="page-title">Edit Profile</h1>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form class="profile-edit-form" method="POST" action="index.php?action=edit_profile" enctype="multipart/form-data" onsubmit="return validateProfileForm()">
                
                <!-- Profile Image Section -->
                <div class="form-section">
                    <h3>Profile Picture</h3>
                    <div class="profile-image-edit">
                        <div class="profile-big-avatar" id="avatarPreview" data-username="<?= htmlspecialchars($user['username']) ?>">
                            <?php if (!empty($user['profile_image']) && $user['profile_image'] !== 'default.png'): ?>
                                <img src="<?= htmlspecialchars($user['profile_image']) ?>?t=<?= time() ?>" alt="Profile" id="currentAvatar" />
                            <?php else: ?>
                                <?= strtoupper(substr($user['username'], 0, 2)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="image-edit-actions">
                            <label class="btn-secondary" for="profileImage">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                                Change Photo
                            </label>
                            <input 
                                type="file" 
                                id="profileImage" 
                                name="profile_image" 
                                accept="image/*" 
                                style="display: none;" 
                            />
                            <?php if (!empty($user['profile_image']) && $user['profile_image'] !== 'default.png'): ?>
                                <button type="button" class="btn-text-danger" onclick="removeProfileImage()">
                                    Remove Photo
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Basic Info Section -->
                <div class="form-section">
                    <h3>Basic Information</h3>
                    
                    <div class="form-group">
                        <label class="form-label" for="fullName">Full Name</label>
                        <input 
                            type="text" 
                            id="fullName" 
                            name="full_name" 
                            class="form-input" 
                            value="<?= htmlspecialchars($user['full_name']) ?>" 
                            required
                            maxlength="100"
                        />
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="username">Username</label>
                        <input 
                            type="text" 
                            id="username" 
                            class="form-input" 
                            value="@<?= htmlspecialchars($user['username']) ?>" 
                            disabled
                        />
                        <small class="form-hint">Username cannot be changed</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="bio">Bio</label>
                        <textarea 
                            id="bio" 
                            name="bio" 
                            class="form-input" 
                            rows="4" 
                            maxlength="500"
                            placeholder="Tell us about yourself..."
                        ><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        <small class="form-hint"><span id="bioCount">0</span>/500 characters</small>
                    </div>
                </div>

                <!-- Fandoms Section -->
                <div class="form-section">
                    <h3>Your Fandoms</h3>
                    <p class="section-description">Select the fandoms you're interested in. This helps us show you relevant content.</p>
                    
                    <div class="fandom-selector-edit">
                        <?php 
                        $allFandoms = ['Anime', 'Games', 'Movies', 'Manga', 'K-Drama', 'Comics', 'Novels', 'Cosplay'];
                        $userFandoms = $currentFandoms ?? [];
                        foreach ($allFandoms as $fandom): 
                            $isChecked = in_array($fandom, $userFandoms);
                        ?>
                            <label class="fandom-checkbox">
                                <input 
                                    type="checkbox" 
                                    name="fandoms[]" 
                                    value="<?= $fandom ?>" 
                                    <?= $isChecked ? 'checked' : '' ?>
                                    onchange="toggleFandom(this)"
                                />
                                <span class="badge badge-<?= strtolower(str_replace(['-', ' '], ['', ''], $fandom)) ?>">
                                    <?= $fandom ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions-sticky">
                    <a href="index.php?action=profile" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary btn-large">Save Changes</button>
                </div>
            </form>

            <!-- Account Section -->
            <div class="form-section danger-section">
                <h3>Account</h3>
                <div class="danger-content">
                    <div>
                        <h4>Log Out</h4>
                        <p>Log out of your account on this device.</p>
                    </div>
                    <a href="index.php?action=logout" class="btn-secondary">Log Out</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Right Sidebar -->
    <aside class="sidebar-right">
        <div class="widget">
            <div class="widget-title">Preview</div>
            <div class="profile-preview-card">
                <div class="preview-avatar">
                    <?php if (!empty($user['profile_image']) && $user['profile_image'] !== 'default.png'): ?>
                        <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile" id="previewAvatar" />
                    <?php else: ?>
                        <?= strtoupper(substr($user['username'], 0, 2)) ?>
                    <?php endif; ?>
                </div>
                <div class="preview-info">
                    <div class="preview-name" id="previewName" data-default="<?= htmlspecialchars($user['full_name']) ?>"><?= htmlspecialchars($user['full_name']) ?></div>
                    <div class="preview-handle">@<?= htmlspecialchars($user['username']) ?></div>
                    <div class="preview-bio" id="previewBio"><?= htmlspecialchars($user['bio'] ?? '') ?></div>
                </div>
            </div>
        </div>

        <div class="widget">
            <div class="widget-title">Tips</div>
            <div class="tip-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <p>Use a clear photo for your profile picture so fans can recognize you.</p>
            </div>
            <div class="tip-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <p>Select fandoms that match your interests to see relevant posts.</p>
            </div>
        </div>
    </aside>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>