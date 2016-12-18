<?php
namespace Ent\Widgets;

class Icons extends \WP_Widget {
    /**
     * Sets up the widgets name etc
     */
	public function __construct() {
		parent::__construct('ent_icons', 'Icons', [
            'classname'   => 'ent-widget-icons',
            'description' => 'Icons. Useful for social media icons. Format, per row: `icon-name: URL`',
        ]);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
        $icons = empty($instance['icons']) ? '' : $instance['icons'];
        $icons = array_map(function ($icon) {
            $icon = explode(':', $icon);
            
            return [
                'icon' => array_shift($icon),
                'url'  => trim(implode(':', $icon)),
            ];
        }, explode("\n", $icons));
        
		echo
        '<div class="ent-widget ent-widget-icons">',
            '<ul class="ent-widget-icons__list">';

                foreach ($icons as $i) {
                    echo 
                    '<li class="ent-widget-icons__entry">',
                        '<a href="', $i['url'] ,'" class="ent-widget-icons__link"><i class="fa fa-', $i['icon'] ,'"></i></a>',
                    '</li>';
                }

            echo
            '</ul>',
        '</div>';
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
    public function form($instance) {
        $instance = wp_parse_args((array) $instance, ['icons' => '']);
 		
        echo
        '<textarea style="width: 100%; margin: 15px 0 10px; height: 200px;" id="', $this->get_field_id('icons') ,'" name="', $this->get_field_name('icons') ,'">',
            $instance['icons'],
        '</textarea>';
 	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $instance) {
        return wp_parse_args((array) $new_instance, ['icons' => '']);
        
		$new_instance = wp_parse_args((array) $new_instance, ['icons' => '']);
		$instance['icons'] = $new_instance['icons'] ? $new_instance['icons'] : '';

		return $instance;
	}
}
