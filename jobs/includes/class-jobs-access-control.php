<?php

/**
 * Handles access control for the plugin.
 */
class Jobs_Access_Control {

	/**
	 * Initialize the class and set its properties.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'restrict_admin_access' ) );
	}

	/**
	 * Restrict access to the WordPress dashboard for non-administrators.
	 */
	public function restrict_admin_access() {
		// Allow AJAX requests
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Allow administrators (or anyone with manage_options capability)
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		// Restrict everyone else from accessing the dashboard
		wp_redirect( home_url() );
		exit;
	}
}
