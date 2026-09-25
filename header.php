<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); // Обязательный хук. Подключает стили и скрипты WP ?>
</head>
<body <?php body_class(); ?>>

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
            wp_nav_menu( array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'nav-menu',
                'fallback_cb'    => false,
            ) );
            ?>
        </nav>

        <!-- 3. ИКОНКИ И КОРЗИНА -->
        <div class="header-actions">
            
            <!-- Поиск -->
            <a href="#" class="header-icon search-toggle">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-search.svg" alt="Поиск">
            </a>

            <!-- Корзина с счетчиком -->
            <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="header-icon cart-icon">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-cart.svg" alt="Корзина">
                <?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
                    <span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                <?php endif; ?>
            </a>

            <!-- Кнопка Войти / Личный кабинет -->
            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-dark">
                <?php if ( is_user_logged_in() ) : ?>
                    Кабинет
                <?php else : ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/icon-login.svg" alt="Войти">
                    Войти
                <?php endif; ?>
            </a>
        </div>

    </div>
</header>

<main class="site-main">