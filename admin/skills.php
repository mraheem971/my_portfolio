<?php
$pageTitle = 'Manage Skills & Categories';
require_once __DIR__ . '/header.php';

$pdo = getDBConnection();
$successMsg = '';
$errorMsg = '';

// ------------------------------------------------------------------
// 1. Actions: Skill Categories (Add / Edit / Delete)
// ------------------------------------------------------------------
if (isset($_GET['delete_cat'])) {
    $delCatId = (int)$_GET['delete_cat'];
    // Delete skills under category first or rely on cascade
    $stmt = $pdo->prepare("DELETE FROM `skill_categories` WHERE `id` = ?");
    if ($stmt->execute([$delCatId])) {
        $successMsg = 'Skill category and associated skills deleted successfully!';
    } else {
        $errorMsg = 'Could not delete category.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_category'])) {
    $catId = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;
    $catName = trim($_POST['cat_name'] ?? '');
    $catIcon = trim($_POST['cat_icon_class'] ?? 'fa-solid fa-code');
    $catSort = (int)($_POST['cat_sort_order'] ?? 0);

    if (empty($catName)) {
        $errorMsg = 'Category name cannot be empty.';
    } else {
        if ($catId > 0) {
            $stmt = $pdo->prepare("UPDATE `skill_categories` SET `name` = ?, `icon_class` = ?, `sort_order` = ? WHERE `id` = ?");
            $stmt->execute([$catName, $catIcon, $catSort, $catId]);
            $successMsg = 'Skill category updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO `skill_categories` (`name`, `icon_class`, `sort_order`) VALUES (?, ?, ?)");
            $stmt->execute([$catName, $catIcon, $catSort]);
            $successMsg = 'New skill category created successfully!';
        }
    }
}

// ------------------------------------------------------------------
// 2. Actions: Skills (Add / Edit / Delete)
// ------------------------------------------------------------------
if (isset($_GET['delete_skill'])) {
    $delSkillId = (int)$_GET['delete_skill'];
    $stmt = $pdo->prepare("DELETE FROM `skills` WHERE `id` = ?");
    if ($stmt->execute([$delSkillId])) {
        $successMsg = 'Skill deleted successfully!';
    } else {
        $errorMsg = 'Could not delete skill.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_skill'])) {
    $skillId = isset($_POST['skill_id']) ? (int)$_POST['skill_id'] : 0;
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $skillName = trim($_POST['skill_name'] ?? '');
    $iconClass = trim($_POST['icon_class'] ?? 'fa-solid fa-code');
    $iconColor = trim($_POST['icon_color'] ?? '#6366f1');
    $skillSort = (int)($_POST['skill_sort_order'] ?? 0);

    if (empty($skillName) || $categoryId <= 0) {
        $errorMsg = 'Please provide a skill name and select a valid category.';
    } else {
        if ($skillId > 0) {
            $stmt = $pdo->prepare("UPDATE `skills` SET `category_id` = ?, `name` = ?, `icon_class` = ?, `icon_color` = ?, `sort_order` = ? WHERE `id` = ?");
            $stmt->execute([$categoryId, $skillName, $iconClass, $iconColor, $skillSort, $skillId]);
            $successMsg = 'Skill updated successfully!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO `skills` (`category_id`, `name`, `icon_class`, `icon_color`, `sort_order`) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$categoryId, $skillName, $iconClass, $iconColor, $skillSort]);
            $successMsg = 'New skill added successfully!';
        }
    }
}

// Edit Fetching
$editSkill = null;
if (isset($_GET['edit_skill'])) {
    $editSkillId = (int)$_GET['edit_skill'];
    $stmt = $pdo->prepare("SELECT * FROM `skills` WHERE `id` = ?");
    $stmt->execute([$editSkillId]);
    $editSkill = $stmt->fetch();
}

$editCategory = null;
if (isset($_GET['edit_cat'])) {
    $editCatId = (int)$_GET['edit_cat'];
    $stmt = $pdo->prepare("SELECT * FROM `skill_categories` WHERE `id` = ?");
    $stmt->execute([$editCatId]);
    $editCategory = $stmt->fetch();
}

