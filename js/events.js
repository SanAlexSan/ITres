document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.item');
    const pastSection   = document.querySelector('.section-3');
    const futureSection = document.querySelector('.section-4');

    function showSection(target) {
        // 1. У всех кнопок убираем класс active
        toggleButtons.forEach(btn => btn.classList.remove('active'));

        // 2. Находим кнопку с нужным data-target и добавляем active
        const activeButton = document.querySelector(`.item[data-target="${target}"]`);
        if (activeButton) {
            activeButton.classList.add('active');
        }

        // 3. Показываем / скрываем соответствующие блоки
        if (target === 'past') {
            pastSection.style.display = 'flex';
            futureSection.style.display = 'none';
        } else if (target === 'future') {
            pastSection.style.display = 'none';
            futureSection.style.display = 'flex';
        }
    }

    // Вешаем обработчик клика на каждую кнопку
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const target = this.getAttribute('data-target');
            showSection(target);
        });
    });

    // По умолчанию показываем прошедшие мероприятия
    showSection('past');

});