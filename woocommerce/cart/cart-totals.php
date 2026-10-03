<?php
/**
 * Cart totals — кастомный шаблон под макет.
 *
 * @package My_Theme
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || exit;

$cart = WC()->cart;
?>

<div class="cart_totals <?php echo esc_attr( $cart->has_calculated_shipping() ? 'calculated_shipping' : '' ); ?>">

    <?php do_action( 'woocommerce_before_cart_totals' ); ?>

    <h2 class="cart-summary__title">Ваш заказ</h2>

    <div class="cart-summary__count">
        Количество паков:
        <span class="cart-summary__count-num" data-cart-summary-count>
            <?php echo esc_html( $cart->get_cart_contents_count() ); ?>
        </span>
    </div>

    <ul class="cart-summary__list" data-cart-summary-list>
        <?php
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) :
            $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

            if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
                continue;
            }
            ?>
            <li class="cart-summary__item" data-cart-item="<?php echo esc_attr( $cart_item_key ); ?>">
                <span class="cart-summary__item-name">
                    <?php echo wp_kses_post( $_product->get_name() ); ?>
                </span>
                <span class="cart-summary__item-price">
                    <?php echo $cart->get_product_subtotal( $_product, 1 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="cart-summary__divider"></div>

    <!-- Купоны (если включены и есть) -->
    <?php if ( wc_coupons_enabled() && $cart->get_coupons() ) : ?>
        <ul class="cart-summary__coupons">
            <?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
                <li class="cart-summary__coupon">
                    <span><?php wc_cart_totals_coupon_label( $coupon ); ?></span>
                    <span><?php wc_cart_totals_coupon_html( $coupon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <!-- Доставка (если нужна) -->
    <?php if ( $cart->needs_shipping() && $cart->show_shipping() ) : ?>
        <?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>
        <div class="cart-summary__shipping">
            <?php wc_cart_totals_shipping_html(); ?>
        </div>
        <?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>
    <?php endif; ?>

    <!-- Доп. сборы -->
    <?php foreach ( $cart->get_fees() as $fee ) : ?>
        <div class="cart-summary__fee">
            <span><?php echo esc_html( $fee->name ); ?></span>
            <span><?php wc_cart_totals_fee_html( $fee ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
        </div>
    <?php endforeach; ?>

    <?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

    <div class="cart-summary__total">
        <span>Итого</span>
        <span class="cart-summary__total-sum" data-cart-summary-total>
            <?php wc_cart_totals_order_total_html(); ?>
        </span>
    </div>

    <?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

    <div class="wc-proceed-to-checkout" data-cart-checkout>
        <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"
           class="btn btn-dark cart-summary__checkout">
            Перейти к оплате
        </a>
    </div>

    <?php if ( ! is_user_logged_in() ) : ?>
        <p class="cart-summary__note">
            <svg width="20" height="20" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="9" cy="9" r="8" stroke="currentColor" stroke-width="1.2"/>
                <path d="M9 5.5V9.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
                <circle cx="9" cy="12.5" r="0.9" fill="currentColor"/>
            </svg>
            <?php esc_html_e( 'Для оплаты нужен аккаунт: после нажатия вы войдёте или зарегистрируетесь.', MY_THEME_TEXTDOMAIN ); ?>
        </p>
    <?php endif; ?>

    <?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>