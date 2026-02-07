<?php

/**
 * Fired during plugin activation
 */
class Jobs_Activator {

	public static function activate() {
		self::add_roles();
		self::create_pages();
        // CPTs must be registered before flushing. Since this runs on activation,
        // we might need to manually trigger CPT registration here if not already loaded,
        // or rely on the fact that activation happens after plugin load.
        // However, standard practice is to flush in the activation hook.
        // Assuming CPT registration is hooked to init, we might need to call it directly here
        // OR just flush. But flush won't work if CPTs aren't known yet.
        // We will instantiate CPT class here to be safe.

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jobs-cpt.php';
        $cpt = new Jobs_CPT();
        $cpt->register_cpt();
        $cpt->register_taxonomies();

        flush_rewrite_rules();
	}

	private static function add_roles() {
		add_role( 'job_seeker', 'Job Seeker', array( 'read' => true ) );
		add_role( 'employer', 'Employer', array( 'read' => true, 'upload_files' => true ) );
		add_role( 'reviewer', 'Reviewer', array( 'read' => true ) );

		$admin = get_role('administrator');
		if ( $admin ) {
			add_role( 'system_administrator', 'System Administrator', $admin->capabilities );
		}
	}

	private static function create_pages() {
		$pages = array(
			'Job Search' => array(
				'slug' => 'jobs-search',
				'content' => '[jobs_search]'
			),
			'Admin Control Panel' => array(
				'slug' => 'jobs-admin-panel',
				'content' => '[jobs_admin_panel]'
			),
			'Login' => array(
				'slug' => 'jobs-login',
				'content' => '[jobs_login]'
			),
			'Registration' => array(
				'slug' => 'jobs-registration',
				'content' => '[jobs_register]'
			)
		);

		foreach ( $pages as $title => $page_data ) {
			$page_check = get_page_by_path( $page_data['slug'] );
			if ( ! isset( $page_check->ID ) ) {
				$new_page = array(
					'post_type' => 'page',
					'post_title' => $title,
					'post_content' => $page_data['content'],
					'post_status' => 'publish',
					'post_author' => 1,
					'post_name' => $page_data['slug']
				);
				wp_insert_post( $new_page );
			}
		}
	}

}
