<?php
$pageTitle = 'Manage Services';
require_once __DIR__ . '/header.php';

$pdo = getDBConnection();
$successMsg = '';
$errorMsg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM `services` WHERE `id` = ?");
    if ($stmt->execute([$delId])) {
        $successMsg = 'Service deleted successfully!';
    } else {
        $errorMsg = 'Failed to delete service.';
    }
}

// Handle Status Toggle
if (isset($_GET['toggle'])) {
    $toggleId = (int)$_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE `services` SET `is_active` = 1 - `is_active` WHERE `id` = ?");
    if ($stmt->execute([$toggleId])) {
        $successMsg = 'Service status updated!';
    }
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $iconClass = trim($_POST['icon_class'] ?? 'fa-solid fa-code');
    $iconColor = trim($_POST['icon_color'] ?? '#6366f1');
    $tags = trim($_POST['tags'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 1);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if (empty($title) || empty($description)) {
        $errorMsg = 'Service title and description are required.';
    } else {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE `services` SET `title` = ?, `description` = ?, `icon_class` = ?, `icon_color` = ?, `tags` = ?, `sort_order` = ?, `is_active` = ? WHERE `id` = ?");
            $stmt->execute([$title, $description, $iconClass, $iconColor, $tags, $sortOrder, $isActive, $id]);
            $successMsg = 'Service updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO `services` (`title`, `description`, `icon_class`, `icon_color`, `tags`, `sort_order`, `is_active`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $iconClass, $iconColor, $tags, $sortOrder, $isActive]);
            $successMsg = 'New service added successfully!';
        }
    }
}

// Fetch record for editing
$editService = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM `services` WHERE `id` = ?");
    $stmt->execute([$editId]);
    $editService = $stmt->fetch();
}

$servicesList = getServicesList(false); // fetch all including inactive for admin view
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

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start;">
    <!-- Add / Edit Service Form -->
    <div class="content-card">
        <div class="card-header">
            <h3>
                <i class="fa-solid <?= $editService ? 'fa-pen-to-square' : 'fa-plus' ?>"></i>
                <?= $editService ? 'Edit Service' : 'Add New Service' ?>
            </h3>
            <?php if ($editService): ?>
                <a href="services.php" class="btn btn-outline btn-sm">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <form action="services.php" method="POST">
            <?php if ($editService): ?>
                <input type="hidden" name="id" value="<?= $editService['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="title">Service Title <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Professional Android App Development" value="<?= htmlspecialchars($editService['title'] ?? '') ?>" required oninput="previewService()">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="icon_class">FontAwesome Icon Class</label>
                    <input type="text" id="icon_class" name="icon_class" class="form-control" placeholder="fa-brands fa-android" value="<?= htmlspecialchars($editService['icon_class'] ?? 'fa-brands fa-android') ?>" oninput="previewService()">
                    <small style="color: var(--adm-text-muted);">e.g. fa-brands fa-android, fa-solid fa-layer-group, fa-solid fa-wand-magic-sparkles, fa-solid fa-file-lines</small>
                </div>

                <div class="form-group">
                    <label for="icon_color">Theme Color</label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="color" id="icon_color_picker" value="<?= htmlspecialchars($editService['icon_color'] ?? '#3ddc84') ?>" style="width: 45px; height: 38px; padding: 2px; border: 1px solid var(--adm-border); border-radius: var(--adm-radius-sm); background: transparent; cursor: pointer;" oninput="document.getElementById('icon_color').value = this.value; previewService();">
                        <input type="text" id="icon_color" name="icon_color" class="form-control" value="<?= htmlspecialchars($editService['icon_color'] ?? '#3ddc84') ?>" placeholder="#3ddc84" oninput="document.getElementById('icon_color_picker').value = this.value; previewService();">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Service Description <span style="color: var(--adm-danger);">*</span></label>
                <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief summary of this service offerings..." required oninput="previewService()"><?= htmlspecialchars($editService['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="tags">Specialty Skills / Tags <small>(Comma separated)</small></label>
                <input type="text" id="tags" name="tags" class="form-control" placeholder="Android Studio, Java, Material UI, APIs" value="<?= htmlspecialchars($editService['tags'] ?? '') ?>" oninput="previewService()">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="sort_order">Display Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editService['sort_order'] ?? 1) ?>" min="0">
                </div>

                <div class="form-group" style="display: flex; align-items: center; margin-top: 28px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; user-select: none;">
                        <input type="checkbox" name="is_active" value="1" <?= (!isset($editService) || !empty($editService['is_active'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--adm-primary);">
                        <span style="font-weight: 500;">Active on Live Portfolio</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="padding: 12px 24px; margin-top: 10px;">
                <i class="fa-solid <?= $editService ? 'fa-floppy-disk' : 'fa-plus' ?>"></i>
                <?= $editService ? 'Update Service' : 'Add Service' ?>
            </button>
        </form>
    </div>

    <!-- Live Card Preview -->
    <div class="content-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-eye"></i> Real-Time Public Card Preview</h3>
            <span style="font-size: 0.8rem; color: var(--adm-text-muted);">Exact Public Page Appearance</span>
        </div>

        <div style="background-color: var(--adm-card-bg); border: 1px solid var(--adm-border); border-radius: 16px; padding: 28px; position: relative; overflow: hidden;">
            <div id="previewIconBox" style="width: 58px; height: 58px; border-radius: 14px; background-color: rgba(61, 220, 132, 0.15); color: #3ddc84; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 20px;">
                <i id="previewIcon" class="fa-brands fa-android"></i>
            </div>
            <h3 id="previewTitle" style="font-size: 1.25rem; font-weight: 700; color: #ffffff; margin-bottom: 12px; line-height: 1.35;">
                Professional Android App Development
            </h3>
            <p id="previewDesc" style="color: var(--adm-text-muted); font-size: 0.92rem; line-height: 1.6; margin-bottom: 18px;">
                Designing and engineering modern, intuitive native mobile applications with Java, Android Studio, Material Design guidelines, and responsive layouts.
            </p>
            <div id="previewTags" style="display: flex; flex-wrap: wrap; gap: 8px;">
                <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 500; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); color: #818cf8;">Android Studio</span>
                <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 500; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); color: #818cf8;">Java & SDK</span>
                <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.78rem; font-weight: 500; background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2); color: #818cf8;">Material UI</span>
            </div>
        </div>
    </div>
