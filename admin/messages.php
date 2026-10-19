<?php
$pageTitle = 'Inbox Messages';
require_once __DIR__ . '/header.php';

$pdo = getDBConnection();
$successMsg = '';
$errorMsg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if (deleteMessage($delId)) {
        $successMsg = 'Message deleted successfully!';
    } else {
        $errorMsg = 'Could not delete message.';
    }
}

// Handle Mark as Read
if (isset($_GET['mark_read'])) {
    $readId = (int)$_GET['mark_read'];
    if (markMessageAsRead($readId)) {
        $successMsg = 'Message marked as read!';
    }
}

// Handle Mark as Unread
if (isset($_GET['mark_unread'])) {
    $unreadId = (int)$_GET['mark_unread'];
    $stmt = $pdo->prepare("UPDATE `messages` SET `is_read` = 0 WHERE `id` = ?");
    if ($stmt->execute([$unreadId])) {
        $successMsg = 'Message marked as unread.';
    }
}

// Filter
$filter = $_GET['filter'] ?? 'all';
$onlyUnread = ($filter === 'unread');

$messages = getMessages(100, $onlyUnread);
$unreadCount = getUnreadMessageCount();
$totalCount = (int)$pdo->query("SELECT COUNT(*) FROM `messages`")->fetchColumn();
?>

<?php if (!empty($successMsg)): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= htmlspecialchars($successMsg) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($errorMsg)): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <span><?= htmlspecialchars($errorMsg) ?></span>
    </div>
<?php endif; ?>

<!-- Filter Tabs -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
    <div style="display: flex; gap: 10px;">
        <a href="messages.php?filter=all" class="btn <?= $filter !== 'unread' ? 'btn-primary' : 'btn-outline' ?>">
            <i class="fa-solid fa-inbox"></i> All Inquiries (<?= $totalCount ?>)
        </a>
        <a href="messages.php?filter=unread" class="btn <?= $filter === 'unread' ? 'btn-primary' : 'btn-outline' ?>">
            <i class="fa-solid fa-envelope-open-text"></i> Unread Only (<?= $unreadCount ?>)
        </a>
    </div>

    <div style="color: var(--adm-text-muted); font-size: 0.88rem;">
        <i class="fa-solid fa-circle-info"></i> Messages sent via your portfolio contact form are stored securely here in MySQL.
    </div>
</div>

<!-- Messages List Table -->
<div class="content-card">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-envelope"></i>
            <?= $filter === 'unread' ? 'Unread Inquiries' : 'All Received Messages' ?>
        </h3>
        <span style="font-size: 0.85rem; color: var(--adm-text-muted);">
            Showing <?= count($messages) ?> message<?= count($messages) === 1 ? '' : 's' ?>
        </span>
    </div>

    <div class="table-responsive">
        <?php if (!empty($messages)): ?>
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Status</th>
                        <th>Sender Name</th>
                        <th>Email Address</th>
                        <th>Subject</th>
                        <th>Date & Time</th>
                        <th style="width: 160px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr style="<?= empty($msg['is_read']) ? 'background: rgba(99, 102, 241, 0.05); font-weight: 500;' : '' ?>">
                            <td>
                                <?php if (empty($msg['is_read'])): ?>
                                    <span style="background: rgba(239, 68, 68, 0.15); color: var(--adm-danger); border: 1px solid rgba(239, 68, 68, 0.3); padding: 3px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">
                                        <i class="fa-solid fa-circle" style="font-size: 0.5rem; vertical-align: middle; margin-right: 4px;"></i> New
                                    </span>
                                <?php else: ?>
                                    <span style="background: rgba(148, 163, 184, 0.1); color: var(--adm-text-muted); padding: 3px 8px; border-radius: 6px; font-size: 0.75rem;">
                                        Read
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: #ffffff;"><?= htmlspecialchars($msg['name']) ?></strong>
                            </td>
                            <td>
                                <a href="mailto:<?= htmlspecialchars($msg['email']) ?>" style="color: var(--adm-primary); text-decoration: underline;">
                                    <?= htmlspecialchars($msg['email']) ?>
                                </a>
                            </td>
                            <td>
                                <span style="color: #ffffff;"><?= htmlspecialchars($msg['subject']) ?></span>
                            </td>
                            <td style="color: var(--adm-text-muted); font-size: 0.85rem; white-space: nowrap;">
                                <?= date('M d, Y h:i A', strtotime($msg['created_at'])) ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <button type="button" class="btn btn-outline btn-sm" onclick='viewMessage(<?= json_encode($msg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' title="View Full Message">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <?php if (empty($msg['is_read'])): ?>
                                        <a href="messages.php?mark_read=<?= $msg['id'] ?>&filter=<?= $filter ?>" class="btn btn-outline btn-sm" title="Mark as Read">
                                            <i class="fa-solid fa-envelope-open"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="messages.php?mark_unread=<?= $msg['id'] ?>&filter=<?= $filter ?>" class="btn btn-outline btn-sm" title="Mark as Unread">
                                            <i class="fa-solid fa-envelope"></i>
                                        </a>
                                    <?php endif; ?>

                                    <a href="messages.php?delete=<?= $msg['id'] ?>&filter=<?= $filter ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this message permanently?');" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px; color: var(--adm-text-muted);">
                <i class="fa-regular fa-envelope-open" style="font-size: 3rem; margin-bottom: 14px; opacity: 0.4;"></i>
                <p>No messages found <?= $filter === 'unread' ? 'under unread filter' : 'in your inbox yet' ?>.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal: View Message Details -->
