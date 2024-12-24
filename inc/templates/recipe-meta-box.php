<?php

/**
 * Recipe Meta Box Template
 *
 * @package TiffyCooks
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if WordPress core is loaded
if (!function_exists('wp_verify_nonce')) {
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}

if (!function_exists('esc_html_e')) {
    require_once(ABSPATH . 'wp-includes/formatting.php');
}

if (!function_exists('esc_attr')) {
    require_once(ABSPATH . 'wp-includes/formatting.php');
}

// Verify this is being loaded in admin context
if (!is_admin()) {
    return;
}
?>

<div class="recipe-meta-box">
    <p>
        <label for="recipe_prep_time"><?php esc_html_e('Prep Time (minutes):', 'adsense-recipe-food-blog'); ?></label>
        <input type="number" id="recipe_prep_time" name="recipe_prep_time" value="<?php echo esc_attr($prep_time); ?>" min="0" />
    </p>

    <p>
        <label for="recipe_cook_time"><?php esc_html_e('Cook Time (minutes):', 'adsense-recipe-food-blog'); ?></label>
        <input type="number" id="recipe_cook_time" name="recipe_cook_time" value="<?php echo esc_attr($cook_time); ?>" min="0" />
    </p>

    <p>
        <label for="recipe_servings"><?php esc_html_e('Servings:', 'adsense-recipe-food-blog'); ?></label>
        <input type="number" id="recipe_servings" name="recipe_servings" value="<?php echo esc_attr($servings); ?>" min="1" />
    </p>

    <p>
        <label for="recipe_ingredients"><?php esc_html_e('Ingredients (one per line):', 'adsense-recipe-food-blog'); ?></label>
        <textarea id="recipe_ingredients" name="recipe_ingredients" rows="10" class="large-text"><?php echo esc_textarea($ingredients); ?></textarea>
        <span class="description"><?php esc_html_e('Enter each ingredient on a new line.', 'adsense-recipe-food-blog'); ?></span>
    </p>

    <p>
        <label for="recipe_instructions"><?php esc_html_e('Instructions (one step per line):', 'adsense-recipe-food-blog'); ?></label>
        <textarea id="recipe_instructions" name="recipe_instructions" rows="10" class="large-text"><?php echo esc_textarea($instructions); ?></textarea>
        <span class="description"><?php esc_html_e('Enter each instruction step on a new line.', 'adsense-recipe-food-blog'); ?></span>
    </p>

    <p>
        <label for="recipe_notes"><?php esc_html_e('Recipe Notes:', 'adsense-recipe-food-blog'); ?></label>
        <textarea id="recipe_notes" name="recipe_notes" rows="5" class="large-text"><?php echo esc_textarea($notes); ?></textarea>
    </p>
</div>

<style>
    .recipe-meta-box label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .recipe-meta-box input[type="number"] {
        width: 100px;
    }

    .recipe-meta-box textarea {
        width: 100%;
    }

    .recipe-meta-box .description {
        display: block;
        font-style: italic;
        color: #666;
        margin-top: 5px;
    }
</style>