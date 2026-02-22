<?php

class Jobs_CPT {

	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomies' ) );
	}

	public function register_post_type() {
		// Job Post Type
		$labels = array(
			'name'                  => _x( 'Jobs', 'Post Type General Name', 'jobs' ),
			'singular_name'         => _x( 'Job', 'Post Type Singular Name', 'jobs' ),
			'menu_name'             => __( 'Jobs', 'jobs' ),
			'name_admin_bar'        => __( 'Job', 'jobs' ),
			'archives'              => __( 'Job Archives', 'jobs' ),
			'attributes'            => __( 'Job Attributes', 'jobs' ),
			'parent_item_colon'     => __( 'Parent Job:', 'jobs' ),
			'all_items'             => __( 'All Jobs', 'jobs' ),
			'add_new_item'          => __( 'Add New Job', 'jobs' ),
			'add_new'               => __( 'Add New', 'jobs' ),
			'new_item'              => __( 'New Job', 'jobs' ),
			'edit_item'             => __( 'Edit Job', 'jobs' ),
			'update_item'           => __( 'Update Job', 'jobs' ),
			'view_item'             => __( 'View Job', 'jobs' ),
			'view_items'            => __( 'View Jobs', 'jobs' ),
			'search_items'          => __( 'Search Job', 'jobs' ),
			'not_found'             => __( 'Not found', 'jobs' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'jobs' ),
			'featured_image'        => __( 'Featured Image', 'jobs' ),
			'set_featured_image'    => __( 'Set featured image', 'jobs' ),
			'remove_featured_image' => __( 'Remove featured image', 'jobs' ),
			'use_featured_image'    => __( 'Use as featured image', 'jobs' ),
			'insert_into_item'      => __( 'Insert into job', 'jobs' ),
			'uploaded_to_this_item' => __( 'Uploaded to this job', 'jobs' ),
			'items_list'            => __( 'Jobs list', 'jobs' ),
			'items_list_navigation' => __( 'Jobs list navigation', 'jobs' ),
			'filter_items_list'     => __( 'Filter jobs list', 'jobs' ),
		);
		$args = array(
			'label'                 => __( 'Job', 'jobs' ),
			'description'           => __( 'Job Listings', 'jobs' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
			'taxonomies'            => array( 'job_specialization', 'job_country', 'job_city' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-businessman',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
		);
		register_post_type( 'job', $args );

		// Job Application Post Type
		$labels_app = array(
			'name'                  => _x( 'Applications', 'Post Type General Name', 'jobs' ),
			'singular_name'         => _x( 'Application', 'Post Type Singular Name', 'jobs' ),
			'menu_name'             => __( 'Applications', 'jobs' ),
			'name_admin_bar'        => __( 'Application', 'jobs' ),
		);
		$args_app = array(
			'label'                 => __( 'Application', 'jobs' ),
			'description'           => __( 'Job Applications', 'jobs' ),
			'labels'                => $labels_app,
			'supports'              => array( 'title', 'editor', 'custom-fields' ),
			'hierarchical'          => false,
			'public'                => false, // Internal use mainly
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=job', // Submenu of Jobs
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'post',
		);
		register_post_type( 'job_application', $args_app );

		// Job Notification Post Type
		$labels_notif = array(
			'name'                  => _x( 'Notifications', 'Post Type General Name', 'jobs' ),
			'singular_name'         => _x( 'Notification', 'Post Type Singular Name', 'jobs' ),
			'menu_name'             => __( 'Notifications', 'jobs' ),
		);
		$args_notif = array(
			'label'                 => __( 'Notification', 'jobs' ),
			'description'           => __( 'System Notifications', 'jobs' ),
			'labels'                => $labels_notif,
			'supports'              => array( 'title', 'editor', 'custom-fields' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=job',
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'post',
		);
		register_post_type( 'job_notification', $args_notif );

		// Activity Log Post Type
		$labels_activity = array(
			'name'                  => _x( 'Activity Logs', 'Post Type General Name', 'jobs' ),
			'singular_name'         => _x( 'Activity Log', 'Post Type Singular Name', 'jobs' ),
			'menu_name'             => __( 'Activity Logs', 'jobs' ),
		);
		$args_activity = array(
			'label'                 => __( 'Activity Log', 'jobs' ),
			'description'           => __( 'User Activity Logs', 'jobs' ),
			'labels'                => $labels_activity,
			'supports'              => array( 'title', 'editor', 'author' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=job',
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'post',
		);
		register_post_type( 'job_activity', $args_activity );
	}

	public function register_taxonomies() {
		// Specialization
		$labels_spec = array(
			'name'              => _x( 'Specializations', 'taxonomy general name', 'jobs' ),
			'singular_name'     => _x( 'Specialization', 'taxonomy singular name', 'jobs' ),
			'search_items'      => __( 'Search Specializations', 'jobs' ),
			'all_items'         => __( 'All Specializations', 'jobs' ),
			'parent_item'       => __( 'Parent Specialization', 'jobs' ),
			'parent_item_colon' => __( 'Parent Specialization:', 'jobs' ),
			'edit_item'         => __( 'Edit Specialization', 'jobs' ),
			'update_item'       => __( 'Update Specialization', 'jobs' ),
			'add_new_item'      => __( 'Add New Specialization', 'jobs' ),
			'new_item_name'     => __( 'New Specialization Name', 'jobs' ),
			'menu_name'         => __( 'Specialization', 'jobs' ),
		);
		register_taxonomy( 'job_specialization', array( 'job' ), array(
			'hierarchical'      => true,
			'labels'            => $labels_spec,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'specialization' ),
		) );

		// Country
		$labels_country = array(
			'name'              => _x( 'Countries', 'taxonomy general name', 'jobs' ),
			'singular_name'     => _x( 'Country', 'taxonomy singular name', 'jobs' ),
			'search_items'      => __( 'Search Countries', 'jobs' ),
			'all_items'         => __( 'All Countries', 'jobs' ),
			'parent_item'       => __( 'Parent Country', 'jobs' ),
			'parent_item_colon' => __( 'Parent Country:', 'jobs' ),
			'edit_item'         => __( 'Edit Country', 'jobs' ),
			'update_item'       => __( 'Update Country', 'jobs' ),
			'add_new_item'      => __( 'Add New Country', 'jobs' ),
			'new_item_name'     => __( 'New Country Name', 'jobs' ),
			'menu_name'         => __( 'Country', 'jobs' ),
		);
		register_taxonomy( 'job_country', array( 'job' ), array(
			'hierarchical'      => true,
			'labels'            => $labels_country,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'country' ),
		) );

		// City
		$labels_city = array(
			'name'              => _x( 'Cities', 'taxonomy general name', 'jobs' ),
			'singular_name'     => _x( 'City', 'taxonomy singular name', 'jobs' ),
			'search_items'      => __( 'Search Cities', 'jobs' ),
			'all_items'         => __( 'All Cities', 'jobs' ),
			'parent_item'       => __( 'Parent City', 'jobs' ),
			'parent_item_colon' => __( 'Parent City:', 'jobs' ),
			'edit_item'         => __( 'Edit City', 'jobs' ),
			'update_item'       => __( 'Update City', 'jobs' ),
			'add_new_item'      => __( 'Add New City', 'jobs' ),
			'new_item_name'     => __( 'New City Name', 'jobs' ),
			'menu_name'         => __( 'City', 'jobs' ),
		);
		register_taxonomy( 'job_city', array( 'job' ), array(
			'hierarchical'      => true,
			'labels'            => $labels_city,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'city' ),
		) );
	}
}
