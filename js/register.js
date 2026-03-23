document.addEventListener('DOMContentLoaded', function() {
    // Автоматическое закрытие уведомлений через 5 секунд
    const toasts = document.querySelectorAll('.toast-notification');
    
    toasts.forEach(toast => {
        // Закрыть через 5 секунд
        setTimeout(() => {
            toast.style.animation = 'fadeOut 0.3s forwards';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
        
        // Закрыть по кнопке
        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                toast.style.animation = 'fadeOut 0.3s forwards';
                setTimeout(() => {
                    toast.remove();
                }, 300);
            });
        }
    });
});