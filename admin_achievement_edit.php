<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$action = $_GET['action'] ?? 'add';
$achievement_id = $_GET['id'] ?? 0;
$achievement = null;

if ($action === 'edit' && $achievement_id) {
    $stmt = $pdo->prepare("SELECT * FROM achievements WHERE id = ?");
    $stmt->execute([$achievement_id]);
    $achievement = $stmt->fetch();
    
    if (!$achievement) {
        header('Location: admin_achievements.php');
        exit();
    }
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $achievement_date = $_POST['achievement_date'] ?? '';
    $image = $_POST['image'] ?? 'img/achievements/default.png';
    
    $errors = [];
    
    if (empty($title)) $errors[] = 'Введите название достижения';
    if (empty($achievement_date)) $errors[] = 'Выберите дату';
    
    if (empty($errors)) {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("
                    INSERT INTO achievements (title, description, achievement_date, image) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$title, $description, $achievement_date, $image]);
                $success = 'Достижение добавлено';
            } else {
                $stmt = $pdo->prepare("
                    UPDATE achievements 
                    SET title = ?, description = ?, achievement_date = ?, image = ?
                    WHERE id = ?
                ");
                $stmt->execute([$title, $description, $achievement_date, $image, $achievement_id]);
                $success = 'Достижение обновлено';
            }
            
            header('Location: admin_achievements.php?success=' . urlencode($success));
            exit();
            
        } catch (PDOException $e) {
            $error = 'Ошибка: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres - <?php echo $action === 'add' ? 'Добавление' : 'Редактирование'; ?> достижения</title>
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
                <h1><?php echo $action === 'add' ? 'Добавление достижения' : 'Редактирование достижения'; ?></h1>
                <a href="admin_achievements.php" class="btn-back">← Назад</a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <?php foreach ($errors as $err): ?>
                        <p><?php echo htmlspecialchars($err); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="admin-form">
                <div class="form-group">
                    <label>Название достижения *</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($achievement['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description" rows="4"><?php echo htmlspecialchars($achievement['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Дата получения *</label>
                        <input type="date" name="achievement_date" value="<?php echo $achievement['achievement_date'] ?? ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Изображение (путь к файлу)</label>
                    <input type="text" name="image" value="<?php echo htmlspecialchars($achievement['image'] ?? 'img/achievements/default.png'); ?>">
                    <small class="form-hint">Пример: img/achievements/diplom.jpg</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <?php echo $action === 'add' ? 'Добавить' : 'Сохранить'; ?>
                    </button>
                    <a href="admin_achievements.php" class="btn-cancel">Отмена</a>
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