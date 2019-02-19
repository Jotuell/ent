<?php
namespace Ent\VisualComposer\Traits;

trait RenderComponent {
    protected function getContextData(array $atts) {
        return [];
    }

    protected function content($atts, $content = null) {
        $definition = \WPBMap::getShortCode($this->shortcode);
        $reflector = new \ReflectionClass(get_class($this));
        $file = basename($reflector->getFileName(), '.php');
        $path = dirname($reflector->getFileName());
        $atts = array_merge(
            vc_map_get_attributes($this->getShortcode(), $atts),
            ['content' => do_shortcode($content)]
        );

        // Process VC attributes
        foreach ($definition['params'] as $param) {
            if ($param['type'] == 'vc_link' &&
                array_key_exists($param['param_name'], $atts) &&
                $atts[$param['param_name']] !== ''
            ) {
                $atts[$param['param_name']] = vc_build_link($atts[$param['param_name']]);
            }

            // Convert image attributes to Timber_Image type
            if ($param['type'] == 'attach_image' &&
                array_key_exists($param['param_name'], $atts) &&
                is_numeric($atts[$param['param_name']])
            ) {
                $atts[$param['param_name']] = new \TimberImage($atts[$param['param_name']]);
            }
        }

        // Inject context
        $context = \Ent::get_context();
        $atts['context'] = $context;

        // Add extra context data
        $atts = array_merge($atts, $this->getContextData($atts));
        
        ob_start();
        \Timber::render($file .'.twig', $atts);

        return ob_get_clean();
    }
}
