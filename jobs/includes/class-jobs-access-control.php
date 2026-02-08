<?php

class Jobs_Access_Control {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'restrict_admin_access' ) );
	}

	public function restrict_admin_access() {
		// Allow AJAX requests
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Roles to restrict
		$restricted_roles = array( 'job_seeker', 'employer', 'reviewer' );

		// Check if user has any of the restricted roles
		// And make sure they don't have an allowed role (like administrator)
		// Assuming a user might have multiple roles, if they have 'administrator', they should be allowed.

		$is_restricted = false;
		foreach ( $restricted_roles as $role ) {
			if ( in_array( $role, $roles ) ) {
				$is_restricted = true;
				break;
			}
		}

		// If user is restricted, but also has an allowed role, we should probably allow them.
		// Allowed roles: administrator.
		if ( in_array( 'administrator', $roles ) ) {
			$is_restricted = false;
		}

		if ( $is_restricted ) {
			wp_redirect( home_url() );
			exit;
		}
	}
}
