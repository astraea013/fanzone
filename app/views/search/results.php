<?php require_once BASE_PATH . '/app/views/layouts/header.php'; ?>

<div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h2 style="margin-bottom: 20px;">🔍 Search Results</h2>
    
    <!-- Search Form -->
    <form method="GET" action="index.php" style="margin-bottom: 30px;">
        <input type="hidden" name="action" value="search">
        <div style="display: flex; gap: 10px;">
            <input 
                type="text" 
                name="q" 
                placeholder="Search by username or name..." 
                value="<?= htmlspecialchars($query) ?>"
                style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px;"
                required
            >
            <button type="submit" style="padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                Search
            </button>
        </div>
    </form>
    
    <!-- Results -->
    <?php if (!empty($query)): ?>
        <p style="color: #666; margin-bottom: 20px;">
            Found <?= count($users) ?> result(s) for "<?= htmlspecialchars($query) ?>"
        </p>
        
        <?php if (empty($users)): ?>
            <div style="text-align: center; padding: 40px; color: #999;">
                <p style="font-size: 48px; margin-bottom: 10px;">😕</p>
                <p>No users found matching your search.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <?php foreach ($users as $user): ?>
                    <a href="index.php?action=profile&id=<?= $user['id'] ?>" 
                       style="text-decoration: none; color: inherit; display: block; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.2s, box-shadow 0.2s;"
                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(0,0,0,0.15)'"
                       onmouseout="this.style.transform='none'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.1)'">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <!-- Avatar -->
                            <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 20px; overflow: hidden;">
                                <?php if (!empty($user['profile_image']) && $user['profile_image'] !== 'default.png'): ?>
                                    <img src="<?= htmlspecialchars($user['profile_image']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <?= strtoupper(substr($user['username'], 0, 2)) ?>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Info -->
                            <div style="flex: 1;">
                                <h3 style="margin: 0; color: #333; font-size: 18px;">
                                    <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>
                                </h3>
                                <p style="margin: 4px 0 0 0; color: #667eea; font-weight: 500;">@<?= htmlspecialchars($user['username']) ?></p>
                                <?php if (!empty($user['bio'])): ?>
                                    <p style="margin: 8px 0 0 0; color: #666; font-size: 14px; line-height: 1.4;">
                                        <?= htmlspecialchars(substr($user['bio'], 0, 100)) ?><?= strlen($user['bio']) > 100 ? '...' : '' ?>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($user['fandoms'])): ?>
                                    <div style="margin-top: 8px;">
                                        <?php foreach (array_slice(explode(',', $user['fandoms']), 0, 3) as $fandom): ?>
                                            <span style="display: inline-block; background: #f0f0f0; color: #666; padding: 3px 10px; border-radius: 12px; font-size: 12px; margin-right: 5px;">
                                                <?= htmlspecialchars(trim($fandom)) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Arrow -->
                            <span style="color: #ccc; font-size: 20px;">→</span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once BASE_PATH . '/app/views/layouts/footer.php'; ?>