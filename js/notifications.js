/**
 * Система уведомлений для ITres
 */
class NotificationSystem {
    constructor() {
        this.container = null;
        // Ждём загрузки DOM перед инициализацией
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.init());
        } else {
            this.init();
        }
    }
    
    init() {
        // Проверяем, существует ли контейнер
        let container = document.querySelector('.notification-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'notification-container';
            document.body.appendChild(container);
        }
        this.container = container;
    }
    
    show(message, type = 'info', duration = 5000) {
        // Убедимся, что контейнер существует
        if (!this.container) {
            this.init();
        }
        
        const types = {
            success: { icon: '✅', title: 'Успех!' },
            error: { icon: '❌', title: 'Ошибка!' },
            warning: { icon: '⚠️', title: 'Внимание!' },
            info: { icon: 'ℹ️', title: 'Информация' }
        };
        
        const settings = types[type] || types.info;
        
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <div class="notification-icon">${settings.icon}</div>
            <div class="notification-content">
                <div class="notification-title">${settings.title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close">×</button>
        `;
        
        this.container.appendChild(notification);
        
        // Анимация появления
        setTimeout(() => notification.classList.add('show'), 10);
        
        // Автоматическое закрытие
        const timeout = setTimeout(() => {
            this.close(notification);
        }, duration);
        
        // Закрытие по кнопке
        const closeBtn = notification.querySelector('.notification-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                clearTimeout(timeout);
                this.close(notification);
            });
        }
        
        return notification;
    }
    
    close(notification) {
        notification.classList.remove('show');
        notification.classList.add('hide');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }
    
    success(message, duration) {
        return this.show(message, 'success', duration);
    }
    
    error(message, duration) {
        return this.show(message, 'error', duration);
    }
    
    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }
    
    info(message, duration) {
        return this.show(message, 'info', duration);
    }
}

// Создаём глобальный экземпляр
const notifications = new NotificationSystem();