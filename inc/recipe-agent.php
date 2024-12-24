<?php

/**
 * Recipe AI Agent
 * 
 * This file contains all the AI-related functionality for recipe generation,
 * including prompts, tools, and API interactions.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Immediate debug output
function debug_to_console($data)
{
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log(is_string($data) ? $data : json_encode($data));
    }
}

class TiffyCooks_Recipe_Agent
{
    private $api_key;
    private $youtube_api_key;
    private $tools;
    private $system_prompt;
    private $user_prompt_template;

    public function __construct()
    {
        // Get API keys from wp-config.php constants
        $this->api_key = defined('TIFFYCOOKS_OPENAI_API_KEY') ? TIFFYCOOKS_OPENAI_API_KEY : '';
        $this->youtube_api_key = defined('TIFFYCOOKS_YOUTUBE_API_KEY') ? TIFFYCOOKS_YOUTUBE_API_KEY : '';

        if (empty($this->api_key)) {
            add_action('admin_notices', array($this, 'show_api_key_notice'));
        }

        debug_to_console('API keys set. OpenAI Length: ' . strlen($this->api_key) . ', YouTube key length: ' . strlen($this->youtube_api_key));

        $this->setup_tools();
        $this->setup_prompts();
    }

    /**
     * Show admin notice for missing API key
     */
    public function show_api_key_notice()
    {
?>
        <div class="notice notice-error">
            <p>
                <?php _e('OpenAI API key not configured. Please set it in your wp-config.php file using TIFFYCOOKS_OPENAI_API_KEY constant.', 'adsense-recipe-food-blog'); ?>
            </p>
        </div>
<?php
    }

    /**
     * Set up the tools available to the AI agent
     */
    private function setup_tools()
    {
        $this->tools = array(
            array(
                'type' => 'time_estimator',
                'description' => 'Estimates preparation and cooking times based on recipe complexity',
                'function' => array($this, 'estimate_recipe_times')
            ),
            array(
                'type' => 'ingredient_parser',
                'description' => 'Converts ingredient text into structured format',
                'function' => array($this, 'parse_ingredients')
            ),
            array(
                'type' => 'instruction_parser',
                'description' => 'Formats cooking instructions into clear steps',
                'function' => array($this, 'parse_instructions')
            ),
            array(
                'type' => 'serving_calculator',
                'description' => 'Calculates serving sizes based on ingredients',
                'function' => array($this, 'calculate_servings')
            )
        );
    }

    /**
     * Set up the AI prompts
     */
    private function setup_prompts()
    {
        // System prompt defines the AI's role and capabilities
        $this->system_prompt = <<<EOT
You are a professional recipe extraction assistant specialized in AMP-compliant recipe schema generation.
Your primary task is to extract recipe information and return it in a specific JSON format.

RESPONSE FORMAT REQUIREMENTS:
You must respond with a valid JSON object containing these exact fields:
{
    "name": "string - Recipe title",
    "prep_time": "number - Minutes required for preparation",
    "cook_time": "number - Minutes required for cooking",
    "total_time": "number - Total minutes (prep + cook)",
    "servings": "number - Number of servings",
    "ingredients": ["array of strings - Each ingredient with exact format: 'amount | unit | ingredient'"],
    "instructions": ["array of strings - Step by step instructions"],
    "description": "string - Brief recipe description",
    "category": "string - Recipe category",
    "cuisine": "string - Cuisine type",
    "keywords": "string - Comma-separated keywords",
    "notes": "string - Optional tips or variations"
}
EOT;

        // User prompt template for extracting recipe details
        $this->user_prompt_template = <<<EOT
Extract recipe details from this blog post titled '{title}' and format as a valid JSON object.
Blog post content:
{content}
EOT;
    }

    /**
     * Generate recipe data from blog post content
     */
    public function generate_recipe($title, $content)
    {
        if (empty($this->api_key)) {
            return new WP_Error('api_key_missing', 'OpenAI API key is not configured');
        }

        try {
            $user_prompt = str_replace(
                array('{title}', '{content}'),
                array($title, wp_strip_all_tags($content)),
                $this->user_prompt_template
            );

            $request_body = array(
                'model' => 'gpt-4',
                'messages' => array(
                    array(
                        'role' => 'system',
                        'content' => $this->system_prompt
                    ),
                    array(
                        'role' => 'user',
                        'content' => $user_prompt
                    )
                ),
                'temperature' => 0.7,
                'max_tokens' => 2000
            );

            $args = array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . trim($this->api_key),
                    'Content-Type' => 'application/json',
                ),
                'body' => json_encode($request_body),
                'timeout' => 60
            );

            $response = wp_remote_post('https://api.openai.com/v1/chat/completions', $args);

            if (is_wp_error($response)) {
                throw new Exception('Failed to connect to OpenAI API: ' . $response->get_error_message());
            }

            $response_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);

            if ($response_code !== 200) {
                throw new Exception('OpenAI API Error: ' . $response_body);
            }

            $response_data = json_decode($response_body, true);
            if (empty($response_data['choices'][0]['message']['content'])) {
                throw new Exception('Empty response from OpenAI API');
            }

            $recipe_data = json_decode($response_data['choices'][0]['message']['content'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON in recipe response');
            }

            return $recipe_data;
        } catch (Exception $e) {
            return new WP_Error('generation_error', $e->getMessage());
        }
    }

    /**
     * Generate recipe from YouTube video
     */
    public function generate_recipe_from_youtube($url)
    {
        if (empty($this->youtube_api_key)) {
            return new WP_Error('api_key_missing', 'YouTube API key is not configured');
        }

        try {
            $video_id = $this->get_youtube_video_id($url);
            if (empty($video_id)) {
                return new WP_Error('invalid_url', 'Invalid YouTube URL');
            }

            $video_url = add_query_arg(array(
                'part' => 'snippet',
                'id' => $video_id,
                'key' => $this->youtube_api_key
            ), 'https://www.googleapis.com/youtube/v3/videos');

            $response = wp_remote_get($video_url);
            if (is_wp_error($response)) {
                throw new Exception('Failed to fetch video data: ' . $response->get_error_message());
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (empty($body['items'][0]['snippet'])) {
                throw new Exception('Video not found');
            }

            $snippet = $body['items'][0]['snippet'];
            return $this->generate_recipe($snippet['title'], $snippet['description']);
        } catch (Exception $e) {
            return new WP_Error('youtube_error', $e->getMessage());
        }
    }

    /**
     * Extract YouTube video ID from URL
     */
    private function get_youtube_video_id($url)
    {
        if (preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return $matches[1];
        }
        return '';
    }
}

// Initialize the Recipe Agent
function tiffycooks_get_recipe_agent()
{
    static $agent = null;
    if ($agent === null) {
        $agent = new TiffyCooks_Recipe_Agent();
    }
    return $agent;
}

// AJAX handler for recipe generation
function tiffycooks_generate_recipe_ajax()
{
    check_ajax_referer('tiffycooks_generate_recipe', 'nonce');

    $post_id = intval($_POST['post_id']);
    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error('Permission denied');
    }

    $post = get_post($post_id);
    if (!$post) {
        wp_send_json_error('Post not found');
    }

    $agent = tiffycooks_get_recipe_agent();
    $recipe_data = $agent->generate_recipe($post->post_title, $post->post_content);

    if (is_wp_error($recipe_data)) {
        wp_send_json_error($recipe_data->get_error_message());
    }

    wp_send_json_success($recipe_data);
}
add_action('wp_ajax_tiffycooks_generate_recipe', 'tiffycooks_generate_recipe_ajax');
