<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

// Обработка смены роли
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $user_id]);
        $success = 'Роль пользователя изменена';
        header('Location: admin_users.php?success=' . urlencode($success));
        exit();
    } catch (PDOException $e) {
        $error = 'Ошибка при изменении роли';
    }
}

// Поиск
$search = $_GET['search'] ?? '';
$where = '';
$params = [];

if (!empty($search)) {
    $where = "WHERE full_name ILIKE ? OR email ILIKE ?";
    $params = ["%$search%", "%$search%"];
}

// Получаем всех пользователей
$sql = "SELECT id, full_name, email, role, created_at FROM users ORDER BY id";
if (!empty($where)) {
    $sql = "SELECT id, full_name, email, role, created_at FROM users $where ORDER BY id";
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$users = $stmt->fetchAll();

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
                <h1>Управление пользователями</h1>
                <form method="GET" class="search-form">
                    <input type="text" name="search" class="search-input" placeholder="Поиск по имени или email" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-search">Найти</button>
                    <?php if (!empty($search)): ?>
                        <a href="admin_users.php" class="btn-reset">Сбросить</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <p>Пользователи не найдены</p>
                </div>
            <?php else: ?>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ФИО</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Дата регистрации</th>
                                <th>Действия</th>
                            </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <?php $isCurrentUser = ($user['id'] == $_SESSION['user_id']); ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <?php if (!$isCurrentUser): ?>
                                            <form method="POST" class="role-form">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <select name="role" class="role-select" onchange="this.form.submit()">
                                                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Пользователь</option>
                                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Администратор</option>
                                                </select>
                                            </form>
                                        <?php else: ?>
                                            <span class="role-badge admin"><?php echo $user['role'] == 'admin' ? 'Администратор' : 'Пользователь'; ?> (Вы)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                                    <td class="actions">
                                        <a href="admin_user_view.php?id=<?php echo $user['id']; ?>" class="btn-action btn-view" title="Просмотр">👁️</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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