<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * FTB_Elementor
 * -------------
 * Registers the "File Type Badge" widget for Elementor, only if
 * Elementor is active. Does nothing if it isn't.
 */
class FTB_Elementor {

    private static $instancia = null;

    public static function instancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    private function __construct() {
        add_action('elementor/widgets/register', array($this, 'registrar_widget'));
        add_action('elementor/elements/categories_registered', array($this, 'registrar_categoria'));
    }

    public function registrar_categoria($elements_manager) {
        $elements_manager->add_category('file-type-badge', array(
            'title' => __('File Type Badge', 'file-type-badge'),
            'icon'  => 'fa fa-file',
        ));
    }

    public function registrar_widget($widgets_manager) {
        if (!did_action('elementor/loaded')) {
            return;
        }

        require_once FTB_PLUGIN_DIR . 'includes/class-ftb-elementor-widget.php';
        $widgets_manager->register(new \FTB_Elementor_Widget());
    }
}
