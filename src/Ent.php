<?php
namespace Ent;

use Carbon_Fields\Carbon_Fields;
use Symfony\Component\Translation;
use Timber;

class Ent {
    protected static $context = null;
    protected $theme_dir;
    protected $opts;
    protected $assets;

    public function __construct($opts) {
        // Create alias so that Ent class can be accessed typing `Ent::` without namespace backslash
        class_alias(get_class($this), 'Ent');

        $this->opts = $opts;
        $this->theme_dir = $opts['theme_dir'];
        $this->assets = new \Ent\AssetsJSON(get_template_directory_uri());

        $this->initTimber();
        $this->setupTheme();
        $this->setupCarbonFields();
        $this->requireFiles();
        $this->setupGoogleMaps();
        $this->registerMenusAndSidebars();
        $this->addMenusAndSidebarsToContext();
        $this->setupInternationalization();
        $this->setupVisualComposer();
        $this->setupGutenberg();
        $this->setupTwig();
        $this->addOptionsToContext();


    }

    // ----
    // THEME
    // ----
    private function setupTheme(){
        // Timezone
        date_default_timezone_set('Europe/Madrid');

        // Add mime types
        add_filter('upload_mimes', function ($mimes) {
            $mimes['svg'] = 'image/svg+xml';

            return $mimes;
        });
    }

    // -------------
    // TIMBER
    // -------------
    private function initTimber(){
        $timber = new Timber\Timber();

        // Twig locations
        Timber::$locations = [
            $this->theme_dir .'/src/views', // User templates
            __DIR__ .'/views',        // Ent templates
        ];

        // Visual composer twig templates
        if (function_exists('vc_map')) {
            Timber::$locations[] = $this->theme_dir .'/src/components/views';
            Timber::$locations[] = __DIR__ .'/VisualComposer/Component/views';
        }

        // Timber post class map
        if ($this->opts['post_class_map']) {
            $map = $this->opts['post_class_map'];
            add_filter('Timber\PostClassMap', function () use ($map) {
                return $map;
            });
        }
    }

    // -------------
    // REQUIRE FILES
    // -------------
    private function requireFiles() {
        require_once $this->theme_dir .'/src/routes.php';
        require_once $this->theme_dir .'/src/theme.php';

        foreach (['/src/pages', '/src/post-types', '/src/taxonomies'] as $folder) {
            foreach (glob($this->theme_dir . $folder .'/*.php') as $filename) {
                require_once $filename;
            }
        }

    }

    // -----------
    // CARBON FIELDS
    // -----------
    private function setupCarbonFields() {
        //Init Carbon Fields
        Carbon_Fields::boot();
        // Collapse this CF complex fields
        Helpers::cf_collapse_complex_fields('media');
        Helpers::cf_collapse_complex_fields('attachments');
        Helpers::cf_collapse_complex_fields('links');
    }

    // ----------------
    // MENUS & SIDEBARS
    // ----------------

    // Registration
    public function registerMenusAndSidebars() {
        $menus = $this->opts['menus'];
        $sidebars = $this->opts['sidebars'];
        add_action( 'after_setup_theme', function () use ($menus, $sidebars) {
            if ($menus) {
                register_nav_menus($menus);
            }

            if ($sidebars) {
                foreach ($sidebars as $id => $name) {
                    register_sidebar([
                        'name'          => $name,
                        'id'            => $id,
                        'before_widget' => '<div class="apo-widget">',
                        'after_widget'  => '</div>',
                        'before_title'  => '<h4 class="apo-widget__title">',
                        'after_title'   => '</h4>',
                    ]);
                }
            }
        } );

    }

    // Add to timber context
    public function addMenusAndSidebarsToContext(){
        $menus = $this->opts['menus'];
        $sidebars = $this->opts['sidebars'];
        add_filter( 'timber/context', function ($data) use ($menus, $sidebars) {
            if ($menus) {
                foreach ($menus as $id => $name) {
                    $data[$id] = new \TimberMenu($id);
                }
            }

            if ($sidebars) {
                foreach ($sidebars as $id => $name) {
                    $data[$id] = \Timber::get_widgets($id);
                }
            }

            return $data;
        } );
    }


    // ----------------
    // THEME OPTIONS
    // ----------------

