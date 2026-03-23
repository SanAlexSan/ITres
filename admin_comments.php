<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

// Только для админов
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$success = null;
$error = null;

// Обработка одобрения/отклонения
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'], $_POST['action'])) {
    $comment_id = $_POST['comment_id'];
    $action = $_POST['action'];
    
    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE comments SET is_approved = true WHERE id = ?");
            $stmt->execute([$comment_id]);
            $success = 'Отзыв одобрен';
        } elseif ($action === 'reject') {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute([$comment_id]);
            $success = 'Отзыв удалён';
        }
    } catch (PDOException $e) {
        $error = 'Ошибка при обработке';
    }
}

// Получаем все отзывы
$stmt = $pdo->query("
    SELECT c.*, u.full_name, u.email, e.title as event_title 
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN events e ON c.event_id = e.id
    ORDER BY 
        CASE WHEN c.is_approved = false THEN 1 ELSE 2 END,
        c.created_at DESC
");
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/admin-comments.css">
    <script src="js/notifications.js"></script>
</head>
<body>
    <?php if (isset($success)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                notifications.success('<?php echo addslashes($success); ?>');
            });
        </script>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                notifications.error('<?php echo addslashes($error); ?>');
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

    <section class="admin-section">
        <div class="admin-container">
            <div class="admin-header">
                <h1>Модерация отзывов</h1>
                <div class="stats-badge">
                    <span class="badge pending">На модерации: <?php echo count(array_filter($comments, fn($c) => !$c['is_approved'])); ?></span>
                </div>
            </div>

            <?php if (empty($comments)): ?>
                <div class="empty-state">
                    <p>Нет отзывов</p>
                </div>
            <?php else: ?>
                <div class="comments-list">
                    <?php foreach ($comments as $comment): ?>
                        <div class="comment-card <?php echo $comment['is_approved'] ? 'approved' : 'pending'; ?>">
                            <div class="comment-header">
                                <div class="comment-user">
                                    <strong><?php echo htmlspecialchars($comment['full_name']); ?></strong>
                                    <span class="comment-email">(<?php echo htmlspecialchars($comment['email']); ?>)</span>
                                </div>
                                <div class="comment-rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?php echo $i <= $comment['rating'] ? 'filled' : 'empty'; ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="comment-event">
                                Мероприятие: <strong><?php echo htmlspecialchars($comment['event_title']); ?></strong>
                                <span class="comment-date"><?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?></span>
                            </div>
                            
                            <div class="comment-text">
                                <?php echo nl2br(htmlspecialchars($comment['text'])); ?>
                            </div>
                            
                            <div class="comment-actions">
                                <?php if (!$comment['is_approved']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-approve">Одобрить</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="btn-reject" onclick="return confirm('Удалить отзыв?')">Удалить</button>
                                    </form>
                                <?php else: ?>
                                    <span class="approved-text">Отзыв опубликован</span>
                                <?php endif; ?>
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