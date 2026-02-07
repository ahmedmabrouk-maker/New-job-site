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
