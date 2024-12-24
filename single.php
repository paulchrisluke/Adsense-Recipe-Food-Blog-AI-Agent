<?php

/**
 * The template for displaying single posts
 *
 * @package TiffyCooks
 */

get_header(); ?>

<main id="main" class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class('recipe-article single-column'); ?>>
            <?php
            // Top of page - responsive ad
            echo tiffycooks_amp_insert_ad('top', 'responsive');
            ?>

            <header class="entry-header">
                <h1 class="entry-title"><?php the_title(); ?></h1>

                <div class="entry-meta">
                    <span class="posted-on"><?php echo get_the_date(); ?></span>
                    <span class="byline"> by <?php the_author(); ?></span>
                </div>
            </header>

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

                <?php
                // After featured image - fixed height ad
                echo tiffycooks_amp_insert_ad('after-image', 'fixed-height');
                ?>
            <?php endif; ?>

            <div class="recipe-content">
                <?php
                // Display the main content
                the_content();

                // Recipe details section
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
                // Before ingredients - responsive ad
                echo tiffycooks_amp_insert_ad('before-ingredients', 'responsive');
                ?>

                <?php
                // Display ingredients
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

                    <?php
                    // After ingredients - fixed height ad
                    echo tiffycooks_amp_insert_ad('after-ingredients', 'fixed-height');
                    ?>
                <?php endif; ?>

                <?php
                // Display instructions
                $instructions = tiffycooks_get_instructions(get_the_ID());
                if (!empty($instructions)) : ?>
                    <div class="recipe-instructions">
                        <h2>Instructions</h2>
                        <ol>
                            <?php
                            $instruction_count = count($instructions);
                            foreach ($instructions as $index => $instruction) :
                                echo '<li>' . esc_html($instruction) . '</li>';

                                // Insert ad after 1/3 and 2/3 of instructions
                                if ($index === floor($instruction_count / 3) || $index === floor($instruction_count * 2 / 3)) {
                                    echo tiffycooks_amp_insert_ad('in-instructions', 'fixed-height');
                                }
                            endforeach;
                            ?>
                        </ol>
                    </div>
                <?php endif; ?>

                <?php
                // Display recipe notes
                $notes = get_post_meta(get_the_ID(), '_recipe_notes', true);
                if (!empty($notes)) : ?>
                    <div class="recipe-notes">
                        <h2>Recipe Notes</h2>
                        <?php echo wp_kses_post($notes); ?>
                    </div>
                <?php endif; ?>

                <?php
                // Bottom of content - responsive ad
                echo tiffycooks_amp_insert_ad('bottom', 'responsive');
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
    <?php endwhile; ?>
</main>

<?php get_footer(); ?>