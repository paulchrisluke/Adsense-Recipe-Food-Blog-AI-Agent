<?php

/**
 * Recipe Video Meta Box Template
 *
 * @package TiffyCooks
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Check if WordPress core is loaded
if (!function_exists('wp_verify_nonce')) {
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}

if (!function_exists('esc_html_e')) {
    require_once(ABSPATH . 'wp-includes/formatting.php');
}

if (!function_exists('esc_attr')) {
    require_once(ABSPATH . 'wp-includes/formatting.php');
}

// Verify this is being loaded in admin context
if (!is_admin()) {
    return;
}
?>

<div class="video-meta-box">
    <p>
        <label for="recipe_video_url"><?php esc_html_e('Video URL:', 'adsense-recipe-food-blog'); ?></label>
        <input type="url" id="recipe_video_url" name="recipe_video_url" value="<?php echo esc_url($video_url); ?>" class="large-text" />
        <span class="description"><?php esc_html_e('Enter the URL of your recipe video (YouTube, Vimeo, etc.).', 'adsense-recipe-food-blog'); ?></span>
    </p>

    <p>
        <label for="recipe_video_embed"><?php esc_html_e('Video Embed Code:', 'adsense-recipe-food-blog'); ?></label>
        <textarea id="recipe_video_embed" name="recipe_video_embed" rows="4" class="large-text"><?php echo esc_textarea($video_embed); ?></textarea>
        <span class="description"><?php esc_html_e('Or paste the embed code from your video platform.', 'adsense-recipe-food-blog'); ?></span>
    </p>

    <div class="video-thumbnail">
        <p>
            <label><?php esc_html_e('Video Thumbnail:', 'adsense-recipe-food-blog'); ?></label>
        <div class="thumbnail-preview">
            <?php if ($video_thumbnail): ?>
                <img src="<?php echo esc_url($video_thumbnail); ?>" alt="<?php esc_attr_e('Video thumbnail', 'adsense-recipe-food-blog'); ?>" />
            <?php endif; ?>
        </div>
        <input type="hidden" name="recipe_video_thumbnail_id" id="recipe_video_thumbnail_id" value="<?php echo esc_attr($video_thumbnail_id); ?>" />
        <button type="button" class="button upload-thumbnail"><?php esc_html_e('Set thumbnail', 'adsense-recipe-food-blog'); ?></button>
        <?php if ($video_thumbnail): ?>
            <button type="button" class="button remove-thumbnail"><?php esc_html_e('Remove thumbnail', 'adsense-recipe-food-blog'); ?></button>
        <?php endif; ?>
        </p>
    </div>
</div>

<style>
    .video-meta-box label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .video-meta-box .description {
        display: block;
        font-style: italic;
        color: #666;
        margin-top: 5px;
    }

    .video-thumbnail .thumbnail-preview {
        margin: 10px 0;
        max-width: 200px;
    }

    .video-thumbnail .thumbnail-preview img {
        max-width: 100%;
        height: auto;
    }

    .video-thumbnail .button {
        margin-right: 10px;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        // Check if wp.media is available
        if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
            var frame;

            $('.upload-thumbnail').on('click', function(e) {
                e.preventDefault();

                if (frame) {
                    frame.open();
                    return;
                }

                frame = wp.media({
                    title: '<?php esc_html_e('Select or Upload Video Thumbnail', 'adsense-recipe-food-blog'); ?>',
                    button: {
                        text: '<?php esc_html_e('Use this image', 'adsense-recipe-food-blog'); ?>'
                    },
                    multiple: false
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('#recipe_video_thumbnail_id').val(attachment.id);
                    $('.thumbnail-preview').html('<img src="' + attachment.url + '" alt="" />');
                    $('.remove-thumbnail').show();
                });

                frame.open();
            });

            $('.remove-thumbnail').on('click', function(e) {
                e.preventDefault();
                $('#recipe_video_thumbnail_id').val('');
                $('.thumbnail-preview').empty();
                $(this).hide();
            });
        }
    });
</script>