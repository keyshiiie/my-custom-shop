<?php
/**
 * Шаблон главной страницы
 */
get_header();
?>

<main class="site-main">

    <!-- СЕКЦИЯ 1: HERO -->
    <section class="hero-section">
        <div class="container">

            <div class="section-badge reveal reveal--fade">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-layer.svg" alt="Слои">
                Берём структуру на себя
            </div>

            <h1 class="hero-title reveal reveal--fade reveal--delay-1">
                Прокачай дизайн<br>
                Экономь <span class="highlight-wrap"><span class="highlight"><span class="highlight__word">время</span></span></span>
            </h1>

            <p class="hero-subtitle reveal reveal--fade reveal--delay-2">
                Готовые UI-киты, иконки и компоненты в одном месте
            </p>

            <div class="hero-buttons reveal reveal--fade reveal--delay-3">
                <div class="composite-btn">
                    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-dark btn-composite-main">В каталог</a><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-dark btn-composite-arrow" aria-label="Перейти в каталог"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-catalog-arrow.svg" alt=""></a>
                </div>
                <a href="#hero-video" class="btn btn-outline">Смотреть видео</a>
            </div>

            <div class="hero-scroll reveal reveal--fade reveal--delay-4">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-mouse.svg" alt="Мышь">
            </div>

            <!-- Видео-превью — здесь reveal НЕ ставим, работает scroll-zoom из main.js -->
            <div id="hero-video" class="hero-video">
                <div class="video-placeholder">
                    <svg width="128" height="128" viewBox="0 0 128 128" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M48.0001 117.333H80.0001C106.667 117.333 117.333 106.667 117.333 80V48C117.333 21.3334 106.667 10.6667 80.0001 10.6667H48.0001C21.3334 10.6667 10.6667 21.3334 10.6667 48V80C10.6667 106.667 21.3334 117.333 48.0001 117.333Z" stroke="#E5E5E5" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M48.5334 64V56.1067C48.5334 45.92 55.7334 41.8133 64.5334 46.88L71.3601 50.8267L78.1868 54.7733C86.9868 59.84 86.9868 68.16 78.1868 73.2267L71.3601 77.1733L64.5334 81.12C55.7334 86.1867 48.5334 82.0267 48.5334 71.8933V64Z" stroke="#E5E5E5" stroke-width="6" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>

        </div>
    </section>

    <!-- СЕКЦИЯ 2: ХИТЫ ПРОДАЖ -->
    <section id="bestsellers" class="bestsellers-section">
        <div class="container">

            <!-- Заголовок секции -->
            <div class="section-header">
                <div class="section-badge reveal reveal--fade">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-document.svg" alt="">
                    Паки и пресеты для UX/UI
                </div>
                <h2 class="section-title reveal reveal--fade reveal--delay-1">Хиты продаж</h2>
                <p class="section-subtitle reveal reveal--fade reveal--delay-2">Самые покупаемые сеты этого месяца</p>
            </div>

            <!-- Слайдер -->
            <div class="bestsellers-wrap reveal reveal--fade reveal--delay-3">

                <button class="slider-arrow slider-arrow--prev" aria-label="Назад" disabled>
                    <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5.33341 0.666664L0.666748 5.33333L5.33341 10M0.666748 5.33333H10.0001" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <button class="slider-arrow slider-arrow--next" aria-label="Вперед">
                    <svg width="11" height="11" viewBox="0 0 11 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M0.666748 5.33333H10.0001M5.33341 10L10.0001 5.33333L5.33341 0.666664" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <div class="bestsellers-slider" data-per-view="3" data-gap="30">
                    <div class="bestsellers-track">
                        <?php
                        $args = array(
                            'post_type'      => 'product',
                            'posts_per_page' => 9,
                            'meta_key'       => 'total_sales',
                            'orderby'        => 'meta_value_num',
                            'order'          => 'DESC',
                            'tax_query'      => array(
                                array(
                                    'taxonomy' => 'product_visibility',
                                    'field'    => 'name',
                                    'terms'    => 'featured',
                                    'operator' => 'IN',
                                ),
                            ),
                        );

                        $bestsellers = new WP_Query( $args );

                        if ( $bestsellers->have_posts() ) :
                            while ( $bestsellers->have_posts() ) : $bestsellers->the_post();
                                global $product;
                                ?>
                                <div class="bestsellers-item">
                                    <?php wc_get_template_part( 'content', 'product' ); ?>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
                </div>

                <div class="slider-footer">
                    <div class="slider-footer__spacer"></div>
                    <div class="slider-pagination"></div>
                    <div class="bestsellers-cta-wrap">
                        <div class="composite-btn bestsellers-cta">
                            <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-dark btn-composite-main">В каталог</a><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-dark btn-composite-arrow" aria-label="Перейти в каталог"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-catalog-arrow.svg" alt=""></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- СЕКЦИЯ 3: ПОЧЕМУ ВЫБИРАЮТ DD -->
    <section id="why-us" class="why-us-section">
        <div class="container">

            <!-- Заголовок секции -->
            <div class="section-header">
                <div class="section-badge reveal reveal--fade">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-star.svg" alt="">
                    Здесь всё самое лучшее
                </div>
                <h2 class="section-title reveal reveal--fade reveal--delay-1">
                    <span class="highlight-title">Почему</span> выбирают DD?
                </h2>
                <p class="section-subtitle reveal reveal--fade reveal--delay-2">Создавайте проекты быстрее и зарабатывайте больше</p>
            </div>

            <!-- Сетка преимуществ -->
            <div class="why-us-grid">

                <div class="why-us-card reveal reveal--delay-1">
                    <div class="why-us-card__icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-layers.svg" alt="">
                    </div>
                    <h3 class="why-us-card__title">Готовые решения</h3>
                    <p class="why-us-card__text">Паки содержат полностью проработанные компоненты, их можно сразу вставить в проект</p>
                </div>

                <div class="why-us-card reveal reveal--delay-2">
                    <div class="why-us-card__icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-new.svg" alt="">
                    </div>
                    <h3 class="why-us-card__title">Новинки каждый месяц</h3>
                    <p class="why-us-card__text">Регулярно добавляем свежие паки, чтобы вы всегда были в курсе современных решений</p>
                </div>

                <div class="why-us-card reveal reveal--delay-3">
                    <div class="why-us-card__icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-figma.svg" alt="">
                    </div>
                    <h3 class="why-us-card__title">Совместимость с Figma</h3>
                    <p class="why-us-card__text">Каждый элемент создан профессиональным дизайнером с вниманием к пикселям, сеткам и типографике</p>
                </div>

                <div class="why-us-card reveal reveal--delay-4">
                    <div class="why-us-card__icon">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-update.svg" alt="">
                    </div>
                    <h3 class="why-us-card__title">Постоянные обновления</h3>
                    <p class="why-us-card__text">Получаешь один раз — получаешь новые компоненты бесплатно с каждым новым обновлением</p>
                </div>

            </div>

        </div>
    </section>

    <!-- СЕКЦИЯ 4: FAQ -->
    <section id="faq" class="faq-section">
        <div class="container">

            <!-- Заголовок секции -->
            <div class="section-header">
                <h2 class="section-title reveal reveal--fade">Остались вопросы?</h2>
                <p class="section-subtitle reveal reveal--fade reveal--delay-1">Ответы на самые частые вопросы о покупке и использовании</p>
            </div>

            <!-- Аккордеон -->
            <div class="faq-list reveal reveal--fade reveal--delay-2">

                <details class="faq-item" open>
                    <summary class="faq-item__question">
                        <span>Как я получу файлы после оплаты?</span>
                        <span class="faq-item__icon"></span>
                    </summary>
                    <div class="faq-item__answer">
                        <p>Сразу после успешной оплаты ссылка на скачивание придет вам на почту, а также появится в вашем Личном кабинете в разделе «Покупки».</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-item__question">
                        <span>В каком формате предоставляются паки?</span>
                        <span class="faq-item__icon"></span>
                    </summary>
                    <div class="faq-item__answer">
                        <p>Все файлы предоставляются в формате .fig для Figma. Дополнительно может входить .sketch для Sketch и .xd для Adobe XD.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-item__question">
                        <span>Можно ли использовать ваши паки в коммерческих проектах?</span>
                        <span class="faq-item__icon"></span>
                    </summary>
                    <div class="faq-item__answer">
                        <p>Да, вы можете использовать все компоненты в личных и коммерческих проектах без дополнительных лицензий. Ограничение только одно — перепродажа самих паков.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-item__question">
                        <span>Могу ли я перепродавать или делиться купленными файлами?</span>
                        <span class="faq-item__icon"></span>
                    </summary>
                    <div class="faq-item__answer">
                        <p>Нет, перепродажа и публичное распространение купленных паков запрещены. Вы можете делиться только результатами своей работы, созданными на основе этих компонентов.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-item__question">
                        <span>Что делать, если файл не открывается или поврежден?</span>
                        <span class="faq-item__icon"></span>
                    </summary>
                    <div class="faq-item__answer">
                        <p>Напишите нам на почту — мы вышлем вам исправленный файл или поможем с открытием. Обычно проблема решается в течение 24 часов.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-item__question">
                        <span>Есть ли скидки при покупке нескольких паков?</span>
                        <span class="faq-item__icon"></span>
                    </summary>
                    <div class="faq-item__answer">
                        <p>Да, при покупке от 3 паков действует скидка 15%, от 5 паков — 25%. Скидка применяется автоматически в корзине.</p>
                    </div>
                </details>

                <details class="faq-item">
                    <summary class="faq-item__question">
                        <span>Какие способы оплаты вы принимаете?</span>
                        <span class="faq-item__icon"></span>
                    </summary>
                    <div class="faq-item__answer">
                        <p>Мы принимаем банковские карты (Visa, MasterCard, МИР), СБП, ЮMoney, а также безналичный расчет для юридических лиц.</p>
                    </div>
                </details>

            </div>

        </div>
    </section>

    <!-- СЕКЦИЯ 5: CTA -->
    <section class="cta-section">
        <div class="container">

            <div class="cta-box reveal reveal--fade">
                <h2 class="cta-title">Цени своё время</h2>
                <p class="cta-subtitle">Не трать его на поиск по сети. Всё нужное здесь.</p>

                <div class="composite-btn cta-btn">
                    <a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-dark btn-composite-main">В каталог</a><a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="btn btn-dark btn-composite-arrow" aria-label="Перейти в каталог"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-catalog-arrow.svg" alt=""></a>
                </div>
            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>