<?php

/**
 * Template part for displaying recipe posts
 */

// Verify we have all required recipe data
$has_recipe_data = true;
$missing_fields = array();
$required_fields = array(
    '_recipe_prep_time' => 'Prep Time',
    '_recipe_cook_time' => 'Cook Time',
    '_recipe_servings' => 'Servings',
    '_recipe_ingredients' => 'Ingredients',
    '_recipe_instructions' => 'Instructions'
);

foreach ($required_fields as $field => $label) {
    if (empty(get_post_meta(get_the_ID(), $field, true))) {
        $has_recipe_data = false;
        $missing_fields[] = $label;
    }
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('recipe-article'); ?>>
    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>

        <div class="entry-meta">
            <?php
            echo sprintf(
                'By %s on %s',
                get_the_author_link(),
                get_the_date()
            );
            ?>
        </div>
    </header>

    <?php if (!$has_recipe_data): ?>
        <div class="recipe-error-notice">
            <p>This recipe is missing required information: <?php echo esc_html(implode(', ', $missing_fields)); ?></p>
            <?php if (current_user_can('edit_post', get_the_ID())): ?>
                <p><a href="<?php echo esc_url(get_edit_post_link()); ?>" class="button">Edit Recipe</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>

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

        <div class="recipe-card">
            <div class="recipe-meta">
                <div class="prep-time">
                    <strong>Prep Time:</strong>
                    <?php echo esc_html(tiffycooks_format_time(get_post_meta(get_the_ID(), '_recipe_prep_time', true))); ?>
                </div>
                <div class="cook-time">
                    <strong>Cook Time:</strong>
                    <?php echo esc_html(tiffycooks_format_time(get_post_meta(get_the_ID(), '_recipe_cook_time', true))); ?>
                </div>
                <div class="total-time">
                    <?php
                    $prep_time = intval(get_post_meta(get_the_ID(), '_recipe_prep_time', true));
                    $cook_time = intval(get_post_meta(get_the_ID(), '_recipe_cook_time', true));
                    ?>
                    <strong>Total Time:</strong>
                    <?php echo esc_html(tiffycooks_format_time($prep_time + $cook_time)); ?>
                </div>
                <div class="servings">
                    <strong>Servings:</strong>
                    <?php
                    $servings = get_post_meta(get_the_ID(), '_recipe_servings', true);
                    echo esc_html($servings) . ' ' . esc_html(_n('serving', 'servings', $servings, 'tiffycooks-amp'));
                    ?>
                </div>
            </div>

            <div class="recipe-content">
                <div class="ingredients">
                    <h2>Ingredients</h2>
                    <?php
                    $ingredients = tiffycooks_get_ingredients(get_the_ID());
                    if (!empty($ingredients)):
                    ?>
                        <ul class="ingredients-list">
                            <?php foreach ($ingredients as $ingredient): ?>
                                <li>
                                    <span class="amount"><?php echo esc_html($ingredient['amount']); ?></span>
                                    <span class="ingredient"><?php echo esc_html($ingredient['ingredient']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-content">No ingredients listed.</p>
                    <?php endif; ?>
                </div>

                <div class="instructions">
                    <h2>Instructions</h2>
                    <?php
                    $instructions = tiffycooks_get_instructions(get_the_ID());
                    if (!empty($instructions)):
                    ?>
                        <ol class="instructions-list">
                            <?php foreach ($instructions as $step): ?>
                                <li><?php echo esc_html($step); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    <?php else: ?>
                        <p class="no-content">No instructions provided.</p>
                    <?php endif; ?>
                </div>

                <?php
                // Insert ad before recipe notes
                echo tiffycooks_amp_insert_ad();
                ?>

                <?php
                $notes = get_post_meta(get_the_ID(), '_recipe_notes', true);
                if (!empty($notes)):
                ?>
                    <div class="recipe-notes">
                        <h2>Recipe Notes</h2>
                        <?php echo wpautop(esc_html($notes)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <footer class="entry-footer">
            <?php
            $categories_list = get_the_category_list(', ');
            if ($categories_list):
                printf('<span class="cat-links">Posted in %s</span>', $categories_list);
            endif;

            $tags_list = get_the_tag_list('', ', ');
            if ($tags_list):
                printf('<span class="tags-links">Tagged %s</span>', $tags_list);
            endif;
            ?>
        </footer>

        <?php
        // Add Recipe Schema only if we have all required data
        $ingredients = tiffycooks_get_ingredients(get_the_ID());
        $ingredient_list = array();
        foreach ($ingredients as $ingredient) {
            $ingredient_list[] = $ingredient['amount'] . ' ' . $ingredient['ingredient'];
        }

        $instructions = tiffycooks_get_instructions(get_the_ID());
        $instruction_steps = array();
        foreach ($instructions as $step) {
            $instruction_steps[] = array(
                "@type" => "HowToStep",
                "text" => $step
            );
        }

        $recipe_schema = array(
            '@context' => 'https://schema.org/',
            '@type' => 'Recipe',
            'name' => get_the_title(),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author()
            ),
            'datePublished' => get_the_date('c'),
            'description' => get_the_excerpt(),
            'prepTime' => 'PT' . intval(get_post_meta(get_the_ID(), '_recipe_prep_time', true)) . 'M',
            'cookTime' => 'PT' . intval(get_post_meta(get_the_ID(), '_recipe_cook_time', true)) . 'M',
            'totalTime' => 'PT' . (intval(get_post_meta(get_the_ID(), '_recipe_prep_time', true)) + intval(get_post_meta(get_the_ID(), '_recipe_cook_time', true))) . 'M',
            'recipeYield' => get_post_meta(get_the_ID(), '_recipe_servings', true) . ' servings',
            'recipeIngredient' => $ingredient_list,
            'recipeInstructions' => $instruction_steps,
            'image' => $img_src ? $img_src[0] : ''
        );
        ?>

        <script type="application/ld+json">
            <?php echo json_encode($recipe_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT); ?>
        </script>
    <?php endif; ?>
</article>

<style>
    .recipe-error-notice {
        margin: 2rem;
        padding: 1rem;
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        color: #856404;
    }

    .recipe-error-notice .button {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        margin-top: 1rem;
    }

    .no-content {
        color: #666;
        font-style: italic;
    }
</style><?php
