<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$event_id = $_GET['event_id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$event_id]);
$event = $stmt->fetch();

if (!$event) {
    header('Location: admin_events.php');
    exit();
}

// Изменение статуса участника
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registration_id'], $_POST['status'])) {
    $stmt = $pdo->prepare("UPDATE registrations SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['registration_id']]);
    header("Location: admin_event_participants.php?event_id=$event_id");
    exit();
}

// Получаем участников
$stmt = $pdo->prepare("
    SELECT r.*, u.full_name, u.email, u.phone, u.vk_link 
    FROM registrations r
    JOIN users u ON r.user_id = u.id
    WHERE r.event_id = ?
    ORDER BY r.registered_at DESC
");
$stmt->execute([$event_id]);
$participants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres - Участники мероприятия</title>
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
                <h1>Участники: <?php echo htmlspecialchars($event['title']); ?></h1>
                <a href="admin_events.php" class="btn-back">← К списку</a>
            </div>

            <div class="event-info-small">
                <p>📅 <?php echo date('d.m.Y', strtotime($event['event_date'])); ?> 
                   <?php echo $event['event_time'] ? '⏰ ' . $event['event_time'] : ''; ?></p>
                <p>📍 <?php echo htmlspecialchars($event['location'] ?? 'Место уточняется'); ?></p>
                <p>👥 Участников: <?php echo count($participants); ?> / <?php echo $event['max_participants'] ?: '∞'; ?></p>
            </div>

            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>ФИО</th>
                            <th>Email</th>
                            <th>Телефон</th>
                            <th>ВК</th>
                            <th>Дата записи</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $p): ?>
                        <tr>
                            <td><?php echo $p['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($p['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($p['email']); ?></td>
                            <td><?php echo htmlspecialchars($p['phone'] ?? '-'); ?></td>
                            <td>
                                <?php if ($p['vk_link']): ?>
                                    <a href="<?php echo htmlspecialchars($p['vk_link']); ?>" target="_blank">VK</a>
                                <?php else: ?>-<?php endif; ?>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($p['registered_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $p['status']; ?>">
                                    <?php 
                                        $statuses = [
                                            'pending' => 'Ожидает',
                                            'approved' => 'Подтверждено',
                                            'rejected' => 'Отклонено'
                                        ];
                                        echo $statuses[$p['status']] ?? $p['status'];
                                    ?>
                                </span>
                            </td>
                            <td class="actions">
                                <?php if ($p['status'] === 'pending'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="registration_id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn-action btn-approve" title="Подтвердить">✅</button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="registration_id" value="<?php echo $p['id']; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn-action btn-reject" title="Отклонить">❌</button>
                                    </form>
                                <?php endif; ?>
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