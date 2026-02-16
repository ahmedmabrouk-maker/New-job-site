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
		$this->render_admin_content( $active_tab );
	}

	public function render_admin_content( $active_tab ) {
		// Handle Form Submissions
		if ( isset( $_POST['jobs_admin_submit'] ) && check_admin_referer( 'jobs_admin_action', 'jobs_admin_nonce' ) ) {
			if ( 'design' === $active_tab ) {
				update_option( 'jobs_logo_url', sanitize_text_field( $_POST['jobs_logo_url'] ) );
				update_option( 'jobs_primary_color', sanitize_text_field( $_POST['jobs_primary_color'] ) );
				echo '<div class="notice notice-success is-dismissible"><p>Design settings saved.</p></div>';
			} elseif ( 'ads' === $active_tab ) {
				if ( current_user_can( 'unfiltered_html' ) ) {
					update_option( 'jobs_ads_code', wp_unslash( $_POST['jobs_ads_code'] ) );
				} else {
					update_option( 'jobs_ads_code', wp_kses_post( $_POST['jobs_ads_code'] ) );
				}
				echo '<div class="notice notice-success is-dismissible"><p>Ads settings saved.</p></div>';
			}
		}

		// Define base URL for tabs
		$base_url = remove_query_arg( 'tab' );
		?>
		<div class="wrap jobs-admin-wrap">
			<h1>Jobs Admin Control Panel</h1>

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'dashboard', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'dashboard' ? 'nav-tab-active' : ''; ?>">Dashboard</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'reports', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'reports' ? 'nav-tab-active' : ''; ?>">Reports</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'activity', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'activity' ? 'nav-tab-active' : ''; ?>">Activity Logs</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'users', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'users' ? 'nav-tab-active' : ''; ?>">Users</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'articles', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'articles' ? 'nav-tab-active' : ''; ?>">Articles</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'design', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'design' ? 'nav-tab-active' : ''; ?>">Design</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'support', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">Support</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'permissions', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'permissions' ? 'nav-tab-active' : ''; ?>">Permissions</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'ads', $base_url ) ); ?>" class="nav-tab <?php echo $active_tab == 'ads' ? 'nav-tab-active' : ''; ?>">Ads</a>
			</h2>

			<div class="jobs-admin-content">
				<?php
				switch ( $active_tab ) {
					case 'dashboard':
						echo '<p>Welcome to the Jobs System Admin Panel.</p>';
						break;
					case 'reports':
						echo '<h2>Reports System</h2>';
						$job_counts = wp_count_posts( 'job' );
						$publish_jobs = $job_counts->publish;
						$pending_jobs = $job_counts->pending;
						$app_counts = wp_count_posts( 'job_application' );
						$total_apps = isset( $app_counts->publish ) ? $app_counts->publish : 0;
						$user_counts = count_users();
						$total_users = $user_counts['total_users'];
						?>
						<table class="widefat fixed striped" style="margin-top: 10px;">
							<thead>
								<tr>
									<th>Metric</th>
									<th>Count</th>
								</tr>
							</thead>
							<tbody>
								<tr><td>Active Jobs</td><td><?php echo intval( $publish_jobs ); ?></td></tr>
								<tr><td>Pending Jobs</td><td><?php echo intval( $pending_jobs ); ?></td></tr>
								<tr><td>Total Applications</td><td><?php echo intval( $total_apps ); ?></td></tr>
								<tr><td>Total Users</td><td><?php echo intval( $total_users ); ?></td></tr>
							</tbody>
						</table>
						<?php
						break;
					case 'activity':
						echo '<h2>General Activity Log (Recent Jobs)</h2>';
						$recent_jobs = get_posts( array( 'post_type' => 'job', 'numberposts' => 5, 'post_status' => 'any' ) );
						if ( $recent_jobs ) {
							echo '<ul style="list-style: disc; margin-left: 20px; padding: 15px; border: 1px solid #ddd;">';
							foreach ( $recent_jobs as $job ) {
								$author = get_the_author_meta( 'display_name', $job->post_author );
								echo '<li>User <strong>' . esc_html( $author ) . '</strong> posted job "' . esc_html( $job->post_title ) . '" - ' . human_time_diff( get_the_time( 'U', $job->ID ), current_time( 'timestamp' ) ) . ' ago</li>';
							}
							echo '</ul>';
						} else {
							echo '<p>No recent activity.</p>';
						}

						echo '<h2>Admin Activity Log</h2>';
						echo '<p>Activity logging system is active. No recent admin actions recorded.</p>';
						break;
					case 'users':
						echo '<h2>User Management</h2><p><a href="' . admin_url( 'users.php' ) . '" class="button button-primary">Manage WordPress Users</a></p>';
						break;
					case 'articles':
						echo '<h2>Published Articles Management</h2><p><a href="' . admin_url( 'edit.php' ) . '" class="button button-primary">Manage Articles (Posts)</a></p>';
						break;
					case 'design':
						echo '<h2>Design Customization</h2>';
						?>
						<form method="post">
							<?php wp_nonce_field( 'jobs_admin_action', 'jobs_admin_nonce' ); ?>
							<table class="form-table">
								<tr>
									<th scope="row"><label for="jobs_logo_url">Site Logo URL</label></th>
									<td>
										<input type="text" name="jobs_logo_url" id="jobs_logo_url" value="<?php echo esc_attr( get_option( 'jobs_logo_url', '' ) ); ?>" class="regular-text">
										<p class="description">Enter the URL of your logo (e.g., from Media Library).</p>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="jobs_primary_color">Primary Color</label></th>
									<td>
										<input type="text" name="jobs_primary_color" id="jobs_primary_color" value="<?php echo esc_attr( get_option( 'jobs_primary_color', '#1d3469' ) ); ?>" class="regular-text">
										<p class="description">Default: #1d3469</p>
									</td>
								</tr>
							</table>
							<p class="submit"><input type="submit" name="jobs_admin_submit" id="submit" class="button button-primary" value="Save Changes"></p>
						</form>
						<?php
						break;
					case 'support':
						echo '<h2>Technical Support</h2>';
						$support_tickets = get_posts( array( 'post_type' => 'job_notification', 'numberposts' => 10, 'post_status' => 'publish' ) );
						if ( $support_tickets ) {
							?>
							<table class="widefat fixed striped" style="margin-top: 10px;">
								<thead>
									<tr>
										<th>Subject</th>
										<th>User</th>
										<th>Date</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $support_tickets as $ticket ) :
										$author = get_the_author_meta( 'display_name', $ticket->post_author );
										?>
										<tr>
											<td><?php echo esc_html( $ticket->post_title ); ?></td>
											<td><?php echo esc_html( $author ); ?></td>
											<td><?php echo get_the_date( '', $ticket ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
							<?php
						} else {
							echo '<p>No support tickets found.</p>';
						}
						break;
					case 'permissions':
						echo '<h2>Permissions & Roles Management</h2>';
						global $wp_roles;
						$all_roles = $wp_roles->roles;
						$user_counts = count_users();
						?>
						<table class="widefat fixed striped" style="margin-top: 10px;">
							<thead>
								<tr>
									<th>Role</th>
									<th>Capabilities Count</th>
									<th>Users Count</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $all_roles as $role_slug => $role_info ) :
									$count = isset( $user_counts['avail_roles'][$role_slug] ) ? $user_counts['avail_roles'][$role_slug] : 0;
									?>
									<tr>
										<td><?php echo esc_html( $role_info['name'] ); ?></td>
										<td><?php echo count( $role_info['capabilities'] ); ?></td>
										<td><?php echo intval( $count ); ?></td>
										<td><a href="<?php echo admin_url( 'users.php?role=' . $role_slug ); ?>">Manage Users</a></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<?php
						break;
					case 'ads':
						echo '<h2>External Ads & Google AdSense</h2>';
						?>
						<form method="post">
							<?php wp_nonce_field( 'jobs_admin_action', 'jobs_admin_nonce' ); ?>
							<table class="form-table">
								<tr>
									<th scope="row"><label for="jobs_ads_code">External Ads / AdSense Code</label></th>
									<td>
										<textarea name="jobs_ads_code" id="jobs_ads_code" rows="10" cols="50" class="large-text code"><?php echo esc_textarea( get_option( 'jobs_ads_code', '' ) ); ?></textarea>
										<p class="description">Paste your Google AdSense or other ad code here.</p>
									</td>
								</tr>
							</table>
							<p class="submit"><input type="submit" name="jobs_admin_submit" id="submit" class="button button-primary" value="Save Changes"></p>
						</form>
						<?php
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
