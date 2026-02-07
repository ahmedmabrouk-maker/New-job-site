<?php
/**
 * Plugin Name:       Jobs
 * Description:       A modern, powerful, and clean job management system.
 * Version:           1.0.0
 * Author:            Jules
 * Text Domain:       jobs
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

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
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jobs.php';

/**
 * Begins execution of the plugin.
 */
function run_jobs() {
	$plugin = new Jobs();
	$plugin->run();
}
run_jobs();