$categoriesWithSkills = getSkillCategoriesWithSkills();
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
    <!-- Column 1: Add/Edit Skill Form -->
    <div class="content-card">
        <div class="card-header">
            <h3>
                <i class="fa-solid <?= $editSkill ? 'fa-pen-to-square' : 'fa-plus' ?>"></i>
                <?= $editSkill ? 'Edit Skill' : 'Add New Skill' ?>
            </h3>
            <?php if ($editSkill): ?>
                <a href="skills.php" class="btn btn-outline btn-sm">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <form action="skills.php" method="POST">
            <input type="hidden" name="action_skill" value="1">
            <?php if ($editSkill): ?>
                <input type="hidden" name="skill_id" value="<?= $editSkill['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="category_id">Category <span style="color: var(--adm-danger);">*</span></label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categoriesWithSkills as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($editSkill && $editSkill['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="skill_name">Skill / Tool Name <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="skill_name" name="skill_name" class="form-control" placeholder="e.g. Python, Android, JavaScript" value="<?= htmlspecialchars($editSkill['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="icon_class">FontAwesome Icon Class <small>(e.g. fa-brands fa-python)</small></label>
                <input type="text" id="icon_class" name="icon_class" class="form-control" placeholder="fa-brands fa-python" value="<?= htmlspecialchars($editSkill['icon_class'] ?? 'fa-solid fa-code') ?>" required oninput="previewSkillIcon()">
            </div>

            <div class="form-group">
                <label for="icon_color">Icon Brand Color</label>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="color" id="icon_color_picker" value="<?= htmlspecialchars($editSkill['icon_color'] ?? '#6366f1') ?>" style="width: 48px; height: 42px; border: 1px solid var(--adm-border); border-radius: var(--adm-radius-sm); background: transparent; cursor: pointer;" oninput="document.getElementById('icon_color').value = this.value; previewSkillIcon();">
                    <input type="text" id="icon_color" name="icon_color" class="form-control" style="flex: 1;" placeholder="#6366f1" value="<?= htmlspecialchars($editSkill['icon_color'] ?? '#6366f1') ?>" oninput="document.getElementById('icon_color_picker').value = this.value; previewSkillIcon();">
                </div>
            </div>

            <!-- Live Card Preview -->
            <div class="form-group">
                <label>Card Live Preview</label>
                <div style="background-color: var(--adm-sidebar); border: 1px solid var(--adm-border); border-radius: var(--adm-radius-sm); padding: 18px; display: flex; align-items: center; gap: 16px;">
                    <div id="previewIconBox" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: <?= htmlspecialchars($editSkill['icon_color'] ?? '#6366f1') ?>;">
                        <i id="previewIconEl" class="<?= htmlspecialchars($editSkill['icon_class'] ?? 'fa-solid fa-code') ?>"></i>
                    </div>
                    <div>
                        <div id="previewNameEl" style="font-weight: 600; font-size: 1rem; color: #ffffff;">
                            <?= htmlspecialchars($editSkill['name'] ?? 'Skill Name') ?>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--adm-text-muted);">
                            Rendered using portfolio card design
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="skill_sort_order">Display Sort Order</label>
                <input type="number" id="skill_sort_order" name="skill_sort_order" class="form-control" value="<?= htmlspecialchars($editSkill['sort_order'] ?? '1') ?>">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fa-solid fa-floppy-disk"></i>
                <?= $editSkill ? 'Update Skill' : 'Save Skill' ?>
            </button>
        </form>
    </div>

    <!-- Column 2: Add/Edit Category Form -->
    <div class="content-card">
        <div class="card-header">
            <h3>
                <i class="fa-solid <?= $editCategory ? 'fa-pen-to-square' : 'fa-folder-plus' ?>"></i>
                <?= $editCategory ? 'Edit Category' : 'Add New Category' ?>
            </h3>
            <?php if ($editCategory): ?>
                <a href="skills.php" class="btn btn-outline btn-sm">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <form action="skills.php" method="POST">
            <input type="hidden" name="action_category" value="1">
            <?php if ($editCategory): ?>
                <input type="hidden" name="cat_id" value="<?= $editCategory['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="cat_name">Category Title <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="cat_name" name="cat_name" class="form-control" placeholder="e.g. Mobile Development, Programming Languages" value="<?= htmlspecialchars($editCategory['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="cat_icon_class">Header Icon Class</label>
                <input type="text" id="cat_icon_class" name="cat_icon_class" class="form-control" placeholder="fa-solid fa-code" value="<?= htmlspecialchars($editCategory['icon_class'] ?? 'fa-solid fa-code') ?>">
            </div>

            <div class="form-group">
                <label for="cat_sort_order">Category Sort Order</label>
                <input type="number" id="cat_sort_order" name="cat_sort_order" class="form-control" value="<?= htmlspecialchars($editCategory['sort_order'] ?? '1') ?>">
            </div>

            <button type="submit" class="btn btn-success" style="width: 100%;">
                <i class="fa-solid fa-folder-check"></i>
                <?= $editCategory ? 'Update Category' : 'Save Category' ?>
            </button>
        </form>
    </div>
</div>

<!-- Existing Categories & Skills Overview -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fa-solid fa-layer-group"></i> Existing Skill Categories & Items</h3>
    </div>

    <?php if (!empty($categoriesWithSkills)): ?>
        <?php foreach ($categoriesWithSkills as $cat): ?>
            <div style="background-color: var(--adm-sidebar); border: 1px solid var(--adm-border); border-radius: var(--adm-radius); padding: 20px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--adm-border); padding-bottom: 12px; flex-wrap: wrap; gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(99,102,241,0.15); color: var(--adm-primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="<?= htmlspecialchars($cat['icon_class'] ?? 'fa-solid fa-code') ?>"></i>
                        </div>
                        <div>
                            <h4 style="font-size: 1.1rem; font-weight: 700; color: #ffffff;"><?= htmlspecialchars($cat['name']) ?></h4>
                            <span style="font-size: 0.78rem; color: var(--adm-text-muted);">Sort Order: <?= $cat['sort_order'] ?> &bull; <?= count($cat['skills'] ?? []) ?> skills</span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <a href="skills.php?edit_cat=<?= $cat['id'] ?>" class="btn btn-outline btn-sm" title="Edit Category">
                            <i class="fa-solid fa-pen"></i> Edit Category
                        </a>
                        <a href="skills.php?delete_cat=<?= $cat['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deleting this category will also remove all its skills. Are you sure?');" title="Delete Category">
                            <i class="fa-solid fa-trash"></i> Delete
                        </a>
                    </div>
                </div>

                <!-- Skills inside this category -->
                <?php if (!empty($cat['skills'])): ?>
                    <div class="table-responsive">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">Icon</th>
                                    <th>Skill Name</th>
                                    <th>Color Hex</th>
                                    <th style="width: 100px;">Sort Order</th>
                                    <th style="width: 150px; text-align: right;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cat['skills'] as $skill): ?>
                                    <tr>
                                        <td>
                                            <div style="width: 40px; height: 40px; border-radius: 8px; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: <?= htmlspecialchars($skill['icon_color']) ?>;">
                                                <i class="<?= htmlspecialchars($skill['icon_class']) ?>"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <strong style="color: #ffffff;"><?= htmlspecialchars($skill['name']) ?></strong>
                                        </td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <span style="display: inline-block; width: 14px; height: 14px; border-radius: 3px; background-color: <?= htmlspecialchars($skill['icon_color']) ?>; border: 1px solid rgba(255,255,255,0.2);"></span>
                                                <code><?= htmlspecialchars($skill['icon_color']) ?></code>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="background: rgba(255,255,255,0.06); padding: 4px 10px; border-radius: 6px; font-weight: 600;">
                                                <?= $skill['sort_order'] ?>
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                                <a href="skills.php?edit_skill=<?= $skill['id'] ?>" class="btn btn-outline btn-sm" title="Edit">
                                                    <i class="fa-solid fa-pen"></i>
                                                </a>
                                                <a href="skills.php?delete_skill=<?= $skill['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this skill?');" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: var(--adm-text-muted); font-size: 0.9rem; padding: 10px 0;">No skills assigned to this category yet. Use the form on the left to add one.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="color: var(--adm-text-muted);">No categories created yet. Create a category using the form above.</p>
    <?php endif; ?>
</div>

<script>
function previewSkillIcon() {
    const iconClass = document.getElementById('icon_class').value.trim() || 'fa-solid fa-code';
    const iconColor = document.getElementById('icon_color').value.trim() || '#6366f1';
    const skillName = document.getElementById('skill_name').value.trim() || 'Skill Name';
    
    const previewEl = document.getElementById('previewIconEl');
    const previewBox = document.getElementById('previewIconBox');
    const previewName = document.getElementById('previewNameEl');
    
    if (previewEl) previewEl.className = iconClass;
    if (previewBox) previewBox.style.color = iconColor;
    if (previewName) previewName.textContent = skillName;
}

document.getElementById('skill_name')?.addEventListener('input', previewSkillIcon);
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
