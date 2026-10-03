<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#content">
    <?php esc_html_e( 'Перейти к содержимому', 'your-textdomain' ); ?>
</a>

<?php
// Безопасно получаем данные WooCommerce, если он активен
$cart_url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;

$account_url = home_url( '/' );
if ( function_exists( 'wc_get_page_permalink' ) ) {
    $account_url = wc_get_page_permalink( 'myaccount' ) ?: wp_login_url();
} else {
    $account_url = wp_login_url();
}
?>

<header class="site-header" itemscope itemtype="https://schema.org/WPHeader">
    <div class="container header-inner">

        <!-- 1. ЛОГОТИП -->
        <div class="site-logo">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                    <?php bloginfo( 'name' ); ?>
                </a>
            <?php endif; ?>
        </div>

        <!-- 2. ГЛАВНОЕ МЕНЮ -->
        <nav class="main-navigation" aria-label="<?php esc_attr_e( 'Главное меню', 'your-textdomain' ); ?>">
            <?php
            wp_nav_menu( [
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-menu',
                'fallback_cb'    => false,
            ] );
            ?>
        </nav>

        <!-- 3. ИКОНКИ И КОРЗИНА -->
        <div class="header-actions">

            <!-- Корзина -->
            <a href="<?php echo esc_url( $cart_url ); ?>"
               class="header-icon cart-icon"
               aria-label="<?php
                   echo esc_attr( sprintf(
                       /* translators: %d: количество товаров */
                       _n( 'Корзина, %d товар', 'Корзина, %d товаров', $cart_count, 'your-textdomain' ),
                       $cart_count
                   ) );
               ?>">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icon-cart.svg' ); ?>"
                     alt="" aria-hidden="true">
                <span class="cart-count"
                      data-cart-count
                      aria-hidden="true"
                      <?php echo $cart_count === 0 ? 'hidden' : ''; ?>>
                    <?php echo esc_html( $cart_count ); ?>
                </span>
            </a>
            <!-- Живой регион для озвучки изменений корзины -->
            <span class="screen-reader-text" aria-live="polite" data-cart-announcer></span>

            <!-- Кнопка Войти / Личный кабинет -->
            <?php
            $btn_classes = is_user_logged_in()
                ? 'btn account-btn is-logged-in'
                : 'btn btn-dark account-btn';
            ?>
            <a href="<?php echo esc_url( $account_url ); ?>" class="<?php echo esc_attr( $btn_classes ); ?>">
                <?php if ( is_user_logged_in() ) :
                    $current_user = wp_get_current_user();

                    $first_name     = $current_user->first_name;
                    $last_name      = $current_user->last_name;
                    $middle_initial = ! empty( $current_user->middle_name )
                        ? mb_substr( $current_user->middle_name, 0, 1 ) . '.'
                        : '';
                    $last_initial   = $last_name ? mb_substr( $last_name, 0, 1 ) . '.' : '';

                    $full_name = trim( $first_name . ' ' . $last_initial . $middle_initial );
                    if ( empty( $first_name ) ) {
                        $full_name = $current_user->display_name;
                    }
                    ?>
                    <span class="account-btn__avatar">
                        <?php echo get_avatar( $current_user->ID, 30, '', '', [ 'class' => 'account-btn__avatar-img' ] ); ?>
                    </span>
                    <span class="account-btn__name"><?php echo esc_html( $full_name ); ?></span>
                    <span class="account-btn__arrow" aria-hidden="true">
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.666992 12.6666L6.66699 6.66663L0.666992 0.666626" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                <?php else : ?>
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icon-login.svg' ); ?>"
                         alt="" aria-hidden="true">
                    <?php esc_html_e( 'Войти', 'your-textdomain' ); ?>
                <?php endif; ?>
            </a>
        </div>

    </div>
</header>

<main id="content" class="site-main">