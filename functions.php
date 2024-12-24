<?php

/**
 * TiffyCooks AMP Theme functions and definitions
 *
 * @package TiffyCooks
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Handle debug constants if not already defined
if (!defined('WP_DEBUG')) {
    define('WP_DEBUG', false);
}

if (!defined('WP_DEBUG_LOG')) {
    define('WP_DEBUG_LOG', false);
}

if (!defined('WP_DEBUG_DISPLAY')) {
    define('WP_DEBUG_DISPLAY', false);
}

// Include required files
require_once get_template_directory() . '/inc/class-recipe-manager.php';
require_once get_template_directory() . '/inc/adsense.php';
require_once get_template_directory() . '/inc/schema.php';

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
    add_theme_support('amp', array(
        'paired' => false, // Force AMP-only mode
        'templates_supported' => array(
            'is_singular' => true,
            'is_front_page' => true,
            'is_home' => true,
            'is_archive' => true
        ),
    ));

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
    // Check if our nonce is set
    if (!isset($_POST['tiffycooks_recipe_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['tiffycooks_recipe_meta_box_nonce'], 'tiffycooks_recipe_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save recipe details
    $fields = array(
        'recipe_prep_time' => 'intval',
        'recipe_cook_time' => 'intval',
        'recipe_servings' => 'intval',
        'recipe_ingredients' => 'sanitize_textarea_field',
        'recipe_instructions' => 'sanitize_textarea_field',
        'recipe_notes' => 'sanitize_textarea_field',
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

// Save Video Meta Box Data
function tiffycooks_save_video_meta_box($post_id)
{
    // Check if our nonce is set
    if (!isset($_POST['tiffycooks_video_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid
    if (!wp_verify_nonce($_POST['tiffycooks_video_meta_box_nonce'], 'tiffycooks_video_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save video details
    $fields = array(
        'recipe_video_url' => 'esc_url_raw',
        'recipe_video_embed' => 'wp_kses_post',
        'recipe_video_thumbnail_id' => 'intval',
    );

    foreach ($fields as $field => $sanitize_callback) {
        if (isset($_POST[$field])) {
            $value = $_POST[$field];
            $value = call_user_func($sanitize_callback, $value);
            update_post_meta($post_id, '_' . $field, $value);
        }
    }
}
add_action('save_post', 'tiffycooks_save_video_meta_box');

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
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        // Remove all non-AMP scripts
        wp_dequeue_script('jquery');
        wp_dequeue_script('jquery-migrate');

        // Add AMP-specific styles inline
        $custom_css = '
            /* Add your AMP-specific styles here */
            .recipe-content {
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
            }
            .recipe-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                margin-bottom: 30px;
            }
            .recipe-instructions {
                counter-reset: instruction;
                list-style-type: none;
                padding: 0;
            }
            .recipe-instructions li {
                counter-increment: instruction;
                margin-bottom: 15px;
                padding-left: 40px;
                position: relative;
            }
            .recipe-instructions li:before {
                content: counter(instruction);
                position: absolute;
                left: 0;
                top: 0;
                width: 30px;
                height: 30px;
                background: #f0f0f0;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
            }
            amp-ad {
                margin: 2em 0;
                min-height: 250px;
            }
        ';
        wp_add_inline_style('amp-custom', $custom_css);
    }
}
add_action('wp_enqueue_scripts', 'tiffycooks_amp_scripts', 20);

// Add AMP-specific meta tags
function tiffycooks_amp_add_meta_tags()
{
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        echo '<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">';
        echo '<meta name="amp-google-client-id-api" content="googleanalytics">';
        echo '<script async custom-element="amp-analytics" src="https://cdn.ampproject.org/v0/amp-analytics-0.1.js"></script>';
    }
}
add_action('wp_head', 'tiffycooks_amp_add_meta_tags', 1);

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

/**
 * Add Google Analytics tracking for AMP pages
 */
function tiffycooks_amp_analytics()
{
    if (function_exists('is_amp_endpoint') && is_amp_endpoint()) {
        $analytics_id = get_option('tiffycooks_analytics_id');
        if (!empty($analytics_id)) {
?>
            <amp-analytics type="googleanalytics">
                <script type="application/json">
                    {
                        "vars": {
                            "account": "<?php echo esc_js($analytics_id); ?>"
                        },
                        "triggers": {
                            "trackPageview": {
                                "on": "visible",
                                "request": "pageview"
                            }
                        }
                    }
                </script>
            </amp-analytics>
<?php
        }
    }
}
add_action('wp_footer', 'tiffycooks_amp_analytics');

/**
 * Add settings to the WordPress Customizer
 */
function tiffycooks_customize_register($wp_customize)
{
    // Add Analytics section
    $wp_customize->add_section('tiffycooks_analytics_settings', array(
        'title'    => __('Analytics Settings', 'adsense-recipe-food-blog'),
        'priority' => 120,
    ));

    // Add setting for Google Analytics ID
    $wp_customize->add_setting('tiffycooks_analytics_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tiffycooks_analytics_id', array(
        'label'       => __('Google Analytics ID', 'adsense-recipe-food-blog'),
        'description' => __('Enter your Google Analytics ID (e.g., UA-XXXXX-Y)', 'adsense-recipe-food-blog'),
        'section'     => 'tiffycooks_analytics_settings',
        'type'        => 'text',
    ));
}
add_action('customize_register', 'tiffycooks_customize_register');
