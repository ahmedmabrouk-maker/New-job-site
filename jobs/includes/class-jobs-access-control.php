<?php

class Jobs_Access_Control {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'restrict_admin_access' ) );
	}

	public function restrict_admin_access() {
		// Allow AJAX
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;

		// Check if user is Job Seeker or Employer
		if ( in_array( 'job_seeker', $roles ) || in_array( 'employer', $roles ) ) {
			// If they are not also an administrator or reviewer (just in case of multiple roles)
			if ( ! in_array( 'administrator', $roles ) && ! in_array( 'reviewer', $roles ) ) {
				wp_redirect( home_url() );
				exit;
			}
		}
	}
}
