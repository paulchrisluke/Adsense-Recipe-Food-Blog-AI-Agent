<?php

/**
 * Template Name: Recipe Feed
 */

get_header();
?>

<div class="recipe-feed">
    <?php
    // Top feed ad
    echo tiffycooks_amp_insert_ad('feed-top', 'responsive');

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 10,
        'paged' => $paged
    );

    $recipe_query = new WP_Query($args);

    if ($recipe_query->have_posts()) :
        while ($recipe_query->have_posts()) : $recipe_query->the_post();
            get_template_part('template-parts/content', 'recipe');

            // Insert ad after every 4th post
            if ($recipe_query->current_post > 0 && ($recipe_query->current_post + 1) % 4 === 0) {
                echo tiffycooks_amp_insert_ad('feed', 'responsive');
            }
        endwhile;

        // Bottom feed ad
        echo tiffycooks_amp_insert_ad('feed-bottom', 'responsive');

        // Pagination
        echo '<div class="pagination">';
        echo paginate_links(array(
            'total' => $recipe_query->max_num_pages,
            'current' => $paged,
            'prev_text' => '← Previous',
            'next_text' => 'Next →',
        ));
        echo '</div>';

        wp_reset_postdata();
    else :
        get_template_part('template-parts/content', 'none');
    endif;
    ?>
</div>

<?php
get_footer();
?>