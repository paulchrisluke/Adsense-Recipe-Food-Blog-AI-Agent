<?php

/**
 * The template for displaying archive pages
 *
 * @package TiffyCooks
 */

get_header();
?>

<div class="archive-header">
    <?php
    if (is_category()) {
        echo '<h1 class="archive-title">' . single_cat_title('', false) . '</h1>';

        // Get category description
        $category_description = category_description();
        if ($category_description) {
            echo '<div class="archive-description">' . $category_description . '</div>';
        }
    } else {
        the_archive_title('<h1 class="archive-title">', '</h1>');
        the_archive_description('<div class="archive-description">', '</div>');
    }
    ?>
</div>

<?php
// Top of archive ad
echo tiffycooks_amp_insert_ad('archive-top', 'responsive');
?>

<div class="posts-list">
    <?php
    if (have_posts()) :
        $post_count = 0;

        while (have_posts()) :
            the_post();
            $post_count++;
    ?>
            <article <?php post_class('post-card'); ?>>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-card-thumbnail">
                        <?php
                        if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                            $thumbnail_url = get_the_post_thumbnail_url(null, 'large');
                            printf(
                                '<amp-img src="%1$s" width="800" height="450" alt="%2$s" layout="responsive"></amp-img>',
                                esc_url($thumbnail_url),
                                esc_attr(get_the_title())
                            );
                        } else {
                            the_post_thumbnail('large');
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="post-card-content">
                    <h2 class="post-card-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>

                    <div class="post-card-excerpt">
                        <?php echo wp_trim_words(get_the_excerpt(), 30); ?>
                    </div>

                    <div class="post-card-meta">
                        <time datetime="<?php echo get_the_date('c'); ?>">
                            <?php echo get_the_date(); ?>
                        </time>
                        <?php
                        if (!is_category()) {
                            $categories = get_the_category();
                            if ($categories) {
                                echo '<span class="separator">•</span>';
                                echo '<a href="' . esc_url(get_category_link($categories[0]->term_id)) . '" class="category">';
                                echo esc_html($categories[0]->name);
                                echo '</a>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </article>

            <?php
            // Insert ad after every 3 posts
            if ($post_count % 3 === 0) {
                echo tiffycooks_amp_insert_ad('archive-between', 'responsive');
            }
            ?>

        <?php
        endwhile;

        // Add infinite scroll only if there are more pages
        global $wp_query;
        if ($wp_query->max_num_pages > 1) :
        ?>
            <amp-next-page>
                <script type="application/json">
                    <?php
                    $pages = array();
                    for ($i = 2; $i <= min($wp_query->max_num_pages, 3); $i++) {
                        $pages[] = array(
                            'url' => get_pagenum_link($i),
                            'title' => get_the_archive_title()
                        );
                    }
                    echo json_encode($pages);
                    ?>
                </script>
                <div load-more-loading>
                    <div class="loading-indicator">Loading more recipes...</div>
                </div>
            </amp-next-page>
        <?php
        endif;

    else :
        ?>
        <p class="no-posts"><?php esc_html_e('No posts found.', 'tiffycooks'); ?></p>
    <?php
    endif;
    ?>
</div>

<?php
// Bottom of archive ad
echo tiffycooks_amp_insert_ad('archive-bottom', 'responsive');

get_footer();
