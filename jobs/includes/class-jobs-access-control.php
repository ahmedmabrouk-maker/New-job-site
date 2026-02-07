<?php

class Jobs_Access_Control {

    public function init() {
        add_action( 'admin_init', array( $this, 'restrict_admin_access' ) );
    }

    public function restrict_admin_access() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            return;
        }

        $user = wp_get_current_user();
        if ( empty( $user->roles ) ) {
            return;
        }

        // Allow Administrators
        if ( current_user_can( 'manage_options' ) ) {
            return;
        }

        // Redirect Job Seekers, Employers, and Reviewers to home
        $restricted_roles = array( 'job_seeker', 'employer', 'reviewer' );
        foreach ( $restricted_roles as $role ) {
            if ( in_array( $role, (array) $user->roles ) ) {
                wp_redirect( home_url() );
                exit;
            }
        }
    }

}
