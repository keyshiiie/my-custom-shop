<?php
/**
 * Theme functions and definitions.
 *
 * @package My_Theme
 * @requires PHP 8.0
 */

require_once get_template_directory() . '/vendor/autoload.php';

use Kucrut\Vite;

/* ============================================================
   КОНСТАНТЫ ТЕМЫ
   ============================================================ */

/**
 * Версия ассетов (единая точка).
 */
define( 'MY_THEME_VERSION', '1.2' );

/**
 * Сколько товаров показывать на странице каталога.
 */
define( 'MY_THEME_PRODUCTS_PER_PAGE', 9 );

/**
 * Textdomain темы. Используется в __() / esc_html_e() и т.п.
 */
define( 'MY_THEME_TEXTDOMAIN', 'my-theme' );

/**
 * Ссылка на директорию темы.
 *
 * @return string
 */
function my_theme_uri(): string {
    return get_template_directory_uri();
}

/* ============================================================
   ПОДДЕРЖКА WORDPRESS / WOOCOMMERCE
   ============================================================ */

/**
 * Базовая настройка темы: WooCommerce, логотип, меню.
 */
function my_theme_setup(): void {
    add_theme_support( 'woocommerce' );

    add_theme_support( 'custom-logo', [
        'height'      => 50,
        'width'       => 150,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    load_theme_textdomain( MY_THEME_TEXTDOMAIN, get_template_directory() . '/languages' );

    register_nav_menus( [
        'primary' => __( 'Главное меню', MY_THEME_TEXTDOMAIN ),
    ] );
}
add_action( 'after_setup_theme', 'my_theme_setup' );

/* ============================================================
   ПОДКЛЮЧЕНИЕ СТИЛЕЙ
   ============================================================ */

/**
 * Базовые стили — грузятся всегда.
 */
function my_theme_enqueue_base_styles(): void {
    $v = MY_THEME_VERSION;
    $u = my_theme_uri();

    wp_enqueue_style( 'main-style', get_stylesheet_uri(), [], $v );
    wp_enqueue_style( 'base-style', "{$u}/assets/css/main.css", [], $v );

    // Секции главной.
    wp_enqueue_style( 'header-style',       "{$u}/assets/css/header.css",       [ 'base-style' ], $v );
    wp_enqueue_style( 'hero-style',         "{$u}/assets/css/hero.css",         [ 'base-style' ], $v );
    wp_enqueue_style( 'bestsellers-style',  "{$u}/assets/css/bestsellers.css",  [ 'base-style' ], $v );
    wp_enqueue_style( 'why-us-style',       "{$u}/assets/css/why-us.css",       [ 'base-style' ], $v );
    wp_enqueue_style( 'faq-style',          "{$u}/assets/css/faq.css",          [ 'base-style' ], $v );
    wp_enqueue_style( 'cta-style',          "{$u}/assets/css/cta.css",          [ 'base-style' ], $v );
    wp_enqueue_style( 'product-card-style', "{$u}/assets/css/product-card.css", [ 'base-style' ], $v );
    wp_enqueue_style( 'footer-style',       "{$u}/assets/css/footer.css",       [ 'base-style' ], $v );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_base_styles' );

/**
 * Стили корзины.
 */
function my_theme_enqueue_cart_style(): void {
    if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
        return;
    }

    wp_enqueue_style(
        'cart-style',
        my_theme_uri() . '/assets/css/cart.css',
        [ 'base-style' ],
        MY_THEME_VERSION
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_cart_style', 15 );

/**
 * Условные стили: каталог и страница товара.
 */
function my_theme_enqueue_contextual_styles(): void {
    $u = my_theme_uri();
    $v = MY_THEME_VERSION;

    if ( ! function_exists( 'is_shop' ) ) {
        return;
    }

    if ( is_shop() || is_product_category() || is_product_tag() ) {
        wp_enqueue_style( 'catalog-style', "{$u}/assets/css/catalog.css", [ 'base-style' ], $v );
    }

    if ( is_product() ) {
        wp_enqueue_style( 'product-single-style', "{$u}/assets/css/product-single.css", [ 'base-style' ], $v );
    }
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_contextual_styles', 15 );

/* ============================================================
   ПОДКЛЮЧЕНИЕ СКРИПТОВ (VITE)
   ============================================================ */

/**
 * Хендл основного скрипта — вынесен в константу,
 * чтобы не разъехался между enqueue и localize.
 */
define( 'MY_THEME_SCRIPT_HANDLE', 'my-theme-main' );

/**
 * Скрипты, собранные Vite.
 */
function my_theme_enqueue_vite(): void {
    Vite\enqueue_asset(
        get_template_directory() . '/assets/js/dist',
        'assets/js/main.js',
        [
            'handle'    => MY_THEME_SCRIPT_HANDLE,
            'in-footer' => true,
        ]
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_vite' );

/**
 * Проверяет, зарегистрирован ли основной скрипт.
 * Нужно, чтобы localize не падал молча, если сборка не готова.
 *
 * @return bool
 */
function my_theme_has_main_script(): bool {
    return wp_script_is( MY_THEME_SCRIPT_HANDLE, 'registered' )
        || wp_script_is( MY_THEME_SCRIPT_HANDLE, 'enqueued' );
}

/**
 * Передаём данные в JS: URL admin-ajax и ID товаров в корзине.
 */
function my_theme_localize_scripts(): void {
    if ( ! my_theme_has_main_script() ) {
        return;
    }

    wp_localize_script( MY_THEME_SCRIPT_HANDLE, 'catalog_ajax', [
        'url' => admin_url( 'admin-ajax.php' ),
    ] );

    if ( is_admin() || ! function_exists( 'WC' ) || ! WC()->cart ) {
        return;
    }

    $cart_items = wp_list_pluck( WC()->cart->get_cart(), 'product_id' );

    wp_localize_script( MY_THEME_SCRIPT_HANDLE, 'wc_cart_data', [
        'items' => array_values( $cart_items ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'my_theme_localize_scripts', 20 );

/* ============================================================
   МЕНЮ: ЯКОРНЫЕ ССЫЛКИ
   ============================================================ */

/**
 * Если пункт меню — якорь (#...), а мы не на главной,
 * добавляем полный URL главной страницы.
 */
function my_theme_fix_anchor_menu_links( array $atts, $item, $args ): array {
    $is_primary = isset( $args->theme_location ) && 'primary' === $args->theme_location;

    if ( ! $is_primary || is_front_page() ) {
        return $atts;
    }

    $url = $atts['href'] ?? '';

    if ( $url && str_starts_with( $url, '#' ) ) {
        $atts['href'] = home_url( '/' ) . $url;
    }

    return $atts;
}
add_filter( 'nav_menu_link_attributes', 'my_theme_fix_anchor_menu_links', 10, 3 );

/* ============================================================
   WOOCOMMERCE: КАТАЛОГ
   ============================================================ */

/**
 * Отключаем стандартные блоки каталога и карточки товара.
 */
function my_theme_disable_wc_defaults(): void {
    // Каталог.
    remove_action( 'woocommerce_shop_loop_header', 'woocommerce_product_taxonomy_archive_header', 10 );
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
    remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
    remove_action( 'woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10 );

    // Карточка товара — у нас своя разметка.
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
    // Related products оставляем.
}
add_action( 'wp', 'my_theme_disable_wc_defaults' );

/**
 * Число товаров на странице каталога (основной запрос).
 *
 * Используем условные теги WooCommerce вместо is_post_type_archive,
 * потому что shop — это страница, а не архив.
 */
function my_theme_set_products_per_page( WP_Query $query ): void {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( ! function_exists( 'is_shop' ) ) {
        return;
    }

    if ( is_shop() || is_product_category() || is_product_tag() ) {
        $query->set( 'posts_per_page', MY_THEME_PRODUCTS_PER_PAGE );
    }
}
add_action( 'pre_get_posts', 'my_theme_set_products_per_page' );

/* ============================================================
   WOOCOMMERCE: RELATED PRODUCTS
   ============================================================ */

/**
 * Заголовок блока «Вам может понравиться».
 */
function my_theme_related_products_heading(): string {
    return __( 'Вам может понравиться', MY_THEME_TEXTDOMAIN );
}
add_filter( 'woocommerce_product_related_products_heading', 'my_theme_related_products_heading' );

/**
 * Количество related-товаров.
 */
function my_theme_related_products_args( array $args ): array {
    $args['posts_per_page'] = 3;
    $args['columns']        = 3;
    return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'my_theme_related_products_args' );

/* ============================================================
   ХЕЛПЕРЫ ДЛЯ ПАРСИНГА $_GET
   ============================================================ */

/**
 * Возвращает строку из $_GET-значения, даже если пришёл массив.
 *
 * @param mixed $value
 * @return string
 */
function my_theme_get_string( $value ): string {
    return is_string( $value ) ? $value : '';
}

/**
 * Парсит CSV-строку в массив slug'ов.
 *
 * @param string $raw
 * @return string[]
 */
function my_theme_sanitize_list( string $raw ): array {
    if ( '' === $raw ) {
        return [];
    }

    return array_values(
        array_filter( array_map( 'sanitize_title', explode( ',', $raw ) ) )
    );
}

/**
 * Возвращает float или null, если значение пустое.
 *
 * @param mixed $value
 * @return float|null
 */
function my_theme_get_float( $value ): ?float {
    if ( null === $value || '' === $value || ! is_scalar( $value ) ) {
        return null;
    }

    return (float) $value;
}

/* ============================================================
   AJAX-ФИЛЬТРАЦИЯ КАТАЛОГА
   ============================================================ */

/**
 * Собирает аргументы WP_Query из $_GET.
 *
 * @return array
 */
function my_theme_build_catalog_query_args(): array {
    $paged      = max( 1, (int) ( $_GET['paged'] ?? 1 ) );
    $categories = my_theme_sanitize_list( my_theme_get_string( $_GET['categories'] ?? '' ) );
    $tags       = my_theme_sanitize_list( my_theme_get_string( $_GET['tags'] ?? '' ) );
    $price_min  = my_theme_get_float( $_GET['price_min'] ?? null );
    $price_max  = my_theme_get_float( $_GET['price_max'] ?? null );
    $free_only  = ! empty( $_GET['free_only'] );
    $sort       = sanitize_key( my_theme_get_string( $_GET['sort'] ?? '' ) );
    $search     = sanitize_text_field( my_theme_get_string( $_GET['search'] ?? '' ) );

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => MY_THEME_PRODUCTS_PER_PAGE,
        'paged'          => $paged,
    ];

    // --- tax_query ---
    $tax_query = [ 'relation' => 'AND' ];

    if ( $categories ) {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $categories,
        ];
    }

    if ( $tags ) {
        $tax_query[] = [
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $tags,
        ];
    }

    // Скрываем товары, исключённые из каталога (штатный механизм WooCommerce).
    $tax_query[] = [
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => 'exclude-from-catalog',
        'operator' => 'NOT IN',
    ];

    $args['tax_query'] = $tax_query;

    // --- meta_query (цена) ---
    $meta_query = [ 'relation' => 'AND' ];

    if ( $free_only ) {
        $meta_query[] = [
            'key'     => '_price',
            'value'   => 0,
            'compare' => '<=',
            'type'    => 'NUMERIC',
        ];
    } else {
        if ( null !== $price_min ) {
            $meta_query[] = [
                'key'     => '_price',
                'value'   => $price_min,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ];
        }
        if ( null !== $price_max ) {
            $meta_query[] = [
                'key'     => '_price',
                'value'   => $price_max,
                'compare' => '<=',
                'type'    => 'NUMERIC',
            ];
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

    if ( $search ) {
        $args['s'] = $search;
    }

    return $args;
}

/**
 * Применяет фильтры каталога к основному запросу (SSR).
 *
 * Использует ту же логику, что и my_theme_build_catalog_query_args(),
 * чтобы SSR и AJAX давали одинаковый результат.
 *
 * ВАЖНО: не трогает paged и posts_per_page — за них отвечает
 * my_theme_set_products_per_page() и сам WP.
 */
function my_theme_apply_catalog_filters_to_main_query( WP_Query $query ): void {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Не применяем на AJAX-запросах — у filter_catalog свой обработчик.
    if ( wp_doing_ajax() ) {
        return;
    }

    if ( ! function_exists( 'is_shop' ) ) {
        return;
    }

    if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
        return;
    }

    // Если в URL нет ни одного фильтра — не трогаем запрос вообще.
    // Это позволяет WooCommerce работать в штатном режиме на чистом /shop/.
    if ( ! my_theme_has_active_filters() ) {
        return;
    }

    $args = my_theme_build_catalog_query_args();

    // tax_query — заменяем полностью, а не мержим.
    // Иначе можно получить дубли product_visibility от WooCommerce.
    if ( ! empty( $args['tax_query'] ) ) {
        $query->set( 'tax_query', $args['tax_query'] );
    }

    // meta_query — аналогично.
    if ( ! empty( $args['meta_query'] ) ) {
        $query->set( 'meta_query', $args['meta_query'] );
    }

    // Сортировка.
    if ( ! empty( $args['meta_key'] ) ) {
        $query->set( 'meta_key', $args['meta_key'] );
    }
    if ( ! empty( $args['orderby'] ) ) {
        $query->set( 'orderby', $args['orderby'] );
        $query->set( 'order', $args['order'] );
    }

    // Поиск.
    if ( ! empty( $args['s'] ) ) {
        $query->set( 's', $args['s'] );
    }
}
add_action( 'pre_get_posts', 'my_theme_apply_catalog_filters_to_main_query', 20 );

/**
 * Проверяет, есть ли в $_GET активные фильтры каталога.
 *
 * Нужно, чтобы не перезаписывать tax_query WooCommerce,
 * когда пользователь просто открыл /shop/ без параметров.
 *
 * @return bool
 */
function my_theme_has_active_filters(): bool {
    $keys = [
        'categories',
        'tags',
        'price_min',
        'price_max',
        'free_only',
        'sort',
        'search',
    ];

    foreach ( $keys as $key ) {
        if ( ! empty( $_GET[ $key ] ) ) {
            return true;
        }
    }

    return false;
}

/**
 * Рендерит карточки товаров в HTML.
 *
 * @param WP_Query $query
 * @return string
 */
function my_theme_render_product_cards( WP_Query $query ): string {
    if ( ! $query->have_posts() ) {
        return '';
    }

    ob_start();

    while ( $query->have_posts() ) {
        $query->the_post();
        wc_get_template_part( 'content', 'product' );
    }

    wp_reset_postdata();

    return ob_get_clean();
}

/**
 * Рендерит пагинацию в разметке WooCommerce.
 *
 * Используется ТОЛЬКО в AJAX-ответе
 * Для серверного рендера работает шаблон woocommerce/pagination.php.
 *
 * @param int $current
 * @param int $total_pages
 * @return string
 */
function my_theme_render_pagination( int $current, int $total_pages ): string {
    if ( $total_pages <= 1 ) {
        return '';
    }

    $prev = '<svg width="5" height="9" viewBox="0 0 5 9" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
          . '<path d="M4.5 0.5L0.5 4.5L4.5 8.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>'
          . '</svg>';

    $next = '<svg width="5" height="9" viewBox="0 0 5 9" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
          . '<path d="M0.5 8.5L4.5 4.5L0.5 0.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>'
          . '</svg>';

    $links = paginate_links( [
        'base'      => '#page/%#%',
        'format'    => '',
        'current'   => $current,
        'total'     => $total_pages,
        'type'      => 'plain',
        'prev_text' => $prev,
        'next_text' => $next,
    ] );

    return '<nav class="woocommerce-pagination" aria-label="' . esc_attr__( 'Постраничная навигация', MY_THEME_TEXTDOMAIN ) . '">' . $links . '</nav>';
}

/**
 * AJAX-обработчик фильтрации каталога.
 */
function my_theme_ajax_filter_catalog(): void {
    $args  = my_theme_build_catalog_query_args();
    $query = new WP_Query( $args );

    wp_send_json_success( [
        'html'        => my_theme_render_product_cards( $query ),
        'pagination'  => my_theme_render_pagination( (int) $args['paged'], (int) $query->max_num_pages ),
        'found_posts' => (int) $query->found_posts,
        'max_pages'   => (int) $query->max_num_pages,
        'current'     => (int) $args['paged'],
    ] );
}
add_action( 'wp_ajax_filter_catalog',        'my_theme_ajax_filter_catalog' );
add_action( 'wp_ajax_nopriv_filter_catalog', 'my_theme_ajax_filter_catalog' );

/* ============================================================
   КОРЗИНА
   ============================================================ */

/**
 * Очистка корзины.
 *
 * Защищено nonce — иначе любой мог бы очистить корзину чужой сессии
 * по ссылке ?clear-cart=1 (CSRF).
 *
 * Ссылка для вывода в шаблоне:
 *   echo esc_url( wp_nonce_url( add_query_arg( 'clear-cart', 1, wc_get_cart_url() ), 'clear_cart' ) );
 */
function my_theme_handle_clear_cart(): void {
    if ( ! function_exists( 'is_cart' ) || ! is_cart() || empty( $_GET['clear-cart'] ) ) {
        return;
    }

    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'clear_cart' ) ) {
        return;
    }

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return;
    }

    WC()->cart->empty_cart();
    wp_safe_redirect( wc_get_cart_url() );
    exit;
}
add_action( 'template_redirect', 'my_theme_handle_clear_cart' );

/**
 * AJAX: очистка корзины.
 *
 * Работает параллельно с my_theme_handle_clear_cart() (fallback по href).
 * Защищён nonce — см. my_theme_localize_cart_nonce().
 */
function my_theme_ajax_clear_cart(): void {
    check_ajax_referer( 'my_theme_cart_nonce', 'nonce' );

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        wp_send_json_error( [ 'message' => 'Корзина недоступна.' ], 500 );
    }

    WC()->cart->empty_cart();
    WC()->cart->calculate_totals();

    wp_send_json_success( my_theme_get_cart_state() );
}
add_action( 'wp_ajax_my_theme_clear_cart',        'my_theme_ajax_clear_cart' );
add_action( 'wp_ajax_nopriv_my_theme_clear_cart', 'my_theme_ajax_clear_cart' );

/**
 * Цифровой товар — всегда 1 шт.
 *
 * Штатный механизм WooCommerce: sold_individually = true.
 * Он сам фиксирует quantity = 1 в корзине и убирает управление количеством.
 */
add_filter( 'woocommerce_is_sold_individually', '__return_true' );

/**
 * Дополнительно убираем поле количества из разметки корзины.
 */
add_filter( 'woocommerce_cart_item_quantity', '__return_empty_string' );

/**
 * Собирает состояние корзины в единый массив.
 * Используется и в AJAX-удалении, и потенциально во фрагментах.
 *
 * @return array
 */
function my_theme_get_cart_state(): array {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return [
            'items_count'   => 0,
            'total_html'    => '',
            'subtotal_html' => '',
            'is_empty'      => true,
            'summary_html'  => '',
        ];
    }

    return [
        'items_count'   => (int) WC()->cart->get_cart_contents_count(),
        'total_html'    => my_theme_render_cart_total_inner(),
        'subtotal_html' => WC()->cart->get_cart_subtotal(),
        'is_empty'      => WC()->cart->is_empty(),
        'summary_html'  => my_theme_render_cart_summary_items(),
    ];
}

/**
 * Рендерит внутренности блока «Итого» — ровно так,
 * как это делает wc_cart_totals_order_total_html() в cart-totals.php.
 *
 * Возвращает HTML без обёртки .cart-summary__total-sum,
 * чтобы JS мог подставить его через innerHTML.
 *
 * @return string
 */
function my_theme_render_cart_total_inner(): string {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return '';
    }

    ob_start();
    wc_cart_totals_order_total_html();
    return ob_get_clean();
}

/**
 * AJAX: удаление товара из корзины.
 */
function my_theme_ajax_remove_cart_item(): void {
    check_ajax_referer( 'my_theme_cart_nonce', 'nonce' );

    $cart_item_key = isset( $_POST['cart_item_key'] )
        ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) )
        : '';

    if ( ! $cart_item_key ) {
        wp_send_json_error( [ 'message' => 'Не указан товар.' ], 400 );
    }

    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        wp_send_json_error( [ 'message' => 'Корзина недоступна.' ], 500 );
    }

    $removed = WC()->cart->remove_cart_item( $cart_item_key );

    if ( ! $removed ) {
        wp_send_json_error( [ 'message' => 'Не удалось удалить товар.' ], 500 );
    }

    WC()->cart->calculate_totals();

    wp_send_json_success( my_theme_get_cart_state() );
}
add_action( 'wp_ajax_my_theme_remove_cart_item',        'my_theme_ajax_remove_cart_item' );
add_action( 'wp_ajax_nopriv_my_theme_remove_cart_item', 'my_theme_ajax_remove_cart_item' );

/**
 * Рендерит список позиций для правой колонки «Ваш заказ».
 *
 * @return string
 */
function my_theme_render_cart_summary_items(): string {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return '';
    }

    ob_start();

    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

        if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
            continue;
        }
        ?>
        <li class="cart-summary__item" data-cart-item="<?php echo esc_attr( $cart_item_key ); ?>">
            <span class="cart-summary__item-name"><?php echo wp_kses_post( $_product->get_name() ); ?></span>
            <span class="cart-summary__item-price"><?php echo WC()->cart->get_product_subtotal( $_product, 1 ); ?></span>
        </li>
        <?php
    }

    return ob_get_clean();
}

