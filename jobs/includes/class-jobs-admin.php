<?php

class Jobs_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	public function add_admin_menu() {
		// Only accessible to users with manage_options capability (Admins & System Admins)
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
		// General Settings
		register_setting( 'jobs_options_group', 'jobs_logo_url' );
		register_setting( 'jobs_options_group', 'jobs_primary_color' );

		// Search Engine Settings
		register_setting( 'jobs_search_group', 'jobs_search_title' );
		register_setting( 'jobs_search_group', 'jobs_search_placeholder' );

		// Ads Settings
		register_setting( 'jobs_ads_group', 'jobs_ad_script' );

		// Sections
		add_settings_section(
			'jobs_general_section',
			'General Settings',
			null,
			'jobs_admin_settings'
		);

		add_settings_section(
			'jobs_search_section',
			'Search Engine Customization',
			null,
			'jobs_admin_search'
		);

		add_settings_section(
			'jobs_ads_section',
			'External Ads & Google AdSense',
			null,
			'jobs_admin_ads'
		);

		// Fields - General
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

		// Fields - Search
		add_settings_field(
			'jobs_search_title',
			'Search Engine Title',
			array( $this, 'render_search_title_field' ),
			'jobs_admin_search',
			'jobs_search_section'
		);

		add_settings_field(
			'jobs_search_placeholder',
			'Main Search Placeholder',
			array( $this, 'render_search_placeholder_field' ),
			'jobs_admin_search',
			'jobs_search_section'
		);

		// Fields - Ads
		add_settings_field(
			'jobs_ad_script',
			'Ad Script / Code',
			array( $this, 'render_ad_script_field' ),
			'jobs_admin_ads',
			'jobs_ads_section'
		);
	}

	public function render_logo_field() {
		$value = get_option( 'jobs_logo_url', '' );
		echo '<input type="text" name="jobs_logo_url" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">Enter the URL of your logo.</p>';
	}

	public function render_color_field() {
		$value = get_option( 'jobs_primary_color', '#1d3469' );
		echo '<input type="text" name="jobs_primary_color" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">Default: #1d3469</p>';
	}

	public function render_search_title_field() {
		$value = get_option( 'jobs_search_title', 'Jobs' );
		echo '<input type="text" name="jobs_search_title" value="' . esc_attr( $value ) . '" class="regular-text">';
		echo '<p class="description">Title displayed above the search bar if no logo is set.</p>';
	}

	public function render_search_placeholder_field() {
		$value = get_option( 'jobs_search_placeholder', 'Search jobs...' );
		echo '<input type="text" name="jobs_search_placeholder" value="' . esc_attr( $value ) . '" class="regular-text">';
	}

	public function render_ad_script_field() {
		$value = get_option( 'jobs_ad_script', '' );
		echo '<textarea name="jobs_ad_script" class="large-text code" rows="5">' . esc_textarea( $value ) . '</textarea>';
		echo '<p class="description">Paste Google AdSense code or other ad scripts here.</p>';
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
				<a href="?page=jobs_admin&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Design & Settings</a>
				<a href="?page=jobs_admin&tab=search" class="nav-tab <?php echo $active_tab == 'search' ? 'nav-tab-active' : ''; ?>">Search Engine</a>
				<a href="?page=jobs_admin&tab=reports" class="nav-tab <?php echo $active_tab == 'reports' ? 'nav-tab-active' : ''; ?>">Reports</a>
				<a href="?page=jobs_admin&tab=activity" class="nav-tab <?php echo $active_tab == 'activity' ? 'nav-tab-active' : ''; ?>">Activity Logs</a>
				<a href="?page=jobs_admin&tab=users" class="nav-tab <?php echo $active_tab == 'users' ? 'nav-tab-active' : ''; ?>">Users</a>
				<a href="?page=jobs_admin&tab=ads" class="nav-tab <?php echo $active_tab == 'ads' ? 'nav-tab-active' : ''; ?>">Ads</a>
			</h2>

			<div class="jobs-admin-content">
				<?php if ( 'dashboard' === $active_tab ) : ?>
					<div class="jobs-admin-section">
						<h2>Welcome to Jobs System</h2>
						<p>Overview of system status.</p>
					</div>
				<?php elseif ( 'settings' === $active_tab ) : ?>
					<form method="post" action="options.php">
						<?php
						settings_fields( 'jobs_options_group' );
						do_settings_sections( 'jobs_admin_settings' );
						submit_button();
						?>
					</form>
				<?php elseif ( 'search' === $active_tab ) : ?>
					<form method="post" action="options.php">
						<?php
						settings_fields( 'jobs_search_group' );
						do_settings_sections( 'jobs_admin_search' );
						submit_button();
						?>
					</form>
				<?php elseif ( 'reports' === $active_tab ) : ?>
					<h2>Reports System</h2>
					<p>No reports available yet.</p>
				<?php elseif ( 'activity' === $active_tab ) : ?>
					<h2>Activity Logs</h2>
					<div class="card">
						<h3>General Activity</h3>
						<p>Log entries...</p>
					</div>
					<div class="card">
						<h3>Admin Activity</h3>
						<p>Log entries...</p>
					</div>
				<?php elseif ( 'users' === $active_tab ) : ?>
					<h2>User Management</h2>
					<p><a href="<?php echo admin_url( 'users.php' ); ?>" class="button button-primary">Manage WordPress Users</a></p>
					<p><a href="<?php echo admin_url( 'edit.php?post_type=job' ); ?>" class="button">Manage Jobs</a></p>
				<?php elseif ( 'ads' === $active_tab ) : ?>
					<form method="post" action="options.php">
						<?php
						settings_fields( 'jobs_ads_group' );
						do_settings_sections( 'jobs_admin_ads' );
						submit_button();
						?>
					</form>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}
