<?php
namespace Ent;

use Ent\VisualComposer\Helpers as VCHelpers;

class VisualComposer {
    public function __construct($user_components = null) {
        // Change standard VC templates path
        // vc_set_shortcodes_templates_dir(__DIR__ . '/VisualComposer/Standard/views');

        // Add Ent backend VC assets
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_script('ent-vc-backend', VCHelpers::getBackendJS(), array(), null, true);
            // wp_enqueue_style('ent-vc-backend-css', Helpers::getBackendCSS(), array('js_composer'));
        }, 9999);

        add_action('wp_ajax_ent_img_src', function () {
            $img = wp_get_attachment_image_src($_POST['id'], array(150,150));

            echo json_encode([
                'id'   => $_POST['id'],
                'size' => $_POST['size'],
                'url'  => $img[0],
            ]);

            wp_die();
        });

        add_action('init', function () use ($user_components) {

            // Load user components
            foreach (glob($user_components .'/*.php') as $filename) {
                require_once $filename;
            }

        });
    }
}