/**
 * Передаём nonce в JS.
 */
function my_theme_localize_cart_nonce(): void {
    if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
        return;
    }

    if ( ! my_theme_has_main_script() ) {
        return;
    }

    wp_localize_script( MY_THEME_SCRIPT_HANDLE, 'cart_ajax', [
        'url'      => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'my_theme_cart_nonce' ),
        'shop_url' => function_exists( 'wc_get_page_permalink' )
            ? wc_get_page_permalink( 'shop' )
            : home_url( '/' ),
    ] );
}
add_action( 'wp_enqueue_scripts', 'my_theme_localize_cart_nonce', 25 );

/* ============================================================
   ХЛЕБНЫЕ КРОШКИ КОРЗИНЫ
   ============================================================ */

/**
 * Определяет «откуда» пришёл пользователь в корзину.
 *
 * @return array{label:string, url:string}|null
 */
function my_theme_cart_breadcrumb_source(): ?array {
    $from = isset( $_GET['from'] ) ? sanitize_key( wp_unslash( $_GET['from'] ) ) : '';

    switch ( $from ) {
        case 'catalog':
            return [
                'label' => __( 'Каталог', MY_THEME_TEXTDOMAIN ),
                'url'   => wc_get_page_permalink( 'shop' ),
            ];

        case 'product':
            $product_id = isset( $_GET['product_id'] ) ? (int) $_GET['product_id'] : 0;
            if ( ! $product_id ) {
                return null;
            }
            $product = wc_get_product( $product_id );
            if ( ! $product ) {
                return null;
            }
            return [
                'label' => $product->get_name(),
                'url'   => get_permalink( $product_id ),
            ];

        case 'category':
            $term_id = isset( $_GET['term_id'] ) ? (int) $_GET['term_id'] : 0;
            if ( ! $term_id ) {
                return null;
            }
            $term = get_term( $term_id, 'product_cat' );
            if ( ! $term || is_wp_error( $term ) ) {
                return null;
            }
            return [
                'label' => $term->name,
                'url'   => get_term_link( $term ),
            ];
    }

    return null;
}

