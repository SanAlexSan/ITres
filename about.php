<?php
require_once 'includes/session.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ITres</title>
    <link rel="stylesheet" href="css/about.css">
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

    <!-- наша история -->
    <section class="section-2">
        <div class="section2-div">
            <div class="section2-text">
                <h1>Наша история</h1>
                <p>Всё началось 27 апреля 2022 года, когда был опубликован первый пост во ВКонтакте.</p>
                <p>18 мая 2022 года в Точке кипения СтГАУ прошло официальное открытие. В тот день мы поняли: это настоящая семья единомышленников.</p>
                <p>За всё время клуб вырос, стал важной частью жизни сотен студентов и пережил смену руководства. 
                    Зимой 2025 года Анна Соболева передала пост руководителя Александре Головченко.</p>
            </div>
            <div class="section2-photo">
                <img src="img/image 10.png" alt="фото">
                    <div class="section2-grid">
                        <img src="img/image 11.png" alt="фото">
                        <img src="img/image 12.png" alt="фото">
                        <img src="img/image 13.png" alt="фото">
                        <img src="img/image 14.png" alt="фото">
                    </div>
            </div>
        </div>
    </section>

    <!-- Направления -->
    <section class="section-3">
        <div class="section3-div">
            <div class="section3-text">
                <h1>Направления</h1>
                <p>
                    В ITres ты можешь развиваться сразу в нескольких направлениях.<br>
                    Выбирай направление — и мы поможем тебе вырасти!
                </p>
            </div>
            <div class="section3-cards">
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

    <!-- команда -->
    <section class="section-4">
        <h2>Наша команда</h2>
        <p class="team-text">
            Мы - студенты, которые не ждут, пока их чему-то научат.<br>
            Мы сами учимся, помогаем друг другу и создаём проекты, от которых у всех горят глаза.
        </p>
        <div class="team-slider">
        <label for="slide3" class="arrow-left">←</label>
        <label for="slide2" class="arrow-right">→</label>
        <input type="radio" name="team" id="slide1" checked hidden>
        <input type="radio" name="team" id="slide2" hidden>
        <input type="radio" name="team" id="slide3" hidden>
        <div class="team-slides">
            <div class="team-card">
                <div class="team-info">
                    <h3>Эвелина Ельникова</h3>
                    <p>руководитель пресс-центра</p>
                </div>
                <img src="img/evelina.png" alt="Эвелина" class="photo">
            </div>
            <div class="team-card">
                <div class="team-info">
                    <h3>Глория Тулузакова</h3>
                    <p>руководитель дизайна</p>
                </div>
                <img src="img/gloriya.png" alt="Глория" class="photo">
            </div>
            <div class="team-card">
                <div class="team-info">
                    <h3>Антон Егоров</h3>
                    <p>разработчик</p>
                </div>
                <img src="img/anton.png" alt="Антон" class="photo">
            </div>
            <div class="team-card">
                <div class="team-info">
                    <h3>Максим Пашков</h3>
                    <p>фотограф</p>
                </div>
                <img src="img/maksim.png" alt="Антон" class="photo">
            </div>
            <div class="team-card">
                <div class="team-info">
                    <h3>Иван Ганусенко</h3>
                    <p>сторисмейкер</p>
                </div>
                <img src="img/ivan.png" alt="Антон" class="photo">
            </div>
            <div class="team-card">
                <div class="team-info">
                    <h3>Валентина Лепихина</h3>
                    <p>Event-менеджер</p>
                </div>
                <img src="img/valya.png" alt="Антон" class="photo">
            </div>
            <div class="team-card">
                <div class="team-info">
                    <h3>Александра Головченко</h3>
                    <p>руководитель клуба</p>
                </div>
                <img src="img/aleksandra.png" alt="Александра" class="photo">
            </div>
        </div>
    </section>

    <!-- почему ITres? -->
    <section class="section-5">
        <div class="section5-div">
            <h1>Почему ITres?</h1>
            <div class="section5-cards">
                <div class="why-card">
                    <h3>Живое общение</h3>
                    <p>Встречаемся, общаемся и кодим вместе</p>
                </div>

                <div class="why-card">
                    <h3>Менторство</h3>
                    <p>Опытные участники помогают новичкам расти — без осуждения и воды</p>
                </div>

                <div class="why-card">
                    <h3>Победы</h3>
                    <p>Занимаем на хакатонах призовые места — и ты можешь стать следующим</p>
                </div>

                <div class="why-card">
                    <h3>Дружная IT-семья</h3>
                    <p>Здесь поддерживают, помогают и радуются твоим успехам как своим</p>
                </div>

                <div class="why-card">
                    <h3>Все уровни</h3>
                    <p>Приходи с любым знанием: научим и дадим возможность проявить себя</p>
                </div>

                <div class="why-card">
                    <h3>Создаём проекты</h3>
                    <p>От идеи до готового результата всей командой</p>
                </div>
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
                <form class="section6-form">
                    <input type="text" id="name" name="name" placeholder="ФИО">
                    <input type="tel" id="phone" name="phone" placeholder="Номер телефона">
                    <input type="url" id="vk" name="vk" placeholder="Ссылка на страницу ВК">
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
    
    <!-- подключение js -->
    <script src="js/about.js"></script>
</body>
</html>