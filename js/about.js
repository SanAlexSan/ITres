document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelector('.team-slides');
    const originals = Array.from(document.querySelectorAll('.team-card'));
    const left = document.querySelector('.arrow-left');
    const right = document.querySelector('.arrow-right');
    const wrapper = document.querySelector('.team-slider');

    if (!slides || originals.length < 3) return;

    const w = 350;
    const g = 40;
    const step = w + g;

    // Дублируем набор карточек несколько раз (чем больше — тем реже заметен «конец»)
    const DUPLICATES = 25;
    for (let i = 0; i < DUPLICATES; i++) {
        originals.forEach(c => {
            const clone = c.cloneNode(true);
            clone.classList.add('clone');
            slides.appendChild(clone);
        });
    }

    const all = document.querySelectorAll('.team-card');

    let px = 0;

    function refresh() {
        const offset = -px + (wrapper.clientWidth / 2 - step);
        slides.style.transform = `translateX(${offset}px)`;

        // Подсветка второй видимой карточки слева → направо
        let visible = [];
        all.forEach((card) => {
            const r = card.getBoundingClientRect();
            const wr = wrapper.getBoundingClientRect();

            if (r.right > wr.left && r.left < wr.right) {
                visible.push({ card, left: r.left });
            }
        });

        visible.sort((a, b) => a.left - b.left);

        all.forEach(c => {
            c.classList.remove('active');
            c.style.opacity = '0.55';
            c.style.transform = 'scale(1)';
        });

        if (visible.length >= 2) {
            const second = visible[1].card;
            second.classList.add('active');
            second.style.opacity = '1';
            second.style.transform = 'scale(1.06)';
        }
    }

    function goRight() {
        px += step;
        refresh();
    }

    function goLeft() {
        px -= step;
        refresh();
    }

    left.addEventListener('click', goLeft);
    right.addEventListener('click', goRight);

    // Автопрокрутка (можно закомментировать)
    let timer = setInterval(goRight, 5000);

    wrapper.addEventListener('mouseenter', () => clearInterval(timer));
    wrapper.addEventListener('mouseleave', () => timer = setInterval(goRight, 5000));

    window.addEventListener('resize', refresh);

    // Первый запуск
    setTimeout(refresh, 100);
});