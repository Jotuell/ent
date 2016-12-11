<?php
namespace Ent\Widgets;

class TermsCloud extends \WP_Widget {
    /**
     * Sets up the widgets name etc
     */
	public function __construct() {
		parent::__construct('ent_terms_cloud', 'Terms Cloud', [
            'classname'   => 'ent-widget-terms-cloud',
            'description' => 'Taxonomy terms cloud.',
        ]);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
        $current_taxonomy = $this->get_current_taxonomy($instance);

		$terms_cloud = wp_tag_cloud(apply_filters('widget_tag_cloud_args', [
			'taxonomy' => $current_taxonomy,
            'format'   => 'array',
			'echo'     => false,
            'smallest' => 1,
            'largest'  => 1.5,
            'unit'     => 'em',
		]));

		if (empty($terms_cloud)) {
			return;
		}

		echo '<div class="ent-widget ent-widget-terms-cloud">',
            '<ul class="mu-icon-list mu-icon-list--horizontal">';
        
            foreach ($terms_cloud as $term) {
                echo '<li class="mu-icon-list__entry">', $term ,'</li>';
            }

		    echo '</ul>',
        '</div>';
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
        $current_taxonomy = $this->get_current_taxonomy($instance);
		$taxonomies       = get_taxonomies(['show_tagcloud' => true], 'object');
		$id               = $this->get_field_id('taxonomy');
		$name             = $this->get_field_name('taxonomy');
		$input            = '<input type="hidden" id="'. $id .'" name="'. $name .'" value="%s" />';

		switch (count($taxonomies)) {
    		// No tag cloud supporting taxonomies found, display error message
    		case 0:
    			echo '<p>'. __('The tag cloud will not be displayed since there are no taxonomies that support the tag cloud widget.') .'</p>';
    			printf($input, '');

    			break;

    		// Just a single tag cloud supporting taxonomy found, no need to display options
    		case 1:
    			$keys = array_keys($taxonomies);
    			$taxonomy = reset($keys);
    			printf($input, esc_attr($taxonomy));

    			break;

    		// More than one tag cloud supporting taxonomy found, display options
    		default:
    			printf(
    				'<p><label for="%1$s">%2$s</label>' .
    				'<select class="widefat" id="%1$s" name="%3$s">',
    				$id,
    				__('Taxonomy:'),
    				$name
    			);

    			foreach ($taxonomies as $taxonomy => $tax) {
    				printf(
    					'<option value="%s"%s>%s</option>',
    					esc_attr($taxonomy),
    					selected($taxonomy, $current_taxonomy, false),
    					$tax->labels->name
    				);
    			}

    			echo '</select></p>';
		}
	}
    
    protected function get_current_taxonomy($instance) {
        if (!empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy'])) {
            return $instance['taxonomy'];
        }

        return 'post_tag';
    }

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $instance) {
        $instance = array();
		$instance['taxonomy'] = stripslashes($new_instance['taxonomy']);

		return $instance;
	}
}
