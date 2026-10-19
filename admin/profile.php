<?php
$pageTitle = 'Profile & Portfolio Settings';
require_once __DIR__ . '/header.php';

$successMsg = '';
$errorMsg = '';

// Handle Settings Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        $fields = [
            'hero_greeting',
            'hero_name',
            'hero_title',
            'hero_desc',
            'father_name',
            'education_summary',
            'institution_name',
            'hometown',
            'about_bio_1',
            'about_bio_2',
            'whatsapp_number',
            'whatsapp_link',
            'email_address',
            'instagram_handle',
            'instagram_link'
        ];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                updateSetting($field, trim($_POST[$field]));
            }
        }
        $successMsg = 'Portfolio settings updated successfully!';
    } elseif ($_POST['action'] === 'change_password') {
        $currentPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (empty($currentPass) || empty($newPass) || empty($confirmPass)) {
            $errorMsg = 'Please complete all password fields.';
        } elseif ($newPass !== $confirmPass) {
            $errorMsg = 'New passwords do not match.';
        } elseif (strlen($newPass) < 6) {
            $errorMsg = 'New password must be at least 6 characters long.';
        } else {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `id` = ?");
            $stmt->execute([$_SESSION['admin_id'] ?? 1]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($currentPass, $admin['password_hash'])) {
                $newHash = password_hash($newPass, PASSWORD_DEFAULT);
                $upd = $pdo->prepare("UPDATE `admin_users` SET `password_hash` = ? WHERE `id` = ?");
                $upd->execute([$newHash, $admin['id']]);
                $successMsg = 'Password changed successfully!';
            } else {
                $errorMsg = 'Current password is incorrect.';
            }
        }
    }
}

$settings = getSettings();
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

<!-- Portfolio Hero & About Details Form -->
<form action="profile.php" method="POST">
    <input type="hidden" name="action" value="update_profile">

    <div class="content-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-wand-magic-sparkles"></i> Hero & Introduction Section</h3>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="hero_greeting">Greeting Text</label>
                <input type="text" id="hero_greeting" name="hero_greeting" class="form-control" value="<?= htmlspecialchars($settings['hero_greeting'] ?? "Assalam-o-Alaikum, I'm") ?>" required>
            </div>

            <div class="form-group">
                <label for="hero_name">Full Name</label>
                <input type="text" id="hero_name" name="hero_name" class="form-control" value="<?= htmlspecialchars($settings['hero_name'] ?? 'Malik Raheem Bukhsh') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="hero_title">Professional Headline / Subtitle <small>(HTML allowed, e.g. &lt;span class="highlight"&gt;...&lt;/span&gt;)</small></label>
            <input type="text" id="hero_title" name="hero_title" class="form-control" value="<?= htmlspecialchars($settings['hero_title'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="hero_desc">Hero Biography & Elevator Pitch</label>
            <textarea id="hero_desc" name="hero_desc" class="form-control" rows="3"><?= htmlspecialchars($settings['hero_desc'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-id-card"></i> Personal Background Details</h3>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="father_name">Father's Name</label>
                <input type="text" id="father_name" name="father_name" class="form-control" value="<?= htmlspecialchars($settings['father_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="education_summary">Education Summary</label>
                <input type="text" id="education_summary" name="education_summary" class="form-control" value="<?= htmlspecialchars($settings['education_summary'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="institution_name">Institution Name</label>
                <input type="text" id="institution_name" name="institution_name" class="form-control" value="<?= htmlspecialchars($settings['institution_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="hometown">Hometown / Location</label>
                <input type="text" id="hometown" name="hometown" class="form-control" value="<?= htmlspecialchars($settings['hometown'] ?? '') ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="about_bio_1">About Me Paragraph 1</label>
            <textarea id="about_bio_1" name="about_bio_1" class="form-control" rows="3"><?= htmlspecialchars($settings['about_bio_1'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="about_bio_2">About Me Paragraph 2</label>
            <textarea id="about_bio_2" name="about_bio_2" class="form-control" rows="3"><?= htmlspecialchars($settings['about_bio_2'] ?? '') ?></textarea>
        </div>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h3><i class="fa-solid fa-address-book"></i> Contact Channels & Social Profiles</h3>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="whatsapp_number">WhatsApp Display Number</label>
                <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_number'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="whatsapp_link">WhatsApp Direct URL</label>
                <input type="url" id="whatsapp_link" name="whatsapp_link" class="form-control" value="<?= htmlspecialchars($settings['whatsapp_link'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email_address">Public Email Address</label>
                <input type="email" id="email_address" name="email_address" class="form-control" value="<?= htmlspecialchars($settings['email_address'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="instagram_handle">Instagram Username / Handle</label>
                <input type="text" id="instagram_handle" name="instagram_handle" class="form-control" value="<?= htmlspecialchars($settings['instagram_handle'] ?? '') ?>" required>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="instagram_link">Instagram Profile URL</label>
                <input type="url" id="instagram_link" name="instagram_link" class="form-control" value="<?= htmlspecialchars($settings['instagram_link'] ?? '') ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 12px 28px;">
            <i class="fa-solid fa-floppy-disk"></i> Save Portfolio Settings
        </button>
    </div>
</form>

<!-- Change Password Card -->
<div class="content-card">
    <div class="card-header">
        <h3><i class="fa-solid fa-lock"></i> Change Admin Password</h3>
    </div>

    <form action="profile.php" method="POST" style="max-width: 520px;">
        <input type="hidden" name="action" value="change_password">

        <div class="form-group">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="new_password">New Password <small>(minimum 6 characters)</small></label>
            <input type="password" id="new_password" name="new_password" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-outline" style="padding: 10px 22px;">
            <i class="fa-solid fa-key"></i> Update Password
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
