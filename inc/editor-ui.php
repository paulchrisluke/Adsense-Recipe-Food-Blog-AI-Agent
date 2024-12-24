<?php

/**
 * Editor UI modifications for TiffyCooks Recipe Theme
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add YouTube URL meta box to post editor
 */
function tiffycooks_add_youtube_metabox()
{
    add_meta_box(
        'tiffycooks_youtube_url',
        'YouTube Recipe Video',
        'tiffycooks_render_youtube_metabox',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'tiffycooks_add_youtube_metabox');

/**
 * Render the YouTube URL meta box
 */
function tiffycooks_render_youtube_metabox($post)
{
    wp_nonce_field('tiffycooks_youtube', 'tiffycooks_youtube_nonce');
    $youtube_url = get_post_meta($post->ID, '_youtube_url', true);
?>
    <div class="tiffycooks-youtube-wrapper">
        <p>
            <label for="tiffycooks_youtube_url">YouTube URL:</label>
            <input type="url"
                id="tiffycooks_youtube_url"
                name="tiffycooks_youtube_url"
                value="<?php echo esc_attr($youtube_url); ?>"
                class="widefat"
                placeholder="https://www.youtube.com/watch?v=...">
        </p>
        <p>
            <button type="button"
                id="generate_from_youtube"
                class="button button-primary">
                Generate Post from YouTube
            </button>
            <span class="spinner" style="float: none; margin-top: 0;"></span>
        </p>
        <div id="generation_status"></div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            function extractVideoId(url) {
                if (!url || typeof url !== 'string') {
                    console.log('Invalid input:', url);
                    return null;
                }

                // Clean the URL
                url = url.trim();
                console.log('Processing URL:', url);

                try {
                    // Try parsing as URL first
                    const urlObj = new URL(url);

                    // Get video ID from query parameters
                    const videoId = urlObj.searchParams.get('v');
                    if (videoId) {
                        console.log('Found video ID from URL params:', videoId);
                        return videoId;
                    }

                    // Check for youtu.be format
                    if (urlObj.hostname === 'youtu.be') {
                        const pathVideoId = urlObj.pathname.substring(1);
                        console.log('Found video ID from youtu.be path:', pathVideoId);
                        return pathVideoId;
                    }

                    // Check for embed format
                    if (urlObj.pathname.includes('/embed/')) {
                        const embedVideoId = urlObj.pathname.split('/embed/')[1];
                        console.log('Found video ID from embed path:', embedVideoId);
                        return embedVideoId;
                    }
                } catch (e) {
                    console.log('URL parsing failed, trying regex:', e);

                    // Fallback to regex patterns if URL parsing fails
                    const patterns = [
                        /(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/,
                        /^[a-zA-Z0-9_-]{11}$/ // Direct video ID
                    ];

                    for (const pattern of patterns) {
                        const match = url.match(pattern);
                        if (match && match[1]) {
                            console.log('Found video ID from regex:', match[1]);
                            return match[1];
                        }
                    }
                }

                console.log('No video ID found');
                return null;
            }

            // Add paste event handler
            $('#tiffycooks_youtube_url').on('paste', function(e) {
                // Get clipboard data directly from the event
                let clipboardData = e.originalEvent.clipboardData || window.clipboardData;
                let pastedData = clipboardData.getData('text');

                console.log('Raw pasted data:', pastedData);

                // Prevent the default paste
                e.preventDefault();

                // Clean the URL
                pastedData = pastedData.trim();

                // Insert at cursor position
                let input = $(this);
                let startPos = this.selectionStart;
                let endPos = this.selectionEnd;
                let currentValue = input.val();
                let newValue = currentValue.substring(0, startPos) + pastedData + currentValue.substring(endPos);

                // Update input value
                input.val(newValue);

                // Validate URL
                const videoId = extractVideoId(pastedData);
                if (!videoId) {
                    $('#generation_status').html('<div class="notice notice-warning"><p>Please ensure this is a valid YouTube URL</p></div>');
                } else {
                    $('#generation_status').html('<div class="notice notice-success"><p>Valid YouTube URL detected</p></div>');
                    // Normalize the URL
                    input.val('https://www.youtube.com/watch?v=' + videoId);
                }
            });

            $('#generate_from_youtube').on('click', function() {
                var button = $(this);
                var spinner = button.next('.spinner');
                var status = $('#generation_status');
                var youtubeUrl = $('#tiffycooks_youtube_url').val();

                console.log('Processing YouTube URL:', youtubeUrl);

                if (!youtubeUrl) {
                    status.html('<div class="notice notice-error"><p>Please enter a YouTube URL</p></div>');
                    return;
                }

                const videoId = extractVideoId(youtubeUrl);
                if (!videoId) {
                    status.html('<div class="notice notice-error"><p>Could not find YouTube video ID. Please enter a valid YouTube URL (e.g., https://www.youtube.com/watch?v=xxxx)</p></div>');
                    return;
                }

                // Normalize the URL
                const normalizedUrl = 'https://www.youtube.com/watch?v=' + videoId;
                console.log('Normalized URL:', normalizedUrl);

                // Disable button and show spinner
                button.prop('disabled', true);
                spinner.addClass('is-active');
                status.html('<div class="notice notice-info"><p>Generating post content...</p></div>');

                // Make AJAX call
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'tiffycooks_generate_from_youtube',
                        nonce: $('#tiffycooks_youtube_nonce').val(),
                        youtube_url: normalizedUrl,
                        post_id: $('#post_ID').val()
                    },
                    success: function(response) {
                        console.log('AJAX Response:', response);
                        if (response.success) {
                            status.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');

                            // Update Gutenberg editor content
                            if (wp.data && wp.data.dispatch('core/editor')) {
                                const {
                                    editPost
                                } = wp.data.dispatch('core/editor');
                                editPost({
                                    title: response.data.title,
                                    content: response.data.content
                                });

                                // Update recipe meta box fields if they exist
                                if (response.data.recipe) {
                                    const recipe = response.data.recipe;
                                    $('#recipe_name').val(recipe.name || '');
                                    $('#recipe_prep_time').val(recipe.prep_time || '');
                                    $('#recipe_cook_time').val(recipe.cook_time || '');
                                    $('#recipe_total_time').val(recipe.total_time || '');
                                    $('#recipe_servings').val(recipe.servings || '');
                                    $('#recipe_ingredients').val(recipe.ingredients || '');
                                    $('#recipe_instructions').val(recipe.instructions || '');
                                    $('#recipe_description').val(recipe.description || '');
                                    $('#recipe_category').val(recipe.category || '');
                                    $('#recipe_cuisine').val(recipe.cuisine || '');
                                    $('#recipe_keywords').val(recipe.keywords || '');
                                    $('#recipe_notes').val(recipe.notes || '');
                                }
                            } else {
                                status.html('<div class="notice notice-error"><p>Could not update editor content. Please refresh the page and try again.</p></div>');
                            }
                        } else {
                            status.html('<div class="notice notice-error"><p>Error: ' + response.data + '</p></div>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error:', {
                            xhr: xhr,
                            status: status,
                            error: error
                        });
                        status.html('<div class="notice notice-error"><p>Failed to generate post content</p></div>');
                    },
                    complete: function() {
                        button.prop('disabled', false);
                        spinner.removeClass('is-active');
                    }
                });
            });
        });
    </script>
<?php
}

/**
 * Save YouTube URL when post is saved
 */
function tiffycooks_save_youtube_url($post_id)
{
    if (
        !isset($_POST['tiffycooks_youtube_nonce']) ||
        !wp_verify_nonce($_POST['tiffycooks_youtube_nonce'], 'tiffycooks_youtube')
    ) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['tiffycooks_youtube_url'])) {
        update_post_meta(
            $post_id,
            '_youtube_url',
            sanitize_text_field($_POST['tiffycooks_youtube_url'])
        );
    }
}
add_action('save_post', 'tiffycooks_save_youtube_url');

/**
 * AJAX handler for generating post from YouTube
 */
function tiffycooks_generate_from_youtube_ajax()
{
    $agent = tiffycooks_get_recipe_agent();
    $agent->handle_youtube_post_generation();
}
add_action('wp_ajax_tiffycooks_generate_from_youtube', 'tiffycooks_generate_from_youtube_ajax');
