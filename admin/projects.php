<?php
$pageTitle = 'Manage Projects';
require_once __DIR__ . '/header.php';

$pdo = getDBConnection();
$successMsg = '';
$errorMsg = '';

// ------------------------------------------------------------------
// 1. Delete Project
// ------------------------------------------------------------------
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM `projects` WHERE `id` = ?");
    if ($stmt->execute([$delId])) {
        $successMsg = 'Project deleted successfully!';
    } else {
        $errorMsg = 'Could not delete project.';
    }
}

// ------------------------------------------------------------------
// 2. Toggle Featured Status
// ------------------------------------------------------------------
if (isset($_GET['toggle_featured'])) {
    $toggleId = (int)$_GET['toggle_featured'];
    $stmt = $pdo->prepare("UPDATE `projects` SET `is_featured` = IF(`is_featured` = 1, 0, 1) WHERE `id` = ?");
    if ($stmt->execute([$toggleId])) {
        $successMsg = 'Project featured status updated!';
    }
}

// ------------------------------------------------------------------
// 3. Add or Update Project
// ------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_project'])) {
    $projectId = isset($_POST['project_id']) ? (int)$_POST['project_id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $tagline = trim($_POST['tagline'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $technologies = trim($_POST['technologies'] ?? '');
    $githubUrl = trim($_POST['github_url'] ?? '');
    $liveUrl = trim($_POST['live_url'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $sortOrder = (int)($_POST['sort_order'] ?? 1);
    $imageUrl = trim($_POST['image_url'] ?? '');

    // Handle Image File Upload if provided
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'svg'];

        if (in_array($fileExt, $allowedExts)) {
            $safeName = 'proj_' . time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $fileName);
            $targetDir = __DIR__ . '/../assets/';
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $targetFile = $targetDir . $safeName;
            if (move_uploaded_file($fileTmp, $targetFile)) {
                $imageUrl = 'assets/' . $safeName;
            } else {
                $errorMsg = 'Failed to upload project screenshot image.';
            }
        } else {
            $errorMsg = 'Invalid image format. Allowed formats: PNG, JPG, JPEG, WEBP, SVG.';
        }
    }

    if (empty($title) || empty($description) || empty($technologies)) {
        $errorMsg = 'Please provide Project Title, Description, and Technologies.';
    } elseif (empty($errorMsg)) {
        if ($projectId > 0) {
            // Update
            $sql = "UPDATE `projects` SET 
                    `title` = ?, 
                    `tagline` = ?, 
                    `description` = ?, 
                    `image_url` = ?, 
                    `github_url` = ?, 
                    `live_url` = ?, 
                    `technologies` = ?, 
                    `is_featured` = ?, 
                    `sort_order` = ? 
                    WHERE `id` = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$title, $tagline, $description, $imageUrl, $githubUrl, $liveUrl, $technologies, $isFeatured, $sortOrder, $projectId])) {
                $successMsg = 'Project updated successfully!';
            } else {
                $errorMsg = 'Failed to update project.';
            }
        } else {
            // Insert
            $sql = "INSERT INTO `projects` 
                    (`title`, `tagline`, `description`, `image_url`, `github_url`, `live_url`, `technologies`, `is_featured`, `sort_order`, `created_at`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$title, $tagline, $description, $imageUrl, $githubUrl, $liveUrl, $technologies, $isFeatured, $sortOrder])) {
                $successMsg = 'New project created successfully!';
            } else {
                $errorMsg = 'Failed to create new project.';
            }
        }
    }
}

// ------------------------------------------------------------------
// 4. Fetch Edit Project
// ------------------------------------------------------------------
$editProject = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $editProject = getProjectById($editId);
}

// Fetch all projects
$allProjects = getProjectsList();
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

