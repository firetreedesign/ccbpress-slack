<?php
/**
 * Uninstall Slack for CCBPress
 *
 * @package		Slack for CCBPress
 * @subpackage	Uninstall
 * @copyright	Copyright (c) 2016, FireTree Design, LLC
 * @license		http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since		1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

$ccbpress_slack_options = get_option('ccbpress_slack', array() );

if ( isset( $ccbpress_slack_options['remove_data'] ) ) {

	// Delete the options
	delete_option( 'ccbpress_slack' );

}
