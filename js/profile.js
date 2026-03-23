document.addEventListener('DOMContentLoaded', function() {
    // Переключение вкладок
    const menuItems = document.querySelectorAll('.profile-menu-item');
    const tabs = document.querySelectorAll('.profile-tab');
    
    // Убираем hash из URL если есть
    if (window.location.hash) {
        const activeTab = document.querySelector(window.location.hash);
        if (activeTab && activeTab.classList.contains('profile-tab')) {
            // Скрываем все табы
            tabs.forEach(tab => tab.classList.remove('active'));
            // Показываем нужный
            activeTab.classList.add('active');
            
            // Обновляем активный пункт меню
            menuItems.forEach(item => {
                item.classList.remove('active');
                if (item.getAttribute('href') === window.location.hash) {
                    item.classList.add('active');
                }
            });
        }
    }
    
    // Обработка кликов по меню
    menuItems.forEach(item => {
        // Проверяем, что это не кнопка выхода И не внешняя ссылка
        const href = item.getAttribute('href');
        const isExternalLink = href && (href.endsWith('.php') || href.includes('.php?'));
        
        if (!item.classList.contains('logout') && !isExternalLink) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                
                // Скрываем все табы
                tabs.forEach(tab => tab.classList.remove('active'));
                
                // Показываем нужный
                document.querySelector(targetId).classList.add('active');
                
                // Обновляем активный пункт меню
                menuItems.forEach(menu => menu.classList.remove('active'));
                this.classList.add('active');
                
                // Обновляем URL без перезагрузки
                history.pushState(null, null, targetId);
            });
        }
        // Для кнопки выхода и внешних ссылок (admin_*.php) ничего не делаем — ссылка работает сама
    });
});