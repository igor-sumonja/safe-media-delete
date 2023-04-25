<?php
/**
Plugin Name: Safe Media Delete
Description: Safe Media Delete
Author: Igor Sumonja
Version: 1.0.0
License:  GPL v2 or later
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: safe-media-delete
 *
 * @package SafeMD
 */

namespace SafeMD;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define plugin constant.
 */
define( 'SAFE_MD_VERSION', '1.0.0' );
define( 'SAFE_MD_SLUG', 'safe-media-delete' );
define( 'SAFE_MD_DIR', plugin_dir_path( __FILE__ ) );
define( 'SAFE_MD_URL', plugin_dir_url( __FILE__ ) );


/**
 * Autoload classes for plugin.
 */
require SAFE_MD_DIR . 'vendor/autoload.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_safe_md(): void {
	$plugin = new SafeMediaDelete();
	$plugin->run();
}
run_safe_md();
