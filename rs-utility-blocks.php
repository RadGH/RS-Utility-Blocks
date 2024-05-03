<?php
/*
Plugin Name: RS Utility Blocks
Description: Adds custom blocks and utilities to the block editor, including visibility conditions and blocks to display the current user's information or current post's information, and a login form block.
Version: 1.2.8
Author: Radley Sustaire
Author URI: https://radleysustaire.com
GitHub Plugin URI: https://github.com/RadGH/RS-Utility-Blocks
*/

define( 'RS_Utility_Blocks_PATH', __DIR__ );
define( 'RS_Utility_Blocks_URL', plugin_dir_url(__FILE__) );
define( 'RS_Utility_Blocks_VERSION', '1.2.8' );

class RS_Utility_Blocks {
	
	/**
	 * Checks that required plugins are loaded before continuing
	 *
	 * @return void
	 */
	public static function load_plugin() {
		// 1. Check for required plugins
		$missing_plugins = array();
		
		if ( ! class_exists('ACF') ) {
			$missing_plugins[] = 'Advanced Custom Fields Pro';
		}
		
		// Show error on the dashboard if any plugins are missing
		if ( $missing_plugins ) {
			self::add_admin_notice( '<strong>RS Utility Blocks:</strong> The following plugins are required: '. implode(', ', $missing_plugins) . '.', 'error' );
			return;
		}
		
		// 2. Load ACF fields
		require_once( RS_Utility_Blocks_PATH . '/assets/acf-fields.php' );
		
		// 3. Load plugin files
		require_once( RS_Utility_Blocks_PATH . '/includes/setup.php' );
		require_once( RS_Utility_Blocks_PATH . '/includes/profile.php' );
		
	}
	
	/**
	 * Adds an admin notice to the dashboard's "admin_notices" hook.
	 *
	 * @param string $message The message to display
	 * @param string $type    The type of notice: info, error, warning, or success. Default is "info"
	 * @param bool $format    Whether to format the message with wpautop()
	 *
	 * @return void
	 */
	public static function add_admin_notice( $message, $type = 'info', $format = true ) {
		add_action( 'admin_notices', function() use ( $message, $type, $format ) {
			?>
			<div class="notice notice-<?php echo $type; ?> rs-utility-blocks-notice">
				<?php echo $format ? wpautop($message) : $message; ?>
			</div>
			<?php
		});
	}
	
}

// Initialize the plugin
add_action( 'plugins_loaded', array('RS_Utility_Blocks', 'load_plugin') );