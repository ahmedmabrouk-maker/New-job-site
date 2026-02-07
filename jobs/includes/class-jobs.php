<?php

class Jobs {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		$this->plugin_name = 'jobs';
		$this->version = '1.0.0';
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jobs-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jobs-cpt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-jobs-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-jobs-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-jobs-public.php';

		$this->loader = new Jobs_Loader();
	}

	private function define_admin_hooks() {
        $plugin_admin = new Jobs_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
	}

	private function define_public_hooks() {
        // CPTs and Taxonomies
        $plugin_cpt = new Jobs_CPT();
        $this->loader->add_action( 'init', $plugin_cpt, 'register_cpt' );
        $this->loader->add_action( 'init', $plugin_cpt, 'register_taxonomies' );

        // Shortcodes
        $plugin_shortcodes = new Jobs_Shortcodes();
        $this->loader->add_action( 'init', $plugin_shortcodes, 'register_shortcodes' );

        // Public UI
        $plugin_public = new Jobs_Public( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'inject_top_bar' );

        // AJAX
        $this->loader->add_action( 'wp_ajax_jobs_load_module', $plugin_public, 'load_module' );
        $this->loader->add_action( 'wp_ajax_nopriv_jobs_load_module', $plugin_public, 'load_module' );
	}

	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
