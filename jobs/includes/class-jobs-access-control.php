<?php

class Jobs_Access_Control {

	public function __construct() {
		add_action( 'init', array( $this, 'restrict_admin_access' ) );
		add_action( 'after_setup_theme', array( $this, 'hide_admin_bar' ) );
	}

	public function restrict_admin_access() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			// Redirect non-admins to home
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_redirect( home_url() );
				exit;
			}

			// Redirect admins to frontend panel if accessing Dashboard
			global $pagenow;
			if ( 'index.php' === $pagenow ) {
				$page_ids = get_option( 'jobs_page_ids', array() );
				$admin_page_id = isset( $page_ids['jobs_admin_panel'] ) ? $page_ids['jobs_admin_panel'] : 0;

				if ( $admin_page_id ) {
					wp_redirect( get_permalink( $admin_page_id ) );
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
