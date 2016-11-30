<?php
extract(shortcode_atts(array(
    'el_class' => '',
    'width'    => '12',
), $atts));

// Fraction to Columns
preg_match('/(\d+)\/(\d+)/', $width, $matches);

if (!empty($matches)) {
	$part_x = (int) $matches[1];
	$part_y = (int) $matches[2];

	if ($part_x > 0 && $part_y > 0) {
		$width = ceil(($part_x / $part_y) * 12);

        if ($width < 0) { $width = 0; }
        if ($width >= 12) { $width = 12; }
	}
} else {
    $width = 12;
}

echo
'<div class="large-', $width ,' small-12 columns ', $el_class ,'">',
    wpb_js_remove_wpautop($content),
'</div>';
