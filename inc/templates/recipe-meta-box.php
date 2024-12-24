<?php

/**
 * Recipe Meta Box Template
 * 
 * This template should only be loaded in the WordPress admin area
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Verify this is being loaded in admin context
if (!is_admin()) {
    return;
}
?>

<div class="recipe-meta-box">
    <div class="recipe-meta-notice">
        <p class="description"><?php _e('This recipe will be displayed in AMP format with schema.org metadata. All fields marked with * are required.', 'adsense-recipe-food-blog'); ?></p>
    </div>

    <div class="recipe-meta-section">
        <h3><?php _e('Basic Information', 'adsense-recipe-food-blog'); ?></h3>
        <p>
            <label for="recipe_prep_time"><?php _e('Prep Time (minutes):', 'adsense-recipe-food-blog'); ?> *</label>
            <input type="number"
                id="recipe_prep_time"
                name="recipe_prep_time"
                value="<?php echo esc_attr($prep_time); ?>"
                min="0"
                required>
        </p>
        <p>
            <label for="recipe_cook_time"><?php _e('Cook Time (minutes):', 'adsense-recipe-food-blog'); ?> *</label>
            <input type="number"
                id="recipe_cook_time"
                name="recipe_cook_time"
                value="<?php echo esc_attr($cook_time); ?>"
                min="0"
                required>
        </p>
        <p>
            <label for="recipe_servings"><?php _e('Servings:', 'adsense-recipe-food-blog'); ?> *</label>
            <input type="number"
                id="recipe_servings"
                name="recipe_servings"
                value="<?php echo esc_attr($servings); ?>"
                min="1"
                required>
        </p>
    </div>

    <div class="recipe-meta-section">
        <h3><?php _e('Ingredients', 'adsense-recipe-food-blog'); ?></h3>
        <div class="ingredient-groups">
            <p>
                <button type="button" class="button add-ingredient-group"><?php _e('Add Ingredient Group', 'adsense-recipe-food-blog'); ?></button>
            </p>
            <div class="groups-container">
                <?php foreach ($ingredient_groups as $group): ?>
                    <div class="ingredient-group">
                        <input type="text"
                            name="recipe_ingredient_groups[]"
                            value="<?php echo esc_attr($group); ?>"
                            placeholder="<?php esc_attr_e('Group name (e.g., Cake Batter)', 'adsense-recipe-food-blog'); ?>">
                        <button type="button" class="button remove-group"><?php _e('Remove', 'adsense-recipe-food-blog'); ?></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <p>
            <label for="recipe_ingredients"><?php _e('Ingredients (one per line):', 'adsense-recipe-food-blog'); ?> *</label>
            <textarea id="recipe_ingredients"
                name="recipe_ingredients"
                rows="10"
                style="width: 100%;"
                required
                placeholder="<?php esc_attr_e("Format: amount | ingredient | group (optional)\nExample:\n1 cup | all-purpose flour | Cake Batter\n2 large | eggs | Cake Batter\n1/2 tsp | salt | Cake Batter", 'adsense-recipe-food-blog'); ?>"><?php echo esc_textarea($ingredients); ?></textarea>
            <span class="description"><?php _e('Format: amount | ingredient | group (optional)', 'adsense-recipe-food-blog'); ?></span>
        </p>
    </div>

    <div class="recipe-meta-section">
        <h3><?php _e('Instructions', 'adsense-recipe-food-blog'); ?></h3>
        <div class="instruction-groups">
            <p>
                <button type="button" class="button add-instruction-group"><?php _e('Add Instruction Group', 'adsense-recipe-food-blog'); ?></button>
            </p>
            <div class="groups-container">
                <?php foreach ($instruction_groups as $group): ?>
                    <div class="instruction-group">
                        <input type="text"
                            name="recipe_instruction_groups[]"
                            value="<?php echo esc_attr($group); ?>"
                            placeholder="<?php esc_attr_e('Group name (e.g., Prepare the Batter)', 'adsense-recipe-food-blog'); ?>">
                        <button type="button" class="button remove-group"><?php _e('Remove', 'adsense-recipe-food-blog'); ?></button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <p>
            <label for="recipe_instructions"><?php _e('Instructions (one step per line):', 'adsense-recipe-food-blog'); ?> *</label>
            <textarea id="recipe_instructions"
                name="recipe_instructions"
                rows="10"
                style="width: 100%;"
                required
                placeholder="<?php esc_attr_e("Format: instruction | group (optional) | image ID (optional)\nExample:\nPreheat oven to 350°F (175°C) | Preparation\nMix dry ingredients in a large bowl | Cake Batter | 123", 'adsense-recipe-food-blog'); ?>"><?php echo esc_textarea($instructions); ?></textarea>
            <span class="description"><?php _e('Format: instruction | group (optional) | image ID (optional)', 'adsense-recipe-food-blog'); ?></span>
        </p>
    </div>

    <div class="recipe-meta-section">
        <h3><?php _e('Recipe Notes', 'adsense-recipe-food-blog'); ?></h3>
        <p>
            <label for="recipe_notes"><?php _e('Additional Notes:', 'adsense-recipe-food-blog'); ?></label>
            <textarea id="recipe_notes"
                name="recipe_notes"
                rows="5"
                style="width: 100%;"
                placeholder="<?php esc_attr_e('Optional: Add any tips, variations, or additional notes about the recipe.', 'adsense-recipe-food-blog'); ?>"><?php echo esc_textarea($notes); ?></textarea>
        </p>
    </div>
</div>

<style>
    .recipe-meta-box {
        padding: 12px;
        background: #fff;
    }

    .recipe-meta-notice {
        background: #f8f9fa;
        padding: 10px;
        margin-bottom: 15px;
        border-left: 4px solid #007cba;
    }

    .recipe-meta-section {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .recipe-meta-section h3 {
        margin: 1rem 0;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007cba;
    }

    .recipe-meta-box label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .recipe-meta-box input[type="number"] {
        width: 100px;
    }

    .recipe-meta-box .description {
        display: block;
        color: #666;
        font-style: italic;
        margin-top: 5px;
    }

    .ingredient-group,
    .instruction-group {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .ingredient-group input,
    .instruction-group input {
        flex: 1;
    }

    .groups-container {
        margin: 10px 0;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .remove-group {
        color: #dc3545;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        function addGroup(container, type) {
            const template = `
            <div class="${type}-group">
                <input type="text"
                    name="recipe_${type}_groups[]"
                    placeholder="${type === 'ingredient' ? 'Group name (e.g., Cake Batter)' : 'Group name (e.g., Prepare the Batter)'}"
                    class="widefat">
                <button type="button" class="button remove-group">Remove</button>
            </div>
        `;
            container.append(template);
        }

        $('.add-ingredient-group').on('click', function() {
            addGroup($('.ingredient-groups .groups-container'), 'ingredient');
        });

        $('.add-instruction-group').on('click', function() {
            addGroup($('.instruction-groups .groups-container'), 'instruction');
        });

        $(document).on('click', '.remove-group', function() {
            $(this).closest('.ingredient-group, .instruction-group').remove();
        });
    });
</script>