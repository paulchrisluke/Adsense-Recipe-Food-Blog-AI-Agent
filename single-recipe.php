<?php

/**
 * Template for displaying single recipe posts
 */

get_header();
?>

<main id="primary" class="site-main">
    <?php
    while (have_posts()) :
        the_post();
    ?>
        <article id="recipe-<?php the_ID(); ?>" <?php post_class('recipe-article'); ?>>
            <header class="entry-header">
                <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

                <div class="entry-meta">
                    <?php
                    printf(
                        esc_html__('By %s on %s', 'adsense-recipe-food-blog'),
                        sprintf('<span class="author vcard">%s</span>', get_the_author()),
                        get_the_date()
                    );
                    ?>
                </div>
            </header>

            <?php
            // First ad unit - After header
            ?>
            <div class="ad-test-container">
                <amp-ad width="300"
                    height="250"
                    type="fake"
                    data-use-a4a="true">
                    <div placeholder>
                        <div style="background: red; color: white; padding: 20px; text-align: center;">
                            TEST AD PLACEHOLDER
                        </div>
                    </div>
                </amp-ad>
            </div>

            <?php if (has_post_thumbnail()) : ?>
                <div class="recipe-featured-image">
                    <?php
                    $thumbnail_id = get_post_thumbnail_id();
                    $img_src = wp_get_attachment_image_src($thumbnail_id, 'full');
                    if ($img_src) :
                    ?>
                        <amp-img src="<?php echo esc_url($img_src[0]); ?>"
                            width="<?php echo esc_attr($img_src[1]); ?>"
                            height="<?php echo esc_attr($img_src[2]); ?>"
                            layout="responsive"
                            alt="<?php echo esc_attr(get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true)); ?>">
                        </amp-img>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="recipe-content">
                <?php if (!empty(get_the_content())) : ?>
                    <div class="recipe-description">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>

                <?php
                // Second ad unit - After description
                ?>
                <div class="ad-test-container">
                    <amp-ad width="300"
                        height="250"
                        type="fake"
                        data-use-a4a="true">
                        <div placeholder>
                            <div style="background: red; color: white; padding: 20px; text-align: center;">
                                TEST AD PLACEHOLDER
                            </div>
                        </div>
                    </amp-ad>
                </div>

                <div class="recipe-meta">
                    <?php
                    $recipe_data = tiffycooks_recipe_manager()->get_recipe_rest_data();
                    ?>
                    <div class="prep-time">
                        <strong><?php esc_html_e('Prep Time:', 'adsense-recipe-food-blog'); ?></strong>
                        <?php echo esc_html(tiffycooks_format_time($recipe_data['prep_time'])); ?>
                    </div>
                    <div class="cook-time">
                        <strong><?php esc_html_e('Cook Time:', 'adsense-recipe-food-blog'); ?></strong>
                        <?php echo esc_html(tiffycooks_format_time($recipe_data['cook_time'])); ?>
                    </div>
                    <div class="total-time">
                        <strong><?php esc_html_e('Total Time:', 'adsense-recipe-food-blog'); ?></strong>
                        <?php echo esc_html(tiffycooks_format_time($recipe_data['prep_time'] + $recipe_data['cook_time'])); ?>
                    </div>
                    <div class="servings">
                        <strong><?php esc_html_e('Servings:', 'adsense-recipe-food-blog'); ?></strong>
                        <?php echo esc_html($recipe_data['servings']); ?>
                    </div>
                </div>

                <?php
                // Third ad unit - Before ingredients
                ?>
                <div class="ad-test-container">
                    <amp-ad width="300"
                        height="250"
                        type="fake"
                        data-use-a4a="true">
                        <div placeholder>
                            <div style="background: red; color: white; padding: 20px; text-align: center;">
                                TEST AD PLACEHOLDER
                            </div>
                        </div>
                    </amp-ad>
                </div>

                <div class="recipe-ingredients">
                    <h2><?php esc_html_e('Ingredients', 'adsense-recipe-food-blog'); ?></h2>
                    <?php if (!empty($recipe_data['ingredients'])) : ?>
                        <ul class="ingredients-list">
                            <?php foreach ($recipe_data['ingredients'] as $ingredient) : ?>
                                <li><?php echo esc_html($ingredient); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <?php
                // Fourth ad unit - Before instructions
                ?>
                <div class="ad-test-container">
                    <amp-ad width="300"
                        height="250"
                        type="fake"
                        data-use-a4a="true">
                        <div placeholder>
                            <div style="background: red; color: white; padding: 20px; text-align: center;">
                                TEST AD PLACEHOLDER
                            </div>
                        </div>
                    </amp-ad>
                </div>

                <div class="recipe-instructions">
                    <h2><?php esc_html_e('Instructions', 'adsense-recipe-food-blog'); ?></h2>
                    <?php if (!empty($recipe_data['instructions'])) : ?>
                        <ol class="instructions-list">
                            <?php foreach ($recipe_data['instructions'] as $step) : ?>
                                <li><?php echo esc_html($step); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </div>

                <?php
                // Fifth ad unit - After instructions
                ?>
                <div class="ad-test-container">
                    <amp-ad width="300"
                        height="250"
                        type="fake"
                        data-use-a4a="true">
                        <div placeholder>
                            <div style="background: red; color: white; padding: 20px; text-align: center;">
                                TEST AD PLACEHOLDER
                            </div>
                        </div>
                    </amp-ad>
                </div>

                <?php if (!empty($recipe_data['notes'])) : ?>
                    <div class="recipe-notes">
                        <h2><?php esc_html_e('Recipe Notes', 'adsense-recipe-food-blog'); ?></h2>
                        <?php echo wp_kses_post(wpautop($recipe_data['notes'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </article>

        <?php
        // Final ad unit - After content
        ?>
        <div class="ad-test-container">
            <amp-ad width="300"
                height="250"
                type="fake"
                data-use-a4a="true">
                <div placeholder>
                    <div style="background: red; color: white; padding: 20px; text-align: center;">
                        TEST AD PLACEHOLDER
                    </div>
                </div>
            </amp-ad>
        </div>

    <?php endwhile; ?>
</main>

<?php
get_footer();
?>