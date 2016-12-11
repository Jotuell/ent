<?php
namespace Ent\Widgets;

class Archives extends \WP_Widget {
    /**
     * Sets up the widgets name etc
     */
	public function __construct() {
		parent::__construct('ent_archives', 'Archives', [
            'classname'   => 'ent-widget-archives',
            'description' => 'Archives.',
        ]);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
        $count = empty($instance['count']) ? '0' : '1';
		//$d = empty($instance['dropdown']) ? '0' : '1';

		echo '<div class="ent-widget ent-widget-archives">',
            '<ul class="mu-icon-list">';

    		wp_get_archives(apply_filters('widget_archives_args', [
    			'type'            => 'monthly',
    			'show_post_count' => $count,
                'format'          => 'custom',
                'before'          => '<li class="mu-icon-list__entry"><i class="fa fa-calendar"></i>',
                'after'           => '</li>',
    		]));

		    echo '</ul>',
        '</div>';
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
    public function form($instance) {
        $instance = wp_parse_args((array) $instance, ['count' => 0, 'dropdown' => '']);
 		?>
 		<p>
 			<!--
            <input class="checkbox" type="checkbox"<?php checked( $instance['dropdown'] ); ?> id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>" /> <label for="<?php echo $this->get_field_id('dropdown'); ?>">Display as dropdown</label>
 			<br/>
            -->
 			<input class="checkbox" type="checkbox"<?php checked( $instance['count'] ); ?> id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" /> <label for="<?php echo $this->get_field_id('count'); ?>">Show post counts</label>
 		</p>
 		<?php
 	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $instance) {
        $instance = $old_instance;
		$new_instance = wp_parse_args((array) $new_instance, ['count' => 0, 'dropdown' => '']);
		$instance['count'] = $new_instance['count'] ? 1 : 0;
		$instance['dropdown'] = $new_instance['dropdown'] ? 1 : 0;

		return $instance;
	}
}
