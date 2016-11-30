<?php
use \Ent\VisualComposer\Helpers;

class WPBakeryShortCode_mu_image extends Ent\VisualComposer\ShortCode {}

$admin_tpl = <<<TPL
    <div class="mu-image">
        <# if (params.link) { #>
            <a href="{{ ent_link_parse(params.link).url }}">
        <# } #>
        {{{ ent_image(params.image, "medium", "100x100") }}}
        <# if (params.link) { #>
            </a>
        <# } #>
    </div>
TPL;

Helpers::map([
    'base' => 'mu_image',
    'custom_markup' => $admin_tpl,
    'params' => [
        [
            'type'       => 'attach_image',
            'heading'    => __('Image', 'ent'),
            'param_name' => 'image',
        ],
        [
            'type'        => 'vc_link',
            'heading'     => __('Link', 'ent'),
            'param_name'  => 'link',
        ],
    ]
]);
