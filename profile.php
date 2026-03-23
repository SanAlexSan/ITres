<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

// Проверяем, авторизован ли пользователь
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Получаем данные пользователя из БД
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Получаем мероприятия пользователя (если есть)
if ($_SESSION['user_role'] === 'user') {
    $stmt = $pdo->prepare("
        SELECT e.*, r.status, r.registered_at 
        FROM events e 
        JOIN registrations r ON e.id = r.event_id 
        WHERE r.user_id = ?
        ORDER BY r.registered_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $my_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <!-- шапка -->
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
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- линия -->
    <section class="section-1">
        <div class="line"></div>
    </section>

    <!-- лк -->
    <section class="profile-section">
        <div class="profile-container">
            
            <!-- боковая панель -->
            <div class="profile-sidebar">
                <div class="profile-avatar">
                    <img src="img/user.svg" alt="avatar">
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p class="profile-role"><?php echo $user['role'] === 'admin' ? 'Администратор' : 'Участник'; ?></p>
                </div>
                <div class="profile-menu">
                    <a href="#profile" class="profile-menu-item active">📋 Мои данные</a>
                    <?php if ($user['role'] === 'user'): ?>
                        <a href="#events" class="profile-menu-item">📅 Мои мероприятия</a>
                        <a href="#my-reviews" class="profile-menu-item">💬 Мои отзывы</a>
                    <?php else: ?>
                        <a href="admin_users.php" class="profile-menu-item">👥 Пользователи</a>
                        <a href="admin_events.php" class="profile-menu-item">📅 Мероприятия</a>
                        <a href="admin_achievements.php" class="profile-menu-item">🏆 Достижения</a>
                        <a href="admin_requests.php" class="profile-menu-item">📋 Заявки</a>
                        <a href="admin_comments.php" class="profile-menu-item">💬 Отзывы</a>
                    <?php endif; ?>
                    <a href="logout.php" class="profile-menu-item logout">🚪 Выйти</a>
                </div>
            </div>

            <!-- основной контент -->
            <div class="profile-content">
                
                <!-- для всех пользователей: мои данные -->
                <div id="profile" class="profile-tab active">
                    <h1>Мои данные</h1>
                    <div class="profile-info-card">
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
                                    <a href="<?php echo htmlspecialchars($user['vk_link']); ?>" target="_blank">Страница ВК</a>
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
                            <span class="info-label">Регистрация:</span>
                            <span class="info-value"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <!-- для обычных пользователей: мои мероприятия -->
            <div id="events" class="profile-tab">
                <h1>Мои мероприятия</h1>
                
                <?php
                $stmt = $pdo->prepare("
                    SELECT e.*, r.status as reg_status, r.registered_at 
                    FROM events e
                    JOIN registrations r ON e.id = r.event_id
                    WHERE r.user_id = ?
                    ORDER BY e.event_date DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $my_events = $stmt->fetchAll();
                ?>
                
                <?php if (empty($my_events)): ?>
                    <div class="empty-state">
                        <p>Вы ещё не записывались на мероприятия</p>
                        <a href="events.php" class="btn">Посмотреть мероприятия</a>
                    </div>
                <?php else: ?>
                    <div class="my-events-list">
                        <?php foreach ($my_events as $event): ?>
                            <div class="my-event-card status-<?php echo $event['reg_status']; ?>">
                                <div class="event-header">
                                    <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                                    <span class="event-status-badge <?php echo $event['reg_status']; ?>">
                                        <?php 
                                            $statuses = [
                                                'pending' => 'Ожидает',
                                                'approved' => 'Подтверждено',
                                                'rejected' => 'Отклонено',
                                                'attended' => 'Посещено'
                                            ];
                                            echo $statuses[$event['reg_status']] ?? $event['reg_status'];
                                        ?>
                                    </span>
                                </div>
                                
                                <div class="event-details">
                                    <p>📅 <?php echo date('d.m.Y', strtotime($event['event_date'])); ?></p>
                                    <p>📍 <?php echo htmlspecialchars($event['location'] ?? 'Место уточняется'); ?></p>
                                </div>
                                
                                <div class="event-footer">
                                    <span class="registration-date">
                                        Записался: <?php echo date('d.m.Y H:i', strtotime($event['registered_at'])); ?>
                                    </span>
                                    
                                    <?php if ($event['reg_status'] === 'attended'): ?>
                                        <a href="leave_review.php?event_id=<?php echo $event['id']; ?>" class="btn-small">Оставить отзыв</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Мои отзывы -->
            <div id="my-reviews" class="profile-tab">
                <h1>Мои отзывы</h1>
                
                <?php
                $stmt = $pdo->prepare("
                    SELECT c.*, e.title as event_title 
                    FROM comments c
                    JOIN events e ON c.event_id = e.id
                    WHERE c.user_id = ?
                    ORDER BY c.created_at DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $my_reviews = $stmt->fetchAll();
                ?>
                
                <?php if (empty($my_reviews)): ?>
                    <div class="empty-state">
                        <p>Вы ещё не оставляли отзывы</p>
                        <a href="events.php" class="btn">Посмотреть мероприятия</a>
                    </div>
                <?php else: ?>
                    <div class="my-reviews-list">
                        <?php foreach ($my_reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <h3><?php echo htmlspecialchars($review['event_title']); ?></h3>
                                    <div class="review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="star <?php echo $i <= $review['rating'] ? 'filled' : 'empty'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <div class="review-text">
                                    <?php echo nl2br(htmlspecialchars($review['text'])); ?>
                                </div>
                                <div class="review-footer">
                                    <span class="review-date">
                                        <?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?>
                                    </span>
                                    <span class="review-status <?php echo $review['is_approved'] ? 'approved' : 'pending'; ?>">
                                        <?php echo $review['is_approved'] ? 'Опубликован' : 'На модерации'; ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

                <!-- для админов: дашборд -->
                <?php if ($user['role'] === 'admin'): ?>
                <div id="dashboard" class="profile-tab active">
                    <h1>Панель управления</h1>
                    <div class="admin-dashboard">
                        <a href="admin_users.php" class="dashboard-card">
                            <h3>Пользователи</h3>
                            <p>Управление пользователями</p>
                        </a>
                        <a href="admin_events.php" class="dashboard-card">
                            <h3>Мероприятия</h3>
                            <p>Создание и редактирование</p>
                        </a>
                        <a href="admin_achievements.php" class="dashboard-card">
                            <h3>Достижения</h3>
                            <p>Добавление побед</p>
                        </a>
                        <a href="admin_requests.php" class="dashboard-card">
                            <h3>Заявки</h3>
                            <p>Модерация записей</p>
                        </a>
                        <a href="admin_comments.php" class="dashboard-card">
                            <h3>Отзывы</h3>
                            <p>Модерация отзывов</p>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <!-- подвал -->
    <footer class="footer">
        <div class="footer-div">
            <p class="footer-copyright">ITres © 2026</p>
            <a href="https://vk.com/stgau_itres" target="_blank" class="footer-vk">
                <img src="img/vk.svg" alt="vk">
                Страница ВКонтакте
            </a>
        </div>
    </footer>

    <script src="js/notifications.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>