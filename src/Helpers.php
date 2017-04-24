<?php
namespace Ent;

use Carbon_Fields\Field;

class Helpers {
    static public $vc_enabled_cpt = [];
    static public $cf_containers = [];
    
    public static function cf_collapse_complex_fields($field) {
        add_action('in_admin_footer', function () use ($field) {
            ?>
            <script type="text/javascript">
                jQuery(function () {
                    var counter = 0; // Tries counter
                    var tries   = 10;
                    var delay   = 250;
                    var query   = '#carbon-_<?php echo $field; ?>-complex-container .carbon-btn-collapse';

                    function check() {
                        var els = jQuery(query);

                        if (els.length) {
                            els.click();
                        } else if (counter < tries) {
                            counter++;
                            setTimeout(check, delay);
                        }
                    }

                    check();
                });
            </script>
            <?php
        });
    }

    public static function cf_create_media($title) {
        return Field::make('complex', 'media', $title)
            ->add_fields('image', 'Imatge', [
                Field::make('image', 'image', 'Imatge')->set_required(true),
                Field::make('text', 'description', 'Descripció'),
            ])->set_header_template('{{ image }} {{ description }}')
            ->add_fields('youtube', 'Video Youtube', [
                Field::make('text', 'video_url', 'Video')
                    ->set_required(true)
                    ->help_text('Dirección URL del video de YouTube. Ejemplo: <code>https://www.youtube.com/watch?v=kfoJUeyMsOE</code>'),
                Field::make('image', 'image', 'Fotograma'),
            ])->set_header_template('{{ image }} <a href="{{ video_url }}" target="_blank">{{ video_url }}</a>');
    }

    public static function cf_create_attachments($title) {
        return Field::make('complex', 'attachments', $title)->add_fields([
            Field::make('text', 'title', 'Nom')->set_required(true),
            Field::make('file', 'file', 'Arxiu')->set_required(true),
        ])->set_header_template('{{ title }}');
    }

    public static function cf_create_links($title) {
        return Field::make('complex', 'links', $title)->add_fields([
            Field::make('text', 'title', 'Nom')->set_required(true),
            Field::make('text', 'url', 'URL')->set_required(true),
        ])->set_header_template('{{ title }}');
    }
    
    public static function convertTaxonomyToRadio($taxonomy) {
        $cb = function () use ($taxonomy) {
            ?>
            <script type="text/javascript">
                jQuery("#<?php echo $taxonomy; ?>checklist input").each(function () {
                    this.type = 'radio';
                });
                jQuery("#<?php echo $taxonomy; ?>-tabs li:odd").hide();
            </script>
            <?php
        };

        add_action('admin_footer-post.php', $cb);
        add_action('admin_footer-post-new.php', $cb);
    }
    
    public static function enableVCFor($cpt) {
        self::$vc_enabled_cpt[] = $cpt;
    }
    
    public static function getPostMeta($key, $obj) {
        self::getMeta($key, $obj, 'carbon_get_post_meta');
    }
    
    public static function getTermMeta($key, $obj) {
        self::getMeta($key, $obj, 'carbon_get_term_meta');
    }
    
    protected static function getMeta($key, $obj, $fn) {
        foreach (self::$cf_containers[$key]->get_fields() as $field) {
            $field_name = $field->get_base_name();

            if (get_class($field) == 'Carbon_Fields\Field\Complex_Field') {
                $type = 'complex';
            } else if (get_class($field) == 'Carbon_Fields\Field\Map_Field') {
                $type = 'map';
            } else {
                $type = null;
            }

            $obj->$field_name = $fn($obj->id, $field_name, $type);
            
            if (get_class($field) == 'Carbon_Fields\Field\Checkbox_Field') {
                $obj->$field_name = $obj->$field_name === 'yes';
            } else if (get_class($field) == 'Carbon_Fields\Field\Date_Field') {
                if (!empty($obj->$field_name)) {
                    $obj->$field_name = new \Datetime($obj->$field_name);
                }
            }
        }
    }
    
    public static function setMeta($key, $cb) {
        self::$cf_containers[$key] = $cb();
    }
    
    public static function adminChangePostLabels(array $labels = []) {
        // https://paulund.co.uk/change-posts-text-in-admin-menu
        global $wp_post_types;

        // Get the post labels
        $postLabels = $wp_post_types['post']->labels;
        $postLabels->name = $labels['name'];
        $postLabels->singular_name = $labels['singular'];

        add_action('admin_menu', function () use ($labels) {
            global $menu;
            global $submenu;

            // Change menu item
            $menu[5][0] = $labels['name'];

            // Change post submenu
            $submenu['edit.php'][5][0] = $labels['name'];
            $submenu['edit.php'][10][0] = $labels['add'];
            $submenu['edit.php'][16][0] = $labels['tags'];
        });
    }
    
    public static function adminRemoveComments() {
        // https://codex.wordpress.org/Function_Reference/remove_menu_page
        add_action('admin_menu', function () {
            remove_menu_page('edit-comments.php');
        });
    }
}
