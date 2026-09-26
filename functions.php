<?php
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

    // Свой слайдер
    wp_enqueue_script(
        'my-theme-scripts',
        $theme_uri . '/assets/js/main.js',
        array(),
        $version,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_scripts' );

/**
 * Передаём ID товаров из корзины в JS
 */
function my_theme_cart_data_to_js() {
    if ( ! function_exists( 'WC' ) || is_admin() ) return;

    $cart_items = array();
    if ( WC()->cart ) {
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $cart_items[] = $cart_item['product_id'];
        }
    }
    ?>
    <script>
        window.wc_cart_data = {
            items: <?php echo wp_json_encode( $cart_items ); ?>
        };
    </script>
    <?php
}
add_action( 'wp_footer', 'my_theme_cart_data_to_js', 5 );