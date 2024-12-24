<?php

/**
 * TiffyCooks AMP Theme functions and definitions
 *
 * @package TiffyCooks
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Theme Setup
function tiffycooks_amp_setup()
{
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Add support for AMP
    add_theme_support('amp');

    // Register nav menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'adsense-recipe-food-blog'),
        'footer' => esc_html__('Footer Menu', 'adsense-recipe-food-blog'),
    ));
}
add_action('after_setup_theme', 'tiffycooks_amp_setup');

// Add Recipe Meta Box
function tiffycooks_add_recipe_meta_boxes()
{
    add_meta_box(
        'recipe_details',
        __('Recipe Details', 'adsense-recipe-food-blog'),
        'tiffycooks_recipe_meta_box_html',
        'post',
        'normal',
        'high'
    );

    add_meta_box(
        'recipe_video',
        __('Recipe Video', 'adsense-recipe-food-blog'),
        'tiffycooks_video_meta_box_html',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'tiffycooks_add_recipe_meta_boxes');

// Recipe Meta Box HTML Callback
function tiffycooks_recipe_meta_box_html($post)
{
    // Get existing meta values with defaults
    $prep_time = get_post_meta($post->ID, '_recipe_prep_time', true) ?: '';
    $cook_time = get_post_meta($post->ID, '_recipe_cook_time', true) ?: '';
    $servings = get_post_meta($post->ID, '_recipe_servings', true) ?: '';
    $ingredients = get_post_meta($post->ID, '_recipe_ingredients', true) ?: '';
    $ingredient_groups = get_post_meta($post->ID, '_recipe_ingredient_groups', true) ?: array();
    $instructions = get_post_meta($post->ID, '_recipe_instructions', true) ?: '';
    $instruction_groups = get_post_meta($post->ID, '_recipe_instruction_groups', true) ?: array();
    $notes = get_post_meta($post->ID, '_recipe_notes', true) ?: '';

    // Security nonce
    wp_nonce_field('tiffycooks_recipe_meta_box', 'tiffycooks_recipe_meta_box_nonce');

    // Include template
    require get_template_directory() . '/inc/templates/recipe-meta-box.php';
}

// Video Meta Box HTML Callback
function tiffycooks_video_meta_box_html($post)
{
    // Get existing meta values with defaults
    $video_url = get_post_meta($post->ID, '_recipe_video_url', true) ?: '';
    $video_embed = get_post_meta($post->ID, '_recipe_video_embed', true) ?: '';
    $video_thumbnail_id = get_post_meta($post->ID, '_recipe_video_thumbnail_id', true) ?: '';
    $video_thumbnail = $video_thumbnail_id ? wp_get_attachment_image_url($video_thumbnail_id, 'medium') : '';

    // Security nonce
    wp_nonce_field('tiffycooks_video_meta_box', 'tiffycooks_video_meta_box_nonce');

    // Include template
    require get_template_directory() . '/inc/templates/video-meta-box.php';
}

// Initialize Recipe Schema
require_once get_template_directory() . '/inc/recipe-schema.php';

// Save Recipe Meta Box Data
function tiffycooks_save_recipe_meta_box($post_id)
{
    if (!isset($_POST['tiffycooks_recipe_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['tiffycooks_recipe_meta_box_nonce'], 'tiffycooks_recipe_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $fields = array(
        'recipe_prep_time' => 'intval',
        'recipe_cook_time' => 'intval',
        'recipe_servings' => 'intval',
        'recipe_ingredients' => 'sanitize_textarea_field',
        'recipe_instructions' => 'sanitize_textarea_field',
        'recipe_notes' => 'sanitize_textarea_field',
        'recipe_video_url' => 'esc_url_raw',
        'recipe_video_embed' => 'wp_kses_post',
        'recipe_video_thumbnail_id' => 'intval'
    );

    foreach ($fields as $field => $sanitize_callback) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            if ($sanitize_callback === 'intval') {
                $value = max(0, intval($value));
            } else {
                $value = call_user_func($sanitize_callback, $value);
            }
            update_post_meta($post_id, '_' . $field, $value);
        }
    }
}
add_action('save_post', 'tiffycooks_save_recipe_meta_box');

// Helper function to get recipe ingredients as array
function tiffycooks_get_ingredients($post_id)
{
    $ingredients_raw = get_post_meta($post_id, '_recipe_ingredients', true);
    $ingredients = array();

    if (!empty($ingredients_raw)) {
        $lines = explode("\n", $ingredients_raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $parts = array_map('trim', explode('|', $line));
                if (count($parts) >= 3) {
                    // Format: amount | unit | ingredient
                    $ingredients[] = array(
                        'amount' => $parts[0] . ' ' . $parts[1],
                        'ingredient' => $parts[2]
                    );
                } else if (count($parts) == 2) {
                    // Legacy format: amount | ingredient
                    $ingredients[] = array(
                        'amount' => $parts[0],
                        'ingredient' => $parts[1]
                    );
                } else {
                    // Fallback: treat entire line as ingredient
                    $ingredients[] = array(
                        'amount' => '',
                        'ingredient' => $line
                    );
                }
            }
        }
    }

    return $ingredients;
}

// Helper function to get recipe instructions as array
function tiffycooks_get_instructions($post_id)
{
    $instructions_raw = get_post_meta($post_id, '_recipe_instructions', true);
    $instructions = array();

    if (!empty($instructions_raw)) {
        $lines = explode("\n", $instructions_raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $instructions[] = $line;
            }
        }
    }

    return $instructions;
}

// Helper function to format time duration
function tiffycooks_format_time($minutes)
{
    if (empty($minutes) || !is_numeric($minutes)) {
        return '0 minutes';
    }

    if ($minutes < 60) {
        return sprintf(_n('%d minute', '%d minutes', $minutes, 'adsense-recipe-food-blog'), $minutes);
    }

    $hours = floor($minutes / 60);
    $remaining_minutes = $minutes % 60;

    if ($remaining_minutes === 0) {
        return sprintf(_n('%d hour', '%d hours', $hours, 'adsense-recipe-food-blog'), $hours);
    }

    return sprintf(
        '%s %s',
        sprintf(_n('%d hour', '%d hours', $hours, 'adsense-recipe-food-blog'), $hours),
        sprintf(_n('%d minute', '%d minutes', $remaining_minutes, 'adsense-recipe-food-blog'), $remaining_minutes)
    );
}

// Enqueue scripts and styles
function tiffycooks_amp_scripts()
{
    wp_enqueue_style('tiffycooks-amp-style', get_stylesheet_uri(), array(), '1.0.0');
}
add_action('wp_enqueue_scripts', 'tiffycooks_amp_scripts');

// Add AMP-specific meta tags
function tiffycooks_amp_add_meta_tags()
{
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        echo '<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">';
    }
}
add_action('wp_head', 'tiffycooks_amp_add_meta_tags');

// AdSense Integration
function tiffycooks_amp_adsense_script()
{
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        echo '<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>';
    }
}
add_action('wp_head', 'tiffycooks_amp_adsense_script');

// Helper function to insert ads
function tiffycooks_amp_insert_ad($position = 'default')
{
    if (!function_exists('is_amp_endpoint') || !is_amp_endpoint()) {
        return '';
    }

    $ad_client = get_option('tiffycooks_adsense_client', '');
    if (empty($ad_client)) {
        return '';
    }

    $ad_html = sprintf(
        '<amp-ad width="100vw" height="320"
            type="adsense"
            data-ad-client="%s"
            data-ad-slot="9393250588"
            data-auto-format="rspv"
            data-full-width="">
            <div overflow=""></div>
        </amp-ad>',
        esc_attr($ad_client)
    );

    return $ad_html;
}

// Include Recipe Agent
require_once get_template_directory() . '/inc/recipe-agent.php';

// Enqueue admin scripts
function tiffycooks_admin_scripts($hook)
{
    if ($hook !== 'post.php' && $hook !== 'post-new.php') {
        return;
    }

    wp_enqueue_script(
        'tiffycooks-admin',
        get_template_directory_uri() . '/js/admin.js',
        array('jquery', 'wp-util'),
        '1.0.0',
        true
    );

    wp_localize_script('tiffycooks-admin', 'tiffycooksAdmin', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('tiffycooks_generate_recipe')
    ));
}
add_action('admin_enqueue_scripts', 'tiffycooks_admin_scripts');

// Add AJAX handler for recipe generation
function tiffycooks_ajax_generate_recipe()
{
    check_ajax_referer('tiffycooks_generate_recipe', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Unauthorized');
        return;
    }

    $post_id = intval($_POST['post_id']);
    if (!$post_id) {
        wp_send_json_error('Invalid post ID');
        return;
    }

    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error('Post not found');
        return;
    }

    $agent = tiffycooks_get_recipe_agent();
    $recipe_data = $agent->generate_recipe($post->post_title, $post->post_content);

    if (is_wp_error($recipe_data)) {
        wp_send_json_error($recipe_data->get_error_message());
        return;
    }

    // Format the response data
    $response = array(
        'prep_time' => isset($recipe_data['prep_time']) ? intval($recipe_data['prep_time']) : '',
        'cook_time' => isset($recipe_data['cook_time']) ? intval($recipe_data['cook_time']) : '',
        'servings' => isset($recipe_data['servings']) ? intval($recipe_data['servings']) : '',
        'ingredients' => isset($recipe_data['ingredients']) ? (array)$recipe_data['ingredients'] : array(),
        'instructions' => isset($recipe_data['instructions']) ? (array)$recipe_data['instructions'] : array(),
        'notes' => isset($recipe_data['notes']) ? sanitize_textarea_field($recipe_data['notes']) : ''
    );

    wp_send_json_success($response);
}
add_action('wp_ajax_tiffycooks_generate_recipe', 'tiffycooks_ajax_generate_recipe');

// Include editor UI modifications
require_once get_template_directory() . '/inc/editor-ui.php';
