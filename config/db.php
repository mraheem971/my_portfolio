<?php
/**
 * Database Connection & Auto-Initialization for MySQL
 * Configured for XAMPP / Local MySQL
 */

define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'my_portfolio');
define('DB_USER', 'root');
define('DB_PASS', '');

function getDBConnection(): PDO {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // Connect to MySQL server first to ensure database exists
        $serverDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
        $serverPdo = new PDO($serverDsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        $serverPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        // Now connect to the database
        $dbDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dbDsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);

        // Auto-initialize tables if not already created
        initializeMySQLDatabase($pdo);

    } catch (PDOException $e) {
        die("MySQL Database Connection Error: " . htmlspecialchars($e->getMessage()));
    }

    return $pdo;
}

/**
 * Creates MySQL tables and initial portfolio data
 */
function initializeMySQLDatabase(PDO $pdo): void {
    // 1. Settings Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `settings` (
            `key_name` VARCHAR(100) NOT NULL PRIMARY KEY,
            `key_value` TEXT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 2. Admin Users Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admin_users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password_hash` VARCHAR(255) NOT NULL,
            `email` VARCHAR(100) NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. Education Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `education` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `degree` VARCHAR(255) NOT NULL,
            `institution` VARCHAR(255) NOT NULL,
            `duration` VARCHAR(100) NOT NULL,
            `status` VARCHAR(100) NOT NULL,
            `is_in_progress` TINYINT(1) DEFAULT 0,
            `description` TEXT NULL,
            `highlights` TEXT NULL,
            `sort_order` INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Skill Categories Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `skill_categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `icon_class` VARCHAR(100) DEFAULT 'fa-solid fa-code',
            `sort_order` INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 5. Skills Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `skills` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `icon_class` VARCHAR(100) NOT NULL,
            `icon_color` VARCHAR(50) NOT NULL,
            `sort_order` INT DEFAULT 0,
            FOREIGN KEY (`category_id`) REFERENCES `skill_categories`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 6. Languages Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `languages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `level` VARCHAR(100) NOT NULL,
            `icon_class` VARCHAR(100) DEFAULT 'fa-solid fa-language',
            `sort_order` INT DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 7. Messages Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `messages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `subject` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `is_read` TINYINT(1) DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Check if initial settings already exist
    $chk = $pdo->query("SELECT COUNT(*) AS total FROM `settings`")->fetch();
    if (($chk['total'] ?? 0) == 0) {
        // Seed Settings
        $settings = [
            'hero_greeting' => "Assalam-o-Alaikum, I'm",
            'hero_name' => "Malik Raheem Bukhsh",
            'hero_title' => "Web & Android App Developer",
            'hero_desc' => "I am a motivated Web and Android App Developer from Pakistan with an interest in building modern, user-friendly, and practical software solutions. Currently pursuing BS Software Engineering (in progress).",
            'father_name' => "Hafiz Muhammad Ashiq",
            'education_summary' => "BS Software Engineering (In Progress)",
            'institution_name' => "The Islamia University of Bahawalpur (IUB)",
            'hometown' => "Bahawalpur, Pakistan",
            'whatsapp_number' => "03042341971",
            'whatsapp_link' => "https://wa.me/923042341971",
            'email_address' => "malik2341971@gmail.com",
            'instagram_handle' => "@malikraheem4113",
            'instagram_link' => "https://instagram.com/malikraheem4113",
            'about_bio_1' => "I am a motivated Web and Android App Developer from Pakistan with an interest in building modern, user-friendly, and practical software solutions. I enjoy developing websites, Android applications, and automation tools that solve real-world problems.",
            'about_bio_2' => "Through my personal projects, I am continuously improving my technical skills, exploring new technologies, and learning better development practices. I am eager to contribute to development projects, gain professional experience, and grow as a software developer."
        ];

        $stmt = $pdo->prepare("INSERT INTO `settings` (`key_name`, `key_value`) VALUES (?, ?)");
        foreach ($settings as $k => $v) {
            $stmt->execute([$k, $v]);
        }
    }

    // Check if admin user exists
    $chkAdmin = $pdo->query("SELECT COUNT(*) AS total FROM `admin_users`")->fetch();
    if (($chkAdmin['total'] ?? 0) == 0) {
        $stmt = $pdo->prepare("INSERT INTO `admin_users` (`username`, `password_hash`, `email`) VALUES (?, ?, ?)");
        $stmt->execute(['malikraheem', password_hash('m@lik786', PASSWORD_BCRYPT), 'malik2341971@gmail.com']);
    }

    // Check if education exists
    $chkEdu = $pdo->query("SELECT COUNT(*) AS total FROM `education`")->fetch();
    if (($chkEdu['total'] ?? 0) == 0) {
        $eduList = [
            [
                'BS Software Engineering',
                'The Islamia University of Bahawalpur (IUB)',
                '2022 – Present',
                'In Progress',
                1,
                'Pursuing an engineering degree covering core computer science principles, modern software architecture, database management, and mobile & web development practices.',
                "Data Structures & Algorithms\nObject-Oriented Programming (OOP)\nDatabase Systems (DBMS)\nAndroid & Web Engineering",
                1
            ],
            [
                'Intermediate / Higher Secondary Certificate',
                'Bahawalpur, Pakistan',
                'Pre-University',
                'Completed',
                0,
                'Solid foundation in mathematics, analytical logic, and computer fundamentals providing the groundwork for software engineering studies.',
                "Computer Science\nAnalytical Mathematics",
                2
            ]
        ];

        $stmt = $pdo->prepare("INSERT INTO `education` (`degree`, `institution`, `duration`, `status`, `is_in_progress`, `description`, `highlights`, `sort_order`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($eduList as $e) {
            $stmt->execute($e);
        }
    }

    // Check if skill categories exist
    $chkCat = $pdo->query("SELECT COUNT(*) AS total FROM `skill_categories`")->fetch();
    if (($chkCat['total'] ?? 0) == 0) {
        $pdo->exec("INSERT INTO `skill_categories` (`id`, `name`, `icon_class`, `sort_order`) VALUES (1, 'Development & Programming', 'fa-solid fa-code', 1)");
        $pdo->exec("INSERT INTO `skill_categories` (`id`, `name`, `icon_class`, `sort_order`) VALUES (2, 'Office Productivity & Typography', 'fa-solid fa-file-lines', 2)");

        $skills = [
            [1, 'Python', 'fa-brands fa-python', '#3776ab', 1],
            [1, 'HTML', 'fa-brands fa-html5', '#e34f26', 2],
            [1, 'CSS', 'fa-brands fa-css3-alt', '#1572b6', 3],
            [1, 'JavaScript', 'fa-brands fa-square-js', '#f7df1e', 4],
            [1, 'Laravel', 'fa-brands fa-laravel', '#ff2d20', 5],
            [1, 'Android', 'fa-brands fa-android', '#3ddc84', 6],
            [1, 'Vibe Coding', 'fa-solid fa-wand-magic-sparkles', '#ec4899', 7],
            [1, 'AI Assistant', 'fa-solid fa-robot', '#06b6d4', 8],
            [2, 'MS Word', 'fa-regular fa-file-word', '#2b579a', 9],
            [2, 'MS Excel', 'fa-regular fa-file-excel', '#217346', 10],
            [2, 'MS PowerPoint', 'fa-regular fa-file-powerpoint', '#d24726', 11],
            [2, 'Inpage', 'fa-solid fa-keyboard', '#8b5cf6', 12]
        ];

        $stmt = $pdo->prepare("INSERT INTO `skills` (`category_id`, `name`, `icon_class`, `icon_color`, `sort_order`) VALUES (?, ?, ?, ?, ?)");
        foreach ($skills as $s) {
            $stmt->execute($s);
        }
    }

    // Check if languages exist
    $chkLang = $pdo->query("SELECT COUNT(*) AS total FROM `languages`")->fetch();
    if (($chkLang['total'] ?? 0) == 0) {
        $languages = [
            ['Urdu', 'Native / Strong', 'fa-solid fa-language', 1],
            ['English', 'Basic', 'fa-solid fa-language', 2]
        ];

        $stmt = $pdo->prepare("INSERT INTO `languages` (`name`, `level`, `icon_class`, `sort_order`) VALUES (?, ?, ?, ?)");
        foreach ($languages as $l) {
            $stmt->execute($l);
        }
    }

    // 8. Projects Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `projects` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `tagline` VARCHAR(255) NULL,
            `description` TEXT NOT NULL,
            `image_url` VARCHAR(255) NULL,
            `github_url` VARCHAR(255) NULL,
            `live_url` VARCHAR(255) NULL,
            `technologies` VARCHAR(255) NOT NULL,
            `is_featured` TINYINT(1) DEFAULT 1,
            `sort_order` INT DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Check if projects exist
    $chkProj = $pdo->query("SELECT COUNT(*) AS total FROM `projects`")->fetch();
    if (($chkProj['total'] ?? 0) == 0) {
        $stmt = $pdo->prepare("
            INSERT INTO `projects` (`title`, `tagline`, `description`, `image_url`, `github_url`, `live_url`, `technologies`, `is_featured`, `sort_order`)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, 1)
        ");
        $stmt->execute([
            'SocialMate – WhatsApp Bot SaaS Dashboard',
            'WhatsApp Automation & Marketing SaaS Platform',
            'Developed SocialMate, a modern WhatsApp automation platform that helps businesses manage WhatsApp accounts, automate customer replies, organize contacts and groups, create message templates, and run marketing campaigns. The dashboard includes gateway management, API integration, messaging logs, anti-ban settings, and subscription plan management, providing an efficient solution for customer communication and business automation.',
            'assets/socialmate.png',
            'https://github.com/mraheem971/auto-whatsapp.git',
            'https://socialmate.iamraheem.com',
            'Blade, PHP, Laravel, CSS, JavaScript'
        ]);

        $stmt->execute([
            'CampusMate – Student Portal & Attendance Android App',
            'Native Android Mobile Application',
            'Developed CampusMate, an intuitive Android application for university students to monitor academic attendance percentages, lecture schedules, CGPA metrics, and real-time department announcements with offline caching and Firebase synchronization.',
            'assets/campusmate.png',
            'https://github.com/mraheem971',
            'https://github.com/mraheem971',
            'Android Studio, Java, Firebase, SQLite, Material UI'
        ]);
    }

    // 9. Services Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `services` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `title` VARCHAR(255) NOT NULL,
            `description` TEXT NOT NULL,
            `icon_class` VARCHAR(100) DEFAULT 'fa-solid fa-code',
            `icon_color` VARCHAR(50) DEFAULT '#6366f1',
            `tags` VARCHAR(255) NULL,
            `sort_order` INT DEFAULT 1,
            `is_active` TINYINT(1) DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Check if services exist
    $chkSrv = $pdo->query("SELECT COUNT(*) AS total FROM `services`")->fetch();
    if (($chkSrv['total'] ?? 0) == 0) {
        $stmt = $pdo->prepare("
            INSERT INTO `services` (`title`, `description`, `icon_class`, `icon_color`, `tags`, `sort_order`, `is_active`)
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ");

        $services = [
            [
                'Professional Android App Development',
                'Designing and engineering modern, intuitive native mobile applications with Java, Android Studio, Material Design guidelines, and responsive layouts.',
                'fa-brands fa-android',
                '#3ddc84',
                'Android Studio, Java & SDK, Material UI, API Integration',
                1
            ],
            [
                'Full Stack Web Engineering',
                'Architecting complete front-to-back responsive web platforms with modern HTML5, CSS3, JavaScript, Python backend logic, Laravel, and RESTful APIs.',
                'fa-solid fa-layer-group',
                '#6366f1',
                'HTML5 / CSS3 / JS, Python & Laravel, RESTful APIs, Clean Architecture',
                2
            ],
            [
                'AI-Assisted & Vibe Coding',
                'Leveraging cutting-edge AI workflows, prompt engineering, and rapid iterative development to prototype tools, solve complex problems, and automate code logic.',
                'fa-solid fa-wand-magic-sparkles',
                '#ec4899',
                'AI Workflows, Rapid Prototyping, Prompt Engineering, Automation',
                3
            ],
            [
                'Office Productivity & Typography',
                'Executive documentation, automated Excel spreadsheets, dynamic presentations with MS PowerPoint, and professional desktop publishing in Inpage Urdu.',
                'fa-solid fa-file-lines',
                '#2b579a',
                'MS Word, MS Excel, PowerPoint, Inpage Urdu',
                4
            ]
        ];

        foreach ($services as $srv) {
            $stmt->execute($srv);
        }
    }
}
