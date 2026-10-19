<?php
require_once __DIR__ . '/config/db.php';

try {
    $pdo = getDBConnection();

    $email = 'malik2341971@gmail.com';
    $password = 'm@lik786';
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $username = 'malikraheem';

    $stmt = $pdo->prepare("SELECT id FROM `admin_users` WHERE `email` = ? OR `username` = ? OR `username` = 'admin' LIMIT 1");
    $stmt->execute([$email, $username]);
    $user = $stmt->fetch();

    if ($user) {
        $upd = $pdo->prepare("UPDATE `admin_users` SET `username` = ?, `email` = ?, `password_hash` = ? WHERE `id` = ?");
        $upd->execute([$username, $email, $hash, $user['id']]);
        echo "SERVER_ADMIN_UPDATED: ID {$user['id']}, {$email}\n";
    } else {
        $ins = $pdo->prepare("INSERT INTO `admin_users` (`username`, `password_hash`, `email`) VALUES (?, ?, ?)");
        $ins->execute([$username, $hash, $email]);
        echo "SERVER_ADMIN_INSERTED: {$email}\n";
    }

    // Verify Services Table
    $srvCount = (int)$pdo->query("SELECT COUNT(*) FROM `services`")->fetchColumn();
    echo "SERVICES_COUNT: {$srvCount}\n";

    // Verify Messages Columns
    $msgCols = $pdo->query("SHOW COLUMNS FROM `messages` LIKE 'reply_content'")->fetchAll();
    echo "REPLY_COL_EXISTS: " . (!empty($msgCols) ? 'YES' : 'NO') . "\n";

} catch (Exception $e) {
    echo "SYNC_ERROR: " . $e->getMessage() . "\n";
}
