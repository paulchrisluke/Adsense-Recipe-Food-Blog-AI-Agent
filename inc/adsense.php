<?php

/**
 * AdSense Integration Functions
 *
 * @package TiffyCooks
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Display an AMP-compatible responsive AdSense ad
 * 
 * @param string $ad_slot The AdSense ad slot ID
 * @param string $ad_client The AdSense client ID
 * @return void
 */
function tiffycooks_display_amp_ad($ad_slot = '', $ad_client = '')
{
    // If no ad slot or client ID is provided, try to get from options
    if (empty($ad_slot)) {
        $ad_slot = get_option('tiffycooks_adsense_slot_id');
    }
    if (empty($ad_client)) {
        $ad_client = get_option('tiffycooks_adsense_client_id');
    }

    // Only display if we have both required values
    if (!empty($ad_slot) && !empty($ad_client)) {
?>
        <amp-ad width="100vw" height="320"
            type="adsense"
            data-ad-client="<?php echo esc_attr($ad_client); ?>"
            data-ad-slot="<?php echo esc_attr($ad_slot); ?>"
            data-auto-format="rspv"
            data-full-width="">
            <div overflow=""></div>
        </amp-ad>
<?php
    }
}

/**
 * Add AdSense settings to the WordPress Customizer
 * 
 * @param WP_Customize_Manager $wp_customize Theme Customizer object
 */
function tiffycooks_customize_adsense_settings($wp_customize)
{
    // Add section
    $wp_customize->add_section('tiffycooks_adsense_settings', array(
        'title'    => __('AdSense Settings', 'tiffycooks'),
        'priority' => 120,
    ));

    // Add setting for AdSense Client ID
    $wp_customize->add_setting('tiffycooks_adsense_client_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tiffycooks_adsense_client_id', array(
        'label'       => __('AdSense Client ID', 'tiffycooks'),
        'description' => __('Enter your AdSense Client ID (e.g., ca-pub-1234567890)', 'tiffycooks'),
        'section'     => 'tiffycooks_adsense_settings',
        'type'        => 'text',
    ));

    // Add setting for AdSense Slot ID
    $wp_customize->add_setting('tiffycooks_adsense_slot_id', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('tiffycooks_adsense_slot_id', array(
        'label'       => __('Default Ad Slot ID', 'tiffycooks'),
        'description' => __('Enter your default AdSense Slot ID', 'tiffycooks'),
        'section'     => 'tiffycooks_adsense_settings',
        'type'        => 'text',
    ));
}
add_action('customize_register', 'tiffycooks_customize_adsense_settings');

/**
 * Insert ads after specific paragraphs in the content
 * 
 * @param string $content The post content
 * @return string Modified content with ads
 */
function tiffycooks_insert_content_ads($content)
{
    if (!is_single() || !function_exists('tiffycooks_display_amp_ad')) {
        return $content;
    }

    // Split content into paragraphs
    $paragraphs = explode('</p>', $content);

    // Insert ad after the 3rd paragraph (if it exists)
    if (count($paragraphs) > 3) {
        ob_start();
        tiffycooks_display_amp_ad();
        $ad = ob_get_clean();

        array_splice($paragraphs, 3, 0, $ad);
    }

    // Reconnect the paragraphs and return
    return implode('</p>', $paragraphs);
}
add_filter('the_content', 'tiffycooks_insert_content_ads', 20);
