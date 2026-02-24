<?php

/**
 * Fired during plugin activation.
 */
class Jobs_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		self::add_roles();
		self::create_pages();
	}

	/**
	 * Add custom user roles.
	 */
	private static function add_roles() {
		// Job Seeker
		add_role( 'job_seeker', 'Job Seeker', array(
			'read' => true,
			'upload_files' => true,
		) );

		// Employer
		add_role( 'employer', 'Employer', array(
			'read' => true,
			'upload_files' => true,
			'edit_posts' => true, // Allow employers to edit their own posts (jobs)
			'publish_posts' => true,
			'delete_posts' => true,
		) );

		// Reviewer
		add_role( 'reviewer', 'Reviewer', array(
			'read' => true,
			'edit_others_posts' => true, // Reviewers might need to see/edit others' posts
		) );
	}

	/**
	 * Create necessary pages.
	 */
	private static function create_pages() {
		$pages = array(
			'job_search' => array(
				'title'   => 'Job Search',
				'content' => '[jobs_search]',
			),
			'login' => array(
				'title'   => 'Login',
				'content' => '[jobs_login]',
			),
			'register' => array(
				'title'   => 'Register',
				'content' => '[jobs_register]',
			),
			'admin_panel' => array(
				'title'   => 'Admin Control Panel',
				'content' => '[jobs_admin_panel]',
			),
		);

		$page_ids = get_option( 'jobs_page_ids', array() );
		$new_page_ids = $page_ids;

		foreach ( $pages as $key => $page ) {
			// Check if page already exists and is tracked
			if ( isset( $page_ids[ $key ] ) && get_post( $page_ids[ $key ] ) ) {
				continue;
			}

			// Create page
			$page_id = wp_insert_post( array(
				'post_title'     => $page['title'],
				'post_content'   => $page['content'],
				'post_status'    => 'publish',
				'post_type'      => 'page',
				'comment_status' => 'closed',
			) );

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				$new_page_ids[ $key ] = $page_id;
			}
		}

		if ( $new_page_ids !== $page_ids ) {
			update_option( 'jobs_page_ids', $new_page_ids );
		}
	}

}
