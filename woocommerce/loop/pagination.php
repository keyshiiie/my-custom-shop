<?php
/**
 * Pagination — вывод пагинации в архивах WooCommerce.
 * Ссылки имеют вид #page/N — их перехватывает AJAX-модуль.
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
    return;
}
?>
<nav class="woocommerce-pagination" aria-label="Постраничная навигация">
    <?php
    echo paginate_links( array(
        'base'      => '#page/%#%',
        'format'    => '',
        'current'   => max( 1, get_query_var( 'paged' ) ),
        'total'     => $wp_query->max_num_pages,
        'type'      => 'plain',
        'prev_text' => '<svg width="4" height="8" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.33301 1.33337L1.33301 7.33337L7.33301 13.3334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
        'next_text' => '<svg width="4" height="8" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.666992 1.33337L6.66699 7.33337L0.666992 13.3334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    ) );
    ?>
</nav>