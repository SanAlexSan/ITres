<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

// Только для админов
if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

// Обработка удаления
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $success = "Мероприятие удалено";
    } catch (PDOException $e) {
        $error = "Ошибка при удалении";
    }
}

// Получаем все мероприятия
$stmt = $pdo->query("
    SELECT e.*, 
           COUNT(r.id) as registrations_count,
           SUM(CASE WHEN r.status = 'approved' THEN 1 ELSE 0 END) as approved_count
    FROM events e
    LEFT JOIN registrations r ON e.id = r.event_id
    GROUP BY e.id
    ORDER BY e.event_date DESC
");
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="js/notifications.js"></script>
</head>
<body>
    <body>
    <?php if (isset($_GET['success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                notifications.success('<?php echo addslashes($_GET['success']); ?>');
            });
        </script>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                notifications.error('<?php echo addslashes($_GET['error']); ?>');
            });
        </script>
    <?php endif; ?>
    <!-- Шапка -->
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
                <h1>Управление мероприятиями</h1>
                <a href="admin_event_edit.php?action=add" class="btn-add">Создать мероприятие</a>
            </div>

            <?php if (isset($success)): ?>
                <script>document.addEventListener('DOMContentLoaded', function() { notifications.success('<?php echo $success; ?>'); });</script>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <script>document.addEventListener('DOMContentLoaded', function() { notifications.error('<?php echo $error; ?>'); });</script>
            <?php endif; ?>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Дата</th>
                            <th>Время</th>
                            <th>Место</th>
                            <th>Участники</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo $event['id']; ?></td>
                            <td><?php echo htmlspecialchars($event['title']); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($event['event_date'])); ?></td>
                            <td><?php echo $event['event_time'] ?? '-'; ?></td>
                            <td><?php echo htmlspecialchars($event['location'] ?? '-'); ?></td>
                            <td>
                                <?php echo $event['current_participants'] ?? 0; ?>/<?php echo $event['max_participants'] ?: '∞'; ?>
                                <br><small>Записей: <?php echo $event['registrations_count']; ?></small>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $event['is_future'] ? 'upcoming' : 'past'; ?>">
                                    <?php echo $event['is_future'] ? 'Предстоит' : 'Прошло'; ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="admin_event_participants.php?event_id=<?php echo $event['id']; ?>" 
                                   class="btn-action btn-view" title="Участники">👥</a>
                                <a href="admin_event_edit.php?action=edit&id=<?php echo $event['id']; ?>" 
                                   class="btn-action btn-edit" title="Редактировать">✏️</a>
                                <a href="admin_events.php?delete=<?php echo $event['id']; ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Удалить мероприятие? Это действие нельзя отменить.')"
                                   title="Удалить">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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