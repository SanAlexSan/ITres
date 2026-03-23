<?php
require_once 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/auth.css">
    <script src="js/notifications.js"></script>
</head>
<body>
    <?php if (isset($_SESSION['join_success'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof notifications !== 'undefined' && notifications.success) {
                    notifications.success('<?php echo addslashes($_SESSION['join_success']); ?>');
                } else {
                    alert('<?php echo addslashes($_SESSION['join_success']); ?>');
                }
            });
        </script>
        <?php unset($_SESSION['join_success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['join_error'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof notifications !== 'undefined' && notifications.error) {
                    notifications.error('<?php echo addslashes($_SESSION['join_error']); ?>');
                } else {
                    alert('<?php echo addslashes($_SESSION['join_error']); ?>');
                }
            });
        </script>
        <?php unset($_SESSION['join_error']); ?>
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

    <!-- главный блок -->
    <section class="section-2">
        <div class="section2-div">
            <img src="img/desktop.png" alt="" class="section2-desktop">
            <img src="img/cursor.png" alt="" class="section2-cursor">
            <img src="img/trophy.png" alt="" class="section2-trophy">
            <img src="img/glass.png" alt="" class="section2-glass">
            <div class="section2-infa">
                <h1>ITres</h1>
                <p>Вовлекаем в IT, развиваем навыки, создаем будущее!</p>
                <a href="#join" class="join-link">Записаться</a>
            </div>
        </div>
    </section>
    
    <!-- о нас -->
    <section class="section-3">
        <div class="section3-div">
            <div class="section3-img">
                <img src="img/image 1.png" alt="фото">
                <div class="section3-grid">
                    <img src="img/image 2.png" alt="фото">
                    <img src="img/image 3.png" alt="фото">
                    <img src="img/image 4.png" alt="фото">
                    <img src="img/image 5.png" alt="фото">
                </div>
            </div>
            <div class="section3-text">
                <h1>О нас</h1>
                <p>ITres — это не просто студенческое объединение. 
                    Это большая и дружная IT-семья Ставропольского ГАУ, 
                    где каждый быстро находит свое место, а атмосфера 
                    поддержки и взаимопомощи помогает раскрыть потенциал.
                </p>
                <p>Мы собираемся вместе, чтобы учиться, 
                    творить, побеждать и проводить время среди единомышленников. 
                    Здесь ценят как свежий взгляд, так и опыт — каждый 
                    может реализовать свои идеи, найти вдохновение и команду 
                    для новых свершений.
                </p>
                <p>В ITres царит тепло, искренний 
                    интерес к общему делу и готовность помочь. Мы растем 
                    вместе, радуемся успехам друг друга и всегда поддерживаем 
                    в сложные моменты.
                </p>
            </div>    
        </div>
    </section>

    <!-- Направления -->
    <section class="section-4">
        <div class="section4-div">
            <div class="section4-text">
                <h1>Направления</h1>
                <p>
                    В ITres ты можешь развиваться сразу в нескольких направлениях.<br>
                    Выбирай направление — и мы поможем тебе вырасти!
                </p>
            </div>
            <div class="section4-cards">
                <div class="card">
                    <img src="img/frontend_logo.png" alt="Frontend" class="card-icon">
                    <h3>Frontend</h3>
                    <p>Разрабатываем стильные сайты с помощью HTML, CSS и JavaScript</p>
                </div>
                <div class="card">
                    <img src="img/telegram_logo.png" alt="Telegram-боты" class="card-icon">
                    <h3>Telegram-боты</h3>
                    <p>Делаем умных помощников для разных задач</p>
                </div>
                <div class="card">
                    <img src="img/design_logo.png" alt="Дизайн" class="card-icon">
                    <h3>Дизайн</h3>
                    <p>Оформляем сайты и создаём картинки в Figma и Photoshop</p>
                </div>
            </div>
        </div>
    </section>

    <!-- инфа -->
    <section class="section-5">
        <div class="section5-stats">
            <div class="stat-item">
                <div class="stat-number">560</div>
                <div class="stat-label">подписчиков</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">47</div>
                <div class="stat-label">мероприятий</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">13</div>
                <div class="stat-label">хакатонов</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">10</div>
                <div class="stat-label">наград</div>
            </div>
        </div>
    </section>

    <!-- присоединиться -->
    <section class="section-6" id="join">
        <div class="section6-div">
            <img src="img/image6.png" alt="Команда ITres" class="section6-img">
            <div class="section6-right">
                <h1>Присоединяйся!</h1>
                <p class="section6-text">
                    Здесь не просто клуб — здесь твоя IT-семья ждёт именно тебя
                </p>
                <form action="join_request.php" method="POST" class="section6-form">
                    <input type="text" name="full_name" placeholder="ФИО" required>
                    <input type="tel" name="phone" placeholder="Номер телефона" required>
                    <input type="url" name="vk_link" placeholder="Ссылка на страницу ВК" required>
                    <button type="submit" class="form-submit">Записаться</button>
                </form>
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