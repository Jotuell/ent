<?php
use \Ent\VisualComposer\Helpers;

add_action('vc_after_init_base', function () {
    global $vc_row_layouts;

    // Override row layout presets
    $vc_row_layouts = [
        ['cells' => '11',          'mask' => '12',  'title' => '12',            'icon_class' => 'l_11'],
        ['cells' => '12_12',       'mask' => '26',  'title' => '6 + 6',         'icon_class' => 'l_12_12'],
        ['cells' => '13_13_13',    'mask' => '39',  'title' => '4 + 4 + 4',     'icon_class' => 'l_13_13_13'],
        ['cells' => '13_23',       'mask' => '29',  'title' => '4 + 8',         'icon_class' => 'l_13_23'],
        ['cells' => '23_13',       'mask' => '29',  'title' => '8 + 4',         'icon_class' => 'l_23_13'],
        ['cells' => '14_14_14_14', 'mask' => '420', 'title' => '3 + 3 + 3 + 3', 'icon_class' => 'l_14_14_14_14'],
        ['cells' => '24_14_14',    'mask' => '316', 'title' => '6 + 3 + 3',     'icon_class' => 'l_24_14_14'],
        ['cells' => '14_24_14',    'mask' => '316', 'title' => '3 + 6 + 3',     'icon_class' => 'l_14_24_14'],
        ['cells' => '14_14_24',    'mask' => '316', 'title' => '3 + 3 + 6',     'icon_class' => 'l_14_14_24'],
    ];
});

add_action('init', function() {
    vc_remove_param('vc_row', 'full_width');
    vc_remove_param('vc_row', 'gap');
    vc_remove_param('vc_row', 'full_height');
    vc_remove_param('vc_row', 'columns_placement');
    vc_remove_param('vc_row', 'equal_height');
    vc_remove_param('vc_row', 'content_placement');
    vc_remove_param('vc_row', 'video_bg');
    vc_remove_param('vc_row', 'video_bg_url');
    vc_remove_param('vc_row', 'video_bg_parallax');
    vc_remove_param('vc_row', 'parallax');
    vc_remove_param('vc_row', 'parallax_image');
    vc_remove_param('vc_row', 'parallax_speed_video');
    vc_remove_param('vc_row', 'parallax_speed_bg');
    vc_remove_param('vc_row', 'css');

    Helpers::updateParams('vc_row', [
        'el_id'           => ['group' => 'Advanced'],
        'disable_element' => ['group' => 'Advanced'],
        'el_class'        => ['group' => 'Advanced'],
    ]);

    // Leave basic controls
    Helpers::setControls('vc_row', ['edit', 'move', 'delete', 'clone']);

    vc_add_params('vc_row', [
        [
            'type'       => 'dropdown',
            'heading'    => __('Layout', 'ent'),
            'param_name' => 'layout',
            'weight'     => 1,
            'value'      => [
                __('Default', 'ent')     => 'vanilla',
                __('Image Left', 'ent')  => 'image-left',
                __('Image Right', 'ent') => 'image-right',
                __('Boxed', 'ent')       => 'boxed',
                __('Full Width', 'ent')  => 'expanded',
                __('None', 'ent')        => 'none',
            ],
        ]
    ]);
}, 100);

/*
vc_add_params('vc_row', array(
    array(
        'type'       => 'dropdown',
        'heading'    => __('Layout', 'ent'),
        'param_name' => 'layout',
        'weight'     => 1,
        'value'      => array(
            __('Default', 'ent')     => 'vanilla',
            __('Image Left', 'ent')  => 'image-left',
            __('Image Right', 'ent') => 'image-right',
            __('Boxed', 'ent')       => 'boxed',
            __('Full Width', 'ent')  => 'expanded',
        ),
    ),
    array(
        'type'       => 'attach_image',
        'heading'    => __('Row image', 'ent'),
        'param_name' => 'row_image',
        'weight'     => 1,
        'dependency' => array(
            'element' => 'layout',
            'value'   => array('image-left', 'image-right')
        ),
    ),
    array(
        'type'       => 'dropdown',
        'heading'    => __('Image type', 'ent'),
        'param_name' => 'image_type',
        'weight'     => 1,
        'value'      => array(
            __('Default', 'ent')     => 'default',
            __('No margin', 'ent')   => 'no-margin',
            __('To the edge', 'ent') => 'edge',
        ),
        'dependency' => array(
            'element' => 'layout',
            'value'   => array('image-left', 'image-right')
        ),
    ),
    array(
        'type'       => 'dropdown',
        'heading'    => __('Image width', 'ent'),
        'param_name' => 'image_width',
        'weight'     => 1,
        'value'      => array(
            __('A third (1/3)', 'ent')    => '4',
            __('Half (1/2)', 'ent')       => '6',
            __('Two thirds (2/3)', 'ent') => '8',
        ),
        'dependency' => array(
            'element' => 'layout',
            'value'   => array('image-left', 'image-right')
        ),
    ),
    array(
        'type'       => 'dropdown',
        'heading'    => __('Background color type', 'ent'),
        'param_name' => 'bg_color_type',
        'weight'     => 1,
        'value'      => array(
            __('Default', 'ent')   => 'default',
            __('Neutral', 'ent')   => 'neutral',
            __('Primary', 'ent')   => 'primary',
            __('Secondary', 'ent') => 'secondary',
            __('Custom', 'ent')    => 'custom',
        ),
    ),
    array(
        'type'       => 'colorpicker',
        'heading'    => __('Background color', 'ent'),
        'param_name' => 'bg_color',
        'weight'     => 1,
        'dependency' => array('element' => 'bg_color_type', 'value' => array('custom')),
    ),
));*/
