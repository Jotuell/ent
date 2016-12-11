<?php
namespace Ent\Widgets;

class TermsList extends \WP_Widget {
    /**
     * Sets up the widgets name etc
     */
	public function __construct() {
		parent::__construct('ent_terms_list', 'Terms List', [
            'classname'   => 'ent-widget-terms-list',
            'description' => 'Taxonomy terms list.',
        ]);
	}
    
    protected function render_terms($terms, $only_root, $hierarchical, $show_count) {
        foreach ($terms as $term) {
            echo 
            '<li class="mu-icon-list__entry">',
                '<i class="fa fa-tag"></i><a href="', get_term_link($term) ,'">', $term->name ,'</a>';
                
                if ($show_count) {
                    echo ' (', $term->count ,')';
                }
                
            if (!$hierarchical) { echo '</li>'; }

            if (!$only_root && count($term->children) != 0) {
                if ($hierarchical) { echo '<ul class="mu-icon-list">'; }
                $this->render_terms($term->children, $only_root, $hierarchical, $show_count);
                if ($hierarchical) { echo '</ul>'; }
            }
            
            if ($hierarchical) { echo '</li>'; }
        }
    }

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
        static $first_dropdown = true;

        $term_id_map  = [];
        $terms_tree   = [];
		$show_count   = empty($instance['count']) ? '0' : '1';
		$hierarchical = empty($instance['hierarchical']) ? '0' : '1';
		$only_root    = empty($instance['only_root']) ? '0' : '1';
        $current_taxonomy = $this->get_current_taxonomy($instance);
        
        $terms = get_terms([
            'taxonomy'     => $current_taxonomy,
            'orderby'      => 'name',
            'hierarchical' => $hierarchical,
        ]);

        // Save ID => OBJECT relation
        foreach ($terms as $term) {
            $term_id_map[$term->term_id] = $term;
            $term->children = [];
        }
        
        // Create the actual TREE
        foreach ($terms as $term) {
            if ($term->parent != 0) {
                $term_id_map[$term->parent]->children[] = $term;
            } else {
                $terms_tree[] = $term;
            }
        }

        echo 
        '<div class="ent-widget ent-widget-terms-list">',
            '<ul class="mu-icon-list">';
            
            $this->render_terms($terms_tree, $only_root, $hierarchical, $show_count);

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
        //Defaults
		$instance = wp_parse_args((array) $instance, []);
		$count = isset($instance['count']) ? (bool) $instance['count'] : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
        $only_root = isset($instance['only_root']) ? (bool) $instance['only_root'] : false;
        
        $current_taxonomy = $this->get_current_taxonomy($instance);
        $taxonomies       = get_taxonomies(['public' => true], 'object');
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
		?>
		<p>
    		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked($count); ?> />
    		<label for="<?php echo $this->get_field_id('count'); ?>">Show post counts</label>
            <br />
    		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical'); ?>" name="<?php echo $this->get_field_name('hierarchical'); ?>"<?php checked($hierarchical); ?> />
    		<label for="<?php echo $this->get_field_id('hierarchical'); ?>">Show hierarchy</label>
            <br />
            <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('only_root'); ?>" name="<?php echo $this->get_field_name('only_root'); ?>"<?php checked($only_root); ?> />
    		<label for="<?php echo $this->get_field_id('only_root'); ?>">Show only root terms</label>
        </p>
		<?php
	}

    protected function get_current_taxonomy($instance) {
        if (!empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy'])) {
            return $instance['taxonomy'];
        }

        return 'category';
    }

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update($new_instance, $instance) {
		$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
		$instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
		$instance['only_root'] = !empty($new_instance['only_root']) ? 1 : 0;
        $instance['taxonomy'] = stripslashes($new_instance['taxonomy']);

		return $instance;
	}
}
