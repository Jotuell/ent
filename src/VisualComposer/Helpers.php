<?php
namespace Ent\VisualComposer;

class Helpers {
    static public $layout_components = ['vc_row'];

    public static function getAssetUrl($path) {
        return get_template_directory_uri() .'/vendor/jotuell/ent/src/VisualComposer/Assets/'. $path;
    }

    public static function getIconUrl($img) {
        return self::getAssetUrl('icons/'. $img .'.png');
    }

    public static function getBackendCSS() {
        return self::getAssetUrl('backend.css');
    }

    public static function getBackendJS() {
        return self::getAssetUrl('backend.js');
    }

    public static function map($config) {
        if (strpos($config['base'], 'mu_') === 0) {
            $icon = str_replace('mu_', '', $config['base']);
            $icon = str_replace('_', '-', $icon);
        } else {
            $icon = 'user-component';
        }

        // Save to layout components array
        // This is used by vc_column to limit which components can be its child
        if (isset($config['is_layout']) && $config['is_layout']) {
            $config['as_child'] = ['only' => 'vc_column'];
            self::$layout_components[] = $config['base'];
        }

        // Pre-configure container components
        /*
        if (isset($config['is_container']) && $config['is_container']) {
            $config['js_view'] = '';
        }*/

        $config = array_merge([
            'name'          => ucFirst(str_replace('_', ' ', str_replace('mu_', '', $config['base']))),
            'icon'          => Helpers::getIconUrl($icon),
            'js_view'       => 'EntCustomView',
            'custom_markup' => '',
            'params'        => [],
        ], $config);

        if (!$config['is_container']) {
            if ($config['custom_markup'] == '') {
                $config['custom_markup'] =
                    '<div data-ent-custom-view class="ent-user-component">'.
                        '<span>'. $config['name'] .'</span>'.
                    '</div>'
                ;
            } else {
                $config['custom_markup'] = '<div data-ent-custom-view>'. $config['custom_markup'] .'</div>';
            }
        }

        // Custom markup sugar
        $config['custom_markup'] = strtr($config['custom_markup'], [
            '<row>'        => '<div class ="ent-row">',
            '</row>'       => '</div>',
            '<column>'     => '<div class ="ent-column">',
            '</column>'    => '</div>',
            '<column-1>'   => '<div class ="ent-column">',
            '</column-1>'  => '</div>',
            '<column-2>'   => '<div class ="ent-column-2">',
            '</column-2>'  => '</div>',
            '<column-3>'   => '<div class ="ent-column-3">',
            '</column-3>'  => '</div>',
            '<box>'        => '<div class ="ent-user-component"><span>',
            '</box>'       => '</span></div>',
            '<container/>' => '<div class="ent-container wpb_column_container vc_container_for_children vc_clearfix ui-droppable ui-sortable"></div>',
        ]);

        // Register component
        vc_lean_map($config['base'], function () use ($config) {
            return $config;
        });
    }

    public static function setControls($tag, $controls = '') {
        $instance = visual_composer()->getShortCode($tag)->shortcodeClass();
        $instance->setSettings('controls', $controls);
    }

    // https://snippets.khromov.se/changing-settings-of-built-in-visual-composer-elements/
    public static function updateParams($tag, $params = []) {
        $shortcode = \WPBMap::getShortCode($tag);

        // Update parameters
        foreach ($shortcode['params'] as $i => $arr) {
            if (array_key_exists($arr['param_name'], $params)) {
                $shortcode['params'][$i] = array_merge($arr, $params[$arr['param_name']]);
            }
        }

        // VC doesn't like even the thought of you changing the shortcode base, and errors out, so we unset it.
        unset($shortcode['base']);

        vc_map_update($tag, $shortcode);
    }
}
