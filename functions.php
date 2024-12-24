<?php

/**
 * Theme functions and definitions
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define theme constants
define('TIFFYCOOKS_VERSION', '1.0.0');
define('TIFFYCOOKS_DIR', get_template_directory());
define('TIFFYCOOKS_URI', get_template_directory_uri());

// Set up theme defaults and register support for various WordPress features
function tiffycooks_setup()
{
    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');

    // Register nav menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'adsense-recipe-food-blog'),
    ));
}
add_action('after_setup_theme', 'tiffycooks_setup');

// Include required files safely
$required_files = array(
    'class-recipe-manager.php',
    'schema.php',
    'adsense.php'
);

foreach ($required_files as $file) {
    $file_path = TIFFYCOOKS_DIR . '/inc/' . $file;
    if (file_exists($file_path)) {
        require_once $file_path;
    }
}

// Initialize Recipe Manager
function tiffycooks_init()
{
    if (function_exists('tiffycooks_recipe_manager')) {
        tiffycooks_recipe_manager();
    }
}
add_action('init', 'tiffycooks_init', 5);

// Helper function for formatting time
function tiffycooks_format_time($minutes)
{
    if (!$minutes) return '';
    $hours = floor($minutes / 60);
    $mins = $minutes % 60;
    $output = '';
    if ($hours > 0) {
        $output .= $hours . ' ' . _n('hour', 'hours', $hours, 'adsense-recipe-food-blog') . ' ';
    }
    if ($mins > 0) {
        $output .= $mins . ' ' . _n('minute', 'minutes', $mins, 'adsense-recipe-food-blog');
    }
    return trim($output);
}

// Enqueue scripts and styles
function tiffycooks_scripts()
{
    // All styles are included inline in header.php for AMP compatibility
    // No additional scripts needed as we're AMP-only
}
add_action('wp_enqueue_scripts', 'tiffycooks_scripts');

// Convert standard img tags to amp-img
function tiffycooks_convert_images($content)
{
    if (!is_string($content)) {
        return $content;
    }

    $content = preg_replace(
        '/<img([^>]+?)>/i',
        '<amp-img$1 layout="responsive"></amp-img>',
        $content
    );
    return $content;
}
add_filter('the_content', 'tiffycooks_convert_images', 20);

// Remove all other scripts and styles for AMP compliance
function tiffycooks_remove_scripts()
{
    if (!is_admin()) {
        global $wp_scripts, $wp_styles;
        if ($wp_scripts instanceof WP_Scripts) {
            $wp_scripts->queue = array();
        }
        if ($wp_styles instanceof WP_Styles) {
            $wp_styles->queue = array();
        }
    }
}
add_action('wp_enqueue_scripts', 'tiffycooks_remove_scripts', 999);

// Remove unnecessary header items
function tiffycooks_clean_header()
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
}
add_action('init', 'tiffycooks_clean_header');

// Disable admin bar for AMP compatibility
add_filter('show_admin_bar', '__return_false');

// Ad insertion function
function tiffycooks_amp_insert_ad($position = 'default', $type = 'square')
{
    $ad_sizes = array(
        'responsive' => array(
            'width' => '100vw',
            'height' => '320',
            'layout' => 'responsive',
            'class' => 'ad-responsive'
        ),
        'fixed-height' => array(
            'width' => 'auto',
            'height' => '250',
            'layout' => 'fixed-height',
            'class' => 'ad-fixed-height'
        ),
        'fill' => array(
            'width' => '100%',
            'height' => '100%',
            'layout' => 'fill',
            'class' => 'ad-fill'
        )
    );

    $size = isset($ad_sizes[$type]) ? $ad_sizes[$type] : $ad_sizes['responsive'];

    // Add custom classes based on position
    $position_classes = array(
        'top' => 'ad-top',
        'sidebar' => 'ad-sidebar',
        'content' => 'ad-content',
        'recipe-start' => 'ad-recipe-start',
        'between-sections' => 'ad-between-sections',
        'bottom' => 'ad-bottom'
    );

    $class = isset($position_classes[$position]) ? $position_classes[$position] : '';

    $ad_html = sprintf(
        '<div class="ad-test-container %s %s">
            <amp-ad 
                type="fake"
                width="%s"
                height="%s"
                layout="%s"
                data-use-a4a="true"
                data-loading-strategy="prefer-viewability-over-views"
                data-enable-refresh="30">
                <div placeholder>
                    <div style="background: #ffebeb; color: #ff4040; padding: 20px; text-align: center; border: 2px dashed #ff8080;">
                        %s TEST AD
                        <div class="ad-size">%s x %s</div>
                    </div>
                </div>
                <div fallback>
                    <div style="background: #fff5f5; color: #ff4040; padding: 10px; text-align: center;">
                        Ad space unavailable
                    </div>
                </div>
            </amp-ad>
        </div>',
        esc_attr($class),
        esc_attr($size['class']),
        esc_attr($size['width']),
        esc_attr($size['height']),
        esc_attr($size['layout']),
        strtoupper($position),
        $size['width'],
        $size['height']
    );

    return $ad_html;
}

// Force AMP version
function tiffycooks_template_redirect()
{
    if (!is_admin()) {
        header('Content-Type: text/html; charset=utf-8');
        header('Cache-Control: public, max-age=3600');
    }
}
add_action('template_redirect', 'tiffycooks_template_redirect');

// Recipe helper functions
function tiffycooks_get_ingredients($post_id)
{
    $ingredients_raw = get_post_meta($post_id, '_recipe_ingredients', true);
    $ingredients = array();

    if (!empty($ingredients_raw)) {
        $lines = explode("\n", $ingredients_raw);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                // Check if the line contains a separator
                if (strpos($line, '|') !== false) {
                    list($amount, $ingredient) = array_map('trim', explode('|', $line, 2));
                } else {
                    $amount = '';
                    $ingredient = $line;
                }
                $ingredients[] = array(
                    'amount' => $amount,
                    'ingredient' => $ingredient
                );
            }
        }
    }

    return $ingredients;
}

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
