<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$user_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: admin_users.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/admin.css">
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
                <a href="profile.php" class="profile-link">
                    <img src="img/user.svg" alt="profile">
                </a>
            </div>
        </div>
    </header>

    <section class="section-1">
        <div class="line"></div>
    </section>

    <section class="admin-section">
        <div class="admin-container">
            <div class="admin-header">
                <h1>Просмотр пользователя</h1>
                <a href="admin_users.php" class="btn-back">← Назад к списку</a>
            </div>

            <div class="profile-info-card">
                <div class="info-row">
                    <span class="info-label">ID:</span>
                    <span class="info-value"><?php echo $user['id']; ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">ФИО:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Телефон:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['phone'] ?? 'Не указан'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">ВКонтакте:</span>
                    <span class="info-value">
                        <?php if ($user['vk_link']): ?>
                            <a href="<?php echo htmlspecialchars($user['vk_link']); ?>" target="_blank" class="vk-link">Страница ВК</a>
                        <?php else: ?>
                            Не указан
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Направление:</span>
                    <span class="info-value"><?php echo htmlspecialchars($user['direction'] ?? 'Не выбрано'); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Роль:</span>
                    <span class="info-value">
                        <span class="role-badge <?php echo $user['role']; ?>">
                            <?php echo $user['role'] === 'admin' ? 'Администратор' : 'Пользователь'; ?>
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Дата регистрации:</span>
                    <span class="info-value"><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></span>
                </div>
            </div>
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