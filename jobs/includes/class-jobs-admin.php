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
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard';
		?>
		<div class="wrap jobs-admin-wrap">
			<h1>Jobs Admin Control Panel</h1>

			<h2 class="nav-tab-wrapper">
				<a href="?page=jobs_admin&tab=dashboard" class="nav-tab <?php echo $active_tab == 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>
				<a href="?page=jobs_admin&tab=reports" class="nav-tab <?php echo $active_tab == 'reports' ? 'nav-tab-active' : ''; ?>">Reports</a>
				<a href="?page=jobs_admin&tab=activity" class="nav-tab <?php echo $active_tab == 'activity' ? 'nav-tab-active' : ''; ?>">Activity Logs</a>
				<a href="?page=jobs_admin&tab=users" class="nav-tab <?php echo $active_tab == 'users' ? 'nav-tab-active' : ''; ?>">Users</a>
				<a href="?page=jobs_admin&tab=articles" class="nav-tab <?php echo $active_tab == 'articles' ? 'nav-tab-active' : ''; ?>">Articles</a>
				<a href="?page=jobs_admin&tab=design" class="nav-tab <?php echo $active_tab == 'design' ? 'nav-tab-active' : ''; ?>">Design</a>
				<a href="?page=jobs_admin&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
				<a href="?page=jobs_admin&tab=permissions" class="nav-tab <?php echo $active_tab == 'permissions' ? 'nav-tab-active' : ''; ?>">Permissions</a>
				<a href="?page=jobs_admin&tab=ads" class="nav-tab <?php echo $active_tab == 'ads' ? 'nav-tab-active' : ''; ?>">Ads</a>
			</h2>

			<div class="jobs-admin-content">
				<?php
				switch ( $active_tab ) {
					case 'dashboard':
						echo '<p>Welcome to the Jobs System Admin Panel.</p>';
						break;
					case 'reports':
						echo '<h2>Reports System</h2><p>System reports will be displayed here.</p>';
						break;
					case 'activity':
						echo '<h2>General Activity Log</h2><p>Log of user activities.</p>';
						echo '<h2>Admin Activity Log</h2><p>Log of admin activities.</p>';
						break;
					case 'users':
						echo '<h2>User Management</h2><p><a href="' . admin_url( 'users.php' ) . '" class="button button-primary">Manage WordPress Users</a></p>';
						break;
					case 'articles':
						echo '<h2>Published Articles Management</h2><p><a href="' . admin_url( 'edit.php' ) . '" class="button button-primary">Manage Articles (Posts)</a></p>';
						break;
					case 'design':
						echo '<h2>Design Customization</h2>';
						echo '<form method="post" action="options.php">';
						settings_fields( 'jobs_options_group' );
						do_settings_sections( 'jobs_admin_settings' );
						submit_button();
						echo '</form>';
						break;
					case 'support':
						echo '<h2>Technical Support</h2><p>Support tickets and inquiries.</p>';
						break;
					case 'permissions':
						echo '<h2>Permissions & Roles Management</h2><p>Manage user roles and capabilities.</p>';
						break;
					case 'ads':
						echo '<h2>External Ads & Google AdSense</h2><p>Manage advertisement settings.</p>';
						break;
					default:
						echo 'Tab not found.';
						break;
				}
				?>
			</div>
		</div>
		<?php
	}
}
