<?php
namespace Ent;

use Symfony\Component\Translation;
use Timber;

class Ent {
    protected static $context = null;

    public function __construct($opts) {
        $theme_dir = $opts['theme_dir'];

        // Create alias so that Ent class can be accessed typing `Ent::` without namespace backslash
        class_alias(get_class($this), 'Ent');

        $timber = new Timber\Timber();
        $assets = new \Ent\AssetsJSON($theme_dir .'/assets/assets.json', get_template_directory_uri());

        // -------------
        // REQUIRE FILES
        // -------------
        foreach (['/src/pages', '/src/post-types', '/src/taxonomies'] as $folder) {
            foreach (glob($theme_dir . $folder .'/*.php') as $filename) {
                require_once $filename;
            }
        }

        require_once $theme_dir .'/src/routes.php';

        // ----------------
        // MENUS & SIDEBARS
        // ----------------
        if ($opts['menus']) {
            add_action('after_setup_theme', function () use ($opts) {
                register_nav_menus($opts['menus']);
            });
        }

        if ($opts['sidebars']) {
            add_action('widgets_init', function () use ($opts) {
                foreach ($opts['sidebars'] as $id => $name) {
                    register_sidebar([
                        'name'          => $name,
                        'id'            => $id,
                        'before_widget' => '<div class="ent-widget">',
                        'after_widget'  => '</div>',
                        'before_title'  => '<h4 class="ent-widget__title">',
                        'after_title'   => '</h4>',
                    ]);
                }
            });
        }

        add_filter('timber/context', function ($data) use ($opts) {
            if ($opts['menus']) {
                foreach ($opts['menus'] as $id => $name) {
                    $data[$id] = new \TimberMenu($id);
                }
            }

            if ($opts['sidebars']) {
                foreach ($opts['sidebars'] as $id => $name) {
                    $data[$id] = \Timber::get_widgets($id);
                }
            }

            return $data;
        });

        // -------
        // WIDGETS
        // -------
        add_action('widgets_init', function () {
            // Unregister default WordPress widgets
            unregister_widget('WP_Widget_Pages');           // Pages Widget
            unregister_widget('WP_Widget_Calendar');        // Calendar Widget
            unregister_widget('WP_Widget_Archives');        // Archives Widget
            unregister_widget('WP_Widget_Links');           // Links Widget
            unregister_widget('WP_Widget_Meta');            // Meta Widget
            unregister_widget('WP_Widget_Search');          // Search Widget
            unregister_widget('WP_Widget_Text');            // Text Widget
            unregister_widget('WP_Widget_Categories');      // Categories Widget
            unregister_widget('WP_Widget_Recent_Posts');    // Recent Posts Widget
            unregister_widget('WP_Widget_Recent_Comments'); // Recent Comments Widget
            unregister_widget('WP_Widget_RSS');             // RSS Widget
            unregister_widget('WP_Widget_Tag_Cloud');       // Tag Cloud Widget
            unregister_widget('WP_Nav_Menu_Widget');        // Menus Widget

            foreach (glob(__DIR__ .'/Widgets/*.php') as $filename) {
                register_widget('Ent\\Widgets\\'. basename($filename, '.php'));
            }
        });

        // ---------------
        // VISUAL COMPOSER
        // ---------------
        if (function_exists('vc_map')) {
            $vc = new \Ent\VisualComposer($theme_dir .'/src/components');

            add_action('vc_before_init', function () {
                vc_set_default_editor_post_types(\Ent\Helpers::$vc_enabled_cpt);
            });
        }

        // ----
        // i18n
        // ----
        if (defined('ICL_LANGUAGE_CODE')) {
            add_action('wp', function () use ($theme_dir) {
                global $sitepress;

                // Get data from WPML
                $default_locale = $sitepress->get_default_language();
                $locales = icl_get_languages('skip_missing=0');

                // Init Symfony Translation component and load resources
                $translator = new Translation\Translator(ICL_LANGUAGE_CODE, new Translation\MessageSelector());
                $translator->setFallbackLocale($default_locale);
                $translator->addLoader('yaml', new Translation\Loader\YamlFileLoader());
                $translator->addResource('yaml', $theme_dir .'/src/locales/'. ICL_LANGUAGE_CODE .'.yml', ICL_LANGUAGE_CODE);

                // Load also the default locale if we're not in the default one
                if (ICL_LANGUAGE_CODE != $default_locale) {
                    $translator->addResource('yaml', $theme_dir .'/src/locales/'. $default_locale .'.yml', $default_locale);
                }

                // WordPress integration
                add_filter('gettext', function ($str, $str_key, $domain) use ($translator) {
                    if (($domain == 'ent' || $domain == 'default') && $str == $str_key) {
                        $str = $translator->trans($str_key);
                    }

                    return $str;
                }, 20, 3);

                // Load locales in Timber
                add_filter('timber/context', function ($data) use ($locales) {
                    $data['locales'] = [
                        'current' => $locales[ICL_LANGUAGE_CODE],
                        'alt'     => array_filter($locales, function ($l) {
                            return $l['code'] !== ICL_LANGUAGE_CODE;
                        }),
                    ];

                    return $data;
                });
            });
        }

        // ----
        // TWIG
        // ----
        // Twig locations
        Timber::$locations = [
            $theme_dir .'/src/views', // User templates
            __DIR__ .'/views',        // Ent templates
        ];

        // Visual composer templates
        if (function_exists('vc_map')) {
            Timber::$locations[] = $theme_dir .'/src/components/views';
            Timber::$locations[] = __DIR__ .'/VisualComposer/Component/views';
        }

        add_filter('get_twig', function ($twig) use ($assets) {
            $twig->addFunction(new \Twig_SimpleFunction('asset', function ($file) use ($assets) {
                return $assets->get($file);
            }));

            // TODO: Zertako dek hau?
            $twig->addFunction(new \Twig_SimpleFunction('get_permalink', function ($id) {
                if (function_exists('icl_object_id')) {
                    $id = apply_filters('wpml_object_id', $id);
                }

                return get_permalink($id);
            }));

            return $twig;
        });

        // ----
        // MISC
        // ----
        // Timezone
        date_default_timezone_set('Europe/Madrid');

        // Collapse this CF complex fields
        Helpers::cf_collapse_complex_fields('media');
        Helpers::cf_collapse_complex_fields('attachments');
        Helpers::cf_collapse_complex_fields('links');

        // Add mime types
        add_filter('upload_mimes', function ($mimes) {
            $mimes['svg'] = 'image/svg+xml';

            return $mimes;
        });

        // Timber post class map
        if ($opts['post_class_map']) {
            add_filter('Timber\PostClassMap', function () use ($opts) {
                return $opts['post_class_map'];
            });
        }

        /*
        // Remove default image sizes
        add_filter('intermediate_image_sizes_advanced', function ($sizes) {
            //unset( $sizes['thumbnail']);
            unset( $sizes['medium']);
            unset( $sizes['large']);

            return $sizes;
        });*/

        // Save Context for VC Components
        add_filter('timber/loader/render_data', function ($data) {
            // Only save context on first call per request
            // This should be the legit Timber::render
            if (is_null(\Ent::$context)) {
                \Ent::$context = $data;
            }

            return $data;
        }, 99999);

        add_action('after_setup_theme', function () {
            // Enable features from Soil when plugin is activated
            // https://roots.io/plugins/soil/
            add_theme_support('soil-clean-up');
            add_theme_support('soil-nice-search');
            add_theme_support('soil-jquery-cdn');
            add_theme_support('soil-relative-urls');
        });
    }

