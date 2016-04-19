<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CCBPress Slack REST API class
 *
 * @since 1.0.0
 */
class CCBPress_Slack_REST_API {

	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	/**
	 * Initialize the endpoint
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function rest_api_init() {

		register_rest_route( 'ccbpress-slack/v1', '/slash', array(
	        'methods' => 'POST',
	        'callback' => array( $this, 'slash_commands' ),
	    ) );

	}

	public function slash_commands( WP_REST_Request $request ) {

		$slack_options = get_option( 'ccbpress_slack', array() );
		$slack_token = false;
		if ( isset( $slack_options['token'] ) ) {
			$slack_token = $slack_options['token'];
		}

		if ( ! $slack_token || $request->get_param('token') != $slack_token ) {
			return array(
				"response_type"	=> "ephemeral",
				"attachments"	=> array(
					array(
						"text"	=> "Invalid token",
						"color"	=> "danger"
					)
				)
			);
		}

		$text = trim( $request->get_param('text') );
		$space = strpos( $text, " ");

		if ( $space ) {
			$command = substr( $text, 0, $space );
		} else {
			$command = $text;
		}
		$command = strtolower( $command );

		if ( $space ) {
			$arg = substr( $text, $space, strlen( $text ) );
		}

		switch ( $command ) {
			case "help":
				$response = $this->command_help();
				break;
			case "whois":
				$response = $this->command_whois( $arg );
				break;
			default:
				$response = array(
					"response_type"	=> "ephemeral",
					"attachments"	=> array(
						array(
							"text"	=> "I'm not sure what you want.\nTry typing `help` for available commands.",
							"color"	=> "warning"
						)
					)
				);
				break;
		}

		return $response;

	}

	private function command_help() {
		return array(
			"response_type" => "ephemeral",
			"text" => "*Available commands*",
			"attachments" => array(
				array(
					"title" => "whois [name]",
					"text" => "Search for a person. Use a * as a wildcard for a first name"
				)
			)
		);
	}

	private function command_whois( $name ) {

		$parts = explode( " ", trim( $name ) );
		$first_name = $parts[0];
		if ( $first_name == "*" ) {
			$first_name = null;
		}
		$last_name = null;
		if ( count( $parts ) > 1 ) {
			$last_name = $parts[1];
		}

		// Setup our connection to CCB
		$ccb = new CCBPress_Slack_CCB_Connection();
		// Retrive the event profile
		$ccb_data = $ccb->individual_search( array(
			'first_name'	=> $first_name,
			'last_name'		=> $last_name,
			'max_results'	=> 20
		) );

		// Check if the data is valid
		if ( $ccb_data ) {

			$count = $ccb_data->response->individuals['count'];

			if ( '0' == $count ) {

				return array(
					"response_type"	=> "ephemeral",
					"text"			=> "Nobody found"
				);

			}

			$what = "person";
			if ( (int)$count > 1 ) {
				$what = "people";
			}

			$attachments = array();

			foreach( $ccb_data->response->individuals->individual as $individual ) {

				$individual_full_name = $individual->full_name;
				$individual_phone = "Not available";
				if ( isset( $individual->phones->phone[0] ) && strlen( $individual->phones->phone[0] ) > 0 ) {
					$individual_phone = $individual->phones->phone[0];
				}
				$individual_email = "Not available";
				if ( isset( $individual->email ) && strlen( $individual->email ) > 0 ) {
					$individual_email = $individual->email;
				}
				$individual_address = "Not available";
				if ( isset( $individual->addresses->address[0]->line_1 ) && isset( $individual->addresses->address[0]->line_2 ) ) {
					$individual_address = '';
					$line_1 = $individual->addresses->address[0]->line_1;
					$line_2 = $individual->addresses->address[0]->line_2;
					if ( strlen( $line_1 ) > 0 ) {
						$individual_address = $line_1;
					}
					if ( strlen( $line_2 ) > 0 ) {
						$individual_address .= ", " . $line_2;
					}
				}

				$person = array(
					"title" => (string)$individual_full_name,
					"color" => "good",
					"fields" => array(
						array(
							"title" => "Phone",
							"value" => (string)$individual_phone,
							"short" => true
						),
						array(
							"title" => "Email",
							"value" => (string)$individual_email,
							"short" => true
						),
						array(
							"title"	=> "Address",
							"value"	=> (string)$individual_address,
							"short"	=> false
						)
					)
				);

				$attachments[] = $person;

			}

			return array(
				"response_type" => "ephemeral",
				"text" => "Found " . $count . " " . $what,
				"attachments" => $attachments
			);

		} else {
			return array(
					"response_type"	=> "ephemeral",
					"text"			=> "There was an error"
				);
		}

	}

}
new CCBPress_Slack_REST_API();
