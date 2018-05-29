<?php
namespace Ent;

use Ent\VisualComposer\Helpers;

class VisualComposer {
    public function __construct($user_components = null) {
        // Change standard VC templates path
        vc_set_shortcodes_templates_dir(__DIR__ . '/VisualComposer/Standard/views');
        vc_disable_frontend();

        add_action('vc_before_init', function () {
            // Remove help tips
            remove_action('admin_enqueue_scripts', 'vc_pointer_load');
        });

        // Remove VC Frontend assets
        add_action('wp_enqueue_scripts', function () {
            wp_dequeue_style('js_composer_front');
            wp_deregister_style('js_composer_front');
            wp_dequeue_script('wpb_composer_front_js');
            wp_deregister_script('wpb_composer_front_js');
        }, 9999);

        // Add Ent backend VC assets
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_script('ent-vc-backend', Helpers::getBackendJS(), array(), null, true);
            wp_enqueue_style('ent-vc-backend-css', Helpers::getBackendCSS(), array('js_composer'));
        }, 9999);

        add_action('wp_ajax_ent_img_src', function () {
            $img = wp_get_attachment_image_src($_POST['id'], $_POST['size']);

            echo json_encode([
                'id'   => $_POST['id'],
                'size' => $_POST['size'],
                'url'  => $img[0],
            ]);

            wp_die();
        });

        // Remove components
        //vc_remove_element('vc_row');
        //vc_remove_element('vc_column');
        vc_remove_element('vc_separator');
        vc_remove_element('vc_column_text');
        vc_remove_element('vc_icon');
        vc_remove_element('vc_text_separator');
        vc_remove_element('vc_message');
        vc_remove_element('vc_facebook');
        vc_remove_element('vc_tweetmeme');
        vc_remove_element('vc_googleplus');
        vc_remove_element('vc_pinterest');
        vc_remove_element('vc_toggle');
        vc_remove_element('vc_single_image');
        vc_remove_element('vc_gallery');
        vc_remove_element('vc_images_carousel');
        vc_remove_element('vc_tta_tabs');
        vc_remove_element('vc_tta_tour');
        vc_remove_element('vc_tta_accordion');
        vc_remove_element('vc_tta_pageable');
        vc_remove_element('vc_tta_section');
        vc_remove_element('vc_custom_heading');
        vc_remove_element('vc_btn');
        vc_remove_element('vc_cta');
        vc_remove_element('vc_widget_sidebar');
        vc_remove_element('vc_posts_slider');
        vc_remove_element('vc_video');
        vc_remove_element('vc_gmaps');
        vc_remove_element('vc_raw_html');
        vc_remove_element('vc_raw_js');
        vc_remove_element('vc_flickr');
        vc_remove_element('vc_progress_bar');
        vc_remove_element('vc_pie');
        vc_remove_element('vc_round_chart');
        vc_remove_element('vc_line_chart');
        vc_remove_element('vc_empty_space');
        vc_remove_element('vc_basic_grid');
        vc_remove_element('vc_media_grid');
        vc_remove_element('vc_masonry_grid');
        vc_remove_element('vc_masonry_media_grid');
        vc_remove_element('vc_tabs');
        vc_remove_element('vc_tour');
        vc_remove_element('vc_accordion');
        vc_remove_element('vc_button');
        vc_remove_element('vc_button2');
        vc_remove_element('vc_cta_button');
        vc_remove_element('vc_cta_button2');
        vc_remove_element('vc_wp_search');
        vc_remove_element('vc_wp_meta');
        vc_remove_element('vc_wp_recentcomments');
        vc_remove_element('vc_wp_calendar');
        vc_remove_element('vc_wp_pages');
        vc_remove_element('vc_wp_tagcloud');
        vc_remove_element('vc_wp_custommenu');
        vc_remove_element('vc_wp_text');
        vc_remove_element('vc_wp_posts');
        vc_remove_element('vc_wp_categories');
        vc_remove_element('vc_wp_archives');
        vc_remove_element('vc_wp_rss');

        // Register new VC components
        //require_once 'VisualComposer/Component/button.php';
        //require_once 'VisualComposer/Component/section.php';
        //require_once 'vc/components/button.php';
        //require_once 'vc/components/heading.php';
        //require_once 'vc/components/quote.php';
        //require_once 'vc/components/separator.php';
        //require_once 'vc/components/text-block.php';
        //require_once 'vc/components/text-columns.php';

        add_action('init', function () use ($user_components) {
            // Load new VC components
            foreach (glob(__DIR__ .'/VisualComposer/Component/*.php') as $filename) {
                require_once $filename;
            }

            // Load user components
            foreach (glob($user_components .'/*.php') as $filename) {
                require_once $filename;
            }

            // Load standard VC components tweaks
            // This must go in the end so that Helpers::map() 'is_layout' can be injected to 'vc_column'
            //
            // vc_row: works as the section with it's variations: full-width, image to the edge…
            // vc_column: is an empty element, we must keep it as that's how VC works
            // vc_row_inner/vc_column_inner: map to foundation .row and .columns
            require_once 'VisualComposer/Standard/vc_column_inner.php';
            require_once 'VisualComposer/Standard/vc_column.php';
            require_once 'VisualComposer/Standard/vc_row_inner.php';
            require_once 'VisualComposer/Standard/vc_row.php';
        });
    }
}
