<?php

/**
 * The template for displaying the footer
 *
 * @package TiffyCooks
 */
?>

</div><!-- #page -->

<footer id="colophon" class="site-footer">
    <div class="site-info">
        <?php
        printf(
            esc_html__('© %1$s %2$s. All rights reserved.', 'adsense-recipe-food-blog'),
            date('Y'),
            get_bloginfo('name')
        );
        ?>
    </div>

    <nav class="footer-navigation">
        <?php
        wp_nav_menu(array(
            'theme_location' => 'footer',
            'menu_id'        => 'footer-menu',
            'fallback_cb'    => false,
        ));
        ?>
    </nav>
</footer>

<?php wp_footer(); ?>
</body>

</html>