<!-- Form Card: Add / Edit Project -->
<div class="content-card">
    <div class="card-header">
        <h3>
            <i class="fa-solid fa-<?= $editProject ? 'pen-to-square' : 'plus-circle' ?>"></i>
            <?= $editProject ? 'Edit Project: ' . htmlspecialchars($editProject['title']) : 'Add New Portfolio Project' ?>
        </h3>
        <?php if ($editProject): ?>
            <a href="projects.php" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-xmark"></i> Cancel Edit
            </a>
        <?php endif; ?>
    </div>

    <form method="POST" action="projects.php" enctype="multipart/form-data">
        <input type="hidden" name="action_project" value="1">
        <?php if ($editProject): ?>
            <input type="hidden" name="project_id" value="<?= $editProject['id'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group" style="flex: 2;">
                <label for="title">Project Title *</label>
                <input type="text" id="title" name="title" class="form-control" required
                       placeholder="e.g. SocialMate – WhatsApp Bot SaaS Dashboard"
                       value="<?= htmlspecialchars($editProject['title'] ?? '') ?>">
            </div>

            <div class="form-group" style="flex: 2;">
                <label for="tagline">Tagline / Subtitle <small>(Optional)</small></label>
                <input type="text" id="tagline" name="tagline" class="form-control"
                       placeholder="e.g. WhatsApp Automation & Marketing SaaS Platform"
                       value="<?= htmlspecialchars($editProject['tagline'] ?? '') ?>">
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="sort_order">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control"
                       value="<?= htmlspecialchars($editProject['sort_order'] ?? '1') ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="technologies">Technologies & Stack * <small>(Comma-separated, e.g. Blade, PHP, Laravel, CSS, JavaScript)</small></label>
            <input type="text" id="technologies" name="technologies" class="form-control" required
                   placeholder="Blade, PHP, Laravel, CSS, JavaScript"
                   value="<?= htmlspecialchars($editProject['technologies'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="description">Project Description * <small>(Detailed overview of features, architecture, and purpose)</small></label>
            <textarea id="description" name="description" class="form-control" rows="5" required
                      placeholder="Describe the platform features, integrations, and capabilities..."><?= htmlspecialchars($editProject['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group" style="flex: 1;">
                <label for="live_url">Live Project URL <small>(Optional)</small></label>
                <input type="url" id="live_url" name="live_url" class="form-control"
                       placeholder="https://socialmate.iamraheem.com"
                       value="<?= htmlspecialchars($editProject['live_url'] ?? '') ?>">
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="github_url">GitHub Repository URL <small>(Optional)</small></label>
                <input type="url" id="github_url" name="github_url" class="form-control"
                       placeholder="https://github.com/mraheem971/auto-whatsapp.git"
                       value="<?= htmlspecialchars($editProject['github_url'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group" style="flex: 1;">
                <label for="image_url">Image Path / URL <small>(e.g. assets/socialmate.png)</small></label>
                <input type="text" id="image_url" name="image_url" class="form-control"
                       placeholder="assets/socialmate.png"
                       value="<?= htmlspecialchars($editProject['image_url'] ?? 'assets/socialmate.png') ?>">
            </div>

            <div class="form-group" style="flex: 1;">
                <label for="image_file">Or Upload Image / Screenshot <small>(PNG, JPG, WEBP)</small></label>
                <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*">
            </div>
        </div>

        <div class="form-group" style="margin-top: 10px;">
            <label style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer; user-select: none;">
                <input type="checkbox" name="is_featured" value="1" <?= (!empty($editProject['is_featured']) || !$editProject) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                <span><strong>Featured Project</strong> (Highlight prominently in portfolio showcase)</span>
            </label>
        </div>

        <div style="margin-top: 24px; display: flex; gap: 12px;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-<?= $editProject ? 'floppy-disk' : 'plus' ?>"></i>
                <?= $editProject ? 'Update Project' : 'Add Project to Showcase' ?>
            </button>
            <?php if ($editProject): ?>
                <a href="projects.php" class="btn btn-outline">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Projects Table Card -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fa-solid fa-laptop-code"></i> Existing Projects (<?= count($allProjects) ?>)</h3>
    </div>

    <?php if (empty($allProjects)): ?>
        <p style="color: var(--adm-text-muted); text-align: center; padding: 30px 0;">No projects added yet. Use the form above to add your first project.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="adm-table">
                <thead>
                    <tr>
                        <th style="width: 90px;">Preview</th>
                        <th>Title & Tagline</th>
                        <th>Technologies</th>
                        <th>Links</th>
                        <th style="width: 110px; text-align: center;">Featured</th>
                        <th style="width: 70px; text-align: center;">Order</th>
                        <th style="width: 130px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allProjects as $p): ?>
                        <tr>
                            <td>
                                <?php if (!empty($p['image_url'])): ?>
                                    <div style="width: 74px; height: 46px; border-radius: 8px; overflow: hidden; border: 1px solid var(--adm-border); background-color: #0f172a;">
                                        <img src="../<?= htmlspecialchars($p['image_url']) ?>" alt="thumb" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                <?php else: ?>
                                    <div style="width: 74px; height: 46px; border-radius: 8px; background-color: rgba(255, 255, 255, 0.05); display: flex; align-items: center; justify-content: center; color: var(--adm-text-muted);">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong style="color: #ffffff; font-size: 0.95rem;"><?= htmlspecialchars($p['title']) ?></strong>
                                <?php if (!empty($p['tagline'])): ?>
                                    <div style="color: var(--adm-text-muted); font-size: 0.82rem; margin-top: 3px;"><?= htmlspecialchars($p['tagline']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                                    <?php 
                                    $tItems = array_map('trim', explode(',', $p['technologies']));
                                    foreach ($tItems as $t): 
                                        if (empty($t)) continue;
                                    ?>
                                        <span style="font-size: 0.74rem; padding: 2px 8px; border-radius: 4px; background-color: rgba(99, 102, 241, 0.15); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.3);">
                                            <?= htmlspecialchars($t) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <?php if (!empty($p['live_url'])): ?>
                                        <a href="<?= htmlspecialchars($p['live_url']) ?>" target="_blank" class="btn btn-outline btn-sm" title="Live Preview" style="padding: 4px 10px; font-size: 0.8rem;">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Live
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($p['github_url'])): ?>
                                        <a href="<?= htmlspecialchars($p['github_url']) ?>" target="_blank" class="btn btn-outline btn-sm" title="GitHub Code" style="padding: 4px 10px; font-size: 0.8rem;">
                                            <i class="fa-brands fa-github"></i> Code
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <a href="projects.php?toggle_featured=<?= $p['id'] ?>" title="Click to toggle featured status">
                                    <?php if (!empty($p['is_featured'])): ?>
                                        <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; font-weight: 600; padding: 3px 8px; border-radius: 12px; background-color: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                            <i class="fa-solid fa-star"></i> Featured
                                        </span>
                                    <?php else: ?>
                                        <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; font-weight: 500; padding: 3px 8px; border-radius: 12px; background-color: rgba(255, 255, 255, 0.05); color: var(--adm-text-muted); border: 1px solid var(--adm-border);">
                                            Standard
                                        </span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td style="text-align: center; color: var(--adm-text-muted); font-weight: 600;">
                                <?= $p['sort_order'] ?>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px;">
                                    <a href="projects.php?edit=<?= $p['id'] ?>" class="btn btn-outline btn-sm" title="Edit Project" style="padding: 6px 10px;">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="projects.php?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" title="Delete Project" style="padding: 6px 10px;" onclick="return confirm('Are you sure you want to delete this project?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/footer.php';
