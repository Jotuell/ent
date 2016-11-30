<?php
use \Ent\VisualComposer\Helpers;

add_action('init', function() {
    vc_remove_param('vc_column', 'css');

    Helpers::updateParams('vc_column', [
        'as_parent'       => ['only' => implode(',', Helpers::$layout_components)],
        'el_id'           => ['group' => 'Advanced'],
        //'disable_element' => ['group' => 'Advanced'],
        'el_class'        => ['group' => 'Advanced'],
    ]);

    // Remove all controls
    Helpers::setControls('vc_column', '');
});
