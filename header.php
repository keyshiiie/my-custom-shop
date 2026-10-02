<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php
// Безопасно получаем данные WooCommerce, если он активен
$cart_url    = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
$cart_count  = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$account_url = function_exists( 'wc_get_page_permalink' )
    ? wc_get_page_permalink( 'myaccount' )
    : wp_login_url();
?>

<header class="site-header">
    <div class="container header-inner">

        <!-- 1. ЛОГОТИП -->
        <div class="site-logo">
            <?php if ( has_custom_logo() ) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a>
            <?php endif; ?>
        </div>

        <!-- 2. ГЛАВНОЕ МЕНЮ -->
        <nav class="main-navigation">
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
            <a href="<?php echo esc_url( $cart_url ); ?>" class="header-icon cart-icon">
                <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icon-cart.svg' ); ?>" alt="Корзина">
                <span class="cart-count" <?php echo $cart_count === 0 ? 'hidden' : ''; ?>>
                    <?php echo esc_html( $cart_count ); ?>
                </span>
            </a>

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
                    <span class="account-btn__arrow">
                        <svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0.666992 12.6666L6.66699 6.66663L0.666992 0.666626" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                <?php else : ?>
                    <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/icon-login.svg' ); ?>" alt="Войти">
                    Войти
                <?php endif; ?>
            </a>
        </div>

    </div>
</header>

<main class="site-main">