<?php
use \Ent\VisualComposer\Helpers;

add_action('init', function() {
    vc_remove_param('vc_column_inner', 'css');

    Helpers::updateParams('vc_column_inner', [
        'el_id'           => ['group' => 'Advanced'],
        'disable_element' => ['group' => 'Advanced'],
        'el_class'        => ['group' => 'Advanced'],
    ]);
});
