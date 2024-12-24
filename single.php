<?php

/**
 * The template for displaying single posts
 *
 * @package TiffyCooks
 */

get_header(); ?>

<main id="main" class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php if (has_post_thumbnail()) : ?>
                    <div class="post-thumbnail">
                        <?php if (function_exists('is_amp_endpoint') && is_amp_endpoint()) : ?>
                            <amp-img src="<?php echo esc_url(get_the_post_thumbnail_url(null, 'large')); ?>"
                                width="800"
                                height="600"
                                layout="responsive"
                                alt="<?php echo esc_attr(get_the_title()); ?>">
                            </amp-img>
                        <?php else : ?>
                            <?php the_post_thumbnail('large'); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <h1 class="entry-title"><?php the_title(); ?></h1>

                <div class="entry-meta">
                    <span class="posted-on"><?php echo get_the_date(); ?></span>
                    <span class="byline"> by <?php the_author(); ?></span>
                </div>
            </header>

            <div class="entry-content">
                <?php
                // Display the main content first
                the_content();

                // Insert ad after the introduction
                echo tiffycooks_amp_insert_ad('after-intro');

                // Display recipe details if available
                $prep_time = get_post_meta(get_the_ID(), '_recipe_prep_time', true);
                $cook_time = get_post_meta(get_the_ID(), '_recipe_cook_time', true);
                $servings = get_post_meta(get_the_ID(), '_recipe_servings', true);

                if ($prep_time || $cook_time || $servings) : ?>
                    <div class="recipe-meta">
                        <?php if ($prep_time) : ?>
                            <div class="prep-time">
                                <strong>Prep Time:</strong> <?php echo esc_html(tiffycooks_format_time($prep_time)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($cook_time) : ?>
                            <div class="cook-time">
                                <strong>Cook Time:</strong> <?php echo esc_html(tiffycooks_format_time($cook_time)); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($servings) : ?>
                            <div class="servings">
                                <strong>Servings:</strong> <?php echo esc_html($servings); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php
                // Display ingredients if available
                $ingredients = tiffycooks_get_ingredients(get_the_ID());
                if (!empty($ingredients)) : ?>
                    <div class="recipe-ingredients">
                        <h2>Ingredients</h2>
                        <ul>
                            <?php foreach ($ingredients as $ingredient) : ?>
                                <li>
                                    <?php if (!empty($ingredient['amount'])) : ?>
                                        <span class="amount"><?php echo esc_html($ingredient['amount']); ?></span>
                                    <?php endif; ?>
                                    <span class="ingredient"><?php echo esc_html($ingredient['ingredient']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php
                // Insert ad before instructions
                echo tiffycooks_amp_insert_ad('before-instructions');

                // Display instructions if available
                $instructions = tiffycooks_get_instructions(get_the_ID());
                if (!empty($instructions)) : ?>
                    <div class="recipe-instructions">
                        <h2>Instructions</h2>
                        <ol>
                            <?php foreach ($instructions as $instruction) : ?>
                                <li><?php echo esc_html($instruction); ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                <?php endif; ?>

                <?php
                // Display recipe notes if available
                $notes = get_post_meta(get_the_ID(), '_recipe_notes', true);
                if (!empty($notes)) : ?>
                    <div class="recipe-notes">
                        <h2>Recipe Notes</h2>
                        <?php echo wp_kses_post($notes); ?>
                    </div>
                <?php endif; ?>

                <?php
                // Insert ad after the recipe
                echo tiffycooks_amp_insert_ad('after-recipe');
                ?>
            </div>

            <footer class="entry-footer">
                <?php
                $categories_list = get_the_category_list(', ');
                if ($categories_list) {
                    printf('<span class="cat-links">Posted in %1$s</span>', $categories_list);
                }

                $tags_list = get_the_tag_list('', ', ');
                if ($tags_list) {
                    printf('<span class="tags-links">Tagged %1$s</span>', $tags_list);
                }
                ?>
            </footer>
        </article>

        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>