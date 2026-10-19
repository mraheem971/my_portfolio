<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$settings = getSettings();
$projectsList = getProjectsList();
$educationList = getEducationList();
$skillCategories = getSkillCategoriesWithSkills();
$languagesList = getLanguagesList();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['hero_name'] ?? 'Malik Raheem Bukhsh') ?> | Full Stack Developer & Professional Android App Developer</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Interactive Custom Cursor Circles -->
    <div class="cursor-circle-outer" id="cursorOuter"></div>
    <div class="cursor-circle-dot" id="cursorDot"></div>

    <!-- Navigation Bar -->
    <header class="header">
        <div class="container navbar">
            <a href="#home" class="logo">
                <span class="logo-mark"><i class="fa-solid fa-terminal"></i></span>
                Malik<span>Raheem</span>
            </a>
            
            <nav class="nav-links">
                <a href="#home" class="nav-link active">Home</a>
                <a href="#about" class="nav-link">About</a>
                <a href="#services" class="nav-link">Services</a>
                <a href="#projects" class="nav-link">Projects</a>
                <a href="#education" class="nav-link">Education</a>
                <a href="#skills" class="nav-link">Skills</a>
                <a href="#languages" class="nav-link">Languages</a>
                <a href="#contact" class="nav-link">Contact</a>
            </nav>

            <div class="nav-actions">
                <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme" title="Switch Theme">
                    <i class="fa-solid fa-moon"></i>
                </button>
                <button class="hamburger-menu" id="menu-toggle" aria-label="Open Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div class="mobile-nav" id="mobile-nav">
        <button class="close-menu" id="menu-close" aria-label="Close Menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <a href="#home" class="mobile-link">Home</a>
        <a href="#about" class="mobile-link">About</a>
        <a href="#services" class="mobile-link">Services</a>
        <a href="#projects" class="mobile-link">Projects</a>
        <a href="#education" class="mobile-link">Education</a>
        <a href="#skills" class="mobile-link">Skills</a>
        <a href="#languages" class="mobile-link">Languages</a>
        <a href="#contact" class="mobile-link">Contact</a>
    </div>

    <!-- Main Content Area -->
    <main>
        <!-- Hero Section -->
        <section id="home" class="hero-section section-padding">
            <div class="container">
                <div class="hero-grid">
                    <div class="hero-content reveal reveal-left">
                        <span class="greeting"><?= htmlspecialchars($settings['hero_greeting'] ?? "Assalam-o-Alaikum, I'm") ?></span>
                        <h1 class="hero-name"><?= htmlspecialchars($settings['hero_name'] ?? 'Malik Raheem Bukhsh') ?></h1>
                        <h2 class="hero-title"><?= $settings['hero_title'] ?? 'Web & Android App Developer' ?></h2>
                        <p class="hero-desc">
                            <?= $settings['hero_desc'] ?? 'I am a motivated Web and Android App Developer from Pakistan with an interest in building modern, user-friendly, and practical software solutions. Currently pursuing BS Software Engineering (in progress).' ?>
                        </p>
                        <div class="hero-buttons">
                            <a href="#contact" class="btn btn-primary"><i class="fa-regular fa-paper-plane"></i> Contact Me</a>
                            <a href="#services" class="btn btn-outline"><i class="fa-solid fa-screwdriver-wrench"></i> My Services</a>
                            <a href="<?= htmlspecialchars($settings['whatsapp_link'] ?? 'https://wa.me/923042341971') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp"><i class="fa-brands fa-whatsapp"></i> Chat Now</a>
                        </div>
                    </div>
                    
                    <!-- Profile Picture with Animated Rotating Rings -->
                    <div class="hero-img-container reveal reveal-right">
                        <div class="profile-circle-wrapper">
                            <!-- Glowing Aura Effect Behind Circle -->
                            <div class="animated-circle-glow"></div>
                            
                            <!-- Outer Rotating Ring (Gradient) -->
                            <div class="animated-circle-ring ring-outer"></div>
                            
                            <!-- Inner Rotating Ring (Counter-Clockwise Dashed) -->
                            <div class="animated-circle-ring ring-inner"></div>

                            <!-- Perfectly Circular Frame (Zero Crossing Over Photo) -->
                            <div class="profile-circle-frame">
                                <img src="assets/profile.jpg" alt="<?= htmlspecialchars($settings['hero_name'] ?? 'Malik Raheem Bukhsh') ?>" class="profile-img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section with Profile Picture Alongside -->
        <section id="about" class="about-section section-padding bg-light">
            <div class="container">
                <div class="section-header reveal reveal-down">
                    <span class="sub-title">Personal Biography</span>
                    <h2 class="section-title">About Me</h2>
                    <div class="line"></div>
                </div>

                <div class="about-grid">
                    <!-- Profile Picture Card Alongside Bio -->
                    <div class="about-image-column reveal reveal-left">
                        <div class="about-profile-card">
                            <div class="about-photo-wrap">
                                <img src="assets/profile.jpg" alt="<?= htmlspecialchars($settings['hero_name'] ?? 'Malik Raheem Bukhsh') ?>" class="about-photo-img">
                            </div>
                            <div class="about-card-info">
                                <h4><?= htmlspecialchars($settings['hero_name'] ?? 'Malik Raheem Bukhsh') ?></h4>
                                <span class="about-card-role"><?= htmlspecialchars($settings['hero_title'] ?? 'Web & Android App Developer') ?></span>
                                <div class="about-status-pill">
                                    <span class="status-indicator-dot"></span> Available for Projects
                                </div>
                            </div>
                            <div class="about-quick-links">
                                <a href="<?= htmlspecialchars($settings['whatsapp_link'] ?? 'https://wa.me/923042341971') ?>" target="_blank" rel="noopener noreferrer" class="about-quick-btn" title="WhatsApp Direct" aria-label="WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                                <a href="mailto:<?= htmlspecialchars($settings['email_address'] ?? 'malik2341971@gmail.com') ?>" class="about-quick-btn" title="Email Direct" aria-label="Email">
                                    <i class="fa-solid fa-envelope"></i>
                                </a>
                                <a href="<?= htmlspecialchars($settings['instagram_link'] ?? 'https://instagram.com/malikraheem4113') ?>" target="_blank" rel="noopener noreferrer" class="about-quick-btn" title="Instagram Profile" aria-label="Instagram">
                                    <i class="fa-brands fa-instagram"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Profile & Details Card -->
                    <div class="about-info-card reveal reveal-right">
                        <h3>Personal Profile & Background</h3>
                        <p>
                            <?= $settings['about_bio_1'] ?? 'I am a motivated Web and Android App Developer from Pakistan with an interest in building modern, user-friendly, and practical software solutions. I enjoy developing websites, Android applications, and automation tools that solve real-world problems.' ?>
                        </p>
                        <p>
                            <?= $settings['about_bio_2'] ?? 'Through my personal projects, I am continuously improving my technical skills, exploring new technologies, and learning better development practices. I am eager to contribute to development projects, gain professional experience, and grow as a software developer.' ?>
                        </p>
                        
                        <div class="personal-details-grid">
                            <div class="detail-card">
                                <div class="detail-icon"><i class="fa-solid fa-user-tie"></i></div>
                                <div class="detail-body">
                                    <span class="detail-label">Father's Name</span>
                                    <span class="detail-val"><?= htmlspecialchars($settings['father_name'] ?? 'Hafiz Muhammad Ashiq') ?></span>
                                </div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                                <div class="detail-body">
                                    <span class="detail-label">Education</span>
                                    <span class="detail-val"><?= htmlspecialchars($settings['education_summary'] ?? 'BS Software Engineering (In Progress)') ?></span>
                                </div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-icon"><i class="fa-solid fa-building-columns"></i></div>
                                <div class="detail-body">
                                    <span class="detail-label">Institution</span>
                                    <span class="detail-val"><?= htmlspecialchars($settings['institution_name'] ?? 'The Islamia University of Bahawalpur (IUB)') ?></span>
                                </div>
                            </div>
                            <div class="detail-card">
                                <div class="detail-icon"><i class="fa-solid fa-location-dot"></i></div>
                                <div class="detail-body">
                                    <span class="detail-label">Hometown</span>
                                    <span class="detail-val"><?= htmlspecialchars($settings['hometown'] ?? 'Bahawalpur, Pakistan') ?></span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section (Dedicated) -->
        <section id="services" class="services-section section-padding">
            <div class="container">
                <div class="section-header reveal reveal-down">
                    <span class="sub-title">What I Offer</span>
                    <h2 class="section-title">My Services</h2>
                    <div class="line"></div>
                </div>

                <div class="services-grid">
                    <!-- Service 1: Professional Android App Development -->
                    <div class="service-card reveal reveal-up delay-1">
                        <div class="service-icon-box" style="background-color: rgba(61, 220, 132, 0.15); color: #3ddc84;">
                            <i class="fa-brands fa-android"></i>
                        </div>
                        <h3>Professional Android App Development</h3>
                        <p>Designing and engineering modern, intuitive native mobile applications with Java, Android Studio, Material Design guidelines, and responsive layouts.</p>
                        <div class="service-tags">
                            <span class="service-tag">Android Studio</span>
                            <span class="service-tag">Java & SDK</span>
                            <span class="service-tag">Material UI</span>
                            <span class="service-tag">API Integration</span>
                        </div>
                    </div>

                    <!-- Service 2: Full Stack Web Engineering -->
                    <div class="service-card reveal reveal-up delay-2">
                        <div class="service-icon-box" style="background-color: rgba(99, 102, 241, 0.15); color: #6366f1;">
                            <i class="fa-solid fa-layer-group"></i>
                        </div>
                        <h3>Full Stack Web Engineering</h3>
                        <p>Architecting complete front-to-back responsive web platforms with modern HTML5, CSS3, JavaScript, Python backend logic, Laravel, and RESTful APIs.</p>
                        <div class="service-tags">
                            <span class="service-tag">HTML5 / CSS3 / JS</span>
                            <span class="service-tag">Python & Laravel</span>
                            <span class="service-tag">RESTful APIs</span>
                            <span class="service-tag">Clean Architecture</span>
                        </div>
                    </div>

                    <!-- Service 3: AI-Assisted & Vibe Coding -->
                    <div class="service-card reveal reveal-up delay-3">
                        <div class="service-icon-box" style="background-color: rgba(236, 72, 153, 0.15); color: #ec4899;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <h3>AI-Assisted & Vibe Coding</h3>
                        <p>Leveraging cutting-edge AI workflows, prompt engineering, and rapid iterative development to prototype tools, solve complex problems, and automate code logic.</p>
                        <div class="service-tags">
                            <span class="service-tag">AI Workflows</span>
                            <span class="service-tag">Rapid Prototyping</span>
                            <span class="service-tag">Prompt Engineering</span>
                            <span class="service-tag">Automation</span>
                        </div>
                    </div>

                    <!-- Service 4: Office Productivity & Typography -->
                    <div class="service-card reveal reveal-up delay-4">
                        <div class="service-icon-box" style="background-color: rgba(43, 87, 154, 0.15); color: #2b579a;">
                            <i class="fa-solid fa-file-lines"></i>
                        </div>
                        <h3>Office Productivity & Typography</h3>
                        <p>Executive documentation, automated Excel spreadsheets, dynamic presentations with MS PowerPoint, and professional desktop publishing in Inpage Urdu.</p>
                        <div class="service-tags">
                            <span class="service-tag">MS Word</span>
                            <span class="service-tag">MS Excel</span>
                            <span class="service-tag">PowerPoint</span>
                            <span class="service-tag">Inpage Urdu</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Projects Section (Featured Showcase) -->
        <section id="projects" class="projects-section section-padding">
            <div class="container">
                <div class="section-header reveal reveal-down">
                    <span class="sub-title">Featured Showcase</span>
                    <h2 class="section-title">My Projects</h2>
                    <div class="line"></div>
                </div>

                <div class="projects-grid">
                    <?php if (!empty($projectsList)): ?>
                        <?php 
                        $projIdx = 0;
                        foreach ($projectsList as $proj): 
                            $projIdx++;
                            $techArray = array_map('trim', explode(',', $proj['technologies'] ?? ''));
                        ?>
                            <div class="project-card reveal reveal-up delay-<?= ($projIdx % 4) ?>">
                                <!-- Card Image Header -->
                                <div class="project-card-image">
                                    <img src="<?= htmlspecialchars(!empty($proj['image_url']) ? $proj['image_url'] : 'assets/socialmate.png') ?>" alt="<?= htmlspecialchars($proj['title']) ?>">
                                    <div class="project-card-overlay"></div>
                                    <span class="project-card-badge">
                                        <i class="fa-solid fa-star"></i> <?= !empty($proj['is_featured']) ? 'Featured' : 'Project' ?>
                                    </span>
                                    <?php if (!empty($proj['live_url'])): ?>
                                        <a href="<?= htmlspecialchars($proj['live_url']) ?>" target="_blank" rel="noopener noreferrer" class="project-card-link-badge" title="Live Preview">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- Card Body: Tagline, Title, Description, Stack, Actions -->
                                <div class="project-card-body">
                                    <?php if (!empty($proj['tagline'])): ?>
                                        <div class="project-card-tagline"><?= htmlspecialchars($proj['tagline']) ?></div>
                                    <?php endif; ?>

                                    <h3 class="project-card-title"><?= htmlspecialchars($proj['title']) ?></h3>

                                    <p class="project-card-desc">
                                        <?= nl2br(htmlspecialchars($proj['description'])) ?>
                                    </p>

                                    <div class="project-card-tech">
                                        <?php foreach ($techArray as $tech): ?>
                                            <?php if (!empty($tech)): ?>
                                                <span class="tech-tag-sm">
                                                    <?php
                                                    $tLower = strtolower($tech);
                                                    $tIcon = 'fa-solid fa-code';
                                                    if (strpos($tLower, 'laravel') !== false) $tIcon = 'fa-brands fa-laravel';
                                                    elseif (strpos($tLower, 'php') !== false) $tIcon = 'fa-brands fa-php';
                                                    elseif (strpos($tLower, 'css') !== false) $tIcon = 'fa-brands fa-css3-alt';
                                                    elseif (strpos($tLower, 'javascript') !== false || strpos($tLower, 'js') !== false) $tIcon = 'fa-brands fa-js';
                                                    elseif (strpos($tLower, 'python') !== false) $tIcon = 'fa-brands fa-python';
                                                    elseif (strpos($tLower, 'android') !== false) $tIcon = 'fa-brands fa-android';
                                                    elseif (strpos($tLower, 'html') !== false) $tIcon = 'fa-brands fa-html5';
                                                    ?>
                                                    <i class="<?= $tIcon ?>"></i>
                                                    <?= htmlspecialchars($tech) ?>
                                                </span>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="project-card-actions">
                                        <?php if (!empty($proj['live_url'])): ?>
                                            <a href="<?= htmlspecialchars($proj['live_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm">
                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Live Demo
                                            </a>
                                        <?php endif; ?>

                                        <?php if (!empty($proj['github_url'])): ?>
                                            <a href="<?= htmlspecialchars($proj['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm">
                                                <i class="fa-brands fa-github"></i> GitHub
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Education Section -->
        <section id="education" class="education-section section-padding">
            <div class="container">
                <div class="section-header reveal reveal-down">
                    <span class="sub-title">Academic Qualifications</span>
                    <h2 class="section-title">My Education</h2>
                    <div class="line"></div>
                </div>

                <div class="education-grid">
                    <?php if (!empty($educationList)): ?>
                        <?php 
                        $eduIdx = 0;
                        foreach ($educationList as $edu): 
                            $eduIdx++;
                            $eduAnimClass = ($eduIdx % 2 === 1) ? 'reveal reveal-left' : 'reveal reveal-right';
                        ?>
                            <div class="education-card <?= $eduAnimClass ?> <?= !empty($edu['is_in_progress']) ? 'featured-degree' : '' ?>">
                                <div class="education-card-header">
                                    <div class="education-icon <?= empty($edu['is_in_progress']) ? 'intermediate-edu' : '' ?>">
                                        <i class="<?= !empty($edu['is_in_progress']) ? 'fa-solid fa-graduation-cap' : 'fa-solid fa-school' ?>"></i>
                                    </div>
                                    <span class="edu-status-badge <?= !empty($edu['is_in_progress']) ? 'in-progress' : 'completed' ?>">
                                        <i class="<?= !empty($edu['is_in_progress']) ? 'fa-solid fa-spinner fa-spin-pulse' : 'fa-solid fa-circle-check' ?>"></i>
                                        <?= htmlspecialchars($edu['status']) ?>
                                    </span>
                                </div>
                                <div class="education-content">
                                    <span class="edu-duration"><?= htmlspecialchars($edu['duration']) ?></span>
                                    <h3 class="edu-degree"><?= htmlspecialchars($edu['degree']) ?></h3>
                                    <h4 class="edu-institution"><i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars($edu['institution']) ?></h4>
                                    <?php if (!empty($edu['description'])): ?>
                                        <p class="edu-desc"><?= htmlspecialchars($edu['description']) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($edu['highlights'])): ?>
                                        <div class="edu-highlights">
                                            <?php 
                                             $lines = explode("\n", str_replace("\r", "", $edu['highlights']));
                                            foreach ($lines as $hl): 
                                                $hl = trim($hl);
                                                if (empty($hl)) continue;
                                            ?>
                                                <span class="highlight-tag"><i class="fa-solid fa-check"></i> <?= htmlspecialchars($hl) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center" style="grid-column: 1 / -1; color: var(--text-light);">No education records added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Skills Section -->
        <section id="skills" class="skills-section section-padding">
            <div class="container">
                <div class="section-header reveal reveal-down">
                    <span class="sub-title">Technical Proficiency</span>
                    <h2 class="section-title">My Skills & Expertise</h2>
                    <div class="line"></div>
                </div>

                <?php if (!empty($skillCategories)): ?>
                    <?php foreach ($skillCategories as $cat): ?>
                        <div class="skills-category-block reveal reveal-up">
                            <h3 class="skills-category-title"><i class="<?= htmlspecialchars($cat['icon_class'] ?? 'fa-solid fa-code') ?>"></i> <?= htmlspecialchars($cat['name']) ?></h3>
                            <div class="skills-tech-grid">
                                <?php if (!empty($cat['skills'])): ?>
                                    <?php 
                                    $sIdx = 0;
                                    foreach ($cat['skills'] as $skill): 
                                        $sIdx = ($sIdx % 8) + 1;
                                    ?>
                                        <div class="skill-card-item reveal reveal-scale delay-<?= $sIdx ?>">
                                            <div class="skill-card-icon" style="color: <?= htmlspecialchars($skill['icon_color']) ?>;">
                                                <i class="<?= htmlspecialchars($skill['icon_class']) ?>"></i>
                                            </div>
                                            <h3 class="skill-card-title"><?= htmlspecialchars($skill['name']) ?></h3>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p style="color: var(--text-light); grid-column: 1 / -1;">No skills in this category yet.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center" style="color: var(--text-light);">No skills added yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Languages Section -->
        <section id="languages" class="languages-section section-padding bg-light">
            <div class="container">
                <div class="section-header reveal reveal-down">
                    <span class="sub-title">Communication & Fluency</span>
                    <h2 class="section-title">Languages</h2>
                    <div class="line"></div>
                </div>

                <div class="languages-row-grid">
                    <?php if (!empty($languagesList)): ?>
                        <?php 
                        $lIdx = 0;
                        foreach ($languagesList as $lang): 
                            $lIdx++;
                            $langAnim = ($lIdx % 2 === 1) ? 'reveal reveal-left' : 'reveal reveal-right';
                        ?>
                            <div class="lang-row-card <?= $langAnim ?>">
                                <div class="lang-row-icon">
                                    <i class="<?= htmlspecialchars($lang['icon_class'] ?? 'fa-solid fa-language') ?>"></i>
                                </div>
                                <div class="lang-row-info">
                                    <h3 class="lang-row-title"><?= htmlspecialchars($lang['name']) ?></h3>
                                    <span class="lang-row-level"><?= htmlspecialchars($lang['level']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center" style="grid-column: 1 / -1; color: var(--text-light);">No languages added yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="contact-section section-padding">
            <div class="container">
                <div class="section-header reveal reveal-down">
                    <span class="sub-title">Get In Touch</span>
                    <h2 class="section-title">Let's Connect</h2>
                    <div class="line"></div>
                </div>

                <div class="contact-grid">
                    <!-- Contact Cards -->
                    <div class="contact-info reveal reveal-left">
                        <h3>Connect With Me</h3>
                        <p>
                            Interested in collaborating, hiring for junior web development projects, or discussing academic work? Reach out via WhatsApp, Instagram, or direct email.
                        </p>
                        
                        <div class="contact-cards-container">
                            <!-- WhatsApp Card -->
                            <a href="<?= htmlspecialchars($settings['whatsapp_link'] ?? 'https://wa.me/923042341971') ?>" target="_blank" rel="noopener noreferrer" class="contact-card">
                                <div class="contact-icon whatsapp"><i class="fa-brands fa-whatsapp"></i></div>
                                <div class="contact-text">
                                    <h4>WhatsApp Direct</h4>
                                    <span><?= htmlspecialchars($settings['whatsapp_number'] ?? '+92 304 2341971') ?></span>
                                </div>
                                <div class="contact-arrow"><i class="fa-solid fa-chevron-right"></i></div>
                            </a>

                            <!-- Email Card -->
                            <a href="mailto:<?= htmlspecialchars($settings['email_address'] ?? 'malik2341971@gmail.com') ?>" class="contact-card">
                                <div class="contact-icon email"><i class="fa-solid fa-envelope"></i></div>
                                <div class="contact-text">
                                    <h4>Email Direct</h4>
                                    <span><?= htmlspecialchars($settings['email_address'] ?? 'malik2341971@gmail.com') ?></span>
                                </div>
                                <div class="contact-arrow"><i class="fa-solid fa-chevron-right"></i></div>
                            </a>

                            <!-- Instagram Card -->
                            <a href="<?= htmlspecialchars($settings['instagram_link'] ?? 'https://instagram.com/malikraheem4113') ?>" target="_blank" rel="noopener noreferrer" class="contact-card">
                                <div class="contact-icon instagram"><i class="fa-brands fa-instagram"></i></div>
                                <div class="contact-text">
                                    <h4>Instagram Profile</h4>
                                    <span><?= htmlspecialchars($settings['instagram_handle'] ?? '@malikraheem4113') ?></span>
                                </div>
                                <div class="contact-arrow"><i class="fa-solid fa-chevron-right"></i></div>
                            </a>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="contact-form-box reveal reveal-right">
                        <h3>Send A Direct Message</h3>
                        <form id="contact-form" class="contact-form" action="api/contact.php" method="POST">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <input type="text" id="name" name="name" required placeholder="e.g. Ali Khan">
                            </div>
                            <div class="form-group">
                                <label for="email">Your Email</label>
                                <input type="email" id="email" name="email" required placeholder="e.g. yourname@example.com">
                            </div>
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <input type="text" id="subject" name="subject" required placeholder="e.g. Web Development / Android App / Project Inquiry">
                            </div>
                            <div class="form-group">
                                <label for="message">Message</label>
                                <textarea id="message" name="message" rows="4" required placeholder="Write your message or project inquiry here..."></textarea>
                            </div>
                            <div class="form-group captcha-group">
                                <label for="captchaAnswer">
                                    <i class="fa-solid fa-shield-halved"></i> Quick Verification: 
                                    <span class="captcha-badge">Prove you're human</span>
                                </label>
                                <div class="captcha-box">
                                    <span class="captcha-question" id="captchaQuestion">7 + 4 = ?</span>
                                    <input type="number" id="captchaAnswer" required placeholder="Answer" class="captcha-input">
                                    <button type="button" id="refreshCaptchaBtn" class="captcha-refresh-btn" title="Refresh challenge" aria-label="Refresh Captcha">
                                        <i class="fa-solid fa-arrows-rotate"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <span>Send Message</span>
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </form>
                        <div id="form-message" class="form-status-msg hidden"></div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-content">
            <div class="footer-left">
                <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($settings['hero_name'] ?? 'Malik Raheem Bukhsh') ?>. All Rights Reserved.</p>
            </div>
            <div class="footer-center">
                <p>Designed with <i class="fa-solid fa-heart heart-icon"></i> & Dedication by Malik Raheem</p>
            </div>
            <div class="footer-right">
                <a href="<?= htmlspecialchars($settings['whatsapp_link'] ?? 'https://wa.me/923042341971') ?>" target="_blank" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                <a href="<?= htmlspecialchars($settings['instagram_link'] ?? 'https://instagram.com/malikraheem4113') ?>" target="_blank" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="mailto:<?= htmlspecialchars($settings['email_address'] ?? 'malik2341971@gmail.com') ?>" aria-label="Email"><i class="fa-solid fa-envelope"></i></a>
            </div>
        </div>
    </footer>

    <!-- Interactive Circular Scroll Progress & Back to Top Widget -->
    <div class="scroll-progress-widget" id="scrollProgressWidget" title="Scroll to top">
        <svg class="progress-ring" width="56" height="56">
            <circle class="progress-ring-bg" stroke="var(--border-color)" stroke-width="4" fill="transparent" r="24" cx="28" cy="28"/>
            <circle class="progress-ring-circle" id="progressRingCircle" stroke="var(--primary-color)" stroke-width="4" stroke-linecap="round" fill="transparent" r="24" cx="28" cy="28"/>
        </svg>
        <div class="progress-ring-content">
            <i class="fa-solid fa-arrow-up"></i>
        </div>
    </div>

    <!-- Custom Script -->
    <script src="script.js"></script>
</body>
</html>
