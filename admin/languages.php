<?php
$pageTitle = 'Manage Languages';
require_once __DIR__ . '/header.php';

$pdo = getDBConnection();
$successMsg = '';
$errorMsg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM `languages` WHERE `id` = ?");
    if ($stmt->execute([$delId])) {
        $successMsg = 'Language deleted successfully!';
    } else {
        $errorMsg = 'Failed to delete language.';
    }
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $name = trim($_POST['name'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $iconClass = trim($_POST['icon_class'] ?? 'fa-solid fa-language');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);

    if (empty($name) || empty($level)) {
        $errorMsg = 'Language name and proficiency level are required.';
    } else {
        if ($id > 0) {
            $stmt = $pdo->prepare("UPDATE `languages` SET `name` = ?, `level` = ?, `icon_class` = ?, `sort_order` = ? WHERE `id` = ?");
            $stmt->execute([$name, $level, $iconClass, $sortOrder, $id]);
            $successMsg = 'Language updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO `languages` (`name`, `level`, `icon_class`, `sort_order`) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $level, $iconClass, $sortOrder]);
            $successMsg = 'New language added successfully!';
        }
    }
}

// Fetch record for editing
$editLang = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM `languages` WHERE `id` = ?");
    $stmt->execute([$editId]);
    $editLang = $stmt->fetch();
}

$languagesList = getLanguagesList();
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
    <!-- Add / Edit Language Form -->
    <div class="content-card">
        <div class="card-header">
            <h3>
                <i class="fa-solid <?= $editLang ? 'fa-pen-to-square' : 'fa-plus' ?>"></i>
                <?= $editLang ? 'Edit Language' : 'Add New Language' ?>
            </h3>
            <?php if ($editLang): ?>
                <a href="languages.php" class="btn btn-outline btn-sm">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <form action="languages.php" method="POST">
            <?php if ($editLang): ?>
                <input type="hidden" name="id" value="<?= $editLang['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Language Name <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="name" name="name" class="form-control" placeholder="e.g. Urdu, English, Punjabi" value="<?= htmlspecialchars($editLang['name'] ?? '') ?>" required oninput="previewLanguage()">
            </div>

            <div class="form-group">
                <label for="level">Proficiency Level <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="level" name="level" class="form-control" placeholder="e.g. Native / Strong, Basic, Fluent" value="<?= htmlspecialchars($editLang['level'] ?? '') ?>" required oninput="previewLanguage()">
                <small style="color: var(--adm-text-muted); margin-top: 4px;">Quick suggestions: Native / Strong, Fluent, Professional Working, Basic</small>
            </div>

            <div class="form-group">
                <label for="icon_class">FontAwesome Icon Class</label>
                <input type="text" id="icon_class" name="icon_class" class="form-control" placeholder="fa-solid fa-language" value="<?= htmlspecialchars($editLang['icon_class'] ?? 'fa-solid fa-language') ?>" oninput="previewLanguage()">
            </div>

            <div class="form-group">
                <label for="sort_order">Display Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editLang['sort_order'] ?? '1') ?>">
            </div>

            <!-- Live Preview -->
            <div class="form-group">
                <label>Card Live Preview</label>
                <div style="background-color: var(--adm-sidebar); border: 1px solid var(--adm-border); border-radius: var(--adm-radius-sm); padding: 16px; display: flex; align-items: center; gap: 14px;">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(99, 102, 241, 0.15); color: var(--adm-primary); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
                        <i id="previewLangIcon" class="<?= htmlspecialchars($editLang['icon_class'] ?? 'fa-solid fa-language') ?>"></i>
                    </div>
                    <div>
                        <h4 id="previewLangName" style="color: #ffffff; font-size: 1rem; font-weight: 600; margin: 0;"><?= htmlspecialchars($editLang['name'] ?? 'Language Name') ?></h4>
                        <span id="previewLangLevel" style="font-size: 0.82rem; color: var(--adm-text-muted);"><?= htmlspecialchars($editLang['level'] ?? 'Proficiency Level') ?></span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fa-solid fa-floppy-disk"></i>
                <?= $editLang ? 'Update Language' : 'Save Language' ?>
            </button>
        </form>
    </div>

    <!-- Instructions / Guidelines -->
    <div class="content-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-circle-info"></i> Language Display Tips</h3>
        </div>
        <p style="color: var(--adm-text-muted); line-height: 1.6; margin-bottom: 16px;">
            Languages will be rendered in the <strong>Languages</strong> section on your portfolio home page.
        </p>
        <ul style="color: var(--adm-text-muted); line-height: 1.8; padding-left: 20px; margin-bottom: 20px;">
            <li>Current setup: <strong>Urdu</strong> (Native / Strong) and <strong>English</strong> (Basic).</li>
            <li>You can change the proficiency labels at any time.</li>
            <li>Icons can use any FontAwesome free icon class (e.g. <code>fa-solid fa-language</code>, <code>fa-solid fa-globe</code>, <code>fa-solid fa-comment-dots</code>).</li>
            <li>Sort order determines which language card appears first on your website.</li>
        </ul>
    </div>
</div>

<!-- List of Existing Languages -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fa-solid fa-list-check"></i> Current Languages</h3>
    </div>

    <div class="table-responsive">
        <?php if (!empty($languagesList)): ?>
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Icon</th>
                        <th>Language</th>
                        <th>Proficiency Level</th>
                        <th style="width: 100px;">Sort Order</th>
                        <th style="width: 140px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($languagesList as $lang): ?>
                        <tr>
                            <td>
                                <div style="width: 38px; height: 38px; border-radius: 8px; background: rgba(99, 102, 241, 0.15); color: var(--adm-primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                                    <i class="<?= htmlspecialchars($lang['icon_class'] ?? 'fa-solid fa-language') ?>"></i>
                                </div>
                            </td>
                            <td>
                                <strong style="color: #ffffff;"><?= htmlspecialchars($lang['name']) ?></strong>
                            </td>
                            <td>
                                <span style="background: rgba(16, 185, 129, 0.15); color: var(--adm-success); border: 1px solid rgba(16, 185, 129, 0.3); padding: 4px 10px; border-radius: 6px; font-size: 0.85rem; font-weight: 500;">
                                    <?= htmlspecialchars($lang['level']) ?>
                                </span>
                            </td>
                            <td>
                                <span style="background: rgba(255,255,255,0.06); padding: 4px 10px; border-radius: 6px; font-weight: 600;">
                                    <?= $lang['sort_order'] ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                    <a href="languages.php?edit=<?= $lang['id'] ?>" class="btn btn-outline btn-sm" title="Edit">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="languages.php?delete=<?= $lang['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this language?');" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: var(--adm-text-muted);">No languages configured. Add one using the form above.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function previewLanguage() {
    const name = document.getElementById('name').value.trim() || 'Language Name';
    const level = document.getElementById('level').value.trim() || 'Proficiency Level';
    const icon = document.getElementById('icon_class').value.trim() || 'fa-solid fa-language';

    const pIcon = document.getElementById('previewLangIcon');
    const pName = document.getElementById('previewLangName');
    const pLevel = document.getElementById('previewLangLevel');

    if (pIcon) pIcon.className = icon;
    if (pName) pName.textContent = name;
    if (pLevel) pLevel.textContent = level;
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
