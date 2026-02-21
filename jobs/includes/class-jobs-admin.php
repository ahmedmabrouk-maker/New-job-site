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
		register_setting( 'jobs_options_group', 'jobs_ads_code' );
		register_setting( 'jobs_options_group', 'jobs_search_settings' );

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
		$page_ids = get_option( 'jobs_page_ids', array() );
		$admin_page_id = isset( $page_ids['admin_panel'] ) ? $page_ids['admin_panel'] : 0;
		if ( $admin_page_id ) {
			echo '<div class="wrap"><h1>Jobs Admin</h1><p><a href="' . get_permalink( $admin_page_id ) . '" class="button button-primary" target="_blank">Open Jobs Admin Panel</a></p></div>';
		} else {
			echo '<div class="wrap"><h1>Jobs Admin</h1><p>Admin panel page not found. Please ensure the page with [jobs_admin_panel] shortcode exists.</p></div>';
		}
	}

	public static function render_frontend_panel() {
		if ( ! current_user_can( 'manage_options' ) ) {
			echo '<p>Access Denied.</p>';
			return;
		}

		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'dashboard';
		$base_url = add_query_arg( 'tab', false, get_permalink() ); // Clean base URL

		?>
		<style>
			.jobs-admin-wrapper { font-family: 'Rubik', sans-serif; background: transparent; }
			.jobs-admin-nav { display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; flex-wrap: wrap; }
			.jobs-admin-nav a { text-decoration: none; color: #1d3469; padding: 10px 15px; border-radius: 5px; transition: background 0.2s; font-weight: 500; }
			.jobs-admin-nav a:hover, .jobs-admin-nav a.active { background: #1d3469; color: #fff; }
			.jobs-admin-content { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
			.jobs-stat-card { background: #f9f9f9; padding: 20px; border-radius: 8px; text-align: center; border: 1px solid #eee; flex: 1; min-width: 150px; }
			.jobs-stats-grid { display: flex; gap: 20px; flex-wrap: wrap; }
			.jobs-stat-number { font-size: 32px; font-weight: bold; color: #1d3469; display: block; }
			.jobs-stat-label { color: #666; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
			table.widefat { width: 100%; border-collapse: collapse; margin-top: 10px; }
			table.widefat th, table.widefat td { text-align: left; padding: 10px; border-bottom: 1px solid #eee; }
			table.widefat th { font-weight: 600; color: #1d3469; }
			.jobs-form-group { margin-bottom: 15px; }
			.jobs-form-group label { display: block; margin-bottom: 5px; font-weight: 500; }
			.jobs-form-group input, .jobs-form-group textarea, .jobs-form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
		</style>

		<div class="jobs-admin-wrapper">
			<div class="jobs-admin-nav">
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'dashboard', $base_url ) ); ?>" class="<?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'reports', $base_url ) ); ?>" class="<?php echo $active_tab == 'reports' ? 'active' : ''; ?>">Reports</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'activity', $base_url ) ); ?>" class="<?php echo $active_tab == 'activity' ? 'active' : ''; ?>">Activity Logs</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'users', $base_url ) ); ?>" class="<?php echo $active_tab == 'users' ? 'active' : ''; ?>">Users</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'articles', $base_url ) ); ?>" class="<?php echo $active_tab == 'articles' ? 'active' : ''; ?>">Articles</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'design', $base_url ) ); ?>" class="<?php echo $active_tab == 'design' ? 'active' : ''; ?>">Design</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'search', $base_url ) ); ?>" class="<?php echo $active_tab == 'search' ? 'active' : ''; ?>">Search Engine</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'support', $base_url ) ); ?>" class="<?php echo $active_tab == 'support' ? 'active' : ''; ?>">Support</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'permissions', $base_url ) ); ?>" class="<?php echo $active_tab == 'permissions' ? 'active' : ''; ?>">Permissions</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'ads', $base_url ) ); ?>" class="<?php echo $active_tab == 'ads' ? 'active' : ''; ?>">Ads</a>
			</div>

			<div class="jobs-admin-content">
				<?php
				switch ( $active_tab ) {
					case 'dashboard':
						echo '<h2>Welcome, System Administrator</h2>';
						echo '<p>Use the navigation tabs to manage the Jobs system.</p>';
						break;

					case 'reports':
						echo '<h2>System Reports</h2>';
						$job_counts = wp_count_posts( 'job' );
						$app_counts = wp_count_posts( 'job_application' );
						$total_users = count_users();
						?>
						<div class="jobs-stats-grid">
							<div class="jobs-stat-card">
								<span class="jobs-stat-number"><?php echo isset( $job_counts->publish ) ? intval( $job_counts->publish ) : 0; ?></span>
								<span class="jobs-stat-label">Active Jobs</span>
							</div>
							<div class="jobs-stat-card">
								<span class="jobs-stat-number"><?php echo isset( $job_counts->pending ) ? intval( $job_counts->pending ) : 0; ?></span>
								<span class="jobs-stat-label">Pending Jobs</span>
							</div>
							<div class="jobs-stat-card">
								<span class="jobs-stat-number"><?php echo isset( $app_counts->publish ) ? intval( $app_counts->publish ) : 0; ?></span>
								<span class="jobs-stat-label">Total Applications</span>
							</div>
							<div class="jobs-stat-card">
								<span class="jobs-stat-number"><?php echo isset( $total_users['total_users'] ) ? intval( $total_users['total_users'] ) : 0; ?></span>
								<span class="jobs-stat-label">Total Users</span>
							</div>
						</div>
						<?php
						break;

					case 'activity':
						echo '<h2>Activity Logs</h2>';
						$logs = get_posts( array(
							'post_type'      => 'job_activity',
							'posts_per_page' => 50,
							'orderby'        => 'date',
							'order'          => 'DESC',
						) );

						if ( $logs ) {
							echo '<table class="widefat">';
							echo '<thead><tr><th>Date</th><th>User</th><th>Action</th><th>Details</th></tr></thead>';
							echo '<tbody>';
							foreach ( $logs as $log ) {
								$user = get_userdata( $log->post_author );
								echo '<tr>';
								echo '<td>' . get_the_date( 'Y-m-d H:i:s', $log ) . '</td>';
								echo '<td>' . ( $user ? esc_html( $user->display_name ) : 'Unknown' ) . '</td>';
								echo '<td>' . esc_html( $log->post_title ) . '</td>';
								echo '<td>' . esc_html( $log->post_content ) . '</td>';
								echo '</tr>';
							}
							echo '</tbody></table>';
						} else {
							echo '<p>No activity recorded yet.</p>';
						}
						break;

					case 'users':
						echo '<h2>User Management</h2>';
						$users = get_users( array( 'number' => 20 ) );
						echo '<table class="widefat">';
						echo '<thead><tr><th>Username</th><th>Email</th><th>Role</th><th>Registered</th><th>Actions</th></tr></thead>';
						echo '<tbody>';
						foreach ( $users as $user ) {
							$roles = ! empty( $user->roles ) ? implode( ', ', $user->roles ) : 'None';
							echo '<tr>';
							echo '<td>' . esc_html( $user->user_login ) . '</td>';
							echo '<td>' . esc_html( $user->user_email ) . '</td>';
							echo '<td>' . esc_html( $roles ) . '</td>';
							echo '<td>' . esc_html( $user->user_registered ) . '</td>';
							echo '<td>';
							echo '<a href="' . get_edit_user_link( $user->ID ) . '" target="_blank">Edit</a>';
							echo '</td>';
							echo '</tr>';
						}
						echo '</tbody></table>';
						echo '<p><a href="' . admin_url( 'user-new.php' ) . '" class="button button-primary" target="_blank">Add New User</a></p>';
						break;

					case 'articles':
						echo '<h2>Published Articles</h2>';
						$recent_posts = wp_get_recent_posts( array( 'numberposts' => 10, 'post_status' => 'publish' ) );
						echo '<table class="widefat">';
						echo '<thead><tr><th>Title</th><th>Date</th><th>Author</th><th>Actions</th></tr></thead>';
						echo '<tbody>';
						foreach ( $recent_posts as $post ) {
							echo '<tr>';
							echo '<td>' . esc_html( $post['post_title'] ) . '</td>';
							echo '<td>' . esc_html( $post['post_date'] ) . '</td>';
							echo '<td>' . get_the_author_meta( 'display_name', $post['post_author'] ) . '</td>';
							echo '<td><a href="' . get_edit_post_link( $post['ID'] ) . '" target="_blank">Edit</a></td>';
							echo '</tr>';
						}
						echo '</tbody></table>';
						echo '<p><a href="' . admin_url( 'post-new.php' ) . '" class="button button-primary" target="_blank">Add New Article</a></p>';
						break;

					case 'design':
						echo '<h2>Design Customization</h2>';
						?>
						<form id="jobs-design-form">
							<div class="jobs-form-group">
								<label>Site Logo URL</label>
								<input type="text" id="jobs_logo_url" value="<?php echo esc_attr( get_option( 'jobs_logo_url' ) ); ?>">
							</div>
							<div class="jobs-form-group">
								<label>Primary Color</label>
								<input type="text" id="jobs_primary_color" value="<?php echo esc_attr( get_option( 'jobs_primary_color', '#1d3469' ) ); ?>">
							</div>
							<button type="button" class="button button-primary" onclick="saveAdminSettings()">Save Design</button>
						</form>
						<?php
						break;

					case 'search':
						echo '<h2>Search Engine Settings</h2>';
						$search_settings = get_option( 'jobs_search_settings', array() );
						$placeholder = isset( $search_settings['placeholder'] ) ? $search_settings['placeholder'] : 'Search jobs...';
						?>
						<form id="jobs-search-form">
							<div class="jobs-form-group">
								<label>Main Search Placeholder</label>
								<input type="text" id="jobs_search_placeholder" value="<?php echo esc_attr( $placeholder ); ?>">
							</div>
							<button type="button" class="button button-primary" onclick="saveSearchSettings()">Save Settings</button>
						</form>
						<?php
						break;

					case 'support':
						echo '<h2>Technical Support (Tickets)</h2>';
						$tickets = get_posts( array( 'post_type' => 'job_notification', 'numberposts' => 20 ) );
						if ( $tickets ) {
							echo '<table class="widefat">';
							echo '<thead><tr><th>Subject</th><th>From</th><th>Date</th><th>Content</th></tr></thead>';
							echo '<tbody>';
							foreach ( $tickets as $ticket ) {
								$author = get_userdata( $ticket->post_author );
								echo '<tr>';
								echo '<td>' . esc_html( $ticket->post_title ) . '</td>';
								echo '<td>' . ( $author ? esc_html( $author->display_name ) : 'Guest' ) . '</td>';
								echo '<td>' . esc_html( $ticket->post_date ) . '</td>';
								echo '<td>' . esc_html( wp_trim_words( $ticket->post_content, 10 ) ) . '</td>';
								echo '</tr>';
							}
							echo '</tbody></table>';
						} else {
							echo '<p>No support tickets found.</p>';
						}
						break;

					case 'permissions':
						echo '<h2>Permissions & Roles</h2>';
						global $wp_roles;
						echo '<table class="widefat">';
						echo '<thead><tr><th>Role</th><th>Capabilities Count</th><th>Users Count</th></tr></thead>';
						echo '<tbody>';
						foreach ( $wp_roles->roles as $role_slug => $role_info ) {
							$user_count = count_users();
							$count = isset( $user_count['avail_roles'][ $role_slug ] ) ? $user_count['avail_roles'][ $role_slug ] : 0;
							echo '<tr>';
							echo '<td>' . esc_html( $role_info['name'] ) . ' (' . esc_html( $role_slug ) . ')</td>';
							echo '<td>' . count( $role_info['capabilities'] ) . '</td>';
							echo '<td>' . $count . '</td>';
							echo '</tr>';
						}
						echo '</tbody></table>';
						break;

					case 'ads':
						echo '<h2>External Ads & Google AdSense</h2>';
						?>
						<form id="jobs-ads-form">
							<div class="jobs-form-group">
								<label>AdSense / Ads Code (HTML/JS)</label>
								<textarea id="jobs_ads_code" rows="10"><?php echo esc_textarea( get_option( 'jobs_ads_code' ) ); ?></textarea>
							</div>
							<button type="button" class="button button-primary" onclick="saveAdminAds()">Save Ads</button>
						</form>
						<?php
						break;
				}
				?>
			</div>
		</div>

		<script>
		function saveAdminSettings() {
			var logo = document.getElementById('jobs_logo_url').value;
			var color = document.getElementById('jobs_primary_color').value;
			jQuery.post(jobs_ajax.ajax_url, {
				action: 'jobs_update_design',
				nonce: jobs_ajax.nonce,
				logo_url: logo,
				primary_color: color
			}, function(res) {
				alert(res.data);
			});
		}
		function saveAdminAds() {
			var code = document.getElementById('jobs_ads_code').value;
			jQuery.post(jobs_ajax.ajax_url, {
				action: 'jobs_update_ads',
				nonce: jobs_ajax.nonce,
				ads_code: code
			}, function(res) {
				alert(res.data);
			});
		}
		function saveSearchSettings() {
			var placeholder = document.getElementById('jobs_search_placeholder').value;
			jQuery.post(jobs_ajax.ajax_url, {
				action: 'jobs_update_search',
				nonce: jobs_ajax.nonce,
				placeholder: placeholder
			}, function(res) {
				alert(res.data);
			});
		}
		</script>
		<?php
	}
}
