<?php
use \Ent\VisualComposer\Helpers;

class WPBakeryShortCode_mu_heading extends Ent\VisualComposer\ShortCode {}

$admin_tpl = <<<TPL
    <div class="mu-heading">
        <# var tag = params.tag ? params.tag : "h2"; #>
        <# if (params.source == "post_title") { #>
            <{{ tag }}>{{{ post_title }}}</{{ tag }}>
        <# } else { #>
            <{{ tag }}>{{{ params.text }}}</{{ tag }}>
        <# } #>
    </div>
TPL;

Helpers::map([
    'base' => 'mu_heading',
    'custom_markup' => $admin_tpl,
    'params' => [
        [
            'type'       => 'dropdown',
            'heading'    => __('Text Source', 'ent'),
            'param_name' => 'source',
            'value' => [
                __('Custom Text', 'ent') => '',
                __('Post or Page Title', 'ent') => 'post_title',
            ],
        ],
        [
            'type'        => 'textfield',
            'heading'     => __('Text', 'ent'),
            'param_name'  => 'text',
            'value'       => __('Heading', 'ent'),
            'admin_label' => true, // Bad when user uses html.. ??
            'vcex_rows'   => 2,
            'description' => __('HTML Supported', 'ent'),
            'dependency'  => ['element' => 'source', 'is_empty' => true],
        ],
        [
            'type'       => 'dropdown',
            'heading'    => __('Tag', 'ent'),
            'param_name' => 'tag',
            'value' => [
                __('Default (h2)', 'ent') => '',
                'h1' => 'h1',
                'h2' => 'h2',
                'h3' => 'h3',
                'h4' => 'h4',
                'h5' => 'h5',
                'h6' => 'h6',
            ],
        ],
        [
            'type'       => 'textfield',
            'heading'    => __('Custom Classes', 'ent'),
            'param_name' => 'el_class',
            'group'      => 'Advanced',
        ],
    ]
]);
