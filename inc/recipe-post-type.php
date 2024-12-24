<?php

/**
 * Recipe Post Type and Enhanced Functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class TiffyCooks_Recipe_Manager
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
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('add_meta_boxes', array($this, 'add_recipe_meta_boxes'));
        add_action('save_post_recipe', array($this, 'save_recipe_meta'));
        add_action('rest_api_init', array($this, 'register_rest_fields'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('template_include', array($this, 'recipe_templates'));
    }

    public function register_post_type()
    {
        $labels = array(
            'name'               => _x('Recipes', 'post type general name', 'adsense-recipe-food-blog'),
            'singular_name'      => _x('Recipe', 'post type singular name', 'adsense-recipe-food-blog'),
            'menu_name'          => _x('Recipes', 'admin menu', 'adsense-recipe-food-blog'),
            'add_new'           => _x('Add New', 'recipe', 'adsense-recipe-food-blog'),
            'add_new_item'      => __('Add New Recipe', 'adsense-recipe-food-blog'),
            'edit_item'         => __('Edit Recipe', 'adsense-recipe-food-blog'),
            'new_item'          => __('New Recipe', 'adsense-recipe-food-blog'),
            'view_item'         => __('View Recipe', 'adsense-recipe-food-blog'),
            'search_items'      => __('Search Recipes', 'adsense-recipe-food-blog'),
            'not_found'         => __('No recipes found', 'adsense-recipe-food-blog'),
            'not_found_in_trash' => __('No recipes found in Trash', 'adsense-recipe-food-blog'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'           => true,
            'show_in_menu'      => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'recipe'),
            'capability_type'    => 'post',
            'has_archive'       => true,
            'hierarchical'      => false,
            'menu_position'     => 5,
            'menu_icon'         => 'dashicons-food',
            'supports'          => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
            'show_in_rest'      => true,
        );

        register_post_type('recipe', $args);
    }

    public function register_taxonomies()
    {
        // Recipe Categories
        $category_labels = array(
            'name'              => _x('Recipe Categories', 'taxonomy general name', 'adsense-recipe-food-blog'),
            'singular_name'     => _x('Recipe Category', 'taxonomy singular name', 'adsense-recipe-food-blog'),
            'menu_name'         => __('Recipe Categories', 'adsense-recipe-food-blog'),
        );

        register_taxonomy('recipe_category', 'recipe', array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'recipe-category'),
            'show_in_rest'      => true,
        ));

        // Recipe Tags
        $tag_labels = array(
            'name'              => _x('Recipe Tags', 'taxonomy general name', 'adsense-recipe-food-blog'),
            'singular_name'     => _x('Recipe Tag', 'taxonomy singular name', 'adsense-recipe-food-blog'),
            'menu_name'         => __('Recipe Tags', 'adsense-recipe-food-blog'),
        );

        register_taxonomy('recipe_tag', 'recipe', array(
            'hierarchical'      => false,
            'labels'            => $tag_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'recipe-tag'),
            'show_in_rest'      => true,
        ));
    }

    public function add_recipe_meta_boxes()
    {
        add_meta_box(
            'recipe_details',
            __('Recipe Details', 'adsense-recipe-food-blog'),
            array($this, 'render_recipe_meta_box'),
            'recipe',
            'normal',
            'high'
        );

        add_meta_box(
            'recipe_video',
            __('Recipe Video', 'adsense-recipe-food-blog'),
            array($this, 'render_video_meta_box'),
            'recipe',
            'normal',
            'high'
        );
    }

    public function render_recipe_meta_box($post)
    {
        wp_nonce_field('recipe_meta_box', 'recipe_meta_box_nonce');

        // Get existing values
        $prep_time = get_post_meta($post->ID, '_recipe_prep_time', true);
        $cook_time = get_post_meta($post->ID, '_recipe_cook_time', true);
        $servings = get_post_meta($post->ID, '_recipe_servings', true);
        $ingredients = get_post_meta($post->ID, '_recipe_ingredients', true);
        $instructions = get_post_meta($post->ID, '_recipe_instructions', true);
        $notes = get_post_meta($post->ID, '_recipe_notes', true);
        $ingredient_groups = get_post_meta($post->ID, '_recipe_ingredient_groups', true) ?: array();
        $instruction_groups = get_post_meta($post->ID, '_recipe_instruction_groups', true) ?: array();

        // Include template
        require dirname(__FILE__) . '/templates/recipe-meta-box.php';
    }

    public function render_video_meta_box($post)
    {
        wp_nonce_field('recipe_video_meta_box', 'recipe_video_meta_box_nonce');

        $video_url = get_post_meta($post->ID, '_recipe_video_url', true);
        $video_embed = get_post_meta($post->ID, '_recipe_video_embed', true);
        $video_thumbnail = get_post_meta($post->ID, '_recipe_video_thumbnail', true);

        // Include template
        require dirname(__FILE__) . '/templates/video-meta-box.php';
    }

    public function save_recipe_meta($post_id)
    {
        if (
            !isset($_POST['recipe_meta_box_nonce']) ||
            !wp_verify_nonce($_POST['recipe_meta_box_nonce'], 'recipe_meta_box')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save recipe details
        $fields = array(
            'recipe_prep_time',
            'recipe_cook_time',
            'recipe_servings',
            'recipe_ingredients',
            'recipe_instructions',
            'recipe_notes',
            'recipe_ingredient_groups',
            'recipe_instruction_groups'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Save video details
        if (isset($_POST['recipe_video_url'])) {
            update_post_meta($post_id, '_recipe_video_url', esc_url_raw($_POST['recipe_video_url']));
        }
        if (isset($_POST['recipe_video_embed'])) {
            update_post_meta($post_id, '_recipe_video_embed', wp_kses_post($_POST['recipe_video_embed']));
        }
        if (isset($_POST['recipe_video_thumbnail'])) {
            update_post_meta($post_id, '_recipe_video_thumbnail', absint($_POST['recipe_video_thumbnail']));
        }
    }

    public function register_rest_fields()
    {
        register_rest_field('recipe', 'recipe_details', array(
            'get_callback' => array($this, 'get_recipe_rest_data'),
            'schema' => array(
                'description' => __('Recipe details including ingredients, instructions, and metadata.', 'adsense-recipe-food-blog'),
                'type'        => 'object'
            )
        ));
    }

    public function get_recipe_rest_data($post)
    {
        $post_id = $post['id'];
        return array(
            'prep_time' => get_post_meta($post_id, '_recipe_prep_time', true),
            'cook_time' => get_post_meta($post_id, '_recipe_cook_time', true),
            'servings' => get_post_meta($post_id, '_recipe_servings', true),
            'ingredients' => $this->get_ingredients($post_id),
            'instructions' => $this->get_instructions($post_id),
            'notes' => get_post_meta($post_id, '_recipe_notes', true),
            'video' => array(
                'url' => get_post_meta($post_id, '_recipe_video_url', true),
                'embed' => get_post_meta($post_id, '_recipe_video_embed', true),
                'thumbnail' => get_post_meta($post_id, '_recipe_video_thumbnail', true),
            )
        );
    }

    public function recipe_templates($template)
    {
        if (is_singular('recipe')) {
            $new_template = locate_template(array('single-recipe.php'));
            if (!empty($new_template)) {
                return $new_template;
            }
        }
        return $template;
    }

    public function get_ingredients($post_id)
    {
        $ingredients_raw = get_post_meta($post_id, '_recipe_ingredients', true);
        $groups = get_post_meta($post_id, '_recipe_ingredient_groups', true) ?: array();

        $ingredients = array();
        if (!empty($ingredients_raw)) {
            $lines = explode("\n", $ingredients_raw);
            foreach ($lines as $line) {
                $parts = array_map('trim', explode('|', $line));
                if (count($parts) === 2) {
                    $ingredients[] = array(
                        'amount' => $parts[0],
                        'ingredient' => $parts[1],
                        'group' => isset($parts[2]) ? $parts[2] : ''
                    );
                }
            }
        }
        return array(
            'groups' => $groups,
            'items' => $ingredients
        );
    }

    public function get_instructions($post_id)
    {
        $instructions_raw = get_post_meta($post_id, '_recipe_instructions', true);
        $groups = get_post_meta($post_id, '_recipe_instruction_groups', true) ?: array();

        $instructions = array();
        if (!empty($instructions_raw)) {
            $lines = explode("\n", $instructions_raw);
            foreach ($lines as $line) {
                $parts = array_map('trim', explode('|', $line));
                $instructions[] = array(
                    'text' => $parts[0],
                    'group' => isset($parts[1]) ? $parts[1] : '',
                    'image' => isset($parts[2]) ? absint($parts[2]) : 0
                );
            }
        }
        return array(
            'groups' => $groups,
            'steps' => $instructions
        );
    }
}

// Initialize the recipe manager
function tiffycooks_recipe_manager()
{
    return TiffyCooks_Recipe_Manager::get_instance();
}
add_action('plugins_loaded', 'tiffycooks_recipe_manager');
