<?php
if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class FTB_Elementor_Widget extends Widget_Base {

    public function get_name() {
        return 'ftb-file-type-badge';
    }

    public function get_title() {
        return __('File Type Badge', 'file-type-badge');
    }

    public function get_icon() {
        return 'eicon-post-list';
    }

    public function get_categories() {
        return array('file-type-badge', 'woocommerce-elements', 'general');
    }

    public function get_keywords() {
        return array('woocommerce', 'product', 'download', 'file', 'badge', 'pdf', 'doc', 'xls');
    }

    protected function register_controls() {
        $this->start_controls_section(
            'seccion_info',
            array(
                'label' => __('Info', 'file-type-badge'),
            )
        );

        $this->add_control(
            'nota_ajustes',
            array(
                'type' => Controls_Manager::RAW_HTML,
                'raw'  => sprintf(
                    /* translators: %s: settings page URL */
                    __('This widget uses the [badge_extension] shortcode logic. It only shows a badge on downloadable WooCommerce products (in a product page or inside a product loop/archive). Customize labels, colors, and typography from <a href="%s" target="_blank">Settings → File Type Badge</a>.', 'file-type-badge'),
                    esc_url(admin_url('options-general.php?page=ftb-ajustes'))
                ),
                'content_classes' => 'elementor-descriptor',
            )
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'seccion_posicion',
            array(
                'label' => __('Position (optional)', 'file-type-badge'),
            )
        );

        $this->add_responsive_control(
            'posicion',
            array(
                'label'   => __('Wrapper alignment', 'file-type-badge'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => array(
                    'flex-start' => array(
                        'title' => __('Left', 'file-type-badge'),
                        'icon'  => 'eicon-h-align-left',
                    ),
                    'center' => array(
                        'title' => __('Center', 'file-type-badge'),
                        'icon'  => 'eicon-h-align-center',
                    ),
                    'flex-end' => array(
                        'title' => __('Right', 'file-type-badge'),
                        'icon'  => 'eicon-h-align-right',
                    ),
                ),
                'default'   => 'flex-start',
                'selectors' => array(
                    '{{WRAPPER}} .ftb-widget-wrapper' => 'display:flex; justify-content: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Resolves the current WooCommerce product: works both on a single
     * product page and inside a loop/archive (Elementor Loop Grid,
     * shop page, etc.) where the global $product changes per item.
     */
    private function resolver_producto() {
        global $product;

        if ($product && is_a($product, 'WC_Product')) {
            return $product;
        }

        $post_id = get_the_ID();
        if ($post_id && function_exists('wc_get_product')) {
            $posible = wc_get_product($post_id);
            if ($posible) {
                return $posible;
            }
        }

        return null;
    }

    protected function render() {
        if (!function_exists('wc_get_product')) {
            return;
        }

        $core     = FTB_Core::instancia();
        $producto = $this->resolver_producto();
        $info     = $core->detectar_tipo_archivo($producto);

        if ($info === null) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<div class="ftb-widget-wrapper"><div style="padding:12px;border:1px dashed #999;text-align:center;color:#666;font-size:13px;">';
                echo esc_html__('Preview: this badge only appears on a downloadable WooCommerce product (e.g. PDF, DOCX, XLSX).', 'file-type-badge');
                echo '</div></div>';
            }
            return;
        }

        printf('<div class="ftb-widget-wrapper">%s</div>', $core->render_badge_html($info));
    }

    protected function content_template() {
        ?>
        <div class="ftb-widget-wrapper">
            <div style="padding:12px;border:1px dashed #999;text-align:center;color:#666;font-size:13px;">
                <?php esc_html_e('Preview: this badge only appears on a downloadable WooCommerce product (e.g. PDF, DOCX, XLSX).', 'file-type-badge'); ?>
            </div>
        </div>
        <?php
    }
}
