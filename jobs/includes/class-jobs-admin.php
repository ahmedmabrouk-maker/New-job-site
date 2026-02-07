<?php

class Jobs_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	public function add_admin_menu() {
		add_menu_page(
			'Jobs Admin Panel',
			'Jobs Admin',
			'manage_options',
			'jobs_admin',
			array( $this, 'render_admin_page' ),
			'dashicons-businessperson',
			2
		);
	}

	public function register_settings() {
		register_setting( 'jobs_options_group', 'jobs_logo_url' );
		register_setting( 'jobs_options_group', 'jobs_primary_color' );

		add_settings_section(
			'jobs_general_section',
			'General Settings',
			null,
			'jobs_admin_settings'
		);

		add_settings_field(
			'jobs_logo_url',
			'Site Logo URL',
			array( $this, 'render_logo_field' ),
			'jobs_admin_settings',
			'jobs_general_section'
		);

		add_settings_field(
			'jobs_primary_color',
			'Primary Color',
			array( $this, 'render_color_field' ),
			'jobs_admin_settings',
			'jobs_general_section'
		);
	}

	public function render_logo_field() {
		$value = get_option( 'jobs_logo_url', '' );
		echo '<input type="text" name="jobs_logo_url" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">Enter the URL of your logo (e.g., from Media Library).</p>';
	}

	public function render_color_field() {
		$value = get_option( 'jobs_primary_color', '#1d3469' );
		echo '<input type="text" name="jobs_primary_color" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">Default: #1d3469</p>';
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'jobs-admin-style', JOBS_PLUGIN_URL . 'assets/css/jobs-admin.css', array(), JOBS_VERSION, 'all' );
	}

	public function render_admin_page() {
		?>
		<div class="wrap jobs-admin-wrap">
			<h1>Jobs Admin Control Panel</h1>

			<div class="jobs-admin-dashboard">
				<div class="jobs-admin-section">
					<h2>Settings</h2>
					<form method="post" action="options.php">
						<?php
						settings_fields( 'jobs_options_group' );
						do_settings_sections( 'jobs_admin_settings' );
						submit_button();
						?>
					</form>
				</div>

				<div class="jobs-admin-section">
					<h2>Reports System</h2>
					<p>Placeholder for reports.</p>
				</div>
				<div class="jobs-admin-section">
					<h2>General Activity Log</h2>
					<p>Placeholder for activity log.</p>
				</div>
				<div class="jobs-admin-section">
					<h2>Admin Activity Log</h2>
					<p>Placeholder for admin activity log.</p>
				</div>
				<div class="jobs-admin-section">
					<h2>User Management</h2>
					<p><a href="<?php echo admin_url( 'users.php' ); ?>" class="button">Manage Users</a></p>
				</div>
				<div class="jobs-admin-section">
					<h2>Published Articles Management</h2>
					<p><a href="<?php echo admin_url( 'edit.php' ); ?>" class="button">Manage Articles</a></p>
				</div>
				<div class="jobs-admin-section">
					<h2>Technical Support</h2>
					<p>Support tickets.</p>
				</div>
				<div class="jobs-admin-section">
					<h2>Permissions & Roles</h2>
					<p>Manage roles.</p>
				</div>
				<div class="jobs-admin-section">
					<h2>External Ads & Google AdSense</h2>
					<p>Manage ads.</p>
				</div>
			</div>
		</div>
		<?php
	}
}
