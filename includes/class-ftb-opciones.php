<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * FTB_Opciones
 * ------------
 * Stores and exposes all customization options for the file type
 * badge (label text and colors per file type, badge dimensions,
 * typography), and registers the settings page under
 * Settings → File Type Badge.
 */
class FTB_Opciones {

    private static $instancia = null;
    const OPTION_KEY = 'ftb_opciones';

    public static function instancia() {
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'registrar_pagina'));
        add_action('admin_init', array($this, 'registrar_ajustes'));
    }

    /**
     * Default values — match the original badge colors/labels exactly,
     * so activating the plugin doesn't change anything visually until
     * the user edits something.
     */
    public static function defaults() {
        return array(
            // Shape / typography (shared by all badges)
            'ancho'          => 40,
            'alto'           => 40,
            'radio_borde'    => 8,
            'tamano_fuente'  => 14,
            'familia_fuente' => 'system-ui, -apple-system, sans-serif',
            'peso_fuente'    => 800,
            'color_texto'    => '#ffffff',

            // Per file-type label + color
            'pdf_label'      => 'PDF',
            'pdf_color'      => '#c83727',
            'xls_label'      => 'XLS',
            'xls_color'      => '#1e7e34',
            'doc_label'      => 'DOC',
            'doc_color'      => '#1a56a6',
            'otro_color'     => '#6c757d',
        );
    }

    public function obtener_opciones() {
        $guardadas = get_option(self::OPTION_KEY, array());
        return wp_parse_args($guardadas, self::defaults());
    }

    // ---------------------------------------------------------
    // Settings → File Type Badge
    // ---------------------------------------------------------
    public function registrar_pagina() {
        add_options_page(
            __('File Type Badge', 'file-type-badge'),
            __('File Type Badge', 'file-type-badge'),
            'manage_options',
            'ftb-ajustes',
            array($this, 'render_pagina')
        );
    }

    public function registrar_ajustes() {
        register_setting('ftb_grupo_opciones', self::OPTION_KEY, array(
            'sanitize_callback' => array($this, 'sanitizar_opciones'),
        ));
    }

    public function sanitizar_opciones($input) {
        $defaults = self::defaults();
        $limpio   = array();

        foreach ($defaults as $clave => $valor_defecto) {
            if (!isset($input[$clave])) {
                $limpio[$clave] = is_numeric($valor_defecto) ? 0 : '';
                continue;
            }

            $valor = $input[$clave];

            if (strpos($clave, 'color') !== false) {
                $limpio[$clave] = sanitize_hex_color($valor) ? $valor : $valor_defecto;
            } elseif (in_array($clave, array('pdf_label', 'xls_label', 'doc_label', 'familia_fuente'), true)) {
                $limpio[$clave] = sanitize_text_field($valor);
            } else {
                $limpio[$clave] = is_numeric($valor) ? floatval($valor) : $valor_defecto;
            }
        }

        return $limpio;
    }

    public function render_pagina() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $o = $this->obtener_opciones();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('File Type Badge — Settings', 'file-type-badge'); ?></h1>
            <p><?php esc_html_e('Customize the badge label, colors, dimensions, and typography for each downloadable file type. Use the [badge_extension] shortcode on a WooCommerce product page/loop.', 'file-type-badge'); ?></p>

            <form method="post" action="options.php">
                <?php settings_fields('ftb_grupo_opciones'); ?>

                <h2 class="title"><?php esc_html_e('Badge shape & typography', 'file-type-badge'); ?></h2>
                <table class="form-table" role="presentation">
                    <?php $this->fila_numero('ancho', __('Width (px)', 'file-type-badge'), $o, 16, 200); ?>
                    <?php $this->fila_numero('alto', __('Height (px)', 'file-type-badge'), $o, 16, 200); ?>
                    <?php $this->fila_numero('radio_borde', __('Border radius (px)', 'file-type-badge'), $o, 0, 999); ?>
                    <?php $this->fila_numero('tamano_fuente', __('Font size (px)', 'file-type-badge'), $o, 8, 72); ?>
                    <?php $this->fila_texto('familia_fuente', __('Font family (CSS font-family)', 'file-type-badge'), $o); ?>
                    <?php $this->fila_numero('peso_fuente', __('Font weight (100–900)', 'file-type-badge'), $o, 100, 900); ?>
                    <?php $this->fila_color('color_texto', __('Text color', 'file-type-badge'), $o); ?>
                </table>

                <h2 class="title"><?php esc_html_e('PDF files', 'file-type-badge'); ?></h2>
                <table class="form-table" role="presentation">
                    <?php $this->fila_texto('pdf_label', __('Label', 'file-type-badge'), $o); ?>
                    <?php $this->fila_color('pdf_color', __('Background color', 'file-type-badge'), $o); ?>
                </table>

                <h2 class="title"><?php esc_html_e('Excel files (XLS, XLSX, CSV)', 'file-type-badge'); ?></h2>
                <table class="form-table" role="presentation">
                    <?php $this->fila_texto('xls_label', __('Label', 'file-type-badge'), $o); ?>
                    <?php $this->fila_color('xls_color', __('Background color', 'file-type-badge'), $o); ?>
                </table>

                <h2 class="title"><?php esc_html_e('Word files (DOC, DOCX)', 'file-type-badge'); ?></h2>
                <table class="form-table" role="presentation">
                    <?php $this->fila_texto('doc_label', __('Label', 'file-type-badge'), $o); ?>
                    <?php $this->fila_color('doc_color', __('Background color', 'file-type-badge'), $o); ?>
                </table>

                <h2 class="title"><?php esc_html_e('Other file types', 'file-type-badge'); ?></h2>
                <table class="form-table" role="presentation">
                    <?php $this->fila_color('otro_color', __('Background color', 'file-type-badge'), $o); ?>
                    <tr>
                        <th scope="row"><?php esc_html_e('Label', 'file-type-badge'); ?></th>
                        <td><p class="description"><?php esc_html_e('The label for other file types is the file extension itself (e.g. ZIP, MP3), detected automatically.', 'file-type-badge'); ?></p></td>
                    </tr>
                </table>

                <?php submit_button(__('Save changes', 'file-type-badge')); ?>
            </form>
        </div>
        <?php
    }

    private function nombre_campo($clave) {
        return self::OPTION_KEY . '[' . $clave . ']';
    }

    private function fila_color($clave, $label, $o) {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($clave); ?>"><?php echo esc_html($label); ?></label></th>
            <td><input type="color" id="<?php echo esc_attr($clave); ?>" name="<?php echo esc_attr($this->nombre_campo($clave)); ?>" value="<?php echo esc_attr($o[$clave]); ?>"></td>
        </tr>
        <?php
    }

    private function fila_numero($clave, $label, $o, $min = 0, $max = 999) {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($clave); ?>"><?php echo esc_html($label); ?></label></th>
            <td><input type="number" min="<?php echo esc_attr($min); ?>" max="<?php echo esc_attr($max); ?>" id="<?php echo esc_attr($clave); ?>" name="<?php echo esc_attr($this->nombre_campo($clave)); ?>" value="<?php echo esc_attr($o[$clave]); ?>" class="small-text"></td>
        </tr>
        <?php
    }

    private function fila_texto($clave, $label, $o) {
        ?>
        <tr>
            <th scope="row"><label for="<?php echo esc_attr($clave); ?>"><?php echo esc_html($label); ?></label></th>
            <td><input type="text" id="<?php echo esc_attr($clave); ?>" name="<?php echo esc_attr($this->nombre_campo($clave)); ?>" value="<?php echo esc_attr($o[$clave]); ?>" class="regular-text"></td>
        </tr>
        <?php
    }
}
