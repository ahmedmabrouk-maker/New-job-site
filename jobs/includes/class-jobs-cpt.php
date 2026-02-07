<?php

class Jobs_CPT {

	public function register_cpt() {
		$labels = array(
			'name'               => _x( 'Jobs', 'post type general name', 'jobs' ),
			'singular_name'      => _x( 'Job', 'post type singular name', 'jobs' ),
			'menu_name'          => _x( 'Jobs', 'admin menu', 'jobs' ),
			'name_admin_bar'     => _x( 'Job', 'add new on admin bar', 'jobs' ),
			'add_new'            => _x( 'Add New', 'job', 'jobs' ),
			'add_new_item'       => __( 'Add New Job', 'jobs' ),
			'new_item'           => __( 'New Job', 'jobs' ),
			'edit_item'          => __( 'Edit Job', 'jobs' ),
			'view_item'          => __( 'View Job', 'jobs' ),
			'all_items'          => __( 'All Jobs', 'jobs' ),
			'search_items'       => __( 'Search Jobs', 'jobs' ),
			'parent_item_colon'  => __( 'Parent Jobs:', 'jobs' ),
			'not_found'          => __( 'No jobs found.', 'jobs' ),
			'not_found_in_trash' => __( 'No jobs found in Trash.', 'jobs' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Description.', 'jobs' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'job' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
		);

		register_post_type( 'job', $args );
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

		$args_spec = array(
			'hierarchical'      => true,
			'labels'            => $labels_spec,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'specialization' ),
		);

		register_taxonomy( 'job_specialization', array( 'job' ), $args_spec );

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

		$args_country = array(
			'hierarchical'      => true,
			'labels'            => $labels_country,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'job-country' ),
		);

		register_taxonomy( 'job_country', array( 'job' ), $args_country );

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

		$args_city = array(
			'hierarchical'      => true,
			'labels'            => $labels_city,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'job-city' ),
		);

		register_taxonomy( 'job_city', array( 'job' ), $args_city );
	}

}
