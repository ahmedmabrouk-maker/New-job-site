<?php

class Jobs_Shortcodes {

	public function __construct() {
		add_shortcode( 'jobs_search', array( $this, 'render_search' ) );
		add_shortcode( 'jobs_login', array( $this, 'render_login' ) );
		add_shortcode( 'jobs_register', array( $this, 'render_register' ) );
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_admin_panel' ) );

		add_action( 'init', array( $this, 'handle_registration' ) );
		add_action( 'init', array( $this, 'handle_pdf_generation' ) );
	}

	public function handle_pdf_generation() {
		if ( isset( $_GET['jobs_pdf_resume'] ) && $_GET['jobs_pdf_resume'] == '1' ) {
			if ( ! is_user_logged_in() ) {
				wp_die( 'You must be logged in to view your resume.', 'Access Denied' );
			}

			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
			$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );

			if ( empty( $cv_data ) ) {
				wp_die( 'No CV data found.', 'Error' );
			}

			$education = isset( $cv_data['education'] ) ? $cv_data['education'] : array();
			$experience = isset( $cv_data['experience'] ) ? $cv_data['experience'] : array();
			$skills = isset( $cv_data['skills'] ) ? $cv_data['skills'] : '';
			$courses = isset( $cv_data['courses'] ) ? $cv_data['courses'] : array();
			$certifications = isset( $cv_data['certifications'] ) ? $cv_data['certifications'] : array();

			?>
			<!DOCTYPE html>
			<html>
			<head>
				<title><?php echo esc_html( $user->display_name ); ?> - Resume</title>
				<style>
					body { font-family: 'Rubik', sans-serif; color: #333; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 40px; }
					h1 { color: #1d3469; border-bottom: 2px solid #1d3469; padding-bottom: 10px; }
					h2 { color: #1d3469; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
					.section { margin-bottom: 20px; }
					.item { margin-bottom: 15px; }
					.item-title { font-weight: bold; font-size: 1.1em; }
					.item-meta { color: #666; font-style: italic; }
					@media print {
						body { padding: 0; }
						.no-print { display: none; }
					}
				</style>
				<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
			</head>
			<body>
				<h1><?php echo esc_html( $user->display_name ); ?></h1>
				<p><?php echo esc_html( $user->user_email ); ?></p>

				<?php if ( ! empty( $skills ) ) : ?>
				<div class="section">
					<h2>Skills</h2>
					<p><?php echo esc_html( $skills ); ?></p>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $experience ) ) : ?>
				<div class="section">
					<h2>Experience</h2>
					<?php foreach ( $experience as $exp ) : ?>
						<div class="item">
							<div class="item-title"><?php echo esc_html( $exp['position'] ); ?> at <?php echo esc_html( $exp['company'] ); ?></div>
							<div class="item-meta"><?php echo esc_html( $exp['years'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $education ) ) : ?>
				<div class="section">
					<h2>Education</h2>
					<?php foreach ( $education as $edu ) : ?>
						<div class="item">
							<div class="item-title"><?php echo esc_html( $edu['degree'] ); ?></div>
							<div class="item-meta"><?php echo esc_html( $edu['school'] ); ?> - <?php echo esc_html( $edu['year'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $courses ) ) : ?>
				<div class="section">
					<h2>Courses</h2>
					<?php foreach ( $courses as $course ) : ?>
						<div class="item">
							<div class="item-title"><?php echo esc_html( $course['name'] ); ?></div>
							<div class="item-meta"><?php echo esc_html( $course['institution'] ); ?> - <?php echo esc_html( $course['year'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $certifications ) ) : ?>
				<div class="section">
					<h2>Certifications</h2>
					<?php foreach ( $certifications as $cert ) : ?>
						<div class="item">
							<div class="item-title"><?php echo esc_html( $cert['name'] ); ?></div>
							<div class="item-meta"><?php echo esc_html( $cert['authority'] ); ?> - <?php echo esc_html( $cert['year'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<script>window.print();</script>
			</body>
			</html>
			<?php
			exit;
		}
	}

	public function handle_registration() {
		if ( isset( $_POST['jobs_register_submit'] ) && isset( $_POST['jobs_register_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_POST['jobs_register_nonce'], 'jobs_register_action' ) ) {
				wp_die( 'Security check failed' );
			}

			$username = sanitize_user( $_POST['user_login'] );
			$email    = sanitize_email( $_POST['user_email'] );
			$password = $_POST['user_pass'];
			$role     = sanitize_text_field( $_POST['role'] );

			// Basic validation
			if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
				// In a real scenario, handle error gracefully (e.g. redirect with error code)
				return;
			}

			// Validate role
			if ( ! in_array( $role, array( 'job_seeker', 'employer' ) ) ) {
				$role = 'job_seeker';
			}

			$user_id = wp_create_user( $username, $password, $email );

			if ( is_wp_error( $user_id ) ) {
				// Handle error
			} else {
				$user = new WP_User( $user_id );
				$user->set_role( $role );

				// Redirect to same page with success message
				wp_redirect( add_query_arg( 'registration', 'success', $_SERVER['REQUEST_URI'] ) );
				exit;
			}
		}
	}

	public function render_search( $atts ) {
		ob_start();
		?>
		<div class="jobs-search-container">
			<div class="jobs-search-header">
				<?php
				$logo_url = get_option( 'jobs_logo_url', '' );
				if ( $logo_url ) {
					echo '<img src="' . esc_url( $logo_url ) . '" alt="Jobs Logo" class="jobs-logo">';
				} else {
					echo '<h1 class="jobs-logo-text">Jobs</h1>';
				}
				?>
			</div>
			<form role="search" method="get" class="jobs-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<input type="hidden" name="post_type" value="job" />
				<div class="jobs-search-fields">
					<input type="text" name="s" placeholder="Search jobs..." class="jobs-input-main" />
					<select name="job_specialization" class="jobs-select">
						<option value="">Specialization</option>
						<?php
						$terms = get_terms( array( 'taxonomy' => 'job_specialization', 'hide_empty' => false ) );
						if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
							foreach ( $terms as $term ) {
								echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
							}
						}
						?>
					</select>
					<select name="job_country" class="jobs-select">
						<option value="">Country</option>
						<?php
						$terms = get_terms( array( 'taxonomy' => 'job_country', 'hide_empty' => false ) );
						if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
							foreach ( $terms as $term ) {
								echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
							}
						}
						?>
					</select>
					<select name="job_city" class="jobs-select">
						<option value="">City</option>
						<?php
						$terms = get_terms( array( 'taxonomy' => 'job_city', 'hide_empty' => false ) );
						if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
							foreach ( $terms as $term ) {
								echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
							}
						}
						?>
					</select>
					<button type="submit" class="jobs-submit-btn">Search</button>
				</div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_login( $atts ) {
		if ( is_user_logged_in() ) {
			return '<p>You are already logged in.</p>';
		}

		ob_start();

		$redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : home_url();

		$args = array(
			'echo' => true,
			'redirect' => $redirect_to,
			'form_id' => 'jobs-login-form',
			'label_username' => __( 'Username or Email Address' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in' => __( 'Log In' ),
			'id_username' => 'user_login',
			'id_password' => 'user_pass',
			'id_remember' => 'rememberme',
			'id_submit' => 'wp-submit',
			'remember' => true,
			'value_username' => '',
			'value_remember' => false
		);

		wp_login_form( $args );

		return ob_get_clean();
	}

	public function render_register( $atts ) {
		if ( is_user_logged_in() ) {
			return '<p>You are already logged in.</p>';
		}

		ob_start();

		if ( isset( $_GET['registration'] ) && 'success' === $_GET['registration'] ) {
			echo '<p class="jobs-success">Registration successful! You can now login.</p>';
		}

		?>
		<div class="jobs-register-container">
			<form method="post" class="jobs-register-form">
				<?php wp_nonce_field( 'jobs_register_action', 'jobs_register_nonce' ); ?>
				<p>
					<label for="user_login">Username</label>
					<input type="text" name="user_login" id="user_login" class="input" value="" size="20" required />
				</p>
				<p>
					<label for="user_email">Email</label>
					<input type="email" name="user_email" id="user_email" class="input" value="" size="25" required />
				</p>
				<p>
					<label for="user_pass">Password</label>
					<input type="password" name="user_pass" id="user_pass" class="input" value="" size="25" required />
				</p>
				<p>
					<label for="role">I am a:</label>
					<select name="role" id="role">
						<option value="job_seeker">Job Seeker</option>
						<option value="employer">Employer</option>
					</select>
				</p>
				<br class="clear" />
				<p class="submit">
					<input type="submit" name="jobs_register_submit" id="jobs_register_submit" class="button button-primary button-large" value="Register" />
				</p>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	public function render_admin_panel() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '<p style="color:red; text-align:center; margin-top:50px;">Access Denied. You must be an administrator to view this page.</p>';
		}

		// Save settings if posted
		if ( isset( $_POST['jobs_admin_submit'] ) && check_admin_referer( 'jobs_admin_panel_nonce', 'jobs_admin_panel_field' ) ) {
			if ( isset( $_POST['jobs_logo_url'] ) ) {
				update_option( 'jobs_logo_url', esc_url_raw( $_POST['jobs_logo_url'] ) );
			}
			if ( isset( $_POST['jobs_primary_color'] ) ) {
				update_option( 'jobs_primary_color', sanitize_hex_color( $_POST['jobs_primary_color'] ) );
			}
			if ( isset( $_POST['jobs_ads_code'] ) ) {
				if ( current_user_can( 'unfiltered_html' ) ) {
					update_option( 'jobs_ads_code', $_POST['jobs_ads_code'] );
				} else {
					update_option( 'jobs_ads_code', wp_kses_post( $_POST['jobs_ads_code'] ) );
				}
			}
			echo '<div class="jobs-notice jobs-success">Settings saved successfully.</div>';
		}

		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'dashboard';
		$base_url = get_permalink();

		ob_start();
		?>
		<div class="jobs-admin-panel-container">
			<div class="jobs-admin-sidebar">
				<ul>
					<li class="<?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'dashboard', $base_url ); ?>">Dashboard</a></li>
					<li class="<?php echo $active_tab == 'reports' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'reports', $base_url ); ?>">Reports</a></li>
					<li class="<?php echo $active_tab == 'activity' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'activity', $base_url ); ?>">Activity Logs</a></li>
					<li class="<?php echo $active_tab == 'users' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'users', $base_url ); ?>">Users</a></li>
					<li class="<?php echo $active_tab == 'articles' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'articles', $base_url ); ?>">Articles</a></li>
					<li class="<?php echo $active_tab == 'design' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'design', $base_url ); ?>">Design</a></li>
					<li class="<?php echo $active_tab == 'support' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'support', $base_url ); ?>">Support</a></li>
					<li class="<?php echo $active_tab == 'permissions' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'permissions', $base_url ); ?>">Permissions</a></li>
					<li class="<?php echo $active_tab == 'ads' ? 'active' : ''; ?>"><a href="<?php echo add_query_arg( 'tab', 'ads', $base_url ); ?>">Ads</a></li>
				</ul>
			</div>
			<div class="jobs-admin-content">
				<?php
				switch ( $active_tab ) {
					case 'dashboard':
						echo '<h2>Welcome to Jobs Admin Panel</h2>';
						echo '<p>Select a tab from the sidebar to manage the system.</p>';
						break;
					case 'reports':
						$active_jobs = wp_count_posts( 'job' )->publish;
						$pending_jobs = wp_count_posts( 'job' )->pending;
						$applications = wp_count_posts( 'application' )->publish;
						$total_users = count_users()['total_users'];
						echo '<h2>System Reports</h2>';
						echo '<div class="jobs-report-cards">';
						echo '<div class="jobs-card"><h3>Active Jobs</h3><p class="jobs-number">' . intval( $active_jobs ) . '</p></div>';
						echo '<div class="jobs-card"><h3>Pending Jobs</h3><p class="jobs-number">' . intval( $pending_jobs ) . '</p></div>';
						echo '<div class="jobs-card"><h3>Total Applications</h3><p class="jobs-number">' . intval( $applications ) . '</p></div>';
						echo '<div class="jobs-card"><h3>Total Users</h3><p class="jobs-number">' . intval( $total_users ) . '</p></div>';
						echo '</div>';
						break;
					case 'activity':
						echo '<h2>Recent Activity</h2>';
						$recent_jobs = wp_get_recent_posts( array( 'post_type' => 'job', 'numberposts' => 10, 'post_status' => 'any' ) );
						echo '<ul class="jobs-activity-list">';
						foreach ( $recent_jobs as $job ) {
							echo '<li>New Job: <strong>' . esc_html( $job['post_title'] ) . '</strong> (' . $job['post_status'] . ') by User ID ' . $job['post_author'] . ' - ' . get_the_time( 'Y-m-d H:i', $job['ID'] ) . '</li>';
						}
						echo '</ul>';
						break;
					case 'users':
						echo '<h2>User Management</h2>';
						$users = get_users( array( 'number' => 20 ) );
						echo '<table class="jobs-table"><thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Roles</th></tr></thead><tbody>';
						foreach ( $users as $user ) {
							echo '<tr>';
							echo '<td>' . $user->ID . '</td>';
							echo '<td>' . esc_html( $user->user_login ) . '</td>';
							echo '<td>' . esc_html( $user->user_email ) . '</td>';
							echo '<td>' . implode( ', ', $user->roles ) . '</td>';
							echo '</tr>';
						}
						echo '</tbody></table>';
						break;
					case 'articles':
						echo '<h2>Articles Management</h2>';
						echo '<p><a href="' . admin_url( 'edit.php' ) . '" class="button button-primary" target="_blank">Manage Articles in WordPress Backend</a></p>';
						break;
					case 'design':
						echo '<h2>Design Customization</h2>';
						echo '<form method="post">';
						wp_nonce_field( 'jobs_admin_panel_nonce', 'jobs_admin_panel_field' );
						echo '<p><label>Logo URL:</label><br><input type="text" name="jobs_logo_url" value="' . esc_attr( get_option( 'jobs_logo_url' ) ) . '" class="regular-text"></p>';
						echo '<p><label>Primary Color:</label><br><input type="text" name="jobs_primary_color" value="' . esc_attr( get_option( 'jobs_primary_color', '#1d3469' ) ) . '" class="regular-text"></p>';
						echo '<p><input type="submit" name="jobs_admin_submit" class="button button-primary" value="Save Changes"></p>';
						echo '</form>';
						break;
					case 'support':
						echo '<h2>Support Tickets</h2>';
						$tickets = get_posts( array( 'post_type' => 'job_notification', 'numberposts' => 10 ) );
						if ( empty( $tickets ) ) {
							echo '<p>No support tickets found.</p>';
						} else {
							echo '<ul class="jobs-support-list">';
							foreach ( $tickets as $ticket ) {
								echo '<li><strong>' . esc_html( $ticket->post_title ) . '</strong><br>' . wp_trim_words( $ticket->post_content, 20 ) . '<br><small>From User ID: ' . $ticket->post_author . '</small></li>';
							}
							echo '</ul>';
						}
						break;
					case 'permissions':
						echo '<h2>Permissions & Roles</h2>';
						global $wp_roles;
						echo '<ul class="jobs-roles-list">';
						foreach ( $wp_roles->roles as $role_key => $role_details ) {
							echo '<li><strong>' . esc_html( $role_details['name'] ) . '</strong> (' . count( $role_details['capabilities'] ) . ' capabilities)</li>';
						}
						echo '</ul>';
						break;
					case 'ads':
						echo '<h2>External Ads Management</h2>';
						echo '<form method="post">';
						wp_nonce_field( 'jobs_admin_panel_nonce', 'jobs_admin_panel_field' );
						echo '<p><label>Ad Code (Google AdSense, etc.):</label><br><textarea name="jobs_ads_code" rows="10" class="large-text code">' . esc_textarea( get_option( 'jobs_ads_code' ) ) . '</textarea></p>';
						echo '<p><input type="submit" name="jobs_admin_submit" class="button button-primary" value="Save Ads"></p>';
						echo '</form>';
						break;
				}
				?>
			</div>
		</div>
		<style>
			.jobs-admin-panel-container { display: flex; gap: 20px; background: transparent; }
			.jobs-admin-sidebar { width: 200px; border-right: 1px solid #ccc; padding-right: 20px; }
			.jobs-admin-sidebar ul { list-style: none; padding: 0; }
			.jobs-admin-sidebar li { margin-bottom: 10px; }
			.jobs-admin-sidebar a { text-decoration: none; color: #1d3469; font-weight: 500; display: block; padding: 10px; border-radius: 5px; }
			.jobs-admin-sidebar li.active a, .jobs-admin-sidebar a:hover { background: #f0f0f0; color: #000; }
			.jobs-admin-content { flex: 1; padding: 20px; background: transparent; border: 1px solid #ccc; border-radius: 8px; }
			.jobs-report-cards { display: flex; gap: 20px; flex-wrap: wrap; }
			.jobs-card { flex: 1; background: transparent; border: 1px solid #ccc; padding: 20px; border-radius: 8px; text-align: center; }
			.jobs-number { font-size: 32px; font-weight: bold; color: #1d3469; margin: 10px 0 0; }
			.jobs-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
			.jobs-table th, .jobs-table td { text-align: left; padding: 10px; border-bottom: 1px solid #ccc; }
			.jobs-activity-list li, .jobs-support-list li { padding: 10px; border-bottom: 1px solid #ccc; }
		</style>
		<?php
		return ob_get_clean();
	}
}
