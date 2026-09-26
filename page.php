<?php
/**
 * Шаблон для всех страниц (page)
 */
get_header();
?>

<main class="site-main page-content">
    <div class="container">
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article <?php post_class(); ?>>
                <h1 class="page-title"><?php the_title(); ?></h1>
                <div class="page-body">
                    <?php the_content(); ?>
                </div>
            </article>
            <?php
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>