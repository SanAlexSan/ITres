<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

// Получаем все достижения
$stmt = $pdo->query("
    SELECT * FROM achievements 
    ORDER BY achievement_date DESC
");
$achievements = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/achievements.css">
</head>
<body>
    <header class="header">
        <div class="header-div">
            <a href="index.php" class="logo-link">
                <img class="header-logo" src="img/logo.svg" alt="logo">
            </a>
            <nav class="header-nav">
                <ul class="header-ul">
                    <li><a href="about.php">О нас</a></li>
                    <li><a href="achievements.php">Достижения</a></li>
                    <li><a href="events.php">Мероприятия</a></li>
                </ul>
            </nav>
            <div class="header-user">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="profile-link">
                        <img src="img/user.svg" alt="profile">
                    </a>
                <?php else: ?>
                    <a href="login.php" class="login-link">
                        <img src="img/user.svg" alt="profile">
                        <span>Войти</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="section-1">
        <div class="line"></div>
    </section>

    <section class="achievements-section">
        <div class="achievements-container">
            <h1>Наши достижения</h1>
            <p class="achievements-subtitle">Гордимся каждой победой!</p>
            
            <?php if (empty($achievements)): ?>
                <div class="empty-state">
                    <p>Пока нет добавленных достижений</p>
                </div>
            <?php else: ?>
                <div class="achievements-grid">
                    <?php foreach ($achievements as $ach): ?>
                        <div class="achievement-card">
                            <?php if (!empty($ach['image']) && file_exists($ach['image'])): ?>
                                <div class="achievement-image">
                                    <img src="<?php echo htmlspecialchars($ach['image']); ?>" alt="<?php echo htmlspecialchars($ach['title']); ?>">
                                </div>
                            <?php endif; ?>
                            <div class="achievement-info">
                                <h3><?php echo htmlspecialchars($ach['title']); ?></h3>
                                <p class="achievement-date"><?php echo date('d.m.Y', strtotime($ach['achievement_date'])); ?></p>
                                <p class="achievement-description"><?php echo nl2br(htmlspecialchars($ach['description'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-div">
            <p class="footer-copyright">ITres © 2026</p>
            <a href="https://vk.com/stgau_itres" target="_blank" class="footer-vk">
                <img src="img/vk.svg" alt="vk">
                Страница ВКонтакте
            </a>
        </div>
    </footer>
</body>
</html>