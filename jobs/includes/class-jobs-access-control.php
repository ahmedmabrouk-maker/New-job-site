<?php

class Jobs_Access_Control {

	public function __construct() {
		// Use admin_init for backend checks
		add_action( 'admin_init', array( $this, 'restrict_admin_access' ) );
		add_action( 'after_setup_theme', array( $this, 'hide_admin_bar' ) );
	}

	public function restrict_admin_access() {
		// Allow AJAX
		if ( wp_doing_ajax() ) {
			return;
		}

		// Non-Admins: Redirect to Home
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_redirect( home_url() );
			exit;
		}

		// Admins: Redirect Dashboard (index.php) to Jobs Admin Panel
		global $pagenow;
		if ( 'index.php' === $pagenow && current_user_can( 'manage_options' ) ) {
			$page_ids = get_option( 'jobs_page_ids', array() );
			$admin_panel_id = isset( $page_ids['admin_panel'] ) ? $page_ids['admin_panel'] : 0;

			if ( $admin_panel_id ) {
				$redirect_url = get_permalink( $admin_panel_id );
				if ( $redirect_url ) {
					wp_redirect( $redirect_url );
					exit;
				}
			}
		}
	}

	public function hide_admin_bar() {
		if ( ! current_user_can( 'manage_options' ) ) {
			show_admin_bar( false );
		}
	}
}