    public function addOptionsToContext() {
        add_filter( 'timber/context', function ($data) {
    		$data['relevant_pages'] = [
    			'contact' 		=> carbon_get_theme_option('contact_page'),
    			'legal_notice' 	=> carbon_get_theme_option('legal_notice_page'),
    			'cookies' 		=> carbon_get_theme_option('cookies_page'),
    			'lopd' 			=> carbon_get_theme_option('lopd_page'),
    		];
    		$data['contact_info'] = [
    			'phone' 	=> carbon_get_theme_option('phone'),
    			'email' 	=> carbon_get_theme_option('email'),
    		];
    		$data['social_links'] = [
    			'facebook' 	=> carbon_get_theme_option('facebook_link'),
    			'twitter' 	=> carbon_get_theme_option('twitter_link'),
    			'instagram' => carbon_get_theme_option('instagram_link'),
    			'linkedin' 	=> carbon_get_theme_option('linkedin_link'),
    		];
    		return $data;
        });
	}

    // -----------
    // GOOGLE MAPS
    // -----------
    private function setupGoogleMaps() {
        $api_key = $this->opts['google_maps_api_key'];
        if ($api_key) {
            add_filter('get_twig', function ($twig) use ($api_key) {
                $twig->addGlobal('google_maps_api_key', $api_key);

                return $twig;
            });

            add_action('carbon_fields_map_field_api_key', function ($current_key) use ($api_key) {
                return $api_key;
            });
        }
    }

    // ----
    // i18n
    // ----
    private function setupInternationalization(){
        if (!defined('ICL_LANGUAGE_CODE'))
            return;

        $theme_dir = $this->theme_dir;
        add_action('wp', function () use ($theme_dir) {
            global $sitepress;

            // Get data from WPML
            $default_locale = $sitepress->get_default_language();
            $locales = icl_get_languages('skip_missing=1');

            // Init Symfony Translation component and load resources
            $translator = new Translation\Translator(ICL_LANGUAGE_CODE);
            $translator->setFallbackLocales([$default_locale]);
            $translator->addLoader('yaml', new Translation\Loader\YamlFileLoader());
            $translator->addResource('yaml', $theme_dir .'/src/locales/'. ICL_LANGUAGE_CODE .'.yml', ICL_LANGUAGE_CODE);

            // Load also the default locale if we're not in the default one
            if (ICL_LANGUAGE_CODE != $default_locale) {
                $translator->addResource('yaml', $theme_dir .'/src/locales/'. $default_locale .'.yml', $default_locale);
            }

            // WordPress integration
            add_filter('gettext', function ($str, $str_key, $domain) use ($translator) {
                if (($domain == 'apostrof' || $domain == 'default') && $str == $str_key) {
                    $str = $translator->trans($str_key);
                }

                return $str;
            }, 20, 3);

            // Load locales in Timber
            if (!empty($locales)) {
                add_filter('timber/context', function ($data) use ($locales) {
                    $data['locales'] = [
                        'current' => $locales[ICL_LANGUAGE_CODE],
                        'alt'     => array_filter($locales, function ($l) {
                            return $l['code'] !== ICL_LANGUAGE_CODE;
                        }),
                    ];

                    return $data;
                });
            }
        });
    }

    // ---------------
    // VISUAL COMPOSER
    // ---------------
    private function setupVisualComposer() {
        if (function_exists('vc_map')) {
            $vc = new \Ent\VisualComposer($this->theme_dir .'/src/components');

            add_action('vc_before_init', function () {
                vc_set_default_editor_post_types(\Ent\Helpers::$vc_enabled_cpt);
            });

            // Save Context for VC Components
            add_filter('timber/loader/render_data', function ($data) {
                // Only save context on first call per request
                // This should be the legit Timber::render
                if (is_null(\Ent::$context)) {
                    \Ent::$context = $data;
                }

                return $data;
            }, 99999);
        }
    }

    // ---------------
    // GUTENBERG
    // ---------------
    private function setupGutenberg(){
        add_filter( 'gutenberg_can_edit_post_type', array($this, 'disableGutenberg'), 10, 2 );
        add_filter( 'use_block_editor_for_post_type', array($this, 'disableGutenberg'), 10, 2 );
    }
    public function disableGutenberg($can_edit, $post_type) {
        if (in_array($post_type, \Ent\Helpers::$gutenberg_enabled_cpt)) {
            $can_edit = true;
        }
        $can_edit = false;
        return $can_edit;
    }

    // ---------------
    // TWIG
    // ---------------
    private function setupTwig(){

        $assets = $this->assets;
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
