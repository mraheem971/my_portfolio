<?php
/**
 * Core Portfolio Dynamic Functions for MySQL
 */

require_once __DIR__ . '/../config/db.php';

function getSettings(): array {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT `key_name`, `key_value` FROM `settings`");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['key_name']] = $row['key_value'];
    }
    return $settings;
}

function updateSetting(string $key, string $value): bool {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `key_value` = VALUES(`key_value`)");
    return $stmt->execute([$key, $value]);
}

function getEducationList(): array {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM `education` ORDER BY `sort_order` ASC, `id` ASC");
    return $stmt->fetchAll();
}

function getSkillCategoriesWithSkills(): array {
    $pdo = getDBConnection();
    $catStmt = $pdo->query("SELECT * FROM `skill_categories` ORDER BY `sort_order` ASC, `id` ASC");
    $categories = $catStmt->fetchAll();

    foreach ($categories as &$cat) {
        $skillStmt = $pdo->prepare("SELECT * FROM `skills` WHERE `category_id` = ? ORDER BY `sort_order` ASC, `id` ASC");
        $skillStmt->execute([$cat['id']]);
        $cat['skills'] = $skillStmt->fetchAll();
    }

    return $categories;
}

function getLanguagesList(): array {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT * FROM `languages` ORDER BY `sort_order` ASC, `id` ASC");
    return $stmt->fetchAll();
}

function getMessages(int $limit = 50, bool $onlyUnread = false): array {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM `messages`";
    if ($onlyUnread) {
        $sql .= " WHERE `is_read` = 0";
    }
    $sql .= " ORDER BY `created_at` DESC LIMIT " . (int)$limit;
    return $pdo->query($sql)->fetchAll();
}

function getUnreadMessageCount(): int {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) AS total FROM `messages` WHERE `is_read` = 0");
    $row = $stmt->fetch();
    return (int)($row['total'] ?? 0);
}

function markMessageAsRead(int $id): bool {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE `messages` SET `is_read` = 1 WHERE `id` = ?");
    return $stmt->execute([$id]);
}

function deleteMessage(int $id): bool {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("DELETE FROM `messages` WHERE `id` = ?");
    return $stmt->execute([$id]);
}

function saveContactMessage(string $name, string $email, string $subject, string $message): bool {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("INSERT INTO `messages` (`name`, `email`, `subject`, `message`, `is_read`, `created_at`) VALUES (?, ?, ?, ?, 0, NOW())");
    return $stmt->execute([$name, $email, $subject, $message]);
}

function getProjectsList(bool $onlyFeatured = false): array {
    $pdo = getDBConnection();
    $sql = "SELECT * FROM `projects`";
    if ($onlyFeatured) {
        $sql .= " WHERE `is_featured` = 1";
    }
    $sql .= " ORDER BY `sort_order` ASC, `id` DESC";
    return $pdo->query($sql)->fetchAll();
}

function getProjectById(int $id): ?array {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM `projects` WHERE `id` = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    return $project ?: null;
}
