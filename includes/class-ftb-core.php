<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * FTB_Core
 * --------
 * Same detection logic as the original [badge_extension] shortcode:
 * reads the product's first downloadable file, extracts its
 * extension, and prints a small colored badge. Colors, labels,
 * dimensions and typography now come from Settings → File Type Badge.
 */
class FTB_Core {

    private static $instancia = null;

    public static function instancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    private function __construct() {
        add_shortcode('badge_extension', array($this, 'shortcode_badge'));
        add_action('wp_head', array($this, 'imprimir_css_dinamico'));
    }

    /**
     * Inspects a WooCommerce product's downloadable files and returns
     * an array with 'type' (pdf|xls|doc|default), 'label' and 'is_free'.
     * Returns null if the product has no downloadable file.
     */
    public function detectar_tipo_archivo($product) {
        if (!$product || !is_a($product, 'WC_Product') || !$product->is_downloadable()) {
            return null;
        }

        $downloads = $product->get_downloads();
        if (empty($downloads)) {
            return null;
        }

        $o = FTB_Opciones::instancia()->obtener_opciones();

        $first_file = reset($downloads);
        $file_url   = $first_file->get_file();
        $ext        = strtoupper(pathinfo(parse_url($file_url, PHP_URL_PATH), PATHINFO_EXTENSION));

        if (in_array($ext, array('DOC', 'DOCX'), true)) {
            $type  = 'doc';
            $label = $o['doc_label'];
        } elseif (in_array($ext, array('XLS', 'XLSX', 'CSV'), true)) {
            $type  = 'xls';
            $label = $o['xls_label'];
        } elseif ($ext === 'PDF') {
            $type  = 'pdf';
            $label = $o['pdf_label'];
        } else {
            $type  = 'default';
            $label = !empty($ext) ? $ext : 'FILE';
        }

        $price   = $product->get_price();
        $is_free = (floatval($price) === 0.0) ? 'free' : 'paid';

        return array(
            'type'    => $type,
            'label'   => $label,
            'is_free' => $is_free,
        );
    }

    // ---------------------------------------------------------
    // Shortcode: [badge_extension]
    // ---------------------------------------------------------
    public function shortcode_badge($atts) {
        global $product;

        $info = $this->detectar_tipo_archivo($product);
        if ($info === null) {
            return '';
        }

        return $this->render_badge_html($info);
    }

    /**
     * Reusable badge HTML — used by the shortcode and by the Elementor
     * widget so both render identically.
     */
    public function render_badge_html($info) {
        return sprintf(
            '<span class="ftb-badge ftb-%s" data-ftype="%s" data-fprice="%s">%s</span>',
            esc_attr($info['type']),
            esc_attr($info['type']),
            esc_attr($info['is_free']),
            esc_html($info['label'])
        );
    }

    // ---------------------------------------------------------
    // Dynamic CSS — generated from saved options
    // ---------------------------------------------------------
    public function imprimir_css_dinamico() {
        $o = FTB_Opciones::instancia()->obtener_opciones();
        ?>
        <style id="ftb-estilos-dinamicos">
            .ftb-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: <?php echo esc_html($o['ancho']); ?>px;
                height: <?php echo esc_html($o['alto']); ?>px;
                border-radius: <?php echo esc_html($o['radio_borde']); ?>px;
                color: <?php echo esc_html($o['color_texto']); ?> !important;
                font-weight: <?php echo esc_html($o['peso_fuente']); ?>;
                font-size: <?php echo esc_html($o['tamano_fuente']); ?>px;
                font-family: <?php echo esc_html($o['familia_fuente']); ?>;
                letter-spacing: 0.5px;
                text-transform: uppercase;
                box-sizing: border-box;
                line-height: 1;
            }
            .ftb-badge.ftb-pdf { background-color: <?php echo esc_html($o['pdf_color']); ?>; }
            .ftb-badge.ftb-xls { background-color: <?php echo esc_html($o['xls_color']); ?>; }
            .ftb-badge.ftb-doc { background-color: <?php echo esc_html($o['doc_color']); ?>; }
            .ftb-badge.ftb-default { background-color: <?php echo esc_html($o['otro_color']); ?>; }
        </style>
        <?php
    }
}