<div id="messageModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 20px;">
    <div style="background-color: var(--adm-card); border: 1px solid var(--adm-border); border-radius: var(--adm-radius); max-width: 600px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.6); overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--adm-border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-envelope-open-text" style="color: var(--adm-primary);"></i>
                <span>Message Details</span>
            </h3>
            <button type="button" onclick="closeMessageModal()" style="background: transparent; border: none; color: var(--adm-text-muted); font-size: 1.3rem; cursor: pointer;">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div style="padding: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px; background: var(--adm-sidebar); padding: 16px; border-radius: var(--adm-radius-sm); border: 1px solid var(--adm-border);">
                <div>
                    <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--adm-text-muted); font-weight: 600;">From:</span>
                    <div id="modalSender" style="font-size: 0.95rem; font-weight: 600; color: #ffffff; margin-top: 2px;"></div>
                </div>
                <div>
                    <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--adm-text-muted); font-weight: 600;">Date:</span>
                    <div id="modalDate" style="font-size: 0.85rem; color: var(--adm-text-muted); margin-top: 2px;"></div>
                </div>
                <div style="grid-column: 1 / -1;">
                    <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--adm-text-muted); font-weight: 600;">Email:</span>
                    <div>
                        <a id="modalEmail" href="" style="color: var(--adm-primary); font-size: 0.92rem; text-decoration: underline;"></a>
                    </div>
                </div>
                <div style="grid-column: 1 / -1;">
                    <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--adm-text-muted); font-weight: 600;">Subject:</span>
                    <div id="modalSubject" style="font-size: 1rem; font-weight: 600; color: #ffffff; margin-top: 2px;"></div>
                </div>
            </div>

            <div style="margin-bottom: 24px;">
                <label style="font-size: 0.82rem; text-transform: uppercase; color: var(--adm-text-muted); font-weight: 600; margin-bottom: 8px; display: block;">Message Body:</label>
                <div id="modalBody" style="background-color: var(--adm-sidebar); border: 1px solid var(--adm-border); border-radius: var(--adm-radius-sm); padding: 18px; color: #e2e8f0; line-height: 1.7; font-size: 0.92rem; white-space: pre-wrap; max-height: 250px; overflow-y: auto;"></div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--adm-border); padding-top: 18px;">
                <a id="modalReplyBtn" href="" class="btn btn-primary">
                    <i class="fa-solid fa-reply"></i> Reply via Email
                </a>
                <button type="button" class="btn btn-outline" onclick="closeMessageModal()">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewMessage(msg) {
    document.getElementById('modalSender').textContent = msg.name;
    document.getElementById('modalDate').textContent = msg.created_at;
    
    const emailEl = document.getElementById('modalEmail');
    emailEl.textContent = msg.email;
    emailEl.href = 'mailto:' + encodeURIComponent(msg.email);
    
    document.getElementById('modalSubject').textContent = msg.subject;
    document.getElementById('modalBody').textContent = msg.message;
    
    const replyBtn = document.getElementById('modalReplyBtn');
    replyBtn.href = 'mailto:' + encodeURIComponent(msg.email) + '?subject=' + encodeURIComponent('Re: ' + msg.subject);

    const modal = document.getElementById('messageModal');
    modal.style.display = 'flex';

    // If message is unread, trigger an async mark-as-read
    if (!parseInt(msg.is_read)) {
        fetch('messages.php?mark_read=' + msg.id);
    }
}

function closeMessageModal() {
    document.getElementById('messageModal').style.display = 'none';
}

window.addEventListener('click', (e) => {
    const modal = document.getElementById('messageModal');
    if (e.target === modal) {
        closeMessageModal();
    }
});

<?php if (isset($_GET['view'])): 
    $viewId = (int)$_GET['view'];
    $stmt = $pdo->prepare("SELECT * FROM `messages` WHERE `id` = ?");
    $stmt->execute([$viewId]);
    $autoMsg = $stmt->fetch();
    if ($autoMsg):
?>
document.addEventListener('DOMContentLoaded', () => {
    viewMessage(<?= json_encode($autoMsg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>);
});
<?php endif; endif; ?>
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
