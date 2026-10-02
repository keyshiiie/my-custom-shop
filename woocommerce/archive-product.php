<?php
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );
?>

<div class="catalog-page">
    <div class="container">
        <!-- 1. Хлебные крошки -->
        <nav class="catalog-breadcrumbs">
            <?php woocommerce_breadcrumb( array(
                'delimiter'   => '<span class="catalog-breadcrumbs__sep">›</span>',
                'wrap_before' => '',
                'wrap_after'  => '',
                'before'      => '<span class="catalog-breadcrumbs__item">',
                'after'       => '</span>',
            ) ); ?>
        </nav>

        <!-- 2. Заголовок -->
        <h1 class="catalog-title"><?php woocommerce_page_title(); ?></h1>

        <!-- 3. Панель поиска и фильтров -->
        <div class="catalog-toolbar">

            <!-- 3.1 Поиск -->
            <form role="search" method="get" class="catalog-search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="hidden" name="post_type" value="product">
                <svg class="catalog-search__icon" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.14591 14.875C11.8623 14.875 14.8751 11.8622 14.8751 8.14579C14.8751 4.42938 11.8623 1.41663 8.14591 1.41663C4.4295 1.41663 1.41675 4.42938 1.41675 8.14579C1.41675 11.8622 4.4295 14.875 8.14591 14.875Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M15.5834 15.5833L14.1667 14.1666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <input
                    type="search"
                    class="catalog-search__input"
                    placeholder="Поиск по названию или ключевым словам..."
                    name="s"
                    value="<?php echo get_search_query(); ?>"
                >
            </form>

            <!-- 3.2 Фильтры-селекты -->
            <div class="catalog-filters">

                <!-- Категория (мультивыбор с чекбоксами) -->
                <div class="catalog-filter catalog-filter--multiselect" data-filter="category" data-placeholder="Категория">
                    <button type="button" class="catalog-filter__toggle" aria-expanded="false" aria-haspopup="listbox">
                        <span class="catalog-filter__label">Категории</span>
                        <svg class="catalog-filter__arrow" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <ul class="catalog-filter__list" role="listbox" aria-multiselectable="true">
                        <?php
                        $terms = get_terms( array(
                            'taxonomy'   => 'product_cat',
                            'hide_empty' => true,
                        ) );
                        if ( ! is_wp_error( $terms ) ) :
                            foreach ( $terms as $term ) :
                                ?>
                                <li class="catalog-filter__option catalog-filter__option--checkbox"
                                    data-value="<?php echo esc_attr( $term->slug ); ?>"
                                    role="option"
                                    aria-selected="false">
                                    <span class="catalog-filter__checkbox" aria-hidden="true">
                                        <svg class="catalog-filter__check" width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M1 5L4.5 8.5L11 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span class="catalog-filter__option-text"><?php echo esc_html( $term->name ); ?></span>
                                </li>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </ul>
                </div>

                <!-- Теги (мультивыбор — чипсы) -->
                <div class="catalog-filter catalog-filter--multiselect catalog-filter--tags" data-filter="tag" data-placeholder="Теги">
                    <button type="button" class="catalog-filter__toggle" aria-expanded="false" aria-haspopup="listbox">
                        <span class="catalog-filter__label">Теги</span>
                        <svg class="catalog-filter__arrow" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="catalog-filter__list catalog-filter__list--chips" role="listbox" aria-multiselectable="true">
                        <?php
                        $tags = get_terms( array(
                            'taxonomy'   => 'product_tag',
                            'hide_empty' => true,
                        ) );
                        if ( ! is_wp_error( $tags ) ) :
                            foreach ( $tags as $tag ) :
                                ?>
                                <button type="button"
                                        class="catalog-tag"
                                        data-value="<?php echo esc_attr( $tag->slug ); ?>"
                                        role="option"
                                        aria-selected="false">
                                    #<?php echo esc_html( $tag->name ); ?>
                                </button>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>

                <!-- Цена (панель) -->
                <div class="catalog-filter catalog-filter--price" data-filter="price" data-placeholder="Цена">
                    <button type="button" class="catalog-filter__toggle" aria-expanded="false" aria-haspopup="dialog">
                        <span class="catalog-filter__label">Цена</span>
                        <svg class="catalog-filter__arrow" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="catalog-filter__list catalog-filter__list--price" role="dialog" aria-label="Фильтр по цене">

                        <!-- Чекбокс «Только бесплатные» -->
                        <label class="catalog-price__free">
                            <input type="checkbox" class="catalog-price__free-input" hidden>
                            <span class="catalog-price__free-box" aria-hidden="true">
                                <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 5L4.5 8.5L11 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="catalog-price__free-text">Только бесплатные</span>
                        </label>

                        <!-- Разделитель -->
                        <div class="catalog-price__divider"></div>

                        <!-- Поля От / До -->
                        <div class="catalog-price__row">
                            <input type="number" class="catalog-price__input" placeholder="От 0₽" min="0" step="1" aria-label="Цена от">
                            <input type="number" class="catalog-price__input" placeholder="До 10 000₽" min="0" step="1" aria-label="Цена до">
                        </div>

                        <!-- Кнопка -->
                        <button type="button" class="catalog-price__apply">Применить</button>

                    </div>
                </div>

                <!-- Сортировка -->
                <div class="catalog-filter catalog-filter--sort" data-filter="sort" data-placeholder="Сортировка">
                    <button type="button" class="catalog-filter__toggle" aria-expanded="false" aria-haspopup="listbox">
                        <span class="catalog-filter__label">Сортировка</span>
                        <svg class="catalog-filter__arrow" width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="catalog-filter__list catalog-filter__list--radio" role="radiogroup" aria-label="Сортировка">

                        <button type="button" class="catalog-sort" data-value="popularity" role="radio" aria-checked="false">
                            <span class="catalog-sort__radio" aria-hidden="true"></span>
                            <span class="catalog-sort__text">Популярное</span>
                        </button>

                        <button type="button" class="catalog-sort" data-value="date" role="radio" aria-checked="false">
                            <span class="catalog-sort__radio" aria-hidden="true"></span>
                            <span class="catalog-sort__text">Новое</span>
                        </button>

                        <button type="button" class="catalog-sort" data-value="price-asc" role="radio" aria-checked="false">
                            <span class="catalog-sort__radio" aria-hidden="true"></span>
                            <span class="catalog-sort__text">
                                Цена
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4.69483 7.57623L9.50025 2.77082L14.3057 7.57623" stroke="#121419" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.5 16.2292L9.5 2.90544" stroke="#121419" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </button>

                        <button type="button" class="catalog-sort" data-value="price-desc" role="radio" aria-checked="false">
                            <span class="catalog-sort__radio" aria-hidden="true"></span>
                            <span class="catalog-sort__text">
                                Цена
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14.3052 11.4237L9.49975 16.2291L4.69434 11.4237" stroke="#121419" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M9.5 2.77087V16.0946" stroke="#121419" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </button>

                    </div>
                </div>

            </div>
        </div>

        <!-- 4. Чипсы активных фильтров + Найдено -->
        <div class="catalog-chips">
            <span class="catalog-chips__count">
                Найдено: <?php echo esc_html( $GLOBALS['wp_query']->found_posts ); ?>
            </span>

            <!-- Активные чипсы (рендерит JS) -->
            <div class="catalog-chips__list" data-chips-list>
                <!-- сюда JS добавит чипсы -->
            </div>

            <button type="button" class="catalog-chips__more" data-chips-more hidden>
                <span data-chips-more-count>0</span>
            </button>

            <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="catalog-chips__reset" data-chips-reset>
                Сбросить всё
            </a>
        </div>

        <!-- 5. Сетка товаров -->
        <?php if ( woocommerce_product_loop() ) : ?>

            <?php
            /**
             * woocommerce_before_shop_loop
             * Здесь WC обычно выводит result count и сортировку — мы их уже сверстали выше,
             * поэтому отключаем через remove_action в functions.php (см. ниже).
             */
            do_action( 'woocommerce_before_shop_loop' );

            woocommerce_product_loop_start();

            if ( wc_get_loop_prop( 'total' ) ) {
                while ( have_posts() ) {
                    the_post();
                    do_action( 'woocommerce_shop_loop' );
                    wc_get_template_part( 'content', 'product' );
                }
            }

            woocommerce_product_loop_end();

            /**
             * woocommerce_after_shop_loop
             * Пагинация.
             */
            do_action( 'woocommerce_after_shop_loop' );
            ?>

        <?php else : ?>

            <?php do_action( 'woocommerce_no_products_found' ); ?>

        <?php endif; ?>
    </div>
</div>

<?php
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );