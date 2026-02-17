<?php

class Jobs_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_frontend_admin_panel' ) );
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

	// Backend Admin Page
	public function render_admin_page() {
		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard';
		?>
		<div class="wrap jobs-admin-wrap">
			<h1>Jobs Admin Control Panel</h1>
			<h2 class="nav-tab-wrapper">
				<?php $this->render_tabs( $active_tab, true ); ?>
			</h2>
			<div class="jobs-admin-content">
				<?php $this->render_tab_content( $active_tab, true ); ?>
			</div>
		</div>
		<?php
	}

	// Frontend Admin Panel Shortcode
	public function render_frontend_admin_panel() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '<p class="jobs-error">Access Denied. You do not have permission to view this page.</p>';
		}

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard';
		ob_start();
		?>
		<div class="jobs-frontend-admin-panel">
			<div class="jobs-admin-header">
				<h1>System Administration</h1>
			</div>
			<div class="jobs-admin-tabs">
				<?php $this->render_tabs( $active_tab, false ); ?>
			</div>
			<div class="jobs-admin-content-box">
				<?php $this->render_tab_content( $active_tab, false ); ?>
			</div>
		</div>
		<style>
			/* Minimal inline styles for frontend admin structure, most should be in CSS file */
			.jobs-frontend-admin-panel { padding: 20px; font-family: 'Rubik', sans-serif; }
			.jobs-admin-tabs { margin-bottom: 20px; border-bottom: 1px solid #ddd; }
			.jobs-admin-tabs a {
				display: inline-block;
				padding: 10px 20px;
				text-decoration: none;
				color: #1d3469;
				border-bottom: 2px solid transparent;
			}
			.jobs-admin-tabs a.active { border-bottom-color: #1d3469; font-weight: bold; }
			.jobs-admin-content-box { background: rgba(255,255,255,0.8); padding: 20px; border-radius: 8px; }
		</style>
		<?php
		return ob_get_clean();
	}

	private function render_tabs( $active_tab, $is_backend ) {
		$tabs = array(
			'dashboard'   => 'Dashboard',
			'reports'     => 'Reports',
			'activity'    => 'Activity Logs',
			'users'       => 'Users',
			'articles'    => 'Articles',
			'design'      => 'Design',
			'support'     => 'Support',
			'permissions' => 'Permissions',
			'ads'         => 'Ads',
		);

		foreach ( $tabs as $key => $label ) {
			$url = $is_backend ? '?page=jobs_admin&tab=' . $key : '?tab=' . $key;
			$class = $active_tab == $key ? 'active' : '';
			if ( $is_backend ) {
				$class = $active_tab == $key ? 'nav-tab-active' : '';
				echo '<a href="' . esc_url( $url ) . '" class="nav-tab ' . esc_attr( $class ) . '">' . esc_html( $label ) . '</a>';
			} else {
				echo '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $label ) . '</a>';
			}
		}
	}

	private function render_tab_content( $active_tab, $is_backend ) {
		switch ( $active_tab ) {
			case 'dashboard':
				echo '<h3>Dashboard</h3><p>Welcome to the Jobs System Admin Panel.</p>';
				break;
			case 'reports':
				echo '<h3>Reports System</h3>';
				echo '<div class="jobs-stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">';
				echo '<div class="stat-box"><h4>Active Jobs</h4><p>' . wp_count_posts( 'job' )->publish . '</p></div>';
				echo '<div class="stat-box"><h4>Pending Jobs</h4><p>' . wp_count_posts( 'job' )->pending . '</p></div>';
				echo '<div class="stat-box"><h4>Total Users</h4><p>' . count_users()['total_users'] . '</p></div>';
				echo '</div>';
				break;
			case 'activity':
				echo '<h3>Activity Logs</h3>';
				echo '<h4>Recent Job Postings</h4>';
				$recent_jobs = get_posts( array( 'post_type' => 'job', 'numberposts' => 5 ) );
				if ( $recent_jobs ) {
					echo '<ul>';
					foreach ( $recent_jobs as $job ) {
						echo '<li>' . esc_html( $job->post_title ) . ' (' . get_post_status( $job->ID ) . ') - ' . get_the_date( '', $job->ID ) . '</li>';
					}
					echo '</ul>';
				} else {
					echo '<p>No recent activity.</p>';
				}
				break;
			case 'users':
				echo '<h3>User Management</h3>';
				if ( $is_backend ) {
					echo '<p><a href="' . admin_url( 'users.php' ) . '" class="button button-primary">Manage WordPress Users</a></p>';
				} else {
					echo '<p>Manage users via the <a href="' . admin_url( 'users.php' ) . '">WordPress Users Dashboard</a>.</p>';
					// List recent users could be added here
					$users = get_users( array( 'number' => 10 ) );
					echo '<table class="widefat fixed striped">';
					echo '<thead><tr><th>Username</th><th>Email</th><th>Role</th></tr></thead><tbody>';
					foreach ( $users as $user ) {
						echo '<tr>';
						echo '<td>' . esc_html( $user->user_login ) . '</td>';
						echo '<td>' . esc_html( $user->user_email ) . '</td>';
						echo '<td>' . implode( ', ', $user->roles ) . '</td>';
						echo '</tr>';
					}
					echo '</tbody></table>';
				}
				break;
			case 'articles':
				echo '<h3>Published Articles Management</h3>';
				if ( $is_backend ) {
					echo '<p><a href="' . admin_url( 'edit.php' ) . '" class="button button-primary">Manage Articles (Posts)</a></p>';
				} else {
					echo '<p>Manage articles via the <a href="' . admin_url( 'edit.php' ) . '">WordPress Posts Dashboard</a>.</p>';
				}
				break;
			case 'design':
				echo '<h3>Design Customization</h3>';
				if ( $is_backend ) {
					echo '<form method="post" action="options.php">';
					settings_fields( 'jobs_options_group' );
					do_settings_sections( 'jobs_admin_settings' );
					submit_button();
					echo '</form>';
				} else {
					echo '<p>Design settings must be changed via the backend admin panel for security reasons.</p>';
				}
				break;
			case 'support':
				echo '<h3>Technical Support</h3>';
				$notifications = get_posts( array( 'post_type' => 'job_notification', 'posts_per_page' => 10 ) );
				if ( $notifications ) {
					echo '<ul>';
					foreach ( $notifications as $notif ) {
						echo '<li><strong>' . esc_html( $notif->post_title ) . '</strong>: ' . esc_html( wp_trim_words( $notif->post_content, 20 ) ) . '</li>';
					}
					echo '</ul>';
				} else {
					echo '<p>No support tickets found.</p>';
				}
				break;
			case 'permissions':
				echo '<h3>Permissions & Roles Management</h3>';
				global $wp_roles;
				$roles = $wp_roles->roles;
				echo '<ul>';
				foreach ( $roles as $role_name => $role_info ) {
					echo '<li><strong>' . esc_html( $role_info['name'] ) . '</strong>: ' . count( $role_info['capabilities'] ) . ' capabilities</li>';
				}
				echo '</ul>';
				break;
			case 'ads':
				echo '<h3>External Ads & Google AdSense</h3><p>Manage advertisement settings.</p>';
				// Placeholder for ads settings
				break;
			default:
				echo 'Tab not found.';
				break;
		}
	}
}
