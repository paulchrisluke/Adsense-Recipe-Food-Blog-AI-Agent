<?php

/**
 * Recipe Video Meta Box Template
 * 
 * This template should only be loaded in the WordPress admin area
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Verify this is being loaded in admin context
if (!is_admin()) {
    return;
}
?>

<div class="video-meta-box">
    <p class="description">Add a video to your recipe. This will be included in the recipe schema and displayed on the recipe page.</p>

    <p>
        <label for="recipe_video_url">Video URL:</label>
        <input type="url"
            id="recipe_video_url"
            name="recipe_video_url"
            value="<?php echo esc_url($video_url); ?>"
            style="width: 100%;"
            placeholder="Enter a YouTube or Vimeo URL for your recipe video">
        <span class="description">Enter a YouTube or Vimeo URL for your recipe video.</span>
    </p>

    <p>
        <label for="recipe_video_embed">Video Embed Code:</label>
        <textarea id="recipe_video_embed"
            name="recipe_video_embed"
            rows="4"
            style="width: 100%;"
            placeholder="Alternatively, paste the video embed code. Make sure it's AMP-compatible."><?php echo esc_textarea($video_embed); ?></textarea>
        <span class="description">Alternatively, paste the video embed code. Make sure it's AMP-compatible.</span>
    </p>

    <p>
        <label>Video Thumbnail:</label>
    <div class="video-thumbnail-preview" style="margin: 10px 0;">
        <?php if ($video_thumbnail): ?>
            <img src="<?php echo esc_url($video_thumbnail); ?>" style="max-width: 150px; height: auto;">
        <?php endif; ?>
    </div>
    <input type="hidden"
        name="recipe_video_thumbnail_id"
        id="recipe_video_thumbnail_id"
        value="<?php echo esc_attr($video_thumbnail_id); ?>">
    <button type="button"
        class="button"
        id="upload_video_thumbnail_button">
        <?php echo $video_thumbnail ? 'Change thumbnail' : 'Set thumbnail'; ?>
    </button>
    <?php if ($video_thumbnail): ?>
        <button type="button"
            class="button"
            id="remove_video_thumbnail_button">
            Remove thumbnail
        </button>
    <?php endif; ?>
    <p class="description">Select an image to use as the video thumbnail. This will be used in the recipe schema.</p>
    </p>
</div>

<script>
    jQuery(document).ready(function($) {
        // Video thumbnail upload
        $('#upload_video_thumbnail_button').click(function(e) {
            e.preventDefault();

            const frame = wp.media({
                title: 'Select or Upload Video Thumbnail',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                $('#recipe_video_thumbnail_id').val(attachment.id);
                $('.video-thumbnail-preview').html(`<img src="${attachment.url}" style="max-width: 150px; height: auto;">`);
                $('#upload_video_thumbnail_button').text('Change thumbnail');

                if (!$('#remove_video_thumbnail_button').length) {
                    $('#upload_video_thumbnail_button').after(`
                    <button type="button" class="button" id="remove_video_thumbnail_button">
                        Remove thumbnail
                    </button>
                `);
                }
            });

            frame.open();
        });

        // Remove thumbnail
        $(document).on('click', '#remove_video_thumbnail_button', function() {
            $('#recipe_video_thumbnail_id').val('');
            $('.video-thumbnail-preview').empty();
            $('#upload_video_thumbnail_button').text('Set thumbnail');
            $(this).remove();
        });
    });
</script>

<style>
    .video-meta-box {
        padding: 12px;
        background: #fff;
    }

    .video-meta-box label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .video-meta-box .description {
        display: block;
        color: #666;
        font-style: italic;
        margin-top: 5px;
    }
</style>