/* ============================================================
   WOOCOMMERCE FRAGMENTS: СЧЁТЧИК КОРЗИНЫ
   ============================================================ */

/**
 * Обновление счётчика корзины через AJAX (WooCommerce fragments).
 */
add_filter( 'woocommerce_add_to_cart_fragments', 'my_theme_cart_count_fragment' );
function my_theme_cart_count_fragment( array $fragments ): array {
    $count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;

    ob_start();
    ?>
    <span class="cart-count"
          data-cart-count
          aria-hidden="true"
          <?php echo $count === 0 ? 'hidden' : ''; ?>>
        <?php echo esc_html( $count ); ?>
    </span>
    <?php
    $fragments['span[data-cart-count]'] = ob_get_clean();

    return $fragments;
}

/**
 * Убеждаемся, что WooCommerce подгружает cart-fragments.js.
 */
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_cart_fragments', 20 );
function my_theme_enqueue_cart_fragments(): void {
    if ( function_exists( 'WC' ) && WC()->cart ) {
        wp_enqueue_script( 'wc-cart-fragments' );
    }
}

/* ============================================================
   CUSTOMIZER: РЕКВИЗИТЫ В ФУТЕРЕ
   ============================================================ */

/**
 * Настройки футера в Customizer.
 */
add_action( 'customize_register', 'my_theme_customize_register_footer' );
function my_theme_customize_register_footer( WP_Customize_Manager $wp_customize ): void {

    $wp_customize->add_section( 'footer_owner_info', [
        'title'    => __( 'Реквизиты в футере', MY_THEME_TEXTDOMAIN ),
        'priority' => 160,
    ] );

    // Статус / ФИО.
    $wp_customize->add_setting( 'owner_status', [
        'default'           => 'Самозанятый: ФИО',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'owner_status', [
        'label'   => __( 'Статус и ФИО', MY_THEME_TEXTDOMAIN ),
        'section' => 'footer_owner_info',
        'type'    => 'text',
    ] );

    // ИНН.
    $wp_customize->add_setting( 'owner_inn', [
        'default'           => '000 000 000 000',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ] );
    $wp_customize->add_control( 'owner_inn', [
        'label'   => __( 'ИНН', MY_THEME_TEXTDOMAIN ),
        'section' => 'footer_owner_info',
        'type'    => 'text',
    ] );
}

/**
 * Убираем штатный notice WooCommerce на странице корзины.
 *
 * woocommerce_output_all_notices выводит «Ваша корзина пока пуста»,
 * если в сессии остался notice. Нам это не нужно — у нас свой
 * блок .cart-empty в cart-empty.php.
 */
add_action( 'template_redirect', function () {
    if ( ! function_exists( 'is_cart' ) || ! is_cart() ) {
        return;
    }

    remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );
} );