<?php
/**
 * Empty cart page — кастом под макет.
 *
 * WooCommerce подгружает этот шаблон, если корзина пуста.
 * Мы не используем штатный вывод — только свой блок.
 *
 * @package My_Theme
 */

defined( 'ABSPATH' ) || exit;

// Отключаем штатный notice «Ваша корзина пока пуста».
remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
?>

<div class="cart-empty">
    <p><?php esc_html_e( 'Корзина пуста.', MY_THEME_TEXTDOMAIN ); ?></p>
    <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-dark">
        <?php esc_html_e( 'В каталог', MY_THEME_TEXTDOMAIN ); ?>
    </a>
</div>