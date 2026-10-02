<?php
/**
 * Cart Page — кастомный шаблон под макет.
 *
 * @package My_Theme
 * @version 11.0.0
 */

defined( 'ABSPATH' ) || exit;

?>

<div class="cart-page">
    <div class="container">

        <!-- Хлебные крошки -->
        <nav class="breadcrumbs reveal reveal--fade">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumbs__item">Главная</a>
            <span class="breadcrumbs__sep">›</span>

            <?php
            $source = my_theme_cart_breadcrumb_source();

            // Если источник — каталог, но не пришёл явный ?from=catalog,
            // всё равно добавим «Каталог» для контекста (опционально).
            if ( ! $source ) {
                $source = [
                    'label' => 'Каталог',
                    'url'   => wc_get_page_permalink( 'shop' ),
                ];
            }
            ?>

            <?php if ( $source ) : ?>
                <a href="<?php echo esc_url( $source['url'] ); ?>" class="breadcrumbs__item">
                    <?php echo esc_html( $source['label'] ); ?>
                </a>
                <span class="breadcrumbs__sep">›</span>
            <?php endif; ?>

            <span class="breadcrumbs__item breadcrumbs__item--current">Корзина</span>
        </nav>

        <h1 class="cart-title reveal reveal--fade">Корзина</h1>

        <?php if ( WC()->cart->is_empty() ) : ?>

            <div class="cart-empty">
                <p><?php esc_html_e( 'Корзина пуста.', 'woocommerce' ); ?></p>
                <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="btn btn-dark">
                    В каталог
                </a>
            </div>

        <?php else : ?>

            <form class="cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

                <?php do_action( 'woocommerce_before_cart_table' ); ?>

                <div class="cart-layout">

                    <!-- ЛЕВАЯ КОЛОНКА -->
                    <div class="cart-main">

                        <!-- Тулбар: выбрать всё + очистить -->
                        <div class="cart-toolbar reveal reveal--fade">
                            <label class="cart-select-all">
                                <input type="checkbox" class="cart-select-all__input" checked>
                                <span class="cart-select-all__box" aria-hidden="true">
                                    <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 5L4.5 8.5L11 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="cart-select-all__text">Выбрать всё</span>
                            </label>

                            <a href="<?php echo esc_url( add_query_arg( 'clear-cart', '1', wc_get_cart_url() ) ); ?>"
                               class="cart-clear"
                               onclick="return confirm('Очистить корзину?');">
                                Очистить корзину
                            </a>
                        </div>

                        <!-- Список товаров -->
                        <div class="cart-items reveal reveal--fade">
                            <?php
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                                $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

                                if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
                                    continue;
                                }

                                if ( ! apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                                    continue;
                                }

                                $product_permalink = apply_filters(
                                    'woocommerce_cart_item_permalink',
                                    $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '',
                                    $cart_item,
                                    $cart_item_key
                                );

                                $thumbnail   = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
                                $short_desc  = $_product->get_short_description();
                                $price_html  = WC()->cart->get_product_subtotal( $_product, 1 );
                                ?>
                                <div class="cart-item" data-cart-item="<?php echo esc_attr( $cart_item_key ); ?>">

                                    <!-- Чекбокс -->
                                    <label class="cart-item__check">
                                        <input type="checkbox"
                                               class="cart-item__check-input"
                                               checked
                                               data-cart-item-check="<?php echo esc_attr( $cart_item_key ); ?>">
                                        <span class="cart-item__check-box" aria-hidden="true">
                                            <svg width="12" height="10" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1 5L4.5 8.5L11 1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </span>
                                    </label>

                                    <!-- Картинка -->
                                    <a href="<?php echo esc_url( $product_permalink ); ?>" class="cart-item__image">
                                        <?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </a>

                                    <!-- Инфо -->
                                    <div class="cart-item__info">
                                        <a href="<?php echo esc_url( $product_permalink ); ?>" class="cart-item__title">
                                            <?php echo wp_kses_post( $_product->get_name() ); ?>
                                        </a>

                                        <?php if ( $short_desc ) : ?>
                                            <p class="cart-item__desc">
                                                <?php echo wp_kses_post( wp_trim_words( $short_desc, 12 ) ); ?>
                                            </p>
                                        <?php endif; ?>

                                        <div class="cart-item__price">
                                            <?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                        </div>
                                    </div>

                                    <!-- Удалить -->
                                    <a href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>"
                                       class="cart-item__remove"
                                       aria-label="Удалить товар"
                                       data-cart-item-remove>
                                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.75 0.75L0.75 10.75M0.75 0.75L10.75 10.75" stroke="#121419" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                </div>
                                <?php
                            endforeach;
                            ?>
                        </div>

                        <?php do_action( 'woocommerce_cart_contents' ); ?>

                        <!-- Скрытая кнопка обновления корзины: нужна для nonce и хуков -->
                        <button type="submit" class="cart-update-hidden" name="update_cart" value="1" hidden></button>
                        <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

                        <?php do_action( 'woocommerce_after_cart_contents' ); ?>
                    </div>

                    <!-- ПРАВАЯ КОЛОНКА -->
                    <aside class="cart-summary reveal reveal--fade">
                        <?php
                        // Рендерим кастомный cart-totals.php из темы
                        woocommerce_cart_totals();
                        ?>
                    </aside>

                </div>

                <?php do_action( 'woocommerce_after_cart_table' ); ?>
            </form>

        <?php endif; ?>

    </div>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>