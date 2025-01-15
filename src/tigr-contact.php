<?php
/**
 * Plugin Name:       Tiger Grades Contact
 * Plugin URI:        https://github.com/spenpo-freelance/tigr-contact
 * Description:       contact form
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Version:           1.0.0
 * Author:            spenpo
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tigr-contact
 *
 * @package tigr-contact
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants - only if not already defined (for testing compatibility)
if (!defined('TIGR_PATH')) {
    define('TIGR_PATH', plugin_dir_path(__FILE__));
}
if (!defined('TIGR_URL')) {
    define('TIGR_URL', plugin_dir_url(__FILE__));
}

// Load dependencies
require_once TIGR_PATH . 'includes/shortcodes/TigrContactShortcode.php';

// Register styles
function tigr_enqueue_styles() {
    wp_enqueue_style(
        'tigr-styles',
        plugins_url('style.css', __FILE__),
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'tigr_enqueue_styles');
