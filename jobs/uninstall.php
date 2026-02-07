<?php

/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete pages created during activation
$page_ids = get_option( 'jobs_page_ids', array() );

if ( ! empty( $page_ids ) ) {
	foreach ( $page_ids as $page_id ) {
		// True to force delete, bypass trash.
		wp_delete_post( $page_id, true );
	}
	delete_option( 'jobs_page_ids' );
}

// Delete other options
delete_option( 'jobs_logo_url' );
