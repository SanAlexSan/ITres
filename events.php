<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

// Получаем все мероприятия
$stmt = $pdo->query("
    SELECT * FROM events 
    ORDER BY event_date DESC
");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Разделяем по is_future
$past_events = [];
$upcoming_events = [];

foreach ($events as $event) {
    if ($event['is_future'] == false || $event['is_future'] == 0) {
        $past_events[] = $event;
    } else {
        $upcoming_events[] = $event;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/events.css">
    <script src="js/notifications.js"></script>
</head>
<body>
    <?php if (isset($_SESSION['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                notifications.success('<?php echo addslashes($_SESSION['success']); ?>');
            });
        </script>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                notifications.error('<?php echo addslashes($_SESSION['error']); ?>');
            });
        </script>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

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

    <!-- переключатель -->
    <section class="section-2">
        <button class="item active" data-target="past">Прошедшие мероприятия</button>
        <span class="razdelitel">|</span>
        <button class="item" data-target="future">Будущие мероприятия</button>
    </section>

    <!-- провели -->
    <section class="section-3">
        <div class="section3-div">
            <h1>Мы уже провели</h1>
            
            <?php if (empty($past_events)): ?>
                <div class="empty-state">
                    <p>Пока нет прошедших мероприятий</p>
                </div>
            <?php else: ?>
                <?php 
                // Группируем по годам
                $events_by_year = [];
                foreach ($past_events as $event) {
                    $year = date('Y', strtotime($event['event_date']));
                    if (!isset($events_by_year[$year])) {
                        $events_by_year[$year] = [];
                    }
                    $events_by_year[$year][] = $event;
                }
                ?>
                
                <?php foreach ($events_by_year as $year => $year_events): ?>
                    <div class="year-div">
                        <h2><?php echo $year; ?> год</h2>
                        <?php foreach ($year_events as $event): ?>
                            <!-- Карточка мероприятия -->
                            <div class="event-card-row">
                                <div class="event-info-glass">
                                    <p class="event-title-glass"><?php echo htmlspecialchars($event['title']); ?></p>
                                    <p class="event-date-glass"><?php echo date('d.m.Y', strtotime($event['event_date'])); ?></p>
                                </div>
                                <img src="<?php echo htmlspecialchars($event['image'] ?? 'img/event_default.png'); ?>" 
                                    alt="<?php echo htmlspecialchars($event['title']); ?>" 
                                    class="event-photo-glass">
                            </div>
                            
                            <!-- Отзывы -->
                            <?php
                            $stmt_reviews = $pdo->prepare("
                                SELECT c.*, u.full_name 
                                FROM comments c
                                JOIN users u ON c.user_id = u.id
                                WHERE c.event_id = ? AND c.is_approved = true
                                ORDER BY c.created_at DESC
                                LIMIT 5
                            ");
                            $stmt_reviews->execute([$event['id']]);
                            $reviews = $stmt_reviews->fetchAll();
                            
                            $stmt_rating = $pdo->prepare("
                                SELECT AVG(rating) as avg_rating, COUNT(*) as total 
                                FROM comments 
                                WHERE event_id = ? AND is_approved = true
                            ");
                            $stmt_rating->execute([$event['id']]);
                            $rating_data = $stmt_rating->fetch();
                            ?>
                            
                            <?php if ($rating_data && $rating_data['total'] > 0): ?>
                                <div class="reviews-container-glass">
                                    <div class="reviews-header-glass">
                                        <div class="reviews-title-glass">Отзывы участников</div>
                                        <div class="reviews-stats-glass">
                                            <span class="avg-rating-glass">⭐ <?php echo round($rating_data['avg_rating'], 1); ?></span>
                                            <span class="reviews-count-glass">(<?php echo $rating_data['total']; ?> отзывов)</span>
                                        </div>
                                    </div>
                                    <div class="reviews-list-glass">
                                        <?php foreach ($reviews as $review): ?>
                                            <div class="review-item-glass">
                                                <div class="review-header-glass">
                                                    <span class="review-author-glass"><?php echo htmlspecialchars($review['full_name']); ?></span>
                                                    <div class="review-rating-glass">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <span class="review-star-glass <?php echo $i <= $review['rating'] ? 'filled' : 'empty'; ?>">★</span>
                                                        <?php endfor; ?>
                                                    </div>
                                                    <span class="review-date-glass"><?php echo date('d.m.Y', strtotime($review['created_at'])); ?></span>
                                                </div>
                                                <div class="review-text-glass">
                                                    <?php echo nl2br(htmlspecialchars($review['text'])); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- будущие -->
    <section class="section-4">
        <div class="section4-div">
            <h1>Что будет дальше?</h1>
            
            <?php if (empty($upcoming_events)): ?>
                <div class="empty-state">
                    <p>Пока нет запланированных мероприятий</p>
                </div>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Название</th>
                            <th>Описание</th>
                            <th>Дата</th>
                            <th>Время</th>
                            <th>Место</th>
                            <th>Запись</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        foreach ($upcoming_events as $event): 
                            // Проверяем, записан ли пользователь
                            $is_registered = false;
                            $reg_status = '';
                            if (isLoggedIn()) {
                                $check = $pdo->prepare("SELECT status FROM registrations WHERE user_id = ? AND event_id = ?");
                                $check->execute([$_SESSION['user_id'], $event['id']]);
                                $reg = $check->fetch();
                                if ($reg) {
                                    $is_registered = true;
                                    $reg_status = $reg['status'];
                                }
                            }
                            
                            $places_left = $event['max_participants'] > 0 ? 
                                $event['max_participants'] - ($event['current_participants'] ?? 0) : 999;
                        ?>
                            <tr>
                                <td><?php echo $counter++; ?>.</td>
                                <td><?php echo htmlspecialchars($event['title']); ?></td>
                                <td><?php echo htmlspecialchars($event['description']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($event['event_date'])); ?></td>
                                <td><?php echo $event['event_time'] ?? '—'; ?></td>
                                <td><?php echo htmlspecialchars($event['location'] ?? '—'); ?></td>
                                <td class="action-cell">
                                    <?php if (isLoggedIn()): ?>
                                        <?php if (!$is_registered): ?>
                                            <?php if ($places_left > 0): ?>
                                                <form method="POST" action="register_for_event.php">
                                                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                                    <button type="submit" class="btn-register-small">Записаться</button>
                                                </form>
                                            <?php else: ?>
                                                <span class="full-badge">Мест нет</span>
                                            <?php endif; ?>
                                        <?php elseif ($reg_status === 'pending'): ?>
                                            <span class="status-badge pending">Ожидает</span>
                                        <?php elseif ($reg_status === 'approved'): ?>
                                            <span class="status-badge approved">Записан</span>
                                        <?php elseif ($reg_status === 'rejected'): ?>
                                            <span class="status-badge rejected">Отклонено</span>
                                        <?php elseif ($reg_status === 'attended'): ?>
                                            <span class="status-badge attended">Посещено</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="login.php" class="btn-register-small">Войдите</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
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

    <!-- подключение js -->
    <script src="js/events.js"></script>
</body>
</html>