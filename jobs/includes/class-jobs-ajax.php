<?php

class Jobs_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_jobs_load_module', array( $this, 'load_module' ) );
	}

	public function load_module() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! isset( $_POST['module'] ) ) {
			wp_send_json_error( 'No module specified' );
		}

		$module = sanitize_key( $_POST['module'] );

		$allowed_modules = array(
			'job-posting',
			'job-listings-history',
			'public-profile',
			'applications-submitted',
			'job-requests',
			'cv-resume',
			'company-profile',
			'favorites',
			'drafts',
			'support',
			'settings',
			'advanced-settings',
			'terms-conditions',
			'articles'
		);

		if ( ! in_array( $module, $allowed_modules ) ) {
			wp_send_json_error( 'Invalid module' );
		}

		// Permission Check
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;
		$has_permission = false;

		switch ( $module ) {
			case 'job-posting':
			case 'job-listings-history':
			case 'job-requests':
				if ( in_array( 'employer', $roles ) || in_array( 'reviewer', $roles ) || in_array( 'administrator', $roles ) ) {
					$has_permission = true;
				}
				break;
			case 'company-profile':
				if ( in_array( 'employer', $roles ) ) {
					$has_permission = true;
				}
				break;
			case 'applications-submitted':
			case 'cv-resume':
				if ( in_array( 'job_seeker', $roles ) ) {
					$has_permission = true;
				}
				break;
			case 'advanced-settings':
				if ( in_array( 'administrator', $roles ) ) {
					$has_permission = true;
				}
				break;
			case 'public-profile':
			case 'favorites':
			case 'drafts':
			case 'support':
			case 'settings':
			case 'terms-conditions':
			case 'articles':
				if ( is_user_logged_in() ) {
					$has_permission = true;
				}
				break;
		}

		if ( ! $has_permission ) {
			wp_send_json_error( 'Permission denied' );
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
