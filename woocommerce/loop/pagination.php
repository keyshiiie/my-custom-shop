<?php
/**
 * Pagination — вывод пагинации в архивах WooCommerce.
 *
 * Ссылки имеют вид #page/N — их перехватывает AJAX-модуль.
 * Разметка синхронизирована с my_theme_render_pagination() в functions.php.
 *
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

if ( $wp_query->max_num_pages <= 1 ) {
    return;
}

$prev = '<svg width="5" height="9" viewBox="0 0 5 9" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
      . '<path d="M4.5 0.5L0.5 4.5L4.5 8.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>'
      . '</svg>';

$next = '<svg width="5" height="9" viewBox="0 0 5 9" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
      . '<path d="M0.5 8.5L4.5 4.5L0.5 0.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>'
      . '</svg>';
?>
<nav class="woocommerce-pagination" aria-label="<?php esc_attr_e( 'Постраничная навигация', 'woocommerce' ); ?>">
    <?php
    echo paginate_links( array(
        'base'      => '#page/%#%',
        'format'    => '',
        'current'   => max( 1, get_query_var( 'paged' ) ),
        'total'     => $wp_query->max_num_pages,
        'type'      => 'plain',
        'prev_text' => $prev,
        'next_text' => $next,
    ) );
    ?>
</nav>