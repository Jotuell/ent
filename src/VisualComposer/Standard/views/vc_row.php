<?php
extract(shortcode_atts(array(
    'el_id'    => '',
    'el_class' => '',
), $atts));

// Vanilla
echo
'<section',
    ($el_id ? ' id="'. $el_id .'" ' : ''),
    ($el_class ? ' class="'. $el_class .'" ': ''),
    '>',
    do_shortcode($content),
'</section>';

/*
$layout = $row_image = '';
extract(shortcode_atts(array(
    'el_class'      => '',
    'layout'        => 'vanilla',
    'row_image'     => '',
    'bg_color_type' => 'default',
    'bg_color'      => '',
    'image_type'    => 'default',
    'image_width'   => '4',
), $atts));

$image_content_width = 12 - $image_width;
$image_width += 1;

if ($bg_color_type == 'custom') {
    $uid = uniqid();
    echo '<style>',
        '.munger-bg-color-', $uid, ', section.munger-bg-color-', $uid, ' .content:after { background-color: ', $bg_color ,'; }',
    '</style>';

    $bg_color_class = 'munger-bg-color-' . $uid;
} else {
    if ($layout == 'boxed' && $bg_color_type == 'default') {
        $bg_color_type = 'neutral';
    }

    $bg_color_class = 'munger-bg-color-' . $bg_color_type;
}

if ($layout == 'vanilla' || $layout == 'boxed') {
    echo
    '<section class="', $layout ,' ', $el_class ,' ', $bg_color_class, '">',
        '<div class="row">',
            do_shortcode($content),
        '</div>',
    '</section>';
} else if ($layout == 'image-left' && $image_type == 'edge') {
    $featured = wp_get_attachment_image_src($row_image, 'large');

    echo
    '<section class="edge-image-left ', $el_class ,' ', $bg_color_class ,'">',
        '<div class="row expanded image">',
            '<div class="medium-', $image_width ,' columns" style="background-image: url(', $featured[0] ,');"></div>',
        '</div>',
        '<div class="row content">',
            '<div class="medium-6 medium-offset-6 large-', $image_content_width ,' large-offset-', (12 - $image_content_width) ,' columns ', $bg_color_class ,'">',
                '<div class="row">',
                    do_shortcode($content),
                '</div>',
            '</div>',
        '</div>',
    '</section>';
} else if ($layout == 'image-right' && $image_type == 'edge') {
    $featured = wp_get_attachment_image_src($row_image, 'large');

    echo
    '<section class="edge-image-right ', $el_class ,' ', $bg_color_class ,'">',
        '<div class="row expanded image">',
            '<div class="medium-', $image_width ,' medium-offset-', (12 - $image_width) ,' columns" style="background-image: url(', $featured[0] ,');"></div>',
        '</div>',
        '<div class="row content">',
            '<div class="medium-6 large-', $image_content_width ,' columns ', $bg_color_class ,'">',
                '<div class="row">',
                    do_shortcode($content),
                '</div>',
            '</div>',
        '</div>',
    '</section>';
} else if ($layout == 'expanded') {
    echo 'expanded';
} else {
    echo 'WASSSSUUUPPP?';
    var_dump($layout);
}
*/
