<?php
/**
 * Result Count — «Найдено: N»
 */

defined( 'ABSPATH' ) || exit;

if ( ! woocommerce_products_will_display() ) {
    return;
}

$total    = $GLOBALS['wp_query']->found_posts;
$per_page = $GLOBALS['wp_query']->get( 'posts_per_page' );
$current  = max( 1, $GLOBALS['wp_query']->get( 'paged' ) );
$first    = ( $per_page * $current ) - $per_page + 1;
$last     = min( $total, $per_page * $current );
?>
<p class="catalog__found">
    <?php
    printf(
        'Найдено: <span class="catalog__found-num">%d</span>',
        $total
    );
    ?>
</p>