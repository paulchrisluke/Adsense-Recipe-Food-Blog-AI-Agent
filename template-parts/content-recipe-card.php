<?php

/**
 * Template part for displaying recipe cards in the feed
 *
 * @package TiffyCooks
 */
?>

<article <?php post_class('feed-card'); ?>>
    <!-- Card Header -->
    <header class="feed-card-header">
        <div class="author-info">
            <?php
            $author_id = get_the_author_meta('ID');
            $avatar = get_avatar_url($author_id, array('size' => 32));
            ?>
            <amp-img class="author-avatar"
                src="<?php echo esc_url($avatar); ?>"
                width="32"
                height="32"
                alt="<?php echo esc_attr(get_the_author()); ?>">
            </amp-img>
            <div class="author-meta">
                <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="author-name">
                    <?php the_author(); ?>
                </a>
                <?php if (get_field('recipe_location')) : ?>
                    <span class="recipe-location"><?php echo esc_html(get_field('recipe_location')); ?></span>
                <?php endif; ?>
            </div>
        </div>
        <button class="more-options" aria-label="More options">
            <svg width="24" height="24" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="2"></circle>
                <circle cx="3" cy="12" r="2"></circle>
                <circle cx="21" cy="12" r="2"></circle>
            </svg>
        </button>
    </header>

    <!-- Recipe Image -->
    <?php if (has_post_thumbnail()) : ?>
        <div class="feed-card-media">
            <?php
            $image_url = get_the_post_thumbnail_url(null, 'large');
            printf(
                '<amp-img src="%1$s" width="614" height="614" layout="responsive" alt="%2$s"></amp-img>',
                esc_url($image_url),
                esc_attr(get_the_title())
            );
            ?>
        </div>
    <?php endif; ?>

    <!-- Card Actions -->
    <div class="feed-card-actions">
        <div class="action-buttons">
            <button class="action-button like-button" aria-label="Like">
                <svg width="24" height="24" viewBox="0 0 24 24">
                    <path d="M16.792 3.904A4.989 4.989 0 0121.5 9.122c0 3.072-2.652 4.959-5.197 7.222-2.512 2.243-3.865 3.469-4.303 3.752-.477-.309-2.143-1.823-4.303-3.752C5.141 14.072 2.5 12.167 2.5 9.122a4.989 4.989 0 014.708-5.218 4.21 4.21 0 013.675 1.941c.84 1.175.98 1.763 1.12 1.763s.278-.588 1.11-1.766a4.17 4.17 0 013.679-1.938m0-2a6.04 6.04 0 00-4.797 2.127 6.052 6.052 0 00-4.787-2.127A6.985 6.985 0 00.5 9.122c0 3.61 2.55 5.827 5.015 7.97.283.246.569.494.853.747l1.027.918a44.998 44.998 0 003.518 3.018 2 2 0 002.174 0 45.263 45.263 0 003.626-3.115l.922-.824c.293-.26.59-.519.885-.774 2.334-2.025 4.98-4.32 4.98-7.94a6.985 6.985 0 00-6.708-7.218z"></path>
                </svg>
            </button>
            <button class="action-button comment-button" aria-label="Comment">
                <svg width="24" height="24" viewBox="0 0 24 24">
                    <path d="M20.656 17.008a9.993 9.993 0 10-3.59 3.615L22 22z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2"></path>
                </svg>
            </button>
            <button class="action-button share-button" aria-label="Share">
                <svg width="24" height="24" viewBox="0 0 24 24">
                    <line fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="2" x1="22" x2="9.218" y1="3" y2="10.083"></line>
                    <polygon fill="none" points="11.698 20.334 22 3.001 2 3.001 9.218 10.084 11.698 20.334" stroke="currentColor" stroke-linejoin="round" stroke-width="2"></polygon>
                </svg>
            </button>
        </div>
        <button class="action-button save-button" aria-label="Save Recipe">
            <svg width="24" height="24" viewBox="0 0 24 24">
                <polygon fill="none" points="20 21 12 13.44 4 21 4 3 20 3 20 21" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></polygon>
            </svg>
        </button>
    </div>

    <!-- Recipe Content -->
    <div class="feed-card-content">
        <!-- Recipe Stats -->
        <div class="recipe-stats">
            <?php if (get_field('recipe_time')) : ?>
                <span class="recipe-time">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-width="2" />
                        <polyline fill="none" stroke="currentColor" stroke-width="2" points="12 6 12 12 16 14" />
                    </svg>
                    <?php echo esc_html(get_field('recipe_time')); ?>
                </span>
            <?php endif; ?>
            <?php if (get_field('recipe_servings')) : ?>
                <span class="recipe-servings">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                        <path d="M17 11V3h2v8h-2zm3-7v2h-2V4h2zm-3 7h2v2h-2v-2zm3 5v2h-2v-2h2zm-3-3h2v2h-2v-2zm3 3h-2v-2h2v2zm-3-3v2h-2v-2h2zm3 3v-2h2v2h-2z" />
                    </svg>
                    <?php echo esc_html(get_field('recipe_servings')); ?> servings
                </span>
            <?php endif; ?>
            <?php if (get_field('recipe_difficulty')) : ?>
                <span class="recipe-difficulty">
                    <svg width="16" height="16" viewBox="0 0 24 24">
                        <path d="M12 3v18M3 12h18" stroke="currentColor" stroke-width="2" />
                    </svg>
                    <?php echo esc_html(get_field('recipe_difficulty')); ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Recipe Title & Excerpt -->
        <h2 class="feed-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <div class="feed-card-excerpt">
            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
        </div>

        <!-- Categories -->
        <?php
        $categories = get_the_category();
        if ($categories) : ?>
            <div class="recipe-categories">
                <?php foreach ($categories as $category) : ?>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>" class="recipe-category">
                        #<?php echo esc_html($category->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Post Meta -->
        <div class="feed-card-meta">
            <time datetime="<?php echo get_the_date('c'); ?>">
                <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?>
            </time>
        </div>
    </div>
</article>