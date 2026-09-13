<?php
/**
 * Plugin Name: File Type Badge (WooCommerce)
 * Description: Detects the downloadable file type (PDF, DOC, XLS, etc.) of a WooCommerce virtual/downloadable product and displays a small customizable badge — available as a shortcode and as an Elementor widget.
 * Version: 1.0.0
 * Author: Fran Velazco
 * Author URI: https://www.linkedin.com/in/fran-velazco/
 * Text Domain: file-type-badge
 * Requires Plugins: woocommerce
 * License: GPLv2
 */

if (!defined('ABSPATH')) {
    exit; // Direct access not allowed.
}

define('FTB_PLUGIN_FILE', __FILE__);
define('FTB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('FTB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FTB_VERSION', '1.0.0');

require_once FTB_PLUGIN_DIR . 'includes/class-ftb-opciones.php';
require_once FTB_PLUGIN_DIR . 'includes/class-ftb-core.php';
require_once FTB_PLUGIN_DIR . 'includes/class-ftb-elementor.php';

// Notice if WooCommerce is not active — the plugin won't break the site,
// it simply won't display anything, but we notify in admin.
add_action('admin_notices', function () {
    if (!function_exists('wc_get_product') && current_user_can('activate_plugins')) {
        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('The "File Type Badge" plugin requires WooCommerce to be active to work.', 'file-type-badge');
        echo '</p></div>';
    }
});

add_action('plugins_loaded', function () {
    FTB_Opciones::instancia();
    FTB_Core::instancia();
    FTB_Elementor::instancia();
});
