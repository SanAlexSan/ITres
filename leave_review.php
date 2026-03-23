<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

// Только для авторизованных
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$event_id = $_GET['event_id'] ?? 0;

// Проверяем, что пользователь посещал это мероприятие
$stmt = $pdo->prepare("
    SELECT * FROM registrations 
    WHERE user_id = ? AND event_id = ? AND status = 'attended'
");
$stmt->execute([$_SESSION['user_id'], $event_id]);
$registration = $stmt->fetch();

if (!$registration) {
    $_SESSION['error'] = 'Вы не можете оставить отзыв на это мероприятие';
    header('Location: events.php');
    exit();
}

// Проверяем, не оставлял ли уже отзыв
$stmt = $pdo->prepare("SELECT id FROM comments WHERE user_id = ? AND event_id = ?");
$stmt->execute([$_SESSION['user_id'], $event_id]);
if ($stmt->fetch()) {
    $_SESSION['error'] = 'Вы уже оставили отзыв на это мероприятие';
    header('Location: events.php');
    exit();
}

// Получаем информацию о мероприятии
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: events.php');
    exit();
}

// Обработка отправки отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating'] ?? 0);
    $text = trim($_POST['text'] ?? '');
    
    $errors = [];
    
    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Выберите оценку от 1 до 5';
    }
    if (empty($text)) {
        $errors[] = 'Напишите ваш отзыв';
    }
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO comments (user_id, event_id, rating, text, is_approved) 
                VALUES (?, ?, ?, ?, false)
            ");
            $stmt->execute([$_SESSION['user_id'], $event_id, $rating, $text]);
            
            $_SESSION['success'] = 'Спасибо за отзыв! Он будет опубликован после модерации.';
            header('Location: events.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = 'Ошибка при сохранении отзыва. Попробуйте позже.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оставить отзыв - <?php echo htmlspecialchars($event['title']); ?></title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/review.css">
    <script src="js/notifications.js"></script>
</head>
<body>
    <?php if (!empty($errors)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php foreach ($errors as $error): ?>
                    notifications.error('<?php echo addslashes($error); ?>');
                <?php endforeach; ?>
            });
        </script>
    <?php endif; ?>

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

    <section class="review-section">
        <div class="review-container">
            <div class="review-header">
                <h1>Оставить отзыв</h1>
                <p>Мероприятие: <strong><?php echo htmlspecialchars($event['title']); ?></strong></p>
                <p>Дата: <?php echo date('d.m.Y', strtotime($event['event_date'])); ?></p>
            </div>

            <form method="POST" class="review-form">
                <div class="form-group">
                    <label>Ваша оценка</label>
                    <div class="rating-stars">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                            <label for="star<?php echo $i; ?>" class="star">★</label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Ваш отзыв</label>
                    <textarea name="text" rows="6" placeholder="Расскажите о ваших впечатлениях..." required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Отправить отзыв</button>
                    <a href="events.php" class="btn-cancel">Отмена</a>
                </div>
            </form>
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