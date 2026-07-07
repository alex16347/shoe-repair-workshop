/**
 * Слайдер для главной страницы
 * Автопрокрутка 3 секунды
 */

class Slider {
    constructor(container) {
        this.container = container;
        this.slides = container.querySelectorAll('.slider-slide');
        this.dotsContainer = container.querySelector('.slider-dots');
        this.prevBtn = container.querySelector('.slider-prev');
        this.nextBtn = container.querySelector('.slider-next');
        
        this.currentSlide = 0;
        this.totalSlides = this.slides.length;
        this.autoplayInterval = null;
        this.autoplayDelay = 3000; // 3 секунды
        this.isTransitioning = false;
        
        this.init();
    }
    
    init() {
        // Создаём индикаторы (точки)
        this.createDots();
        
        // Показываем первый слайд
        this.goToSlide(0);
        
        // Обработчики событий
        this.prevBtn.addEventListener('click', () => this.prev());
        this.nextBtn.addEventListener('click', () => this.next());
        
        // Клавиатура
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prev();
            if (e.key === 'ArrowRight') this.next();
        });
        
        // Touch-события для мобильных
        this.initTouchEvents();
        
        // Запускаем автопрокрутку
        this.startAutoplay();
        
        // Остановка автопрокрутки при наведении
        this.container.addEventListener('mouseenter', () => this.stopAutoplay());
        this.container.addEventListener('mouseleave', () => this.startAutoplay());
        
        // Адаптив: обновляем размеры
        window.addEventListener('resize', () => this.updateSliderHeight());
        this.updateSliderHeight();
    }
    
    createDots() {
        for (let i = 0; i < this.totalSlides; i++) {
            const dot = document.createElement('button');
            dot.className = 'slider-dot';
            dot.setAttribute('aria-label', `Перейти к слайду ${i + 1}`);
            dot.setAttribute('role', 'tab');
            dot.addEventListener('click', () => this.goToSlide(i));
            this.dotsContainer.appendChild(dot);
        }
    }
    
    goToSlide(index) {
        if (this.isTransitioning) return;
        if (index === this.currentSlide) return;
        
        this.isTransitioning = true;
        
        // Ограничиваем индекс
        if (index < 0) index = this.totalSlides - 1;
        if (index >= this.totalSlides) index = 0;
        
        // Убираем активный класс у всех слайдов
        this.slides.forEach(slide => slide.classList.remove('active'));
        
        // Добавляем активный класс текущему слайду
        this.slides[index].classList.add('active');
        
        // Обновляем индикаторы
        const dots = this.dotsContainer.querySelectorAll('.slider-dot');
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === index);
        });
        
        this.currentSlide = index;
        
        // Снимаем блокировку после анимации
        setTimeout(() => {
            this.isTransitioning = false;
        }, 600);
    }
    
    next() {
        this.goToSlide(this.currentSlide + 1);
        this.resetAutoplay();
    }
    
    prev() {
        this.goToSlide(this.currentSlide - 1);
        this.resetAutoplay();
    }
    
    startAutoplay() {
        if (this.autoplayInterval) return;
        this.autoplayInterval = setInterval(() => {
            this.next();
        }, this.autoplayDelay);
    }
    
    stopAutoplay() {
        if (this.autoplayInterval) {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        }
    }
    
    resetAutoplay() {
        this.stopAutoplay();
        this.startAutoplay();
    }
    
    initTouchEvents() {
        let startX = 0;
        let startY = 0;
        let isSwiping = false;
        
        this.container.addEventListener('touchstart', (e) => {
            startX = e.changedTouches[0].screenX;
            startY = e.changedTouches[0].screenY;
            isSwiping = true;
            this.stopAutoplay();
        }, { passive: true });
        
        this.container.addEventListener('touchmove', (e) => {
            if (!isSwiping) return;
            // Блокируем вертикальный скролл при свайпе по слайдеру
            const diffX = Math.abs(e.changedTouches[0].screenX - startX);
            const diffY = Math.abs(e.changedTouches[0].screenY - startY);
            if (diffX > diffY) {
                e.preventDefault();
            }
        }, { passive: false });
        
        this.container.addEventListener('touchend', (e) => {
            if (!isSwiping) return;
            isSwiping = false;
            
            const endX = e.changedTouches[0].screenX;
            const diff = startX - endX;
            
            // Минимальное расстояние для свайпа (50px)
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    this.next();
                } else {
                    this.prev();
                }
            }
            
            this.startAutoplay();
        }, { passive: true });
    }
    
    updateSliderHeight() {
        const width = window.innerWidth;
        let height = 400;
        
        if (width <= 390) {
            height = 200;
        } else if (width <= 768) {
            height = 300;
        }
        
        this.container.style.height = height + 'px';
    }
}

// Инициализация слайдера при загрузке страницы
document.addEventListener('DOMContentLoaded', () => {
    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
        new Slider(sliderContainer);
    }
});