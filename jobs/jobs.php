<?php
/**
 * Plugin Name: Jobs
 * Description: A modern, powerful, and clean job management system.
 * Version: 1.0.0
 * Author: Jules
 * Text Domain: jobs
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'JOBS_VERSION', '1.0.0' );

/**
 * Plugin Directory Path
 */
define( 'JOBS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin Directory URL
 */
define( 'JOBS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_jobs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-activator.php';
	Jobs_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_jobs() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-deactivator.php';
	Jobs_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_jobs' );
register_deactivation_hook( __FILE__, 'deactivate_jobs' );

/**
 * Include the shortcodes class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-shortcodes.php';

/**
 * Instantiate the shortcodes class.
 */
function run_jobs_shortcodes() {
	$plugin = new Jobs_Shortcodes();
}
run_jobs_shortcodes();

/**
 * Include the admin class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-admin.php';

/**
 * Instantiate the admin class.
 */
function run_jobs_admin() {
	$plugin = new Jobs_Admin();
}
run_jobs_admin();

/**
 * Include the top bar class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-top-bar.php';

/**
 * Instantiate the top bar class.
 */
function run_jobs_top_bar() {
	$plugin = new Jobs_Top_Bar();
}
run_jobs_top_bar();

/**
 * Include the CPT class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-cpt.php';

/**
 * Instantiate the CPT class.
 */
function run_jobs_cpt() {
	$plugin = new Jobs_CPT();
}
run_jobs_cpt();

/**
 * Include the Ajax class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-ajax.php';

/**
 * Instantiate the Ajax class.
 */
function run_jobs_ajax() {
	$plugin = new Jobs_Ajax();
}
run_jobs_ajax();

/**
 * Include the Access Control class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-access-control.php';

/**
 * Instantiate the Access Control class.
 */
function run_jobs_access_control() {
	new Jobs_Access_Control();
}
run_jobs_access_control();

/**
 * Include the Admin Panel class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobs-admin-panel.php';

/**
 * Instantiate the Admin Panel class.
 */
function run_jobs_admin_panel() {
	new Jobs_Admin_Panel();
}
run_jobs_admin_panel();
