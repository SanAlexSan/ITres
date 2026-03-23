<?php
require_once 'includes/connect.php';
require_once 'includes/session.php';

// Если это отправка формы (POST запрос)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Здесь код обработки входа
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    try {
        if (empty($email) || empty($password)) {
            $error = 'Заполните все поля';
        } else {
            $stmt = $pdo->prepare("SELECT id, full_name, email, password_hash, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                
                header('Location: profile.php');
                exit();
            } else {
                $error = 'Неверный email или пароль';
            }
        }
    } catch (PDOException $e) {
        $error = 'Ошибка базы данных';
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

    <!-- форма входа -->
    <section class="auth1-section">
        <div class="auth-container">
            <h1>Вход</h1>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form action="login.php" method="POST" class="auth-form">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit" class="auth-submit">Войти</button>
            </form>
            <div class="auth-switch">
                Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
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