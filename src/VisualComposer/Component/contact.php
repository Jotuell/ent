<?php
use \Ent\VisualComposer\Helpers;

class WPBakeryShortCode_mu_contact extends Ent\VisualComposer\ShortCode {
    protected function getContextData(array $atts) {
        return [];
    }
}

$admin_tpl = <<<TPL
    <row>
        <column>
            {{ params.content }}
        </column>
        <column>
            <div class="ent-user-component"><span><a href="http://maps.google.com/?q={{ params.lat }},{{ params.lng }}" target="_blank">Mapa</a></span></div>
        </column>
    </row>
TPL;

Helpers::map([
    'base' => 'mu_contact',
    'custom_markup' => $admin_tpl,
    'is_layout' => true,
    'params' => [
        [
            'type' => 'textfield',
            'heading' => 'Latitud',
            'param_name' => 'lat',
        ],
        [
            'type' => 'textfield',
            'heading' => 'Longitud',
            'param_name' => 'lng',
        ],
        [
            'type' => 'textarea_html',
            'heading' => __('Contenido', 'ent'),
            'param_name' => 'content',
        ],
    ]
]); 
