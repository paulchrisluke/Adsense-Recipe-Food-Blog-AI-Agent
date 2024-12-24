<?php

/**
 * The sidebar containing the main widget area
 *
 * @package TiffyCooks
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<aside id="sidebar" class="site-sidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <div class="site-icon">
            <?php
            // Check for custom logo first
            if (has_custom_logo()) {
                $custom_logo_id = get_theme_mod('custom_logo');
                $logo_image = wp_get_attachment_image_src($custom_logo_id, 'full');
                if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                    printf(
                        '<amp-img src="%1$s" width="48" height="48" alt="%2$s" layout="fixed"></amp-img>',
                        esc_url($logo_image[0]),
                        esc_attr(get_bloginfo('name'))
                    );
                } else {
                    the_custom_logo();
                }
            }
            // Fallback to site icon if no custom logo
            elseif (has_site_icon()) {
                $site_icon_url = get_site_icon_url(96);
                if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                    printf(
                        '<amp-img src="%1$s" width="48" height="48" alt="%2$s" layout="fixed"></amp-img>',
                        esc_url($site_icon_url),
                        esc_attr(get_bloginfo('name'))
                    );
                } else {
                    printf(
                        '<img src="%1$s" width="48" height="48" alt="%2$s">',
                        esc_url($site_icon_url),
                        esc_attr(get_bloginfo('name'))
                    );
                }
            }
            ?>
        </div>

        <div class="site-branding">
            <p class="site-title">
                <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                    <?php bloginfo('name'); ?>
                </a>
            </p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-navigation">
        <!-- Primary Menu -->
        <?php if (has_nav_menu('primary')) : ?>
            <div class="sidebar-section">
                <h2 class="sidebar-heading">Menu</h2>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'sidebar-menu',
                    'container' => false,
                    'fallback_cb' => false,
                ));
                ?>
            </div>
        <?php endif; ?>

        <!-- Categories -->
        <div class="sidebar-section">
            <h2 class="sidebar-heading">Categories</h2>
            <ul class="sidebar-menu">
                <?php
                $categories = get_categories(array(
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'hide_empty' => true
                ));

                foreach ($categories as $category) {
                    printf(
                        '<li><a href="%1$s">%2$s</a></li>',
                        esc_url(get_category_link($category->term_id)),
                        esc_html($category->name)
                    );
                }
                ?>
            </ul>
        </div>

        <!-- Recent Posts -->
        <div class="sidebar-section">
            <h2 class="sidebar-heading">Recent Posts</h2>
            <ul class="recent-posts-list">
                <?php
                $recent_posts = wp_get_recent_posts(array(
                    'numberposts' => 5,
                    'post_status' => 'publish'
                ));

                foreach ($recent_posts as $recent) {
                    $post_thumbnail = '';
                    if (has_post_thumbnail($recent['ID'])) {
                        if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                            $post_thumbnail = sprintf(
                                '<amp-img src="%1$s" width="40" height="40" alt="%2$s" layout="fixed"></amp-img>',
                                esc_url(get_the_post_thumbnail_url($recent['ID'], 'thumbnail')),
                                esc_attr($recent['post_title'])
                            );
                        } else {
                            $post_thumbnail = get_the_post_thumbnail($recent['ID'], array(40, 40));
                        }
                    }
                ?>
                    <li class="recent-post-item">
                        <a href="<?php echo esc_url(get_permalink($recent['ID'])); ?>">
                            <?php if ($post_thumbnail) : ?>
                                <div class="recent-post-thumbnail">
                                    <?php echo $post_thumbnail; ?>
                                </div>
                            <?php endif; ?>
                            <div class="recent-post-content">
                                <h3 class="recent-post-title"><?php echo esc_html($recent['post_title']); ?></h3>
                                <span class="recent-post-date"><?php echo get_the_date('', $recent['ID']); ?></span>
                            </div>
                        </a>
                    </li>
                <?php
                }
                wp_reset_postdata();
                ?>
            </ul>
        </div>
    </nav>

    <!-- Footer -->
    <footer class="sidebar-footer">
        <?php if (is_active_sidebar('sidebar-footer')) : ?>
            <div class="sidebar-footer-widgets">
                <?php dynamic_sidebar('sidebar-footer'); ?>
            </div>
        <?php endif; ?>
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. <?php _e('All rights reserved.', 'tiffycooks'); ?></p>
        </div>
    </footer>
</aside>