</div>

<!-- Services Table -->
<div class="content-card" style="margin-top: 24px;">
    <div class="card-header">
        <h3><i class="fa-solid fa-list-check"></i> Configured Portfolio Services (<?= count($servicesList) ?>)</h3>
    </div>

    <div class="table-responsive">
        <?php if (!empty($servicesList)): ?>
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Order</th>
                        <th>Icon</th>
                        <th>Service Title</th>
                        <th>Description</th>
                        <th>Tags</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($servicesList as $srv): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($srv['sort_order']) ?></strong></td>
                            <td>
                                <div style="width: 36px; height: 36px; border-radius: 8px; background-color: <?= htmlspecialchars($srv['icon_color']) ?>26; color: <?= htmlspecialchars($srv['icon_color']) ?>; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                    <i class="<?= htmlspecialchars($srv['icon_class']) ?>"></i>
                                </div>
                            </td>
                            <td><strong style="color: #ffffff;"><?= htmlspecialchars($srv['title']) ?></strong></td>
                            <td style="max-width: 280px; color: var(--adm-text-muted); font-size: 0.85rem; line-height: 1.4;">
                                <?= htmlspecialchars(mb_strimwidth($srv['description'], 0, 90, '...')) ?>
                            </td>
                            <td style="max-width: 200px;">
                                <?php 
                                $tList = array_map('trim', explode(',', $srv['tags'] ?? ''));
                                foreach (array_slice($tList, 0, 3) as $t): 
                                    if (empty($t)) continue;
                                ?>
                                    <span style="display: inline-block; padding: 2px 6px; font-size: 0.72rem; border-radius: 4px; background: rgba(255,255,255,0.06); margin: 2px; color: var(--adm-text);"><?= htmlspecialchars($t) ?></span>
                                <?php endforeach; ?>
                                <?php if (count($tList) > 3): ?>
                                    <span style="font-size: 0.72rem; color: var(--adm-text-muted);">+<?= count($tList) - 3 ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="services.php?toggle=<?= $srv['id'] ?>" title="Click to toggle status" style="text-decoration: none;">
                                    <?php if (!empty($srv['is_active'])): ?>
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="fa-solid fa-circle-check"></i> Active
                                        </span>
                                    <?php else: ?>
                                        <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);">
                                            <i class="fa-solid fa-circle-pause"></i> Inactive
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <a href="services.php?edit=<?= $srv['id'] ?>" class="btn btn-outline btn-sm" title="Edit Service">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="services.php?delete=<?= $srv['id'] ?>" class="btn btn-outline btn-sm" style="color: var(--adm-danger); border-color: rgba(239,68,68,0.3);" onclick="return confirm('Are you sure you want to delete this service?');" title="Delete Service">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: var(--adm-text-muted); text-align: center; padding: 24px 0;">No services configured. Add your first service above.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function previewService() {
    const title = document.getElementById('title').value || 'Service Title Here';
    const desc = document.getElementById('description').value || 'Service description will appear here as you type...';
    const iconClass = document.getElementById('icon_class').value || 'fa-solid fa-code';
    const iconColor = document.getElementById('icon_color').value || '#6366f1';
    const tagsVal = document.getElementById('tags').value || '';

    document.getElementById('previewTitle').textContent = title;
    document.getElementById('previewDesc').textContent = desc;
    document.getElementById('previewIcon').className = iconClass;
    
    const iconBox = document.getElementById('previewIconBox');
    iconBox.style.color = iconColor;
    iconBox.style.backgroundColor = iconColor + '26';

    const tagsContainer = document.getElementById('previewTags');
    tagsContainer.innerHTML = '';
    if (tagsVal.trim() !== '') {
        const tags = tagsVal.split(',');
        tags.forEach(t => {
            if (t.trim()) {
                const span = document.createElement('span');
                span.style.padding = '4px 10px';
                span.style.borderRadius = '6px';
                span.style.fontSize = '0.78rem';
                span.style.fontWeight = '500';
                span.style.background = 'rgba(99, 102, 241, 0.1)';
                span.style.border = '1px solid rgba(99, 102, 241, 0.2)';
                span.style.color = '#818cf8';
                span.textContent = t.trim();
                tagsContainer.appendChild(span);
            }
        });
    }
}

// Initial preview setup on load
document.addEventListener('DOMContentLoaded', () => {
    previewService();
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
