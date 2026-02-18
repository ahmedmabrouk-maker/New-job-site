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

	public function render_admin_page() {
		// Keep the backend render as a fallback or for consistency, but frontend is primary.
		$this->render_frontend_admin_panel( array() );
	}

	public function render_frontend_admin_panel( $atts ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			if ( ! is_admin() ) {
				wp_redirect( home_url() );
				exit;
			} else {
				return '<p>Access Denied.</p>';
			}
		}

		// Enqueue styles and scripts if not already enqueued (frontend)
		if ( ! is_admin() ) {
			wp_enqueue_style( 'jobs-admin-style', JOBS_PLUGIN_URL . 'assets/css/jobs-admin.css', array(), JOBS_VERSION, 'all' );
			wp_enqueue_style( 'jobs-public-style', JOBS_PLUGIN_URL . 'assets/css/jobs-public.css', array(), JOBS_VERSION, 'all' );

			if ( ! wp_script_is( 'jobs-public-script', 'enqueued' ) ) {
				wp_enqueue_script( 'jobs-public-script', JOBS_PLUGIN_URL . 'assets/js/jobs-public.js', array( 'jquery' ), JOBS_VERSION, true );
				wp_localize_script( 'jobs-public-script', 'jobs_ajax', array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'jobs_ajax_nonce' ),
				) );
			}
		}

		$active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dashboard';
		$base_url = is_admin() ? '?page=jobs_admin' : '?';

		ob_start();
		?>
		<div class="wrap jobs-admin-wrap">
			<h1>Jobs Admin Control Panel</h1>

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( $base_url . '&tab=dashboard' ); ?>" class="nav-tab <?php echo $active_tab == 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>
				<a href="<?php echo esc_url( $base_url . '&tab=reports' ); ?>" class="nav-tab <?php echo $active_tab == 'reports' ? 'nav-tab-active' : ''; ?>">Reports</a>
				<a href="<?php echo esc_url( $base_url . '&tab=activity' ); ?>" class="nav-tab <?php echo $active_tab == 'activity' ? 'nav-tab-active' : ''; ?>">Activity Logs</a>
				<a href="<?php echo esc_url( $base_url . '&tab=users' ); ?>" class="nav-tab <?php echo $active_tab == 'users' ? 'nav-tab-active' : ''; ?>">Users</a>
				<a href="<?php echo esc_url( $base_url . '&tab=articles' ); ?>" class="nav-tab <?php echo $active_tab == 'articles' ? 'nav-tab-active' : ''; ?>">Articles</a>
				<a href="<?php echo esc_url( $base_url . '&tab=design' ); ?>" class="nav-tab <?php echo $active_tab == 'design' ? 'nav-tab-active' : ''; ?>">Design</a>
				<a href="<?php echo esc_url( $base_url . '&tab=support' ); ?>" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
				<a href="<?php echo esc_url( $base_url . '&tab=permissions' ); ?>" class="nav-tab <?php echo $active_tab == 'permissions' ? 'nav-tab-active' : ''; ?>">Permissions</a>
				<a href="<?php echo esc_url( $base_url . '&tab=ads' ); ?>" class="nav-tab <?php echo $active_tab == 'ads' ? 'nav-tab-active' : ''; ?>">Ads</a>
			</h2>

			<div class="jobs-admin-content">
				<?php
				switch ( $active_tab ) {
					case 'dashboard':
						$this->render_dashboard_tab();
						break;
					case 'reports':
						$this->render_reports_tab();
						break;
					case 'activity':
						$this->render_activity_tab();
						break;
					case 'users':
						$this->render_users_tab();
						break;
					case 'articles':
						$this->render_articles_tab();
						break;
					case 'design':
						$this->render_design_tab();
						break;
					case 'support':
						$this->render_support_tab();
						break;
					case 'permissions':
						$this->render_permissions_tab();
						break;
					case 'ads':
						$this->render_ads_tab();
						break;
					default:
						echo 'Tab not found.';
						break;
				}
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	private function render_dashboard_tab() {
		echo '<p>Welcome to the Jobs System Admin Panel. Select a tab to manage the system.</p>';
	}

	private function render_reports_tab() {
		$job_counts = wp_count_posts( 'job' );
		$app_counts = wp_count_posts( 'job_application' );
		$user_count = count_users();

		echo '<h2>Reports System</h2>';
		echo '<div class="jobs-admin-card">';
		echo '<p><strong>Active Jobs:</strong> ' . ( isset( $job_counts->publish ) ? $job_counts->publish : 0 ) . '</p>';
		echo '<p><strong>Pending Jobs:</strong> ' . ( isset( $job_counts->pending ) ? $job_counts->pending : 0 ) . '</p>';
		echo '<p><strong>Total Applications:</strong> ' . ( isset( $app_counts->publish ) ? $app_counts->publish : 0 ) . '</p>';
		echo '<p><strong>Total Users:</strong> ' . $user_count['total_users'] . '</p>';
		echo '</div>';
	}

	private function render_activity_tab() {
		echo '<h2>Activity Log</h2>';
		// Simple activity log showing recent jobs
		$recent_jobs = get_posts( array( 'post_type' => 'job', 'numberposts' => 10, 'post_status' => 'any' ) );
		if ( $recent_jobs ) {
			echo '<ul class="jobs-activity-list">';
			foreach ( $recent_jobs as $job ) {
				$author = get_userdata( $job->post_author );
				echo '<li>';
				echo '<strong>' . esc_html( $job->post_title ) . '</strong> (' . $job->post_status . ') by ' . esc_html( $author ? $author->display_name : 'Unknown' );
				echo ' <span class="jobs-date">' . get_the_date( '', $job->ID ) . '</span>';
				echo '</li>';
			}
			echo '</ul>';
		} else {
			echo '<p>No recent activity.</p>';
		}
	}

	private function render_users_tab() {
		echo '<h2>User Management</h2>';
		echo '<p>Manage users directly via WordPress user management.</p>';
		echo '<p><a href="' . admin_url( 'users.php' ) . '" class="button button-primary" target="_blank">Manage Users</a></p>';
	}

	private function render_articles_tab() {
		echo '<h2>Published Articles Management</h2>';
		echo '<p>Manage articles via WordPress posts.</p>';
		echo '<p><a href="' . admin_url( 'edit.php' ) . '" class="button button-primary" target="_blank">Manage Articles</a></p>';
	}

	private function render_design_tab() {
		echo '<h2>Design Customization</h2>';
		// In frontend, we can't easily use settings API form submission without more work.
		// For now, link to backend or simple form if needed.
		// The prompt says "Design customization (colors, fonts, buttons, etc.)".
		if ( isset( $_POST['jobs_save_design'] ) && check_admin_referer( 'jobs_design_nonce' ) ) {
			update_option( 'jobs_logo_url', sanitize_text_field( $_POST['jobs_logo_url'] ) );
			update_option( 'jobs_primary_color', sanitize_text_field( $_POST['jobs_primary_color'] ) );
			echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
		}

		$logo_url = get_option( 'jobs_logo_url', '' );
		$primary_color = get_option( 'jobs_primary_color', '#1d3469' );

		?>
		<form method="post">
			<?php wp_nonce_field( 'jobs_design_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="jobs_logo_url">Logo URL</label></th>
					<td><input name="jobs_logo_url" type="text" id="jobs_logo_url" value="<?php echo esc_attr( $logo_url ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="jobs_primary_color">Primary Color</label></th>
					<td><input name="jobs_primary_color" type="text" id="jobs_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" class="regular-text"></td>
				</tr>
			</table>
			<p class="submit"><input type="submit" name="jobs_save_design" id="submit" class="button button-primary" value="Save Changes"></p>
		</form>
		<?php
	}

	private function render_support_tab() {
		echo '<h2>Technical Support</h2>';
		// List job_notification posts
		$tickets = get_posts( array( 'post_type' => 'job_notification', 'numberposts' => 20 ) );
		if ( $tickets ) {
			echo '<table class="widefat fixed striped">';
			echo '<thead><tr><th>Subject</th><th>User</th><th>Date</th></tr></thead><tbody>';
			foreach ( $tickets as $ticket ) {
				$author = get_userdata( $ticket->post_author );
				echo '<tr>';
				echo '<td>' . esc_html( $ticket->post_title ) . '</td>';
				echo '<td>' . esc_html( $author ? $author->display_name : 'Unknown' ) . '</td>';
				echo '<td>' . get_the_date( '', $ticket->ID ) . '</td>';
				echo '</tr>';
			}
			echo '</tbody></table>';
		} else {
			echo '<p>No support tickets found.</p>';
		}
	}

	private function render_permissions_tab() {
		echo '<h2>Permissions & Roles Management</h2>';
		global $wp_roles;
		$roles = $wp_roles->roles;
		echo '<table class="widefat fixed striped">';
		echo '<thead><tr><th>Role</th><th>Capabilities Count</th><th>Users</th></tr></thead><tbody>';
		foreach ( $roles as $key => $role ) {
			$user_count = count_users();
			$count = isset( $user_count['avail_roles'][ $key ] ) ? $user_count['avail_roles'][ $key ] : 0;
			echo '<tr>';
			echo '<td>' . esc_html( $role['name'] ) . ' (' . esc_html( $key ) . ')</td>';
			echo '<td>' . count( $role['capabilities'] ) . '</td>';
			echo '<td>' . $count . '</td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}

	private function render_ads_tab() {
		echo '<h2>External Ads & Google AdSense</h2>';
		if ( isset( $_POST['jobs_save_ads'] ) && check_admin_referer( 'jobs_ads_nonce' ) ) {
			// Allow filtered html for admins
			if ( current_user_can( 'unfiltered_html' ) ) {
				update_option( 'jobs_ads_code', $_POST['jobs_ads_code'] ); // Potentially unsafe but standard for admin ads
			} else {
				update_option( 'jobs_ads_code', wp_kses_post( $_POST['jobs_ads_code'] ) );
			}
			echo '<div class="notice notice-success"><p>Ads settings saved.</p></div>';
		}
		$ads_code = get_option( 'jobs_ads_code', '' );
		?>
		<form method="post">
			<?php wp_nonce_field( 'jobs_ads_nonce' ); ?>
			<p><label for="jobs_ads_code">Ad Code (HTML/JS):</label></p>
			<textarea name="jobs_ads_code" id="jobs_ads_code" rows="10" class="large-text code"><?php echo esc_textarea( $ads_code ); ?></textarea>
			<p class="submit"><input type="submit" name="jobs_save_ads" id="submit" class="button button-primary" value="Save Ads"></p>
		</form>
		<?php
	}
}
