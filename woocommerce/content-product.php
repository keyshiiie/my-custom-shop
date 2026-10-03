<?php
/**
 * Шаблон карточки товара (переопределён в теме).
 *
 * @package My_Theme
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>

<li <?php wc_product_class( 'product-card', $product ); ?>>

    <!-- Обложка -->
    <a href="<?php echo esc_url( get_permalink() ); ?>" class="product-card__image">
        <?php echo $product->get_image( 'full' ); ?>
    </a>

    <!-- Белая плашка -->
    <div class="product-card__body">
        <div class="product-card__row">
            <h3 class="product-card__title">
                <a href="<?php echo esc_url( get_permalink() ); ?>">
                    <?php echo esc_html( get_the_title() ); ?>
                </a>
            </h3>
            <div class="product-card__price">
                <?php echo $product->get_price_html(); ?>
            </div>
        </div>

        <div class="product-card__excerpt">
            <?php
            $excerpt = $product->get_short_description();
            if ( $excerpt ) {
                echo esc_html(
                    wp_trim_words( wp_strip_all_tags( $excerpt ), 12, '...' )
                );
            }
            ?>
        </div>
    </div>

    <!-- Кнопки -->
    <div class="product-card__buttons">

        <?php
        // Возвращаем кастомную кнопку — она уже работала через AJAX.
        // Класс ajax_add_to_cart перехватывается wc-add-to-cart.js.
        ?>
        <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
           data-quantity="1"
           data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
           data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
           data-cart_url="<?php echo esc_url( wc_get_cart_url() ); ?>"
           class="btn btn-dark btn-sm add_to_cart_button ajax_add_to_cart product-card__cart-btn"
           aria-label="<?php echo esc_attr( sprintf( 'Добавить «%s» в корзину', $product->get_name() ) ); ?>"
           rel="nofollow">
            <?php esc_html_e( 'В корзину', MY_THEME_TEXTDOMAIN ); ?>
        </a>

        <a href="<?php echo esc_url( get_permalink() ); ?>"
           class="btn btn-outline btn-sm">
            <?php esc_html_e( 'Подробнее', MY_THEME_TEXTDOMAIN ); ?>
        </a>
    </div>

</li>