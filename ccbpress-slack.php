<?php
/**
 * Plugin Name: Slack for CCBPress
 * Plugin URI: http://ccbpress.com/
 * Description: Retrieve data from CCB into Slack.
 * Version: 1.0.0
 * Author: FireTree Design, LLC <info@firetreedesign.com>
 * Author URI: https://firetreedesign.com/
 * Text Domain: ccbpress-slack
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'CCBPress_Slack' ) ) :

/**
 * CCBPress Slack class
 */
class CCBPress_Slack {

    /**
     * @var CCBPress_Slack The one true CCBPress_Slack
     *
     * @since 1.0.0
     */
    private static $instance;

    /**
     * Main CCBPress_Slack Instance
     *
     * Insures that only one instance of CCBPress_Slack exists in memory at any
     * one time.
     *
     * @since 1.0
     * @static
     * @staticvar array $instance
     * @uses CCBPress_Slack::includes() Include the required files
     * @see CCBPress_Slack()
     * @return The one true CCBPress_Slack
     */
    public static function instance() {

         if ( ! isset( self::$instance ) && ! ( self::$instance instanceof CCBPress_Slack ) ) {

             self::$instance = new CCBPress_Slack;
             self::$instance->setup_constants();
			 self::$instance->register_addon();
             self::$instance->includes();

         }

         return self::$instance;

    }

     /**
      * Setup plugin constants
      *
      * @access private
      *
      * @since 1.0.0
      *
      * @return void
      */
     private function setup_constants() {

         // Plugin File
         if ( ! defined( 'CCBPRESS_SLACK_PLUGIN_FILE' ) ) {
             define( 'CCBPRESS_SLACK_PLUGIN_FILE', __FILE__ );
         }

         // Plugin Folder Path
         if ( ! defined( 'CCBPRESS_SLACK_PLUGIN_DIR' ) ) {
             define( 'CCBPRESS_SLACK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
         }

         // Plugin Folder URL
		if ( ! defined( 'CCBPRESS_SLACK_PLUGIN_URL' ) ) {
			define( 'CCBPRESS_SLACK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

     }

	 /**
	  * Register the addon with CCBPress Core
	  *
	  * @since 1.0.0
	  *
	  * @return void
	  */
	 private function register_addon() {

		$addon = new CCBPress_Addon( array(
			'services' => array(
				'individual_search',
			),
		) );

		$options = new CCBPress_Options( array(
			'settings' => array(
				'tabs' => array(
					array(
						'tab_id'		=> 'slack',
						'settings_id'	=> 'ccbpress_settings_slack',
						'title'			=> __('Slack', 'ccbpress-slack'),
						'submit'		=> TRUE,
					),
				),
                'actions' => array(
					array(
						'tab_id'		=> 'slack',
						'type'			=> 'secondary',
						'class'			=> 'ccbpress-slack-help',
						'link'			=> '#',
						'target'		=> NULL,
						'title'			=> '<span class="dashicons dashicons-info" style="vertical-align: text-bottom;"></span> ' . __('How to get a token', 'ccbpress-slack'),
					),
				),
			),
		) );

	 }

     /**
      * Include required files
      *
      * @access private
      *
      * @since 1.0
      *
      * @return void
      */
     private function includes() {

		require_once CCBPRESS_SLACK_PLUGIN_DIR . 'includes/ccb-connection.php';
		require_once CCBPRESS_SLACK_PLUGIN_DIR . 'includes/settings-settings.php';
		require_once CCBPRESS_SLACK_PLUGIN_DIR . 'includes/wp-rest-api.php';
        require_once CCBPRESS_SLACK_PLUGIN_DIR . 'includes/admin-scripts.php';

     }

}

endif; // End if class_exists check

/**
 * Initialize the CCBPress_Slack class
 *
 * @since 1.0.0
 *
 * @return void
 */
 function CCBPress_Slack() {
	 if ( class_exists( 'CCBPress_Core' ) ) {
		 return CCBPress_Slack::instance();
	 } else {
		 add_action( 'admin_init', 'CCBPress_Slack_Deactivate' );
		 add_action( 'admin_notices', 'CCBPress_Slack_Deactivate_Notice' );
	 }
 }
add_action( 'plugins_loaded', 'CCBPress_Slack');

/**
 * Deactivate our plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function CCBPress_Slack_Deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Show a notice explaining why our plugin was deactivated
 *
 * @since 1.0.0
 *
 * @return void
 */
function CCBPress_Slack_Deactivate_Notice() {
	echo '<div class="updated"><p><strong>Slack for CCBPress</strong> requires <strong>CCBPress Core</strong> be installed and activated; the plug-in has been <strong>deactivated</strong>.</p></div>';
	if ( isset( $_GET['activate'] ) )
		unset( $_GET['activate'] );
}
