<?php
require_once get_template_directory() . '/vendor/autoload.php';

use Kucrut\Vite;

function my_theme_enqueue_styles() {
    $theme_uri = get_template_directory_uri();
    $version   = '1.0';

    // 1. Базовые стили (шрифт, reset, container, .btn)
    wp_enqueue_style( 'main-style', get_stylesheet_uri(), array(), $version );
    wp_enqueue_style( 'base-style', $theme_uri . '/assets/css/main.css', array(), $version );

    // 2. Стили хедера
    wp_enqueue_style( 'header-style', $theme_uri . '/assets/css/header.css', array('base-style'), $version );

    // 3. Стили hero
    wp_enqueue_style( 'hero-style', $theme_uri . '/assets/css/hero.css', array('base-style'), $version );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

// Подключаем поддержку WooCommerce
function my_theme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'my_theme_add_woocommerce_support' );

// Регистрируем меню
function my_theme_register_menus() {
    register_nav_menus( array(
        'primary' => 'Главное меню',
    ) );
}
add_action( 'init', 'my_theme_register_menus' );

// Регистрируем логотип
function my_theme_setup() {
    add_theme_support( 'custom-logo', array(
        'height'      => 50,
        'width'       => 150,
        'flex-height' => true,
        'flex-width'  => true,
    ) );
}
add_action( 'after_setup_theme', 'my_theme_setup' );

/**
 * Правильные ссылки-якоря в меню.
 * Если мы не на главной — добавляем полный URL к главной.
 * Если на главной — оставляем просто #якорь для плавного скролла.
 */
