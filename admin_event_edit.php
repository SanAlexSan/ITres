<?php
require_once 'includes/session.php';
require_once 'includes/connect.php';

if (!isAdmin()) {
    header('Location: index.php');
    exit();
}

$action = $_GET['action'] ?? 'add';
$event_id = $_GET['id'] ?? 0;
$event = null;
$old_is_future = null;

if ($action === 'edit' && $event_id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        header('Location: admin_events.php');
        exit();
    }    $old_is_future = $event['is_future'];
}

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? null;
    $location = trim($_POST['location'] ?? '');
    $max_participants = intval($_POST['max_participants'] ?? 0);
    $is_future = isset($_POST['is_future']) ? 1 : 0;
    
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Введите название мероприятия';
    }
    if (empty($event_date)) {
        $errors[] = 'Выберите дату проведения';
    }
    
    if (empty($errors)) {
        try {
            if ($action === 'add') {
                $stmt = $pdo->prepare("
                    INSERT INTO events (title, description, event_date, event_time, location, max_participants, is_future) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$title, $description, $event_date, $event_time, $location, $max_participants, $is_future]);
                $success = 'Мероприятие успешно создано';
            } else {
                $stmt = $pdo->prepare("
                    UPDATE events 
                    SET title = ?, description = ?, event_date = ?, event_time = ?, 
                        location = ?, max_participants = ?, is_future = ?
                    WHERE id = ?
                ");
                $stmt->execute([$title, $description, $event_date, $event_time, $location, $max_participants, $is_future, $event_id]);
                $success = 'Мероприятие успешно обновлено';
                
                // Если галочка is_future была снята (было 1, стало 0)
                if ($old_is_future == 1 && $is_future == 0) {
                    // Обновляем статусы всех подтверждённых записей на "attended"
                    $stmt = $pdo->prepare("
                        UPDATE registrations 
                        SET status = 'attended' 
                        WHERE event_id = ? AND status = 'approved'
                    ");
                    $stmt->execute([$event_id]);
                    $updated = $stmt->rowCount();
                    if ($updated > 0) {
                        $success .= " ($updated участников теперь могут оставить отзыв)";
                    }
                }
            }
            
            header('Location: admin_events.php?success=' . urlencode($success));
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
    <title>ITres - <?php echo $action === 'add' ? 'Создание' : 'Редактирование'; ?> мероприятия</title>
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
                <h1><?php echo $action === 'add' ? 'Создание мероприятия' : 'Редактирование мероприятия'; ?></h1>
                <a href="admin_events.php" class="btn-back">← Назад</a>
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
                    <label>Название мероприятия *</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($event['title'] ?? ''); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Дата *</label>
                        <input type="date" name="event_date" value="<?php echo $event['event_date'] ?? ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Время</label>
                        <input type="time" name="event_time" value="<?php echo $event['event_time'] ?? ''; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Место проведения</label>
                    <input type="text" name="location" value="<?php echo htmlspecialchars($event['location'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label>Описание</label>
                    <textarea name="description" rows="6"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Макс. участников (0 = без ограничений)</label>
                        <input type="number" name="max_participants" value="<?php echo $event['max_participants'] ?? 0; ?>" min="0">
                    </div>
                    <div class="form-group">
                        <label>Статус</label>
                        <label style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
                            <input type="checkbox" name="is_future" value="1" <?php echo ($event['is_future'] ?? 1) ? 'checked' : ''; ?>>
                            Будущее мероприятие (если снять галочку — будет в "Мы уже провели")
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <?php echo $action === 'add' ? 'Создать' : 'Сохранить'; ?>
                    </button>
                    <a href="admin_events.php" class="btn-cancel">Отмена</a>
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