<?php
$pageTitle = 'Dashboard Overview';
require_once __DIR__ . '/header.php';

$pdo = getDBConnection();

$totalSkills = (int)$pdo->query("SELECT COUNT(*) FROM `skills`")->fetchColumn();
$totalServices = (int)$pdo->query("SELECT COUNT(*) FROM `services`")->fetchColumn();
$totalProjects = (int)$pdo->query("SELECT COUNT(*) FROM `projects`")->fetchColumn();
$totalEducation = (int)$pdo->query("SELECT COUNT(*) FROM `education`")->fetchColumn();
$totalLanguages = (int)$pdo->query("SELECT COUNT(*) FROM `languages`")->fetchColumn();
$totalMessages = (int)$pdo->query("SELECT COUNT(*) FROM `messages`")->fetchColumn();
$recentMessages = getMessages(5);
?>

<!-- Statistics Overview -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fa-solid fa-laptop-code"></i>
        </div>
        <div class="stat-info">
            <h4>Total Projects</h4>
            <div class="stat-number"><?= $totalProjects ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background-color: rgba(99, 102, 241, 0.15); color: #818cf8;">
            <i class="fa-solid fa-screwdriver-wrench"></i>
        </div>
        <div class="stat-info">
            <h4>Total Services</h4>
            <div class="stat-number"><?= $totalServices ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon primary" style="background-color: rgba(16, 185, 129, 0.12); color: #10b981;">
            <i class="fa-solid fa-code"></i>
        </div>
        <div class="stat-info">
            <h4>Total Skills</h4>
            <div class="stat-number"><?= $totalSkills ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <div class="stat-info">
            <h4>Education</h4>
            <div class="stat-number"><?= $totalEducation ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fa-solid fa-language"></i>
        </div>
        <div class="stat-info">
            <h4>Languages</h4>
            <div class="stat-number"><?= $totalLanguages ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon <?= $unreadCount > 0 ? 'danger' : 'success' ?>">
            <i class="fa-solid fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h4>Inbox Messages</h4>
            <div class="stat-number"><?= $totalMessages ?> <span style="font-size: 0.85rem; font-weight: 500; color: <?= $unreadCount > 0 ? 'var(--adm-danger)' : 'var(--adm-success)' ?>;">(<?= $unreadCount ?> new)</span></div>
        </div>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fa-solid fa-bolt"></i> Quick Management Actions</h3>
    </div>
    <div style="display: flex; flex-wrap: wrap; gap: 14px;">
        <a href="projects.php" class="btn btn-outline">
            <i class="fa-solid fa-laptop-code"></i> Manage Projects
        </a>
        <a href="services.php" class="btn btn-outline">
            <i class="fa-solid fa-screwdriver-wrench"></i> Manage Services
        </a>
        <a href="profile.php" class="btn btn-outline">
            <i class="fa-solid fa-user-pen"></i> Edit Profile & Details
        </a>
        <a href="education.php" class="btn btn-outline">
            <i class="fa-solid fa-plus"></i> Manage Education
        </a>
        <a href="skills.php" class="btn btn-outline">
            <i class="fa-solid fa-layer-group"></i> Manage Skills & Tech
        </a>
        <a href="languages.php" class="btn btn-outline">
            <i class="fa-solid fa-globe"></i> Manage Languages
        </a>
        <a href="messages.php" class="btn btn-primary">
            <i class="fa-solid fa-inbox"></i> View Inquiries Inbox
        </a>
    </div>
</div>

<!-- Recent Messages Table -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fa-solid fa-comments"></i> Recent Contact Messages</h3>
        <a href="messages.php" class="btn btn-outline btn-sm">View All Messages</a>
    </div>

    <div class="table-responsive">
        <?php if (!empty($recentMessages)): ?>
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Sender Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentMessages as $msg): ?>
                        <tr>
                            <td>
                                <?php if (empty($msg['is_read'])): ?>
                                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: var(--adm-danger); margin-right: 6px;"></span>
                                    <strong style="color: var(--adm-danger); font-size: 0.8rem;">NEW</strong>
                                <?php else: ?>
                                    <span style="color: var(--adm-text-muted); font-size: 0.8rem;">Read</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($msg['name']) ?></strong></td>
                            <td><?= htmlspecialchars($msg['email']) ?></td>
                            <td><?= htmlspecialchars($msg['subject']) ?></td>
                            <td style="color: var(--adm-text-muted); font-size: 0.85rem;"><?= date('M j, Y h:i A', strtotime($msg['created_at'])) ?></td>
                            <td style="text-align: right;">
                                <a href="messages.php?view=<?= $msg['id'] ?>" class="btn btn-outline btn-sm">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: var(--adm-text-muted); text-align: center; padding: 20px 0;">No inquiries received yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
