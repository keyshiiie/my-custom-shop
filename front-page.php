<?php
/**
 * Шаблон главной страницы
 */
get_header(); // Подключает header.php
?>

<main class="site-main">

    <!-- СЕКЦИЯ 1: HERO -->
    <section class="hero-section">
        <div class="container">
            
            <!-- Плашка -->
            <div class="hero-badge">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-layer.svg" alt="Слои">
                Берём структуру на себя
            </div>

            <!-- Заголовок -->
            <h1 class="hero-title">
                Прокачай дизайн<br>
                Экономь <span class="highlight">время</span>
            </h1>

            <!-- Подзаголовок -->
            <p class="hero-subtitle">
                Готовые UI-киты, иконки и компоненты в одном месте
            </p>

            <!-- Кнопки -->
            <div class="hero-buttons">
                <div class="composite-btn">
                    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-dark btn-composite-main">В каталог</a><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-dark btn-composite-arrow" aria-label="Перейти в каталог"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-catalog-arrow.svg" alt=""></a>
                </div>
                <a href="#" class="btn btn-outline">Смотреть видео</a>
            </div>

            <!-- Стрелка вниз -->
            <div class="hero-scroll">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-mouse.svg" alt="Мышь">
            </div>

            <!-- Видео-превью -->
            <div class="hero-video">
                <div class="video-placeholder">
                    <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="40" cy="40" r="38" stroke="#ddd" stroke-width="2"/>
                        <path d="M32 28L54 40L32 52V28Z" fill="#ddd"/>
                    </svg>
                </div>
            </div>

        </div>
    </section>

    <!-- СЕКЦИЯ 2: ХИТЫ ПРОДАЖ -->
    <section class="bestsellers-section">
        <!-- ... -->
    </section>

    <!-- СЕКЦИЯ 3: ПОЧЕМУ ВЫБИРАЮТ DD -->
    <section id="why-us" class="why-us-section">
        <!-- ... -->
    </section>

    <!-- СЕКЦИЯ 4: FAQ -->
    <section id="faq" class="faq-section">
        <!-- ... -->
    </section>

    <!-- СЕКЦИЯ 5: CTA -->
    <section class="cta-section">
        <!-- ... -->
    </section>

</main>

<?php get_footer(); // Подключает footer.php ?>