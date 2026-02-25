<?php

class Jobs_Access_Control {

	public function __construct() {
		add_action( 'init', array( $this, 'restrict_admin_access' ) );
		add_action( 'after_setup_theme', array( $this, 'hide_admin_bar' ) );
	}

	public function restrict_admin_access() {
		if ( is_admin() && ! wp_doing_ajax() ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_redirect( home_url() );
				exit;
			}

			global $pagenow;
			if ( 'index.php' === $pagenow ) {
				$page_ids = get_option( 'jobs_page_ids', array() );
				if ( isset( $page_ids['admin_panel'] ) ) {
					$admin_url = get_permalink( $page_ids['admin_panel'] );
					if ( $admin_url ) {
						wp_redirect( $admin_url );
						exit;
					}
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
