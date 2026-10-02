<?php
/**
 * Content Single Product — вёрстка карточки товара под макет.
 * Мета-поля берутся из ACF:
 *   - product_format       (text)
 *   - product_license      (text)
 *   - product_composition  (textarea, формат "Название : Значение")
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product ) return;

$product_id    = $product->get_id();
$gallery_ids   = $product->get_gallery_image_ids();
$main_image_id = $product->get_image_id();

// Собираем все картинки: главная + галерея
$all_image_ids = array_filter( array_merge( array( $main_image_id ), $gallery_ids ) );

// Теги
$tag_ids = wp_get_post_terms( $product_id, 'product_tag', array( 'fields' => 'ids' ) );

// --- ACF-поля ---
$format      = function_exists( 'get_field' ) ? get_field( 'product_format', $product_id )      : '';
$license     = function_exists( 'get_field' ) ? get_field( 'product_license', $product_id )     : '';
$composition = function_exists( 'get_field' ) ? get_field( 'product_composition', $product_id ) : '';

// --- Парсим textarea «Состав набора» в массив [['label' => ..., 'value' => ...], ...] ---
$composition_items = array();

if ( $composition && is_string( $composition ) ) {
    // Разбиваем по переносам строк
    $lines = preg_split( '/\r\n|\r|\n/', $composition );

    foreach ( $lines as $line ) {
        $line = trim( $line );
        if ( $line === '' ) continue;

        // Ищем разделитель: сначала " : ", потом ":", потом " - "
        if ( strpos( $line, ' : ' ) !== false ) {
            $parts = explode( ' : ', $line, 2 );
        } elseif ( strpos( $line, ':' ) !== false ) {
            $parts = explode( ':', $line, 2 );
        } elseif ( strpos( $line, ' - ' ) !== false ) {
            $parts = explode( ' - ', $line, 2 );
        } else {
            $parts = array( $line, '' );
        }

        $label = trim( $parts[0] );
        $value = isset( $parts[1] ) ? trim( $parts[1] ) : '';

        if ( $label !== '' ) {
            $composition_items[] = array(
                'label' => $label,
                'value' => $value,
            );
        }
    }
}
?>

<div class="container">

    <!-- ХЛЕБНЫЕ КРОШКИ -->
    <nav class="breadcrumbs reveal reveal--fade">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="breadcrumbs__item">Главная</a>
        <span class="breadcrumbs__sep">›</span>
        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="breadcrumbs__item">Каталог</a>
        <span class="breadcrumbs__sep">›</span>
        <span class="breadcrumbs__item breadcrumbs__item--current"><?php the_title(); ?></span>
    </nav>

    <!-- ВЕРХНИЙ БЛОК: ГАЛЕРЕЯ + ИНФО -->
    <div class="product-single__top">

        <!-- ЛЕВАЯ КОЛОНКА: ГАЛЕРЕЯ -->
        <div class="product-gallery reveal reveal--fade">

            <div class="product-gallery__main">
                <?php if ( $main_image_id ) : ?>
                    <?php echo wp_get_attachment_image( $main_image_id, 'large', false, array(
                        'class' => 'product-gallery__main-img',
                        'id'    => 'product-gallery-main',
                    ) ); ?>
                <?php else : ?>
                    <img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="">
                <?php endif; ?>
            </div>

            <?php if ( count( $all_image_ids ) > 1 ) : ?>
                <div class="product-gallery__thumbs">
                    <?php foreach ( $all_image_ids as $index => $img_id ) : ?>
                        <button type="button"
                                class="product-gallery__thumb <?php echo $index === 0 ? 'is-active' : ''; ?>"
                                data-image-id="<?php echo esc_attr( $img_id ); ?>"
                                data-image-src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'large' ) ); ?>">
                            <?php echo wp_get_attachment_image( $img_id, 'thumbnail' ); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>

        <!-- ПРАВАЯ КОЛОНКА: ИНФО -->
        <div class="product-info reveal reveal--fade">

            <!-- Теги -->
            <?php if ( ! empty( $tag_ids ) ) : ?>
                <div class="product-info__tags">
                    <?php foreach ( $tag_ids as $tag_id ) :
                        $tag = get_term( $tag_id, 'product_tag' );
                        if ( ! $tag || is_wp_error( $tag ) ) continue;
                        ?>
                        <a href="<?php echo esc_url( get_term_link( $tag ) ); ?>" class="product-info__tag">
                            #<?php echo esc_html( $tag->name ); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Название -->
            <h1 class="product-info__title"><?php the_title(); ?></h1>

            <!-- Короткое описание -->
            <?php if ( $product->get_short_description() ) : ?>
                <div class="product-info__excerpt">
                    <?php echo wp_kses_post( $product->get_short_description() ); ?>
                </div>
            <?php endif; ?>

            <!-- Цена -->
            <div class="product-info__price">
                <?php echo $product->get_price_html(); ?>
            </div>

            <!-- Кнопка "В корзину" -->
            <div class="product-info__cart">
                <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
                    data-quantity="1"
                    data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
                    data-cart_url="<?php echo esc_url( wc_get_cart_url() ); ?>"
                    class="btn btn-dark btn-sm add_to_cart_button ajax_add_to_cart product-card__cart-btn">
                    В корзину
                </a>
            </div>

            <!-- Плашки: Формат / Лицензия -->
            <?php if ( $format || $license ) : ?>
                <div class="product-info__meta">

                    <?php if ( $format ) : ?>
                        <div class="product-meta-box">
                            <div class="product-meta-box__label">Формат:</div>
                            <div class="product-meta-box__value"><?php echo esc_html( $format ); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if ( $license ) : ?>
                        <div class="product-meta-box">
                            <div class="product-meta-box__label">Лицензия:</div>
                            <div class="product-meta-box__value"><?php echo esc_html( $license ); ?></div>
                        </div>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

            <!-- Примечание про скачивание -->
            <p class="product-info__note">
                Ссылка на скачивание придёт на email и появится в личном кабинете сразу после оплаты.
                Возврат цифровых товаров не предусмотрен — см. оферту.
            </p>

        </div>

    </div>

    <!-- ОПИСАНИЕ + СОСТАВ НАБОРА -->
    <div class="product-single__details reveal reveal--fade">

        <!-- Описание -->
        <div class="product-description">
            <h2 class="product-description__title">Описание</h2>
            <div class="product-description__content">
                <?php echo wp_kses_post( wpautop( $product->get_description() ) ); ?>
            </div>
        </div>

        <!-- Состав набора -->
        <?php if ( ! empty( $composition_items ) ) : ?>
            <div class="product-composition">
                <h2 class="product-composition__title">Состав набора</h2>
                <ul class="product-composition__list">
                    <?php foreach ( $composition_items as $row ) : ?>
                        <li class="product-composition__item">
                            <span class="product-composition__label"><?php echo esc_html( $row['label'] ); ?></span>
                            <?php if ( $row['value'] !== '' ) : ?>
                                <span class="product-composition__value"><?php echo esc_html( $row['value'] ); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

    </div>
    
    <!-- RELATED — «Вам может понравиться» -->
    <div class="reveal reveal--fade">
        <?php do_action( 'woocommerce_after_single_product_summary' ); ?>
    </div>
</div>

<?php