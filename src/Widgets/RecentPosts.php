<?php
namespace Ent\Widgets;

class RecentPosts extends \WP_Widget {
    /**
     * Sets up the widgets name etc
     */
	public function __construct() {
		parent::__construct('ent_recent_posts', 'Recent Posts', [
            'classname'   => 'ent-widget-recent-posts',
            'description' => 'Recent posts list.',
        ]);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;
        $number = (!empty($instance['number'])) ? absint($instance['number']) : 5;

        if (!$number) {
            $number = 5;
        }
        
        $query = new \WP_Query([
            'posts_per_page'      => $number,
            'no_found_rows'       => true,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true
        ]);
        
        if ($query->have_posts()) {
            echo '<div class="ent-widget ent-widget-recent-posts">',
                '<ul class="mu-icon-list">';

                while ($query->have_posts()) {
                    $query->the_post(); 
                    
                    echo '<li class="mu-icon-list__entry">',
                        '<i class="fa fa-file-o"></i>',
                        '<a href="', get_the_permalink(), '">', get_the_title() ,'</a>';
                        
                        if ($show_date) {
                            echo '<span class="ent-widget-recent-posts__date">', get_the_date() ,'</span>';
                        }

                    echo '</li>';
                }

                echo '</ul>',
            '</div>';

            // Reset the global $the_post as this query will have stomped on it
            wp_reset_postdata();
        }

	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
        $number    = isset($instance['number']) ? absint($instance['number']) : 5;
        $show_date = isset($instance['show_date']) ? (bool) $instance['show_date'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('number'); ?>">Number of posts to show:</label>
        <input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" /></p>

        <p><input class="checkbox" type="checkbox"<?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
        <label for="<?php echo $this->get_field_id( 'show_date' ); ?>">Display post date?</label></p>
        <?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $instance) {
        $instance['number'] = (int) $new_instance['number'];
        $instance['show_date'] = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        
        return $instance;
	}
}
