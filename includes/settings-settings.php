<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CCBPress_Settings_Slack extends CCBPress_Settings {

    public function __construct() {
        add_action( 'admin_init', array( $this, 'initialize' ) );
        add_filter( 'ccbpress_settings_help_tabs', array( $this, 'help_tabs' ) );
    }

	/**
	 * Initialize the settings fields
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function initialize() {

        // First, we register a section. This is necessary since all future options must belong to one.
    	add_settings_section(
    		'ccbpress_settings_slack_section',
    		__( 'Slack Settings', 'ccbpress-slack' ),
    		array( $this, 'section_callback' ),
    		'ccbpress_settings_slack'
    	);

        // If the option does not exist, then add it
    	if ( false == get_option( 'ccbpress_slack' ) ) {
    		add_option( 'ccbpress_slack' );
    	}

    	// The Token field
    	add_settings_field(
    		'api_key',
    		'<strong>' . __('Token', 'ccbpress-slack') . '</strong>',
    		array( $this, 'input_callback' ),
    		'ccbpress_settings_slack',
    		'ccbpress_settings_slack_section',
    		array(
    			'field_id'  => 'token',
    			'page_id'   => 'ccbpress_slack',
                'size'      => 'regular',
    			'label'     => __('The token for your slash command.', 'ccbpress-slack'),
    		)
    	);

        // Finally, we register the fields with WordPress
    	register_setting(
    		'ccbpress_settings_slack',			// The group name of the settings being registered
    		'ccbpress_slack',			// The name of the set of options being registered
    		array( $this, 'sanitize_callback' )	// The name of the function responsible for validating the fields
    	);

    }

	/**
	 * The settings section callback
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function section_callback() {
        // do nothing
	}

	/**
	 * Sanitize our settings fields
	 *
	 * @since 1.0.0
	 *
	 * @param  array $input The input from our settings form
	 *
	 * @return array        The sanitized settings
	 */
	public function sanitize_callback( $input ) {

        // Define all of the variables that we'll be using
    	$output = array();

    	// Loop through each of the incoming options
    	foreach ( $input as $key => $value ) {

			// Strip all HTML and PHP tags and properly handle quoted strings
			$output[$key] = strip_tags( stripslashes( $input[$key] ) );

    	}

    	// Return the array
    	return $output;

    }

    public function help_tabs( $help_tabs ) {

		ob_start();
		?>
		Stuff goes here.
		<?php
		$content = ob_get_clean();

		$help_tabs[] = array(
			'id'		=> 'ccbpress-slack',
			'tab_id'	=> 'slack',
			'title'		=> __('Creating a Slash Command', 'ccbpress-slack'),
			'content'	=> $content,
		);

		return $help_tabs;

	}

}
new CCBPress_Settings_Slack();
