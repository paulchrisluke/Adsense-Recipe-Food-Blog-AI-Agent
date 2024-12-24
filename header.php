<!DOCTYPE html>
<html <?php language_attributes(); ?> amp>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>

    <!-- AMP Boilerplate -->
    <style amp-boilerplate>
        body {
            -webkit-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -moz-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            -ms-animation: -amp-start 8s steps(1, end) 0s 1 normal both;
            animation: -amp-start 8s steps(1, end) 0s 1 normal both
        }

        @-webkit-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-moz-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-ms-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @-o-keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }

        @keyframes -amp-start {
            from {
                visibility: hidden
            }

            to {
                visibility: visible
            }
        }
    </style>
    <noscript>
        <style amp-boilerplate>
            body {
                -webkit-animation: none;
                -moz-animation: none;
                -ms-animation: none;
                animation: none
            }
        </style>
    </noscript>

    <!-- AMP Components -->
    <script async src="https://cdn.ampproject.org/v0.js"></script>
    <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
    <script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>

    <!-- Google Fonts for AMP -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&family=Noto+Serif:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom Styles -->
    <style amp-custom>
        <?php include get_template_directory() . '/style.css'; ?>
    </style>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <div id="page" class="site">
        <!-- Desktop Sidebar -->
        <aside class="desktop-sidebar">
            <?php get_sidebar(); ?>
        </aside>

        <!-- Mobile Menu Toggle -->
        <button on="tap:sidebar-mobile.toggle" class="menu-toggle" aria-controls="sidebar-mobile" aria-expanded="false">
            <span class="menu-toggle-icon"></span>
            <span class="sr-only">Menu</span>
        </button>

        <!-- Mobile Sidebar -->
        <amp-sidebar id="sidebar-mobile" layout="nodisplay" side="left">
            <?php get_sidebar(); ?>
        </amp-sidebar>

        <!-- Main Content Wrapper -->
        <div class="site-wrapper">
            <!-- Header -->
            <header id="masthead" class="site-header">
                <div class="site-header-inner">
                    <?php if (is_single() || is_page()) : ?>
                        <nav class="breadcrumbs">
                            <?php
                            if (is_single()) {
                                $categories = get_the_category();
                                if ($categories) {
                                    $category = $categories[0];
                                    echo '<a href="' . esc_url(home_url('/')) . '">Home</a>';
                                    echo '<span class="separator">/</span>';
                                    echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                }
                            } else {
                                echo '<a href="' . esc_url(home_url('/')) . '">Home</a>';
                            }
                            ?>
                        </nav>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Main Content Area -->
            <main id="content" class="site-content">
                <?php if (function_exists('amp_auto_ads_tag') && is_single()) : ?>
                    <?php amp_auto_ads_tag(); ?>
                <?php endif; ?>