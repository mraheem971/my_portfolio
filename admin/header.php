<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdminLogin();

$currentPage = basename($_SERVER['PHP_SELF']);
$unreadCount = getUnreadMessageCount();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Dashboard' ?> | Portfolio Admin</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Admin CSS -->
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <a href="index.php" class="sidebar-logo">
                <i class="fa-solid fa-terminal"></i>
                <div>Malik<span>Admin</span></div>
            </a>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item <?= $currentPage === 'index.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>

            <a href="profile.php" class="nav-item <?= $currentPage === 'profile.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-gear"></i>
                <span>Profile & Bio</span>
            </a>

            <a href="services.php" class="nav-item <?= $currentPage === 'services.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-screwdriver-wrench"></i>
                <span>Services</span>
            </a>

            <a href="education.php" class="nav-item <?= $currentPage === 'education.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-graduation-cap"></i>
                <span>Education</span>
            </a>

            <a href="skills.php" class="nav-item <?= $currentPage === 'skills.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-code"></i>
                <span>Skills & Tech</span>
            </a>

            <a href="languages.php" class="nav-item <?= $currentPage === 'languages.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-language"></i>
                <span>Languages</span>
            </a>

            <a href="projects.php" class="nav-item <?= $currentPage === 'projects.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-laptop-code"></i>
                <span>Projects</span>
            </a>

            <a href="messages.php" class="nav-item <?= $currentPage === 'messages.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-inbox"></i>
                <span>Inbox Messages</span>
                <?php if ($unreadCount > 0): ?>
                    <span class="nav-badge"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="user-profile-badge">
                <div class="user-avatar">M</div>
                <div>
                    <div class="user-name"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?></div>
                    <div class="user-role">Administrator</div>
                </div>
            </div>
            <a href="logout.php" class="logout-btn" title="Sign Out">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-title">
                <span><?= $pageTitle ?? 'Dashboard' ?></span>
            </div>

            <div class="topbar-actions">
                <a href="../index.php" target="_blank" class="btn-view-site">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i>
                    <span>View Live Site</span>
                </a>
            </div>
        </header>

        <main class="admin-body">
