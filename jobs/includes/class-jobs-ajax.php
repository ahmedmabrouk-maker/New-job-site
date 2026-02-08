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

		// Permission checks
		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;

		$is_employer = in_array( 'employer', $roles );
		$is_reviewer = in_array( 'reviewer', $roles );
		$is_admin = in_array( 'administrator', $roles );
		$is_job_seeker = in_array( 'job_seeker', $roles );

		$allowed = false;

		switch ( $module ) {
			case 'job-posting':
			case 'job-listings-history':
			case 'job-requests':
				if ( $is_employer || $is_reviewer || $is_admin ) $allowed = true;
				break;
			case 'company-profile':
				if ( $is_employer ) $allowed = true;
				break;
			case 'applications-submitted':
			case 'cv-resume':
				if ( $is_job_seeker ) $allowed = true;
				break;
			case 'advanced-settings':
				if ( $is_admin ) $allowed = true;
				break;
			case 'public-profile':
			case 'favorites':
			case 'drafts':
			case 'support':
			case 'settings':
			case 'terms-conditions':
			case 'articles':
				$allowed = true;
				break;
		}

		if ( ! $allowed ) {
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
