<?php

class Jobs_Admin_Panel {

	public function __construct() {
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_admin_panel' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'init', array( $this, 'handle_actions' ) );
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'jobs-admin-panel', JOBS_PLUGIN_URL . 'assets/css/jobs-admin.css', array(), JOBS_VERSION, 'all' );
	}

	public function handle_actions() {
		if ( isset( $_POST['jobs_admin_save_settings'] ) && is_user_logged_in() && current_user_can( 'administrator' ) ) {
			if ( ! isset( $_POST['jobs_admin_nonce'] ) || ! wp_verify_nonce( $_POST['jobs_admin_nonce'], 'jobs_admin_action' ) ) {
				return;
			}

			if ( isset( $_POST['jobs_logo_url'] ) ) {
				update_option( 'jobs_logo_url', sanitize_text_field( $_POST['jobs_logo_url'] ) );
			}
			if ( isset( $_POST['jobs_primary_color'] ) ) {
				update_option( 'jobs_primary_color', sanitize_text_field( $_POST['jobs_primary_color'] ) );
			}
			// Redirect to avoid resubmission
			wp_redirect( add_query_arg( 'updated', 'true', $_SERVER['REQUEST_URI'] ) );
			exit;
		}
	}

	public function render_admin_panel( $atts ) {
		if ( ! is_user_logged_in() || ! current_user_can( 'administrator' ) ) {
			return '<p>Access Denied. You must be an administrator to view this page.</p>';
		}

		ob_start();
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'dashboard';
		$base_url = get_permalink();
		?>
		<div class="jobs-admin-panel-container">
			<h1>Jobs Admin Control Panel</h1>

			<div class="jobs-admin-nav">
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'dashboard', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'reports', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'reports' ? 'nav-tab-active' : ''; ?>">Reports</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'activity', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'activity' ? 'nav-tab-active' : ''; ?>">Activity Logs</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'users', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'users' ? 'nav-tab-active' : ''; ?>">Users</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'articles', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'articles' ? 'nav-tab-active' : ''; ?>">Articles</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'design', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'design' ? 'nav-tab-active' : ''; ?>">Design</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'support', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'permissions', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'permissions' ? 'nav-tab-active' : ''; ?>">Permissions</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'ads', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'ads' ? 'nav-tab-active' : ''; ?>">Ads</a>
			</div>

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
						$logo_url = get_option( 'jobs_logo_url', '' );
						$primary_color = get_option( 'jobs_primary_color', '#1d3469' );
						?>
						<h2>Design Customization</h2>
						<form method="post" action="">
							<?php wp_nonce_field( 'jobs_admin_action', 'jobs_admin_nonce' ); ?>
							<p>
								<label for="jobs_logo_url">Site Logo URL</label><br>
								<input type="text" name="jobs_logo_url" id="jobs_logo_url" value="<?php echo esc_attr( $logo_url ); ?>" class="regular-text">
							</p>
							<p>
								<label for="jobs_primary_color">Primary Color</label><br>
								<input type="text" name="jobs_primary_color" id="jobs_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" class="regular-text">
							</p>
							<p>
								<input type="submit" name="jobs_admin_save_settings" value="Save Changes" class="button button-primary">
							</p>
						</form>
						<?php
						break;
					case 'support':
						echo '<h2>Technical Support</h2><p>Support tickets and inquiries.</p>';
						$tickets = get_posts(array('post_type' => 'job_notification', 'posts_per_page' => 10));
						if ($tickets) {
							foreach($tickets as $ticket) {
								echo '<p><strong>' . esc_html($ticket->post_title) . '</strong><br>' . esc_html($ticket->post_content) . '</p>';
							}
						} else {
							echo '<p>No support tickets.</p>';
						}
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
		return ob_get_clean();
	}
}