function my_theme_fix_anchor_menu_links( $atts, $item, $args ) {
    if ( isset( $args->theme_location ) && $args->theme_location === 'primary' ) {
        $url = $atts['href'] ?? '';

        // Если URL начинается с # — это якорь
        if ( strpos( $url, '#' ) === 0 && ! is_front_page() ) {
            $atts['href'] = home_url( '/' ) . $url;
        }
    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'my_theme_fix_anchor_menu_links', 10, 3 );

/**
 * Стили секций (bestsellers, why-us, faq, cta, footer).
 */
function my_theme_enqueue_scripts() {
    $theme_uri = get_template_directory_uri();
    $version   = '1.1';

    // Стили секции bestsellers
    wp_enqueue_style(
        'bestsellers-style',
        $theme_uri . '/assets/css/bestsellers.css',
        array( 'base-style' ),
        $version
    );

    // Стили секции why-us
    wp_enqueue_style(
        'why-us-style',
        $theme_uri . '/assets/css/why-us.css',
        array( 'base-style' ),
        $version
    );

    // Стили секции FAQ
    wp_enqueue_style(
        'faq-style',
        $theme_uri . '/assets/css/faq.css',
        array( 'base-style' ),
        $version
    );

    // Стили секции CTA
    wp_enqueue_style(
        'cta-style',
        $theme_uri . '/assets/css/cta.css',
        array( 'base-style' ),
        $version
    );

    // Стили футера
    wp_enqueue_style(
        'footer-style',
        $theme_uri . '/assets/css/footer.css',
        array( 'base-style' ),
        $version
    );

    // ⚠️ wp_enqueue_script для main.js УДАЛЁН.
    // Скрипты теперь подключает Vite через Vite\enqueue_asset() (см. ниже).
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_scripts' );

/**
 * Подключение скриптов, собранных Vite.
 *
 * В dev-режиме (npm run dev) Vite-плагин отдаёт скрипты с localhost:5173,
 * работает HMR. В prod (npm run build) — подключается собранный бандл
 * из /assets/js/dist/ по manifest.json.
 */
function my_theme_enqueue_vite() {
    Vite\enqueue_asset(
        get_template_directory() . '/assets/js/dist',
        'assets/js/main.js',
        [
            'handle'    => 'theme-main',
            'in-footer' => true,
        ]
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_vite' );

/**
 * Передаём ID товаров из корзины в JS.
 *
 * Привязано к хендлу Vite-скрипта 'theme-main', чтобы window.wc_cart_data
 * гарантированно появился ДО выполнения cart-button.js.
 */
function my_theme_localize_cart_data() {
    if ( ! function_exists( 'WC' ) || is_admin() ) return;

    $cart_items = array();
    if ( WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $cart_items[] = $cart_item['product_id'];
        }
    }

    wp_localize_script(
        'theme-main',
        'wc_cart_data',
        array( 'items' => $cart_items )
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_localize_cart_data', 20 );

/**
 * Отключаем стандартные элементы каталога WooCommerce,
 * потому что мы сверстали их вручную в archive-product.php.
 */
function my_theme_disable_wc_catalog_defaults() {
    // Заголовок архива
    remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );

    // «Показано 1–12 из 12»
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

    // Сортировка WC
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

    // Сайдбар — у нас его нет
    remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

    // Стандартная обёртка WC
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
    remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );
}
add_action( 'init', 'my_theme_disable_wc_catalog_defaults' );

/**
 * Стили страницы каталога.
 */
function my_theme_enqueue_catalog_styles() {
    if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
        return;
    }

    wp_enqueue_style(
        'catalog-style',
        get_template_directory_uri() . '/assets/css/catalog.css',
        array( 'base-style' ),
        '1.0'
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_catalog_styles', 15 );

/**
 * AJAX-фильтрация каталога.
 */
function my_theme_ajax_filter_catalog() {
    // --- Собираем параметры ---
    $paged       = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
    $categories  = array_filter( array_map( 'sanitize_title', explode( ',', $_GET['categories'] ?? '' ) ) );
    $tags        = array_filter( array_map( 'sanitize_title', explode( ',', $_GET['tags'] ?? '' ) ) );
    $price_min   = isset( $_GET['price_min'] ) && $_GET['price_min'] !== '' ? (float) $_GET['price_min'] : null;
    $price_max   = isset( $_GET['price_max'] ) && $_GET['price_max'] !== '' ? (float) $_GET['price_max'] : null;
    $free_only   = ! empty( $_GET['free_only'] );
    $sort        = sanitize_key( $_GET['sort'] ?? '' );
    $search      = sanitize_text_field( $_GET['search'] ?? '' );

    // --- Базовый запрос ---
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 9,
        'paged'          => $paged,
    );

    // --- tax_query ---
    $tax_query = array( 'relation' => 'AND' );

    if ( ! empty( $categories ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $categories,
        );
    }

    if ( ! empty( $tags ) ) {
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $tags,
        );
    }

    if ( count( $tax_query ) > 1 ) {
        $args['tax_query'] = $tax_query;
    }

    // --- meta_query (цена) ---
    $meta_query = array( 'relation' => 'AND' );

    // «Только бесплатные» — товары с ценой 0
    if ( $free_only ) {
        $meta_query[] = array(
            'key'     => '_price',
            'value'   => 0,
            'compare' => '<=',
            'type'    => 'NUMERIC',
        );
    } else {
        if ( $price_min !== null ) {
            $meta_query[] = array(
                'key'     => '_price',
                'value'   => $price_min,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            );
        }
        if ( $price_max !== null ) {
            $meta_query[] = array(
                'key'     => '_price',
                'value'   => $price_max,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            );
        }
    }

    if ( count( $meta_query ) > 1 ) {
        $args['meta_query'] = $meta_query;
    }

    // --- Сортировка ---
    switch ( $sort ) {
        case 'popularity':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = 'total_sales';
            $args['order']    = 'DESC';
            break;
        case 'date':
            $args['orderby'] = 'date';
            $args['order']   = 'DESC';
            break;
        case 'price-asc':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order']    = 'ASC';
            break;
        case 'price-desc':
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order']    = 'DESC';
            break;
        default:
            $args['orderby'] = 'menu_order';
            $args['order']   = 'ASC';
    }

    // --- Поиск ---
    if ( $search ) {
        $args['s'] = $search;
    }

    // --- Выполняем запрос ---
    $query = new WP_Query( $args );

    // --- Рендерим карточки ---
    ob_start();

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            wc_get_template_part( 'content', 'product' );
        }
    }
    wp_reset_postdata();

    $html = ob_get_clean();

    // --- Пагинация ---
    $pagination_html = '';

    if ( $query->max_num_pages > 1 ) {
        $pagination_html = paginate_links( array(
            'base'      => '#page/%#%',
            'format'    => '',
            'current'   => $paged,
            'total'     => $query->max_num_pages,
            'type'      => 'plain',
            'prev_text' => '‹',
            'next_text' => '›',
        ) );
    }

    // --- Ответ ---
    wp_send_json_success( array(
        'html'        => $html,
        'pagination'  => $pagination_html,
        'found_posts' => $query->found_posts,
        'max_pages'   => $query->max_num_pages,
        'current'     => $paged,
    ) );
}

add_action( 'wp_ajax_filter_catalog',        'my_theme_ajax_filter_catalog' );
add_action( 'wp_ajax_nopriv_filter_catalog', 'my_theme_ajax_filter_catalog' );

function my_theme_localize_ajax_url() {
    wp_localize_script(
        'theme-main',
        'catalog_ajax',
        array(
            'url' => admin_url( 'admin-ajax.php' ),
        )
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_localize_ajax_url', 20 );