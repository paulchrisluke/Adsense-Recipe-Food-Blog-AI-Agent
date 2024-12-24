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
        if (get_the_archive_description()) {
            echo '<div class="archive-description">' . get_the_archive_description() . '</div>';
        }
    } else {
        the_archive_title('<h1 class="archive-title">', '</h1>');
        the_archive_description('<div class="archive-description">', '</div>');
    }
    ?>
</div>

<div class="posts-list">
    <?php
    if (have_posts()) :
        while (have_posts()) :
            the_post();
    ?>
            <article <?php post_class('post-card'); ?>>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-card-thumbnail">
                        <?php
                        if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                            $thumbnail_url = get_the_post_thumbnail_url(null, 'large');
                            $image_width = 800;
                            $image_height = 450; // 16:9 aspect ratio
                            printf(
                                '<amp-img src="%1$s" width="%2$d" height="%3$d" alt="%4$s" layout="responsive"></amp-img>',
                                esc_url($thumbnail_url),
                                $image_width,
                                $image_height,
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
                        $categories = get_the_category();
                        if ($categories) {
                            echo '<span class="separator">•</span>';
                            echo '<span class="category">' . esc_html($categories[0]->name) . '</span>';
                        }
                        ?>
                    </div>
                </div>
            </article>
        <?php
        endwhile;

        // Add pagination if needed
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '&larr;',
            'next_text' => '&rarr;',
        ));

    else :
        ?>
        <p class="no-posts"><?php esc_html_e('No posts found.', 'tiffycooks'); ?></p>
    <?php
    endif;
    ?>
</div>

<?php
get_footer();
