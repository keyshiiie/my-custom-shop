<?php
/**
 * Футер сайта.
 *
 * @package My_Theme
 */

$shop_url = home_url( '/' );
if ( function_exists( 'wc_get_page_id' ) ) {
    $shop_id = wc_get_page_id( 'shop' );
    if ( $shop_id > 0 ) {
        $shop_url = get_permalink( $shop_id );
    }
}

$owner_status = get_theme_mod( 'owner_status', 'Самозанятый: ФИО' );
$owner_inn    = get_theme_mod( 'owner_inn', '000 000 000 000' );
?>

<footer class="site-footer">
    <div class="container">

        <!-- Верхняя часть: логотип + колонки ссылок -->
        <div class="footer-top">

            <!-- Левая колонка: лого + соцсети -->
            <div class="footer-brand">

                <div class="footer-brand__logo">
                    <?php if ( has_custom_logo() ) : ?>
                        <?php the_custom_logo(); ?>
                    <?php else : ?>
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-logo__text" rel="home">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="footer-contact">
                    <span class="owner-info__text"><?php echo esc_html( $owner_status ); ?></span>
                    <span class="owner-info__text">
                        <?php esc_html_e( 'ИНН:', MY_THEME_TEXTDOMAIN ); ?>
                        <?php echo esc_html( $owner_inn ); ?>
                    </span>
                </div>

                <div class="footer-socials">
                    <a href="mailto:denis.kiselev.design@gmail.com"
                       class="footer-social"
                       aria-label="<?php esc_attr_e( 'Написать на email', MY_THEME_TEXTDOMAIN ); ?>">
                        <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path d="M1.75 1.75L7.85764 6.36227L7.85967 6.36396C8.53785 6.86128 8.87714 7.1101 9.24876 7.20621C9.57723 7.29117 9.92251 7.29117 10.251 7.20621C10.6229 7.11001 10.9632 6.86047 11.6426 6.36227C11.6426 6.36227 15.5601 3.35594 17.75 1.75M0.75 11.5502V3.9502C0.75 2.83009 0.75 2.26962 0.967987 1.8418C1.15973 1.46547 1.46547 1.15973 1.8418 0.967987C2.26962 0.75 2.83009 0.75 3.9502 0.75H15.5502C16.6703 0.75 17.2296 0.75 17.6574 0.967987C18.0337 1.15973 18.3405 1.46547 18.5322 1.8418C18.75 2.2692 18.75 2.82899 18.75 3.94691V11.5536C18.75 12.6715 18.75 13.2305 18.5322 13.6579C18.3405 14.0342 18.0337 14.3405 17.6574 14.5322C17.23 14.75 16.671 14.75 15.5531 14.75H3.94691C2.82899 14.75 2.2692 14.75 1.8418 14.5322C1.46547 14.3405 1.15973 14.0342 0.967987 13.6579C0.75 13.2301 0.75 12.6703 0.75 11.5502Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="https://www.youtube.com/@denis.design"
                       target="_blank" rel="noopener noreferrer"
                       class="footer-social"
                       aria-label="<?php esc_attr_e( 'YouTube', MY_THEME_TEXTDOMAIN ); ?>">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path d="M15.5833 18.3334H6.41659C3.66659 18.3334 1.83325 16.5001 1.83325 13.7501V8.25008C1.83325 5.50008 3.66659 3.66675 6.41659 3.66675H15.5833C18.3333 3.66675 20.1666 5.50008 20.1666 8.25008V13.7501C20.1666 16.5001 18.3333 18.3334 15.5833 18.3334Z" stroke="#737373" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M10.45 8.70828L12.7417 10.0833C13.5667 10.6333 13.5667 11.4583 12.7417 12.0083L10.45 13.3833C9.53338 13.9333 8.80005 13.475 8.80005 12.4666V9.71662C8.80005 8.52495 9.53338 8.15828 10.45 8.70828Z" stroke="#737373" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="https://t.me/denis_figma"
                       target="_blank" rel="noopener noreferrer"
                       class="footer-social"
                       aria-label="<?php esc_attr_e( 'Telegram', MY_THEME_TEXTDOMAIN ); ?>">
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path d="M9.4487 12.5512L13.891 8.10893M18.4353 5.39787L14.6855 17.5846C14.3495 18.6767 14.1813 19.2231 13.8915 19.4041C13.6401 19.5612 13.3288 19.5875 13.055 19.4739C12.7393 19.3429 12.4831 18.8313 11.9719 17.8089L9.59685 13.0588C9.51574 12.8966 9.47514 12.8158 9.42095 12.7455C9.37287 12.6831 9.31738 12.6269 9.255 12.5789C9.1863 12.5259 9.10688 12.4862 8.95187 12.4087L4.19085 10.0282C3.16848 9.51702 2.65726 9.26119 2.52626 8.94548C2.41265 8.67168 2.43856 8.36013 2.59563 8.10873C2.77676 7.81885 3.32305 7.65044 4.41554 7.31429L16.6022 3.56454C17.4611 3.30027 17.8907 3.16824 18.1808 3.27474C18.4335 3.36751 18.6327 3.56652 18.7255 3.81922C18.832 4.10918 18.6998 4.53858 18.4358 5.3966L18.4353 5.39787Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                    <a href="https://www.behance.net/denis_figma"
                       target="_blank" rel="noopener noreferrer"
                       class="footer-social"
                       aria-label="<?php esc_attr_e( 'Behance', MY_THEME_TEXTDOMAIN ); ?>">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path d="M3 18V6H7.5C8.29565 6 9.05871 6.31607 9.62132 6.87868C10.1839 7.44129 10.5 8.20435 10.5 9C10.5 9.79565 10.1839 10.5587 9.62132 11.1213C9.05871 11.6839 8.29565 12 7.5 12C8.29565 12 9.05871 12.3161 9.62132 12.8787C10.1839 13.4413 10.5 14.2044 10.5 15C10.5 15.7956 10.1839 16.5587 9.62132 17.1213C9.05871 17.6839 8.29565 18 7.5 18H3Z" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 12H7.5" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M14 13H21C21 12.0717 20.6313 11.1815 19.9749 10.5251C19.3185 9.86875 18.4283 9.5 17.5 9.5C16.5717 9.5 15.6815 9.86875 15.0251 10.5251C14.3687 11.1815 14 12.0717 14 13ZM14 13V15C14.1031 15.7487 14.4459 16.4438 14.9769 16.9816C15.5079 17.5193 16.1987 17.8707 16.946 17.9833C17.6933 18.0958 18.457 17.9634 19.1228 17.606C19.7887 17.2485 20.3209 16.6851 20.64 16" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 6H19" stroke="#737373" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Правая часть: две колонки ссылок -->
            <div class="footer-nav">

                <nav class="footer-col" aria-label="<?php esc_attr_e( 'Разделы сайта', MY_THEME_TEXTDOMAIN ); ?>">
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-link">
                        <?php esc_html_e( 'Главная', MY_THEME_TEXTDOMAIN ); ?>
                    </a>
                    <a href="<?php echo esc_url( $shop_url ); ?>" class="footer-link">
                        <?php esc_html_e( 'Каталог', MY_THEME_TEXTDOMAIN ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/#why-us' ) ); ?>" class="footer-link">
                        <?php esc_html_e( 'Почему мы', MY_THEME_TEXTDOMAIN ); ?>
                    </a>
                    <a href="<?php echo esc_url( home_url( '/#faq' ) ); ?>" class="footer-link">
                        <?php esc_html_e( 'FAQ', MY_THEME_TEXTDOMAIN ); ?>
                    </a>
                </nav>

                <nav class="footer-col" aria-label="<?php esc_attr_e( 'Правовая информация', MY_THEME_TEXTDOMAIN ); ?>">
                    <a href="https://t.me/denis_figma"
                       target="_blank" rel="noopener noreferrer"
                       class="footer-link">
                        <?php esc_html_e( 'Поддержка', MY_THEME_TEXTDOMAIN ); ?>
                    </a>

                    <?php
                    $legal_pages = [
                        'offer'   => __( 'Пользовательское соглашение (оферта)', MY_THEME_TEXTDOMAIN ),
                        'privacy' => __( 'Политика конфиденциальности', MY_THEME_TEXTDOMAIN ),
                        'license' => __( 'Лицензионное соглашение', MY_THEME_TEXTDOMAIN ),
                    ];

                    foreach ( $legal_pages as $slug => $label ) :
                        $page = get_page_by_path( $slug );
                        if ( $page ) : ?>
                            <a href="<?php echo esc_url( get_permalink( $page ) ); ?>" class="footer-link">
                                <?php echo esc_html( $label ); ?>
                            </a>
                        <?php else : ?>
                            <span class="footer-link footer-link--disabled" aria-disabled="true">
                                <?php echo esc_html( $label ); ?>
                            </span>
                        <?php endif;
                    endforeach;
                    ?>
                </nav>

            </div>
        </div>

        <!-- Нижняя строка -->
        <div class="footer-bottom">
            <a href="https://t.me/keyshi_ii"
               target="_blank" rel="noopener noreferrer"
               class="footer-bottom__item">
                <?php esc_html_e( 'Designed by SHURHHH', MY_THEME_TEXTDOMAIN ); ?>
            </a>

            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-bottom__item">
                © <?php echo esc_html( date_i18n( 'Y' ) ); ?>.
                <?php esc_html_e( 'Все права защищены.', MY_THEME_TEXTDOMAIN ); ?>
            </a>

            <span class="footer-bottom__item">
                <?php esc_html_e( 'Developed by keyshiiie', MY_THEME_TEXTDOMAIN ); ?>
            </span>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>