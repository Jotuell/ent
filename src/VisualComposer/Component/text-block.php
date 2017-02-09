<?php
use \Ent\VisualComposer\Helpers;

class WPBakeryShortCode_mu_text_block extends Ent\VisualComposer\ShortCode {}

$admin_tpl = <<<TPL
    <div class="mu-text-block">
        {{{ vc_wpautop(params.content) }}}
    </div>
TPL;

Helpers::map([
    'base' => 'mu_text_block',
    'custom_markup' => $admin_tpl,
    'params' => [
        [
            'type'       => 'textarea_html',
            'heading'    => __('Text', 'ent'),
            'param_name' => 'content',
        ],
    ]
]);
