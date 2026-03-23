<?php
require_once 'includes/connect.php';
require_once 'includes/session.php';

$errors = [];
$success = '';

// Перенаправляем с параметрами для уведомлений
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors) && !empty($success)) {
    header('Location: register.php?success=1');
    exit();
}

// Получаем ошибки из сессии (если есть)
// session_start(); // УБРАТЬ - сессия уже запущена в session.php
if (isset($_SESSION['register_errors'])) {
    $errors = $_SESSION['register_errors'];
    unset($_SESSION['register_errors']);
}

// Если это отправка формы (POST запрос)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем и очищаем данные из формы
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $vk = trim($_POST['vk'] ?? '');
    $direction = $_POST['direction'] ?? '';

    // Валидация данных
    $full_name = preg_replace('/\s+/', ' ', trim($full_name));
    $name_parts = explode(' ', $full_name);

    if (count($name_parts) < 2) {
        $errors[] = 'Введите полное ФИО (минимум фамилия и имя)';
    } elseif (count($name_parts) > 3) {
        $errors[] = 'ФИО не должно содержать больше 3 слов';
    } else {
        // Проверяем, что каждая часть содержит только буквы
        foreach ($name_parts as $part) {
            if (!preg_match('/^[а-яА-ЯёЁ\-\s]+$/u', $part)) {
                $errors[] = 'ФИО должно содержать только буквы и дефис';
                break;
            }
        }
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Введите корректный email';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль должен содержать минимум 6 символов';
    }

    if (!preg_match('/^[0-9+\-\s\(\)]+$/', $phone)) {
        $errors[] = 'Введите корректный номер телефона';
    }

    if (!filter_var($vk, FILTER_VALIDATE_URL)) {
        $errors[] = 'Введите корректную ссылку на страницу ВК';
    }

    // Если нет ошибок, сохраняем в БД
    if (empty($errors)) {
        try {
            // Проверяем, не занят ли email
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = 'Этот email уже зарегистрирован';
            } else {
                // Хешируем пароль
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Определяем роль
                $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                $user_count = $stmt->fetchColumn();
                $role = ($user_count == 0) ? 'admin' : 'user';

                // Вставляем пользователя
                $sql = "INSERT INTO users (full_name, email, password_hash, phone, vk_link, direction, role) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$full_name, $email, $hashed_password, $phone, $vk, $direction, $role]);

                $_SESSION['register_success'] = 'Регистрация прошла успешно! Теперь вы можете войти.';
                if ($role === 'admin') {
                    $_SESSION['register_success'] .= ' Вы назначены администратором.';
                }
                header('Location: register.php?success=1');
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['register_errors'] = ['Ошибка при регистрации. Попробуйте позже.'];
            header('Location: register.php');
            exit();
        }
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
    <link rel="stylesheet" href="css/auth.css">
</head>
<body>
    <!-- Подключаем уведомления -->
    <script src="js/notifications.js"></script>
    
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                notifications.success('Регистрация прошла успешно! Теперь вы можете войти.');
            });
        </script>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                <?php foreach ($errors as $error): ?>
                    notifications.error('<?php echo addslashes($error); ?>');
                <?php endforeach; ?>
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

    <!-- форма регистрации -->
    <section class="auth2-section">
        <div class="auth-container">
            <h1>Регистрация</h1>            
            <form action="register.php" method="POST" class="auth-form">
                <input type="text" name="full_name" placeholder="ФИО" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <input type="tel" name="phone" placeholder="Номер телефона" required>
                <input type="url" name="vk" placeholder="Ссылка на страницу ВК" required>
                <select name="direction" required>
                    <option value="" disabled selected>Выберите направление</option>
                    <option value="Frontend">Frontend</option>
                    <option value="Дизайн">Дизайн</option>
                    <option value="Telegram-боты">Telegram-боты</option>
                    <option value="Другое">Другое</option>
                </select>
                <button type="submit" class="auth-submit">Зарегистрироваться</button>
            </form>
            <div class="auth-switch">
                Уже есть аккаунт? <a href="login.php">Войти</a>
            </div>
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

</body>
</html>