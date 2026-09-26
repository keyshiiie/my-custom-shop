<?php
/**
 * Шаблон карточки товара (переопределен в теме)
 * Используется в цикле WooCommerce.
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Проверка на валидность товара
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<li <?php wc_product_class( 'product-card', $product ); ?>>

    <!-- Обложка -->
    <a href="<?php the_permalink(); ?>" class="product-card__image">
        <?php echo $product->get_image( 'full' ); ?>
    </a>

    <!-- Белая плашка -->
    <div class="product-card__body">
        <div class="product-card__row">
            <h3 class="product-card__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <div class="product-card__price"><?php echo $product->get_price_html(); ?></div>
        </div>
        <div class="product-card__excerpt">
            <?php echo wp_trim_words( $product->get_short_description(), 12, '...' ); ?>
        </div>
    </div>

    <!-- Кнопки -->
    <div class="product-card__buttons">
        <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
           data-quantity="1"
           data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
           data-cart_url="<?php echo esc_url( wc_get_cart_url() ); ?>"
           class="btn btn-dark btn-sm add_to_cart_button ajax_add_to_cart product-card__cart-btn">
            В корзину
        </a>
        <a href="<?php the_permalink(); ?>" class="btn btn-outline btn-sm">
            Подробнее
        </a>
    </div>

</li>