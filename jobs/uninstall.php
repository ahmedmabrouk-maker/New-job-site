<?php

/**
 * Fired when the plugin is uninstalled.
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$pages = array( 'jobs-search', 'jobs-admin-panel', 'jobs-login', 'jobs-registration' );
foreach ( $pages as $slug ) {
    $page = get_page_by_path( $slug );
    if ( $page ) {
        wp_delete_post( $page->ID, true );
    }
}