    public static function get_context() {
        return self::$context;
    }

    public static function handle_request($opts) {
        $context = Timber::get_context();
        $context['layout'] = isset($opts['layout']) ? $opts['layout'] : 'layout.twig';
        $context['layout_sidebar'] = isset($opts['layout_sidebar']) ? $opts['layout_sidebar'] : 'ent/layouts/sidebar.twig';

        if (is_home() || is_archive() || is_search()) {
            $context['posts'] =  new \Timber\PostQuery();

            if (is_home()) {
                $tpl = 'blog.twig';
            } else if (is_archive()) {
                $q = get_queried_object();

                if (is_category() || is_tag() || is_tax()) {
                    $context['type'] = 'term';
                    $context['term'] = Timber::get_term($q->term_taxonomy_id, $q->taxonomy);
                } else if (is_author()) {
                    $context['type'] = 'author';
                    $context['author'] = new Timber\User($q->ID);
                } else if (is_year()) {
                    $context['type'] = 'year';
                    $context['year'] = get_the_date(__('wp.blog.year_format'));
                } else if (is_month()) {
                    $context['type'] = 'month';
                    $context['month'] = get_the_date(__('wp.blog.month_format'));
                } else if (is_day()) {
                    $context['type'] = 'day';
                    $context['day'] = get_the_date(__('wp.blog.day_format'));
                } else {
                    $context['type'] = '';
                }

                $tpl = 'archive.twig';
            } else if (is_search()) {
                $tpl = 'search.twig';
            }
        } else if (is_single()) {
            $context['post'] = new Timber\Post();
            $tpl = 'post.twig';
        } else if (is_page()) {
            $context['page'] = new Timber\Post();
            $tpl = 'page.twig';
        } else if (is_404()) {
            $tpl = '404.twig';
        } else {
            // Catch-all
            $tpl = 'index.twig';
        }

        Timber::render([$tpl, 'ent/'. $tpl], $context);
    }
}
