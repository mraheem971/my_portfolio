<?php
$pageTitle = 'Manage Education';
require_once __DIR__ . '/header.php';

$pdo = getDBConnection();
$successMsg = '';
$errorMsg = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM `education` WHERE `id` = ?");
    if ($stmt->execute([$delId])) {
        $successMsg = 'Education entry deleted successfully!';
    }
}

// Handle Add / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $degree = trim($_POST['degree'] ?? '');
    $institution = trim($_POST['institution'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $is_in_progress = isset($_POST['is_in_progress']) ? 1 : 0;
    $description = trim($_POST['description'] ?? '');
    $highlights = trim($_POST['highlights'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if (empty($degree) || empty($institution) || empty($duration) || empty($status)) {
        $errorMsg = 'Please complete all required fields (Degree, Institution, Duration, Status).';
    } else {
        if ($id > 0) {
            $stmt = $pdo->prepare("
                UPDATE `education` 
                SET `degree` = ?, `institution` = ?, `duration` = ?, `status` = ?, `is_in_progress` = ?, `description` = ?, `highlights` = ?, `sort_order` = ? 
                WHERE `id` = ?
            ");
            $stmt->execute([$degree, $institution, $duration, $status, $is_in_progress, $description, $highlights, $sort_order, $id]);
            $successMsg = 'Education entry updated successfully!';
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `education` (`degree`, `institution`, `duration`, `status`, `is_in_progress`, `description`, `highlights`, `sort_order`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$degree, $institution, $duration, $status, $is_in_progress, $description, $highlights, $sort_order]);
            $successMsg = 'New education entry added successfully!';
        }
    }
}

// Fetch record for editing if requested
$editRecord = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM `education` WHERE `id` = ?");
    $stmt->execute([$editId]);
    $editRecord = $stmt->fetch();
}

$educationList = getEducationList();
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

<!-- Form: Add or Edit Education -->
<div class="content-card">
    <div class="card-header">
        <h3>
            <i class="fa-solid <?= $editRecord ? 'fa-pen-to-square' : 'fa-plus' ?>"></i>
            <?= $editRecord ? 'Edit Education Entry' : 'Add New Education Entry' ?>
        </h3>
        <?php if ($editRecord): ?>
            <a href="education.php" class="btn btn-outline btn-sm">Cancel Edit</a>
        <?php endif; ?>
    </div>

    <form action="education.php" method="POST">
        <?php if ($editRecord): ?>
            <input type="hidden" name="id" value="<?= $editRecord['id'] ?>">
        <?php endif; ?>

        <div class="form-grid">
            <div class="form-group">
                <label for="degree">Degree / Certificate Name <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="degree" name="degree" class="form-control" placeholder="e.g. BS in Software Engineering" value="<?= htmlspecialchars($editRecord['degree'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="institution">Institution / University <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="institution" name="institution" class="form-control" placeholder="e.g. The Islamia University of Bahawalpur (IUB)" value="<?= htmlspecialchars($editRecord['institution'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="duration">Duration / Years <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="duration" name="duration" class="form-control" placeholder="e.g. 2022 – Present" value="<?= htmlspecialchars($editRecord['duration'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Status Badge Text <span style="color: var(--adm-danger);">*</span></label>
                <input type="text" id="status" name="status" class="form-control" placeholder="e.g. In Progress (Completed 4th Semester)" value="<?= htmlspecialchars($editRecord['status'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="sort_order">Display Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= htmlspecialchars($editRecord['sort_order'] ?? '1') ?>">
            </div>

            <div class="form-group" style="justify-content: center;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-top: 25px;">
                    <input type="checkbox" name="is_in_progress" value="1" <?= !empty($editRecord['is_in_progress']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                    <span>Highlight as Featured / In-Progress Degree</span>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label for="description">Description & Coursework Overview</label>
            <textarea id="description" name="description" class="form-control" rows="3" placeholder="Brief summary of syllabus and learning focus..."><?= htmlspecialchars($editRecord['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="highlights">Key Highlights / Subjects <small>(One highlight per line)</small></label>
            <textarea id="highlights" name="highlights" class="form-control" rows="3" placeholder="Data Structures & Algorithms&#10;Object-Oriented Programming (OOP)"><?= htmlspecialchars($editRecord['highlights'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <?= $editRecord ? 'Update Education' : 'Save Education Entry' ?>
        </button>
    </form>
</div>

<!-- List of Existing Education -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fa-solid fa-list-check"></i> Existing Education Records</h3>
    </div>

    <div class="table-responsive">
        <?php if (!empty($educationList)): ?>
            <table class="adm-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Degree</th>
                        <th>Institution</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($educationList as $edu): ?>
                        <tr>
                            <td><?= $edu['sort_order'] ?></td>
                            <td><strong><?= htmlspecialchars($edu['degree']) ?></strong></td>
                            <td><?= htmlspecialchars($edu['institution']) ?></td>
                            <td><?= htmlspecialchars($edu['duration']) ?></td>
                            <td>
                                <span class="nav-badge" style="background-color: <?= !empty($edu['is_in_progress']) ? 'var(--adm-info)' : 'var(--adm-success)' ?>;">
                                    <?= htmlspecialchars($edu['status']) ?>
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <a href="education.php?edit=<?= $edu['id'] ?>" class="btn btn-outline btn-sm">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <a href="education.php?delete=<?= $edu['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this education entry?');">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: var(--adm-text-muted); text-align: center; padding: 20px;">No education records added yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
