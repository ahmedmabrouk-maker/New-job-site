<?php

class Jobs_Access_Control {

	public function __construct() {
		add_action( 'init', array( $this, 'restrict_admin_access' ) );
		add_action( 'after_setup_theme', array( $this, 'hide_admin_bar' ) );
	}

	public function restrict_admin_access() {
		if ( wp_doing_ajax() ) {
			return;
		}

		if ( is_admin() ) {
			if ( current_user_can( 'administrator' ) ) {
				global $pagenow;
				if ( 'index.php' === $pagenow ) {
					$page_ids = get_option( 'jobs_page_ids', array() );
					$admin_panel_url = isset( $page_ids['admin_panel'] ) ? get_permalink( $page_ids['admin_panel'] ) : home_url();
					wp_redirect( $admin_panel_url );
					exit;
				}
			} else {
				wp_redirect( home_url() );
				exit;
			}
		}
	}

	public function hide_admin_bar() {
		show_admin_bar( false );
	}
}
