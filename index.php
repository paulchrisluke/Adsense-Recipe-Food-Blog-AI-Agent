<?php

/**
 * The main template file
 *
 * @package TiffyCooks
 */

get_header();

// Set up query for all posts
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type' => 'post',
    'posts_per_page' => 12,
    'paged' => $paged
);
$posts_query = new WP_Query($args);
?>

<div class="site-content">
    <div class="container">
        <?php if ($posts_query->have_posts()) : ?>
            <div class="posts-grid">
                <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
                        <div class="post-card-inner">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('medium_large', array('class' => 'post-image')); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="post-content">
                                <header class="post-header">
                                    <?php the_title('<h2 class="post-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>'); ?>

                                    <div class="post-meta">
                                        <?php
                                        // Show recipe meta if available
                                        $prep_time = get_post_meta(get_the_ID(), '_recipe_prep_time', true);
                                        $cook_time = get_post_meta(get_the_ID(), '_recipe_cook_time', true);

                                        if (!empty($prep_time) || !empty($cook_time)) {
                                            $total_time = intval($prep_time) + intval($cook_time);
                                            echo '<span class="recipe-time">';
                                            echo '<i class="dashicons dashicons-clock"></i> ';
                                            echo esc_html($total_time) . ' mins';
                                            echo '</span>';
                                        }
                                        ?>

                                        <span class="post-author">
                                            <i class="dashicons dashicons-admin-users"></i>
                                            <?php the_author(); ?>
                                        </span>

                                        <span class="post-date">
                                            <i class="dashicons dashicons-calendar-alt"></i>
                                            <?php echo get_the_date(); ?>
                                        </span>
                                    </div>
                                </header>

                                <div class="post-excerpt">
                                    <?php the_excerpt(); ?>
                                </div>
                            </div>
                        </div>
                    </article>

                    <?php
                    // Insert an ad after every 6 posts
                    if (($posts_query->current_post + 1) % 6 === 0 && !$posts_query->is_last) {
                        echo tiffycooks_amp_insert_ad('archive');
                    }
                    ?>

                <?php endwhile; ?>
            </div>

            <?php
            // Display AMP-compatible pagination
            echo '<div class="pagination">';
            echo paginate_links(array(
                'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $posts_query->max_num_pages,
                'prev_text' => '&laquo; Previous',
                'next_text' => 'Next &raquo;',
                'before_page_number' => '<span class="meta-nav screen-reader-text">Page </span>'
            ));
            echo '</div>';
            ?>

        <?php else : ?>
            <div class="no-posts">
                <h2>No posts found</h2>
                <p>It seems we can't find what you're looking for. Perhaps try creating your first post?</p>
                <?php if (current_user_can('edit_posts')) : ?>
                    <p>
                        <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="button button-primary">
                            Add New Post
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>
    </div>
</div>

<style>
    .site-content {
        padding: 2rem 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .posts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .post-card {
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
    }

    .post-card:hover {
        transform: translateY(-5px);
    }

    .post-card-inner {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .post-thumbnail {
        position: relative;
        padding-top: 56.25%;
        /* 16:9 aspect ratio */
    }

    .post-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .post-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .post-title {
        margin: 0 0 1rem;
        font-size: 1.25rem;
        line-height: 1.4;
    }

    .post-title a {
        color: #333;
        text-decoration: none;
    }

    .post-title a:hover {
        color: #007bff;
    }

    .post-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        color: #666;
    }

    .post-meta span {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .recipe-time {
        color: #28a745;
    }

    .post-excerpt {
        font-size: 0.9375rem;
        line-height: 1.6;
        color: #555;
    }

    .no-posts {
        text-align: center;
        padding: 3rem 1rem;
    }

    .pagination {
        margin-top: 2rem;
        text-align: center;
    }

    .pagination .nav-links {
        display: inline-flex;
        gap: 0.5rem;
    }

    .pagination a,
    .pagination span {
        padding: 0.5rem 1rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        text-decoration: none;
        color: #333;
    }

    .pagination a:hover,
    .pagination span.current {
        background: #007bff;
        color: #fff;
        border-color: #007bff;
    }

    @media (max-width: 768px) {
        .posts-grid {
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .post-content {
            padding: 1rem;
        }

        .post-title {
            font-size: 1.125rem;
        }
    }
</style>

<?php get_footer(); ?>