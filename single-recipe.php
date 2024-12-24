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
            <!-- Top of page ad -->
            <div class="ad-test-container">
                <amp-ad width="300"
                    height="250"
                    type="fake"
                    data-use-a4a="true">
                    <div placeholder>
                        <div style="background: red; color: white; padding: 20px; text-align: center;">
                            TOP OF PAGE AD
                        </div>
                    </div>
                </amp-ad>
            </div>

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

            <!-- After header ad -->
            <div class="ad-test-container">
                <amp-ad width="300"
                    height="250"
                    type="fake"
                    data-use-a4a="true">
                    <div placeholder>
                        <div style="background: red; color: white; padding: 20px; text-align: center;">
                            AFTER HEADER AD
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

                <!-- After featured image ad -->
                <div class="ad-test-container">
                    <amp-ad width="300"
                        height="250"
                        type="fake"
                        data-use-a4a="true">
                        <div placeholder>
                            <div style="background: red; color: white; padding: 20px; text-align: center;">
                                AFTER IMAGE AD
                            </div>
                        </div>
                    </amp-ad>
                </div>
            <?php endif; ?>

            <div class="recipe-content">
                <?php if (!empty(get_the_content())) : ?>
                    <div class="recipe-description">
                        <?php the_content(); ?>
                    </div>

                    <!-- After description ad -->
                    <div class="ad-test-container">
                        <amp-ad width="300"
                            height="250"
                            type="fake"
                            data-use-a4a="true">
                            <div placeholder>
                                <div style="background: red; color: white; padding: 20px; text-align: center;">
                                    AFTER DESCRIPTION AD
                                </div>
                            </div>
                        </amp-ad>
                    </div>
                <?php endif; ?>

                <!-- Recipe meta information -->
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

                <!-- Before ingredients ad -->
                <div class="ad-test-container">
                    <amp-ad width="300"
                        height="250"
                        type="fake"
                        data-use-a4a="true">
                        <div placeholder>
                            <div style="background: red; color: white; padding: 20px; text-align: center;">
                                BEFORE INGREDIENTS AD
                            </div>
                        </div>
                    </amp-ad>
                </div>

                <!-- Ingredients section -->
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

                <!-- After ingredients ad -->
                <div class="ad-test-container">
                    <amp-ad width="300"
                        height="250"
                        type="fake"
                        data-use-a4a="true">
                        <div placeholder>
                            <div style="background: red; color: white; padding: 20px; text-align: center;">
                                AFTER INGREDIENTS AD
                            </div>
                        </div>
                    </amp-ad>
                </div>

                <!-- Instructions section -->
                <div class="recipe-instructions">
                    <h2><?php esc_html_e('Instructions', 'adsense-recipe-food-blog'); ?></h2>
                    <?php if (!empty($recipe_data['instructions'])) : ?>
                        <ol class="instructions-list">
                            <?php
                            $instruction_count = count($recipe_data['instructions']);
                            foreach ($recipe_data['instructions'] as $index => $step) :
                                echo '<li>' . esc_html($step) . '</li>';

                                // Add an ad after every 3 steps (except the last step)
                                if (($index + 1) % 3 === 0 && ($index + 1) < $instruction_count) :
                            ?>
                                    <div class="ad-test-container">
                                        <amp-ad width="300"
                                            height="250"
                                            type="fake"
                                            data-use-a4a="true">
                                            <div placeholder>
                                                <div style="background: red; color: white; padding: 20px; text-align: center;">
                                                    MID-INSTRUCTIONS AD <?php echo floor(($index + 1) / 3); ?>
                                                </div>
                                            </div>
                                        </amp-ad>
                                    </div>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        </ol>
                    <?php endif; ?>
                </div>

                <!-- After instructions ad -->
                <div class="ad-test-container">
                    <amp-ad width="300"
                        height="250"
                        type="fake"
                        data-use-a4a="true">
                        <div placeholder>
                            <div style="background: red; color: white; padding: 20px; text-align: center;">
                                AFTER INSTRUCTIONS AD
                            </div>
                        </div>
                    </amp-ad>
                </div>

                <?php if (!empty($recipe_data['notes'])) : ?>
                    <div class="recipe-notes">
                        <h2><?php esc_html_e('Recipe Notes', 'adsense-recipe-food-blog'); ?></h2>
                        <?php echo wp_kses_post(wpautop($recipe_data['notes'])); ?>
                    </div>

                    <!-- After notes ad -->
                    <div class="ad-test-container">
                        <amp-ad width="300"
                            height="250"
                            type="fake"
                            data-use-a4a="true">
                            <div placeholder>
                                <div style="background: red; color: white; padding: 20px; text-align: center;">
                                    AFTER NOTES AD
                                </div>
                            </div>
                        </amp-ad>
                    </div>
                <?php endif; ?>
            </div>
        </article>

        <!-- Bottom of page ad -->
        <div class="ad-test-container">
            <amp-ad width="300"
                height="250"
                type="fake"
                data-use-a4a="true">
                <div placeholder>
                    <div style="background: red; color: white; padding: 20px; text-align: center;">
                        BOTTOM OF PAGE AD
                    </div>
                </div>
            </amp-ad>
        </div>

    <?php endwhile; ?>
</main>

<?php
get_footer();
?>