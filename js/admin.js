jQuery(document).ready(function($) {
    // Add Generate Recipe button
    $('#recipe_details .inside').prepend(
        '<div class="generate-recipe-wrapper" style="margin-bottom: 15px;">' +
        '<button type="button" class="button button-primary" id="generate-recipe">Generate Recipe from Content</button>' +
        '<span class="spinner" style="float: none; margin-top: 0;"></span>' +
        '</div>'
    );

    // Handle Generate Recipe button click
    $('#generate-recipe').on('click', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $spinner = $button.next('.spinner');
        var $wrapper = $button.closest('.generate-recipe-wrapper');

        // Remove any existing messages
        $wrapper.find('.message').remove();

        // Disable button and show spinner
        $button.prop('disabled', true);
        $spinner.addClass('is-active');

        // Make AJAX call
        wp.ajax.post('tiffycooks_generate_recipe', {
            post_id: $('#post_ID').val(),
            nonce: tiffycooksAdmin.nonce
        })
        .done(function(response) {
            // Update form fields with generated data
            if (response.prep_time) $('#recipe_prep_time').val(response.prep_time);
            if (response.cook_time) $('#recipe_cook_time').val(response.cook_time);
            if (response.servings) $('#recipe_servings').val(response.servings);
            if (response.ingredients) $('#recipe_ingredients').val(response.ingredients.join('\n'));
            if (response.instructions) $('#recipe_instructions').val(response.instructions.join('\n'));
            if (response.notes) $('#recipe_notes').val(response.notes);

            // Show success message
            $wrapper.append(
                '<div class="message notice notice-success" style="margin-top: 10px;">' +
                '<p>Recipe details have been generated successfully!</p>' +
                '</div>'
            );
        })
        .fail(function(error) {
            // Show error message
            $wrapper.append(
                '<div class="message notice notice-error" style="margin-top: 10px;">' +
                '<p>Failed to generate recipe details: ' + error + '</p>' +
                '</div>'
            );
        })
        .always(function() {
            // Re-enable button and hide spinner
            $button.prop('disabled', false);
            $spinner.removeClass('is-active');
        });
    });
}); 