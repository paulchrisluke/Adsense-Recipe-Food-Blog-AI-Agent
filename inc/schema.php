<?php

/**
 * Schema.org Recipe Markup Functions
 *
 * @package TiffyCooks
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Generate Recipe Schema.org JSON-LD markup
 * 
 * @param array $recipe_data Recipe data array
 * @return string JSON-LD markup
 */
function tiffycooks_generate_recipe_schema($recipe_data)
{
    if (empty($recipe_data)) {
        return '';
    }

    $schema = array(
        '@context' => 'https://schema.org/',
        '@type' => 'Recipe',
        'name' => get_the_title(),
        'author' => array(
            '@type' => 'Person',
            'name' => get_the_author()
        ),
        'datePublished' => get_the_date('c'),
        'description' => get_the_excerpt(),
    );

    // Add time information if available
    if (!empty($recipe_data['prep_time'])) {
        $schema['prepTime'] = 'PT' . $recipe_data['prep_time'] . 'M';
    }
    if (!empty($recipe_data['cook_time'])) {
        $schema['cookTime'] = 'PT' . $recipe_data['cook_time'] . 'M';
    }
    if (!empty($recipe_data['prep_time']) && !empty($recipe_data['cook_time'])) {
        $schema['totalTime'] = 'PT' . ($recipe_data['prep_time'] + $recipe_data['cook_time']) . 'M';
    }

    // Add servings if available
    if (!empty($recipe_data['servings'])) {
        $schema['recipeYield'] = $recipe_data['servings'] . ' servings';
    }

    // Add categories and tags
    $schema['recipeCategory'] = wp_get_post_terms(get_the_ID(), 'category', array('fields' => 'names'));
    $schema['keywords'] = wp_get_post_terms(get_the_ID(), 'post_tag', array('fields' => 'names'));

    // Add image if available
    if (has_post_thumbnail()) {
        $image_id = get_post_thumbnail_id();
        $image_url = wp_get_attachment_image_src($image_id, 'full');
        if ($image_url) {
            $schema['image'] = array(
                '@type' => 'ImageObject',
                'url' => $image_url[0],
                'width' => $image_url[1],
                'height' => $image_url[2]
            );
        }
    }

    // Add video if available
    if (!empty($recipe_data['video']['url'])) {
        $schema['video'] = array(
            '@type' => 'VideoObject',
            'name' => get_the_title() . ' Video',
            'description' => 'Video guide for ' . get_the_title(),
            'contentUrl' => $recipe_data['video']['url'],
            'uploadDate' => get_the_date('c')
        );
    }

    // Add ingredients if available
    if (!empty($recipe_data['ingredients'])) {
        $schema['recipeIngredient'] = array_map('strip_tags', $recipe_data['ingredients']);
    }

    // Add instructions if available
    if (!empty($recipe_data['instructions'])) {
        $schema['recipeInstructions'] = array_map(function ($step) {
            return array(
                '@type' => 'HowToStep',
                'text' => strip_tags($step)
            );
        }, $recipe_data['instructions']);
    }

    return wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * Output Recipe Schema.org JSON-LD markup in the page head
 */
function tiffycooks_output_recipe_schema()
{
    if (!is_singular('post')) {
        return;
    }

    if (!function_exists('tiffycooks_recipe_manager')) {
        return;
    }

    $recipe_data = tiffycooks_recipe_manager()->get_recipe_rest_data();
    if (empty($recipe_data)) {
        return;
    }

    $schema = tiffycooks_generate_recipe_schema($recipe_data);
    if ($schema) {
        echo '<script type="application/ld+json">' . $schema . '</script>';
    }
}
add_action('wp_head', 'tiffycooks_output_recipe_schema');
