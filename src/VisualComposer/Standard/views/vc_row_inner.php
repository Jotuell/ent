<?php
extract(shortcode_atts(array(
    'el_class' => '',
), $atts));

echo '<div class="row ', $el_class ,'">', do_shortcode($content), '</div>';
