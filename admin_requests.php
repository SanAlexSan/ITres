<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

// Обработка отметки "Связались"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $contacted = isset($_POST['contacted']) ? true : false;
    
    try {
        $stmt = $pdo->prepare("
            UPDATE join_requests 
            SET contacted = ?, contacted_at = CASE WHEN ? THEN CURRENT_TIMESTAMP ELSE NULL END
            WHERE id = ?
        ");
        $stmt->execute([$contacted, $contacted, $request_id]);
        $success = $contacted ? 'Отмечено как "Связались"' : 'Отметка снята';
    } catch (PDOException $e) {
        $error = 'Ошибка при обновлении';
    }
}

// Получаем все заявки
$stmt = $pdo->query("
    SELECT * FROM join_requests 
    ORDER BY 
        CASE WHEN contacted = false THEN 1 ELSE 2 END,
        created_at DESC
");
$requests = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/admin-requests.css">
    <script src="js/notifications.js"></script>
</head>
<body>
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
                <h1>Заявки на вступление</h1>
                <div class="stats-badge">
                    <span class="badge pending">Новых: <?php echo count(array_filter($requests, fn($r) => !$r['contacted'])); ?></span>
                </div>
            </div>

            <?php if (isset($success)): ?>
                <script>document.addEventListener('DOMContentLoaded', function() { notifications.success('<?php echo $success; ?>'); });</script>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <script>document.addEventListener('DOMContentLoaded', function() { notifications.error('<?php echo $error; ?>'); });</script>
            <?php endif; ?>

            <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <p>Нет заявок на вступление</p>
                </div>
            <?php else: ?>
                <div class="requests-list">
                    <?php foreach ($requests as $req): ?>
                        <div class="request-card <?php echo $req['contacted'] ? 'contacted' : 'pending'; ?>">
                            <div class="request-header">
                                <div>
                                    <h3><?php echo htmlspecialchars($req['full_name']); ?></h3>
                                    <span class="request-date">
                                        <?php echo date('d.m.Y H:i', strtotime($req['created_at'])); ?>
                                    </span>
                                </div>
                                <span class="status-badge <?php echo $req['contacted'] ? 'contacted' : 'pending'; ?>">
                                    <?php echo $req['contacted'] ? 'Связались' : 'Новая'; ?>
                                </span>
                            </div>
                            
                            <div class="request-details">
                                <p><strong>Телефон:</strong> <?php echo htmlspecialchars($req['phone']); ?></p>
                                <p><strong>ВКонтакте:</strong> 
                                    <a href="<?php echo htmlspecialchars($req['vk_link']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($req['vk_link']); ?>
                                    </a>
                                </p>
                                <?php if ($req['contacted_at']): ?>
                                    <p><strong>Связались:</strong> <?php echo date('d.m.Y H:i', strtotime($req['contacted_at'])); ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <form method="POST" class="request-form">
                                <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                
                                <div class="request-actions">
                                    <?php if (!$req['contacted']): ?>
                                        <button type="submit" name="contacted" value="1" class="btn-contact">📞 Отметить как "Связались"</button>
                                    <?php endif; ?>
                                </div>
                            </form>
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