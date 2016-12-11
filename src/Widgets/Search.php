<?php
namespace Ent\Widgets;

class Search extends \WP_Widget {
    /**
     * Sets up the widgets name etc
     */
	public function __construct() {
		parent::__construct('ent_search', 'Search', [
            'classname'   => 'ent-widget-search',
            'description' => 'Compact search form.',
        ]);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
        $show_placeholder_text = isset($instance['show_placeholder_text']) ? (bool) $instance['show_placeholder_text'] : true;
        $show_search_button = isset($instance['show_search_button']) ? (bool) $instance['show_search_button'] : false;
        ?>
        <div class="ent-widget ent-widget-search">            
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="input-group">
                    <input class="ent-widget-search__input input-group-field" type="text" name="s" value="<?php echo get_search_query() ?>" <?php if($show_placeholder_text) { ?>placeholder="<?php echo __('wp.search.text') ?>…"<?php } ?>>
                    <?php if($show_search_button) { ?>
                        <div class="input-group-button">
                            <button type="submit" class="ent-widget-search__button button"><i class="fa fa-search"></i></button>
                        </div>
                    <?php } ?>
                </div>
            </form>
        </div>
        <?php
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form($instance) {
        //Defaults
        $show_placeholder_text = isset($instance['show_placeholder_text']) ? (bool) $instance['show_placeholder_text'] : true;
        $show_search_button = isset($instance['show_search_button']) ? (bool) $instance['show_search_button'] : false;
        ?>
        <p>
        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_placeholder_text'); ?>" name="<?php echo $this->get_field_name('show_placeholder_text'); ?>"<?php checked($show_placeholder_text); ?> />
        <label for="<?php echo $this->get_field_id('show_placeholder_text'); ?>">Show placeholder text</label>
        <br />
        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('show_search_button'); ?>" name="<?php echo $this->get_field_name('show_search_button'); ?>"<?php checked($show_search_button); ?> />
        <label for="<?php echo $this->get_field_id('show_search_button'); ?>">Show search button</label>
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
        $instance['show_placeholder_text'] = empty($new_instance['show_placeholder_text']) ? 0 : 1;
        $instance['show_search_button'] = empty($new_instance['show_search_button']) ? 0 : 1;

        return $instance;
	}
}
