<?php

class Jobs_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_jobs_load_module', array( $this, 'load_module' ) );
	}

	public function load_module() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'Please login first.' );
		}

		if ( ! isset( $_POST['module'] ) ) {
			wp_send_json_error( 'No module specified' );
		}

		$module = sanitize_key( $_POST['module'] );
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;

		// Define allowed modules and their required roles
		$allowed_modules = array(
			// Employer, Reviewer, Admin
			'job-posting'            => array( 'employer', 'reviewer', 'administrator' ),
			'job-listings-history'   => array( 'employer', 'reviewer', 'administrator' ),
			'job-requests'           => array( 'employer', 'reviewer', 'administrator' ),

			// Employer only
			'company-profile'        => array( 'employer' ),

			// Job Seeker only
			'applications-submitted' => array( 'job_seeker' ),
			'cv-resume'              => array( 'job_seeker' ),

			// All Roles (Logged in)
			'public-profile'         => array( 'job_seeker', 'employer', 'reviewer', 'administrator' ),
			'favorites'              => array( 'job_seeker', 'employer', 'reviewer', 'administrator' ),
			'drafts'                 => array( 'job_seeker', 'employer', 'reviewer', 'administrator' ),
			'support'                => array( 'job_seeker', 'employer', 'reviewer', 'administrator' ),
			'settings'               => array( 'job_seeker', 'employer', 'reviewer', 'administrator' ),
			'terms-conditions'       => array( 'job_seeker', 'employer', 'reviewer', 'administrator' ),
			'articles'               => array( 'job_seeker', 'employer', 'reviewer', 'administrator' ),

			// Admin Only
			'advanced-settings'      => array( 'administrator' ),
		);

		if ( ! array_key_exists( $module, $allowed_modules ) ) {
			wp_send_json_error( 'Invalid module' );
		}

		// Check permission
		$has_permission = false;
		foreach ( $roles as $role ) {
			if ( in_array( $role, $allowed_modules[ $module ] ) ) {
				$has_permission = true;
				break;
			}
		}

		if ( ! $has_permission ) {
			wp_send_json_error( 'You do not have permission to view this module.' );
		}

		$file_path = JOBS_PLUGIN_DIR . 'modules/' . $module . '.php';

		if ( file_exists( $file_path ) ) {
			ob_start();
			include $file_path;
			$content = ob_get_clean();
			wp_send_json_success( $content );
		} else {
			wp_send_json_error( 'Module not found' );
		}
	}
}
