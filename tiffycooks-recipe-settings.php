<?php

/**
 * Plugin Name: TiffyCooks Recipe Settings
 * Plugin URI: https://tiffycooks.com
 * Description: Handles recipe generation settings and API key configuration
 * Version: 1.0
 * Author: TiffyCooks
 * Author URI: https://tiffycooks.com
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TIFFYCOOKS_SETTINGS_VERSION', '1.0.0');
define('TIFFYCOOKS_SETTINGS_PATH', plugin_dir_path(__FILE__));

// Initialize the plugin
function tiffycooks_settings_init()
{
    // Register settings
    register_setting('tiffycooks_settings', 'tiffycooks_openai_api_key');

    // Add settings page
    add_options_page(
        'TiffyCooks Settings',
        'TiffyCooks Settings',
        'manage_options',
        'tiffycooks-settings',
        'tiffycooks_settings_page'
    );

    // Set initial API key from wp-config if it exists and option is not set
    $current_key = get_option('tiffycooks_openai_api_key', '');
    if (empty($current_key) && defined('OPENAI_API_KEY')) {
        update_option('tiffycooks_openai_api_key', OPENAI_API_KEY);
    }
}
add_action('admin_init', 'tiffycooks_settings_init');

// Settings page HTML
function tiffycooks_settings_page()
{
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings if form is submitted
    if (isset($_POST['tiffycooks_openai_api_key'])) {
        $api_key = sanitize_text_field($_POST['tiffycooks_openai_api_key']);
        update_option('tiffycooks_openai_api_key', $api_key);
        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }

    // Get current API key
    $api_key = get_option('tiffycooks_openai_api_key', '');
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <?php settings_fields('tiffycooks_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="tiffycooks_openai_api_key">OpenAI API Key</label>
                    </th>
                    <td>
                        <input type="text"
                            id="tiffycooks_openai_api_key"
                            name="tiffycooks_openai_api_key"
                            value="<?php echo esc_attr($api_key); ?>"
                            class="regular-text">
                        <p class="description">
                            Enter your OpenAI API key here. This is required for recipe generation.
                            <?php if (defined('OPENAI_API_KEY')): ?>
                                <br><strong>Note:</strong> A default API key is configured in wp-config.php
                            <?php endif; ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Settings'); ?>
        </form>
    </div>
<?php
}

// Add settings link on plugin page
function tiffycooks_settings_link($links)
{
    $settings_link = '<a href="options-general.php?page=tiffycooks-settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
$plugin_basename = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin_basename", 'tiffycooks_settings_link');
