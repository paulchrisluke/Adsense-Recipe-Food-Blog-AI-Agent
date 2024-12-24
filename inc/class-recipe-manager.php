<?php

/**
 * Recipe Manager Class
 *
 * @package TiffyCooks
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if WordPress core is loaded
if (!function_exists('get_post_meta')) {
    return;
}

/**
 * Recipe Manager Class
 */
class TiffyCooks_Recipe_Manager
{
    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Get instance of this class
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize if needed
        add_action('init', array($this, 'init'));
    }

    /**
     * Initialize the class
     */
    public function init()
    {
        // Add initialization code if needed
    }

    /**
     * Get recipe data for REST API
     *
     * @param array $args Arguments including post ID
     * @return array Recipe data
     */
    public function get_recipe_rest_data($args = array())
    {
        if (!function_exists('get_post_meta') || !function_exists('get_the_ID')) {
            return array();
        }

        $post_id = isset($args['id']) ? absint($args['id']) : get_the_ID();
        if (!$post_id) {
            return array();
        }

        $recipe_data = array(
            'prep_time' => get_post_meta($post_id, '_recipe_prep_time', true),
            'cook_time' => get_post_meta($post_id, '_recipe_cook_time', true),
            'servings' => get_post_meta($post_id, '_recipe_servings', true),
            'ingredients' => $this->get_ingredients($post_id),
            'instructions' => $this->get_instructions($post_id),
            'notes' => get_post_meta($post_id, '_recipe_notes', true),
            'video' => array(
                'url' => get_post_meta($post_id, '_recipe_video_url', true),
                'embed' => get_post_meta($post_id, '_recipe_video_embed', true),
            ),
        );

        return array_filter($recipe_data);
    }

    /**
     * Get recipe ingredients
     *
     * @param int $post_id Post ID
     * @return array
     */
    private function get_ingredients($post_id)
    {
        if (!function_exists('get_post_meta')) {
            return array();
        }

        $ingredients_raw = get_post_meta($post_id, '_recipe_ingredients', true);
        $ingredients = array();

        if (!empty($ingredients_raw)) {
            $lines = explode("\n", $ingredients_raw);
            foreach ($lines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $ingredients[] = $line;
                }
            }
        }

        return $ingredients;
    }

    /**
     * Get recipe instructions
     *
     * @param int $post_id Post ID
     * @return array
     */
    private function get_instructions($post_id)
    {
        if (!function_exists('get_post_meta')) {
            return array();
        }

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
}

/**
 * Get recipe manager instance
 *
 * @return TiffyCooks_Recipe_Manager|null
 */
function tiffycooks_recipe_manager()
{
    if (!function_exists('get_post_meta') || !function_exists('get_the_ID')) {
        return null;
    }
    return TiffyCooks_Recipe_Manager::get_instance();
}
