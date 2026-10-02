<?php
/**
 * Single Product Template — карточка товара под макет.
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

/**
 * woocommerce_before_main_content
 * Убираем обёртку WC, ставим свою.
 */
?>
<main class="site-main product-page">
    <div class="container">

        <?php while ( have_posts() ) : the_post(); ?>

            <?php wc_get_template_part( 'content', 'single-product' ); ?>

        <?php endwhile; ?>

    </div>
</main>
<?php
get_footer( 'shop' );