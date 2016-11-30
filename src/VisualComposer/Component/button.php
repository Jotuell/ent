<?php
use \Ent\VisualComposer\Helpers;

class WPBakeryShortCode_mu_button extends Ent\VisualComposer\ShortCode {}

$admin_tpl = <<<TPL
    <div class="mu-button">
        {{{ ent_link(params.link) }}}
    </div>
TPL;

Helpers::map([
    'base' => 'mu_button',
    'custom_markup' => $admin_tpl,
    'params' => [
        [
            'type'        => 'vc_link',
            'heading'     => __('Link', 'ent'),
            'param_name'  => 'link',
        ],
    ]
]);
