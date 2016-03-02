<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class CCBPress_Slack_CCB_Connection {
	
	/**
	 * Searches for an individual.
	 *
	 * @param	array	$args	An array of the arguments.
	 *
	 * @return	string	An XML string containing the data.
	 */
	public function individual_search( $args ) {
		
		$defaults = array(
			'first_name'		=> NULL,		// First name.
			'last_name'			=> NULL,		// Last name.
			'phone'				=> NULL,		// Phone number.
			'email'				=> NULL,		// Email address.
			'street_address'	=> NULL,		// Street address.
			'city'				=> NULL,		// City.
			'state'				=> NULL,		// State.
			'zip'				=> NULL,		// Zip code.
			'include_inactive'	=> FALSE,		// TRUE/FALSE, Include inactive individuals.
			'max_results'		=> 99999999,	// Maximum number of results.
			'describe_api'		=> NULL,		// Optional. 1 = Yes.
			'cache_lifespan'	=> CCBPress()->ccb->cache_lifespan( 'individual_search' ),	// In minutes.
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		try {
				
			// Retrieve the data from CCB
			$url = add_query_arg( 'srv', 'individual_search', CCBPress()->ccb->api_url );
			
			if ( $args['first_name'] ) { $url = add_query_arg( 'first_name', $args['first_name'], $url ); }
			if ( $args['last_name'] ) { $url = add_query_arg( 'last_name', $args['last_name'], $url ); }
			if ( $args['phone'] ) { $url = add_query_arg( 'phone', $args['phone'], $url ); }
			if ( $args['email'] ) { $url = add_query_arg( 'email', $args['email'], $url ); }
			if ( $args['street_address'] ) { $url = add_query_arg( 'street_address', $args['street_address'], $url ); }
			if ( $args['city'] ) { $url = add_query_arg( 'city', $args['city'], $url ); }
			if ( $args['state'] ) { $url = add_query_arg( 'state', $args['state'], $url ); }
			if ( $args['zip'] ) { $url = add_query_arg( 'zip', $args['zip'], $url ); }
			if ( $args['include_inactive'] ) { $url = add_query_arg( 'include_inactive', $args['include_inactive'], $url ); }
			if ( $args['max_results'] ) { $url = add_query_arg( 'max_results', $args['max_results'], $url ); }
			if ( $args['describe_api'] ) { $url = add_query_arg( 'describe_api', $args['describe_api'], $url ); }
						
			$ccb_data = CCBPress()->ccb->get( $url, $args['cache_lifespan'] );
			
			if ( CCBPress()->ccb->is_valid( $ccb_data ) ) {
				
				// Return the data
				return $ccb_data;
			
			} else {
				
				// Return the data
				return FALSE;	
				
			}
			
		} catch ( Exception $e ) {
			
			// Return the data
			return FALSE;
			
		}
		
	}

}
