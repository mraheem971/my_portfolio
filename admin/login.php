<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if (isAdminLoggedIn()) {
    header("Location: index.php");
    exit;
}

// Secret Access Gate: Deny regular visitors and redirect to public homepage
if (isset($_GET['key']) && $_GET['key'] === ADMIN_SECRET_KEY) {
    $_SESSION['admin_gate_unlocked'] = true;
}

if (empty($_SESSION['admin_gate_unlocked'])) {
    header("Location: ../index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `username` = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_email'] = $user['email'];

            header("Location: index.php");
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Malik Raheem Portfolio</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Fira+Code:wght@500&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="admin.css">
</head>
<body class="login-body">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2>Portfolio Admin Panel</h2>
            <p>Enter your credentials to manage your portfolio</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username"><i class="fa-solid fa-user"></i> Username</label>
                <input type="text" id="username" name="username" class="form-control" placeholder="admin" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 10px; padding: 13px;">
                <span>Sign In to Dashboard</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--adm-border); text-align: center;">
            <p style="font-size: 0.8rem; color: var(--adm-text-muted);">
                <i class="fa-solid fa-key" style="color: var(--adm-primary);"></i> Default credentials: 
                <strong style="color: #fff;">admin</strong> / <strong style="color: #fff;">admin123</strong>
            </p>
            <div style="margin-top: 12px;">
                <a href="../index.php" style="font-size: 0.85rem; color: var(--adm-primary); display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-arrow-left"></i> Return to Live Portfolio
                </a>
            </div>
        </div>
    </div>
</body>
</html>
