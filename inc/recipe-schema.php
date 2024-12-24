<?php

/**
 * Recipe Schema Implementation
 */

if (!defined('ABSPATH')) {
    exit;
}

class TiffyCooks_Recipe_Schema
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('wp_head', array($this, 'output_schema'), 1);
    }

    public function output_schema()
    {
        if (!is_singular('recipe')) {
            return;
        }

        $post_id = get_the_ID();
        $recipe_manager = tiffycooks_recipe_manager();
        $recipe_data = $recipe_manager->get_recipe_rest_data(array('id' => $post_id));

        // Base recipe schema
        $schema = array(
            '@context' => 'https://schema.org/',
            '@type' => 'Recipe',
            'name' => get_the_title($post_id),
            'description' => get_the_excerpt($post_id),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', get_post_field('post_author', $post_id))
            ),
            'prepTime' => 'PT' . intval($recipe_data['prep_time']) . 'M',
            'cookTime' => 'PT' . intval($recipe_data['cook_time']) . 'M',
            'totalTime' => 'PT' . (intval($recipe_data['prep_time']) + intval($recipe_data['cook_time'])) . 'M',
            'recipeYield' => $recipe_data['servings'] . ' servings',
            'recipeCategory' => $this->get_recipe_categories($post_id),
            'recipeCuisine' => $this->get_recipe_cuisines($post_id),
            'keywords' => $this->get_recipe_keywords($post_id),
        );

        // Add nutrition information if available
        $nutrition = $this->get_nutrition_info($post_id);
        if (!empty($nutrition)) {
            $schema['nutrition'] = $nutrition;
        }

        // Add recipe images
        $images = $this->get_recipe_images($post_id);
        if (!empty($images)) {
            $schema['image'] = count($images) === 1 ? $images[0] : $images;
        }

        // Add video if available
        if (!empty($recipe_data['video']['url'])) {
            $schema['video'] = array(
                '@type' => 'VideoObject',
                'name' => get_the_title($post_id) . ' Video',
                'description' => 'Video guide for ' . get_the_title($post_id),
                'contentUrl' => $recipe_data['video']['url'],
                'thumbnailUrl' => array($this->get_video_thumbnail($recipe_data['video']['thumbnail'])),
                'uploadDate' => get_the_date('c', $post_id)
            );
        }

        // Add ingredients
        $ingredients = array();
        foreach ($recipe_data['ingredients']['items'] as $ingredient) {
            $ingredients[] = trim($ingredient['amount'] . ' ' . $ingredient['ingredient']);
        }
        $schema['recipeIngredient'] = $ingredients;

        // Add instructions
        $instructions = array();
        foreach ($recipe_data['instructions']['steps'] as $index => $step) {
            $instruction = array(
                '@type' => 'HowToStep',
                'position' => $index + 1,
                'text' => $step['text']
            );

            if (!empty($step['image'])) {
                $img_url = wp_get_attachment_image_url($step['image'], 'full');
                if ($img_url) {
                    $instruction['image'] = array(
                        '@type' => 'ImageObject',
                        'url' => $img_url
                    );
                }
            }

            $instructions[] = $instruction;
        }
        $schema['recipeInstructions'] = $instructions;

        // Add aggregateRating if available
        $rating_data = $this->get_recipe_rating($post_id);
        if ($rating_data) {
            $schema['aggregateRating'] = $rating_data;
        }

        // Output schema
        echo '<script type="application/ld+json">' . PHP_EOL;
        echo wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo PHP_EOL . '</script>' . PHP_EOL;
    }

    private function get_recipe_categories($post_id)
    {
        $categories = wp_get_post_terms($post_id, 'recipe_category', array('fields' => 'names'));
        return is_wp_error($categories) ? array() : $categories;
    }

    private function get_recipe_cuisines($post_id)
    {
        $cuisines = wp_get_post_terms($post_id, 'recipe_cuisine', array('fields' => 'names'));
        return is_wp_error($cuisines) ? array() : $cuisines;
    }

    private function get_recipe_keywords($post_id)
    {
        $tags = wp_get_post_terms($post_id, 'recipe_tag', array('fields' => 'names'));
        return is_wp_error($tags) ? '' : implode(', ', $tags);
    }

    private function get_recipe_images($post_id)
    {
        $images = array();

        // Featured image
        if (has_post_thumbnail($post_id)) {
            $thumbnail_id = get_post_thumbnail_id($post_id);
            $img_url = wp_get_attachment_image_url($thumbnail_id, 'full');
            if ($img_url) {
                $images[] = array(
                    '@type' => 'ImageObject',
                    'url' => $img_url,
                    'width' => wp_get_attachment_metadata($thumbnail_id)['width'],
                    'height' => wp_get_attachment_metadata($thumbnail_id)['height']
                );
            }
        }

        return $images;
    }

    private function get_video_thumbnail($thumbnail_id)
    {
        if (!$thumbnail_id) {
            return '';
        }
        return wp_get_attachment_image_url($thumbnail_id, 'full');
    }

    private function get_recipe_rating($post_id)
    {
        $rating_count = get_post_meta($post_id, '_recipe_rating_count', true);
        $rating_sum = get_post_meta($post_id, '_recipe_rating_sum', true);

        if ($rating_count && $rating_sum) {
            return array(
                '@type' => 'AggregateRating',
                'ratingValue' => number_format($rating_sum / $rating_count, 1),
                'ratingCount' => intval($rating_count)
            );
        }

        return null;
    }

    private function get_nutrition_info($post_id)
    {
        $nutrition_fields = array(
            'calories',
            'carbohydrateContent',
            'cholesterolContent',
            'fatContent',
            'fiberContent',
            'proteinContent',
            'saturatedFatContent',
            'sodiumContent',
            'sugarContent',
            'transFatContent',
            'unsaturatedFatContent'
        );

        $nutrition = array('@type' => 'NutritionInformation');
        $has_nutrition = false;

        foreach ($nutrition_fields as $field) {
            $value = get_post_meta($post_id, '_recipe_nutrition_' . $field, true);
            if ($value !== '') {
                $nutrition[$field] = $value;
                $has_nutrition = true;
            }
        }

        return $has_nutrition ? $nutrition : null;
    }
}

// Initialize the recipe schema
function tiffycooks_recipe_schema()
{
    return TiffyCooks_Recipe_Schema::get_instance();
}
add_action('plugins_loaded', 'tiffycooks_recipe_schema');
