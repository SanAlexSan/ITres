<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

// Обработка удаления
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM achievements WHERE id = ?");
        $stmt->execute([$id]);
        $success = 'Достижение удалено';
        header('Location: admin_achievements.php?success=' . urlencode($success));
        exit();
    } catch (PDOException $e) {
        $error = 'Ошибка при удалении';
    }
}

// Получаем все достижения
$stmt = $pdo->query("
    SELECT * FROM achievements 
    ORDER BY achievement_date DESC
");
$achievements = $stmt->fetchAll();

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
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
                <h1>Управление достижениями</h1>
                <a href="admin_achievement_edit.php?action=add" class="btn-add">Добавить достижение</a>
            </div>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (empty($achievements)): ?>
                <div class="empty-state">
                    <p>Нет добавленных достижений</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Дата</th>
                            <th>Действия</th>
                        </thead>
                    <tbody>
                        <?php foreach ($achievements as $ach): ?>
                        <tr>
                            <td><?php echo $ach['id']; ?></td>
                            <td><?php echo htmlspecialchars($ach['title']); ?></td>
                            <td><?php echo date('d.m.Y', strtotime($ach['achievement_date'])); ?></td>
                            <td class="actions">
                                <a href="admin_achievement_edit.php?action=edit&id=<?php echo $ach['id']; ?>" class="btn-action btn-edit" title="Редактировать">✏️</a>
                                <a href="admin_achievements.php?delete=<?php echo $ach['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Удалить достижение?')" title="Удалить">🗑️</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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