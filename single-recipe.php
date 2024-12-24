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

        $recipe_manager = tiffycooks_recipe_manager();
        $recipe_data = $recipe_manager->get_recipe_rest_data(array('id' => get_the_ID()));
        $has_video = !empty($recipe_data['video']['url']) || !empty($recipe_data['video']['embed']);
    ?>
        <article id="recipe-<?php the_ID(); ?>" <?php post_class('recipe-article'); ?>>
            <header class="entry-header">
                <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

                <div class="entry-meta">
                    <?php
                    // Author and date
                    printf(
                        esc_html__('By %s on %s', 'adsense-recipe-food-blog'),
                        sprintf('<span class="author vcard">%s</span>', get_the_author()),
                        get_the_date()
                    );

                    // Print button
                    echo '<div class="recipe-actions">';
                    printf(
                        '<button onclick="window.print()" class="print-button">%s</button>',
                        esc_html__('Print Recipe', 'adsense-recipe-food-blog')
                    );
                    echo '</div>';
                    ?>
                </div>
            </header>

            <?php if (has_post_thumbnail()): ?>
                <div class="recipe-featured-image">
                    <?php
                    $thumbnail_id = get_post_thumbnail_id();
                    $img_src = wp_get_attachment_image_src($thumbnail_id, 'full');
                    if ($img_src):
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

            <?php
            // Insert ad after featured image
            if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                echo tiffycooks_amp_insert_ad('after_image');
            }
            ?>

            <div class="recipe-content">
                <?php if (!empty(get_the_content())): ?>
                    <div class="recipe-description">
                        <?php the_content(); ?>
                    </div>
                <?php endif; ?>

                <?php if ($has_video): ?>
                    <div class="recipe-video">
                        <h2><?php esc_html_e('Recipe Video', 'adsense-recipe-food-blog'); ?></h2>
                        <?php
                        if (!empty($recipe_data['video']['embed'])) {
                            echo wp_kses_post($recipe_data['video']['embed']);
                        } elseif (!empty($recipe_data['video']['url'])) {
                            echo wp_oembed_get($recipe_data['video']['url']);
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="recipe-meta">
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

                <div class="recipe-ingredients">
                    <h2><?php esc_html_e('Ingredients', 'adsense-recipe-food-blog'); ?></h2>
                    <?php if (!empty($recipe_data['ingredients']['items'])): ?>
                        <?php if (!empty($recipe_data['ingredients']['groups'])): ?>
                            <?php foreach ($recipe_data['ingredients']['groups'] as $group): ?>
                                <h3><?php echo esc_html($group); ?></h3>
                                <ul class="ingredients-list">
                                    <?php foreach ($recipe_data['ingredients']['items'] as $ingredient): ?>
                                        <?php if ($ingredient['group'] === $group): ?>
                                            <li>
                                                <span class="amount"><?php echo esc_html($ingredient['amount']); ?></span>
                                                <span class="ingredient"><?php echo esc_html($ingredient['ingredient']); ?></span>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endforeach; ?>

                            <?php if ($ungrouped = array_filter($recipe_data['ingredients']['items'], function ($i) {
                                return empty($i['group']);
                            })): ?>
                                <ul class="ingredients-list">
                                    <?php foreach ($ungrouped as $ingredient): ?>
                                        <li>
                                            <span class="amount"><?php echo esc_html($ingredient['amount']); ?></span>
                                            <span class="ingredient"><?php echo esc_html($ingredient['ingredient']); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php else: ?>
                            <ul class="ingredients-list">
                                <?php foreach ($recipe_data['ingredients']['items'] as $ingredient): ?>
                                    <li>
                                        <span class="amount"><?php echo esc_html($ingredient['amount']); ?></span>
                                        <span class="ingredient"><?php echo esc_html($ingredient['ingredient']); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="no-content"><?php esc_html_e('No ingredients listed.', 'adsense-recipe-food-blog'); ?></p>
                    <?php endif; ?>
                </div>

                <?php
                // Insert ad before instructions
                if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                    echo tiffycooks_amp_insert_ad('before_instructions');
                }
                ?>

                <div class="recipe-instructions">
                    <h2><?php esc_html_e('Instructions', 'adsense-recipe-food-blog'); ?></h2>
                    <?php if (!empty($recipe_data['instructions']['steps'])): ?>
                        <?php if (!empty($recipe_data['instructions']['groups'])): ?>
                            <?php foreach ($recipe_data['instructions']['groups'] as $group): ?>
                                <h3><?php echo esc_html($group); ?></h3>
                                <ol class="instructions-list">
                                    <?php foreach ($recipe_data['instructions']['steps'] as $step): ?>
                                        <?php if ($step['group'] === $group): ?>
                                            <li>
                                                <?php
                                                echo esc_html($step['text']);
                                                if (!empty($step['image'])) {
                                                    echo '<div class="step-image">';
                                                    echo wp_get_attachment_image($step['image'], 'medium');
                                                    echo '</div>';
                                                }
                                                ?>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ol>
                            <?php endforeach; ?>

                            <?php if ($ungrouped = array_filter($recipe_data['instructions']['steps'], function ($s) {
                                return empty($s['group']);
                            })): ?>
                                <ol class="instructions-list">
                                    <?php foreach ($ungrouped as $step): ?>
                                        <li>
                                            <?php
                                            echo esc_html($step['text']);
                                            if (!empty($step['image'])) {
                                                echo '<div class="step-image">';
                                                echo wp_get_attachment_image($step['image'], 'medium');
                                                echo '</div>';
                                            }
                                            ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            <?php endif; ?>
                        <?php else: ?>
                            <ol class="instructions-list">
                                <?php foreach ($recipe_data['instructions']['steps'] as $step): ?>
                                    <li>
                                        <?php
                                        echo esc_html($step['text']);
                                        if (!empty($step['image'])) {
                                            echo '<div class="step-image">';
                                            echo wp_get_attachment_image($step['image'], 'medium');
                                            echo '</div>';
                                        }
                                        ?>
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="no-content"><?php esc_html_e('No instructions provided.', 'adsense-recipe-food-blog'); ?></p>
                    <?php endif; ?>
                </div>

                <?php
                // Insert ad after instructions
                if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
                    echo tiffycooks_amp_insert_ad('after_instructions');
                }

                if (!empty($recipe_data['notes'])):
                ?>
                    <div class="recipe-notes">
                        <h2><?php esc_html_e('Recipe Notes', 'adsense-recipe-food-blog'); ?></h2>
                        <?php echo wp_kses_post(wpautop($recipe_data['notes'])); ?>
                    </div>
                <?php endif; ?>
            </div>

            <footer class="entry-footer">
                <?php
                $categories_list = get_the_category_list(', ');
                if ($categories_list) {
                    printf(
                        '<span class="cat-links">%s %s</span>',
                        esc_html__('Posted in:', 'adsense-recipe-food-blog'),
                        $categories_list
                    );
                }

                $tags_list = get_the_tag_list('', ', ');
                if ($tags_list) {
                    printf(
                        '<span class="tags-links">%s %s</span>',
                        esc_html__('Tagged:', 'adsense-recipe-food-blog'),
                        $tags_list
                    );
                }
                ?>
            </footer>
        </article>

    <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;

    endwhile;
    ?>
</main>

<?php
get_footer();
?>