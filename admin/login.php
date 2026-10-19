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

// Rate Limiting & Lockout Parameters
$maxAttempts = 5;
$lockoutDuration = 600; // 10 minutes in seconds

if (!isset($_SESSION['login_failed_attempts'])) {
    $_SESSION['login_failed_attempts'] = 0;
}

$isLockedOut = false;
$remainingLockout = 0;

if (isset($_SESSION['login_lockout_time'])) {
    $timeSinceLockout = time() - $_SESSION['login_lockout_time'];
    if ($timeSinceLockout < $lockoutDuration) {
        $isLockedOut = true;
        $remainingLockout = $lockoutDuration - $timeSinceLockout;
    } else {
        // Lockout expired, reset attempts
        unset($_SESSION['login_lockout_time']);
        $_SESSION['login_failed_attempts'] = 0;
    }
}

$error = '';
$csrfToken = generateCSRFToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isLockedOut) {
        $minutes = ceil($remainingLockout / 60);
        $error = "Too many failed attempts. Please wait {$minutes} minute(s) before trying again.";
    } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security session expired. Please refresh and try again.';
    } else {
        $loginInput = trim($_POST['login_input'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($loginInput) || empty($password)) {
            $error = 'Please enter your email/username and password.';
        } else {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT * FROM `admin_users` WHERE `email` = ? OR `username` = ? LIMIT 1");
            $stmt->execute([$loginInput, $loginInput]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Successful Login: Reset failed attempts & regenerate session ID
                $_SESSION['login_failed_attempts'] = 0;
                unset($_SESSION['login_lockout_time']);
                
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_email'] = $user['email'];
                $_SESSION['admin_login_time'] = time();

                header("Location: index.php");
                exit;
            } else {
                $_SESSION['login_failed_attempts']++;
                $remainingAttempts = $maxAttempts - $_SESSION['login_failed_attempts'];

                if ($_SESSION['login_failed_attempts'] >= $maxAttempts) {
                    $_SESSION['login_lockout_time'] = time();
                    $isLockedOut = true;
                    $error = 'Account temporarily locked due to 5 failed attempts. Please wait 10 minutes.';
                } else {
                    $error = "Invalid credentials. {$remainingAttempts} attempt(s) remaining before security lockout.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Admin Login | Malik Raheem Portfolio</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Fira+Code:wght@500&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="admin.css">
    <style>
        .password-input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            background: transparent;
            border: none;
            color: var(--adm-text-muted);
            cursor: pointer;
            padding: 4px;
            font-size: 1rem;
            transition: color 0.2s ease;
        }
        .password-toggle-btn:hover {
            color: var(--adm-primary);
        }
        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #10b981;
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 14px;
        }
    </style>
</head>
<body class="login-body">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2>Portfolio Admin Portal</h2>
            <div class="security-badge">
                <i class="fa-solid fa-lock"></i>
                <span>End-to-End Encrypted Session</span>
            </div>
            <p>Enter your authorized administrator credentials to manage your live portfolio.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="form-group">
                <label for="login_input"><i class="fa-solid fa-envelope"></i> Email or Username</label>
                <input type="text" id="login_input" name="login_input" class="form-control" placeholder="malik2341971@gmail.com" required autofocus value="<?= htmlspecialchars($_POST['login_input'] ?? '') ?>" <?= $isLockedOut ? 'disabled' : '' ?>>
            </div>

            <div class="form-group">
                <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                <div class="password-input-wrapper">
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required style="padding-right: 42px;" <?= $isLockedOut ? 'disabled' : '' ?>>
                    <button type="button" class="password-toggle-btn" id="togglePasswordBtn" aria-label="Toggle password visibility">
                        <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; margin-top: 14px; padding: 13px;" <?= $isLockedOut ? 'disabled' : '' ?>>
                <span>Sign In to Dashboard</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--adm-border); text-align: center;">
            <a href="../index.php" style="font-size: 0.85rem; color: var(--adm-text-muted); display: inline-flex; align-items: center; gap: 6px; text-decoration: none; transition: color 0.2s ease;">
                <i class="fa-solid fa-arrow-left"></i> Return to Live Portfolio
            </a>
        </div>
    </div>

    <script>
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleIcon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            });
        }
    </script>
</body>
</html>
