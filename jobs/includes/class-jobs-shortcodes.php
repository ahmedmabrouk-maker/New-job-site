<?php

class Jobs_Shortcodes {

	public function __construct() {
		add_shortcode( 'jobs_search', array( $this, 'render_search' ) );
		add_shortcode( 'jobs_login', array( $this, 'render_login' ) );
		add_shortcode( 'jobs_register', array( $this, 'render_register' ) );
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_admin_panel' ) );

		add_action( 'init', array( $this, 'handle_registration' ) );
		add_action( 'init', array( $this, 'handle_pdf_resume' ) );
	}

	public function handle_pdf_resume() {
		if ( isset( $_GET['jobs_pdf_resume'] ) && '1' == $_GET['jobs_pdf_resume'] ) {
			if ( ! is_user_logged_in() ) {
				wp_die( 'You must be logged in to download your resume.' );
			}

			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
			$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );

			if ( ! $cv_data ) {
				wp_die( 'No CV data found.' );
			}

			$education = isset( $cv_data['education'] ) ? $cv_data['education'] : array();
			$experience = isset( $cv_data['experience'] ) ? $cv_data['experience'] : array();
			$skills = isset( $cv_data['skills'] ) ? $cv_data['skills'] : '';
			$courses = isset( $cv_data['courses'] ) ? $cv_data['courses'] : array();
			$certifications = isset( $cv_data['certifications'] ) ? $cv_data['certifications'] : array();

			?>
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<title>Resume - <?php echo esc_html( $user->display_name ); ?></title>
				<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
				<style>
					body { font-family: 'Rubik', sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 40px; }
					h1 { color: #1d3469; border-bottom: 2px solid #1d3469; padding-bottom: 10px; margin-bottom: 20px; }
					h2 { color: #1d3469; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
					.job-item, .edu-item, .course-item, .cert-item { margin-bottom: 15px; }
					.job-title, .edu-school { font-weight: 700; font-size: 18px; }
					.job-meta, .edu-meta { font-style: italic; color: #666; font-size: 14px; }
					.skills-list { background: #f9f9f9; padding: 15px; border-radius: 5px; }
					@media print {
						body { padding: 0; }
						.no-print { display: none; }
					}
				</style>
			</head>
			<body>
				<div class="no-print" style="margin-bottom: 20px; text-align: right;">
					<button onclick="window.print()" style="padding: 10px 20px; background: #1d3469; color: #fff; border: none; cursor: pointer; border-radius: 4px;">Print / Save as PDF</button>
				</div>

				<h1><?php echo esc_html( $user->display_name ); ?></h1>
				<p>Email: <?php echo esc_html( $user->user_email ); ?></p>

				<?php if ( ! empty( $experience ) ) : ?>
				<h2>Experience</h2>
				<?php foreach ( $experience as $exp ) : ?>
					<div class="job-item">
						<div class="job-title"><?php echo esc_html( $exp['position'] ); ?> at <?php echo esc_html( $exp['company'] ); ?></div>
						<div class="job-meta"><?php echo esc_html( $exp['years'] ); ?></div>
					</div>
				<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( ! empty( $education ) ) : ?>
				<h2>Education</h2>
				<?php foreach ( $education as $edu ) : ?>
					<div class="edu-item">
						<div class="edu-school"><?php echo esc_html( $edu['school'] ); ?></div>
						<div class="edu-meta"><?php echo esc_html( $edu['degree'] ); ?> - <?php echo esc_html( $edu['year'] ); ?></div>
					</div>
				<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( ! empty( $courses ) ) : ?>
				<h2>Courses</h2>
				<?php foreach ( $courses as $course ) : ?>
					<div class="course-item">
						<div class="job-title"><?php echo esc_html( $course['name'] ); ?></div>
						<div class="job-meta"><?php echo esc_html( $course['provider'] ); ?> - <?php echo esc_html( $course['year'] ); ?></div>
					</div>
				<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( ! empty( $certifications ) ) : ?>
				<h2>Certifications</h2>
				<?php foreach ( $certifications as $cert ) : ?>
					<div class="cert-item">
						<div class="job-title"><?php echo esc_html( $cert['name'] ); ?></div>
						<div class="job-meta"><?php echo esc_html( $cert['issuer'] ); ?> - <?php echo esc_html( $cert['year'] ); ?></div>
					</div>
				<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( ! empty( $skills ) ) : ?>
				<h2>Skills</h2>
				<div class="skills-list">
					<?php echo esc_html( $skills ); ?>
				</div>
				<?php endif; ?>

				<script>
					window.onload = function() { window.print(); }
				</script>
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

	public function render_admin_panel( $atts ) {
		if ( ! is_user_logged_in() || ! current_user_can( 'administrator' ) ) {
			return '<p>Access Denied. You must be an administrator to view this page.</p>';
		}

		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'dashboard';
		$base_url = get_permalink(); // URL of the page containing the shortcode

		ob_start();
		?>
		<div class="jobs-admin-panel-frontend">
			<div class="jobs-admin-nav">
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'dashboard', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'reports', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'reports' ? 'active' : ''; ?>">Reports</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'activity', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'activity' ? 'active' : ''; ?>">Activity Logs</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'users', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'users' ? 'active' : ''; ?>">Users</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'articles', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'articles' ? 'active' : ''; ?>">Articles</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'design', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'design' ? 'active' : ''; ?>">Design</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'support', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'support' ? 'active' : ''; ?>">Support</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'permissions', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'permissions' ? 'active' : ''; ?>">Permissions</a>
				<a href="<?php echo esc_url( add_query_arg( 'tab', 'ads', $base_url ) ); ?>" class="jobs-nav-tab <?php echo $active_tab == 'ads' ? 'active' : ''; ?>">Ads</a>
			</div>

			<div class="jobs-admin-content">
				<?php
				switch ( $active_tab ) {
					case 'dashboard':
						echo '<h2>Dashboard</h2>';
						echo '<p>Welcome to the Jobs System Admin Panel.</p>';
						echo '<div class="jobs-dashboard-widgets">';
						// Quick stats widget
						$active_jobs = wp_count_posts( 'job' )->publish;
						$pending_jobs = wp_count_posts( 'job' )->pending;
						$users_count = count_users();
						$total_users = $users_count['total_users'];

						echo '<div class="jobs-card"><h3>Active Jobs</h3><p class="jobs-big-number">' . intval( $active_jobs ) . '</p></div>';
						echo '<div class="jobs-card"><h3>Pending Jobs</h3><p class="jobs-big-number">' . intval( $pending_jobs ) . '</p></div>';
						echo '<div class="jobs-card"><h3>Total Users</h3><p class="jobs-big-number">' . intval( $total_users ) . '</p></div>';
						echo '</div>';
						break;

					case 'reports':
						echo '<h2>Reports System</h2>';
						$active_jobs = wp_count_posts( 'job' )->publish;
						$pending_jobs = wp_count_posts( 'job' )->pending;
						$apps_count = wp_count_posts( 'job_application' )->publish;
						$users_count = count_users();

						echo '<table class="widefat fixed striped">';
						echo '<thead><tr><th>Metric</th><th>Count</th></tr></thead>';
						echo '<tbody>';
						echo '<tr><td>Active Jobs</td><td>' . intval( $active_jobs ) . '</td></tr>';
						echo '<tr><td>Pending Jobs</td><td>' . intval( $pending_jobs ) . '</td></tr>';
						echo '<tr><td>Total Applications</td><td>' . intval( $apps_count ) . '</td></tr>';
						echo '<tr><td>Total Users</td><td>' . intval( $users_count['total_users'] ) . '</td></tr>';
						echo '</tbody></table>';
						break;

					case 'activity':
						echo '<h2>Activity Logs</h2>';
						echo '<div class="jobs-activity-container">';

						echo '<h3>General Activity Log</h3>';
						$logs = get_posts( array(
							'post_type'      => 'job_activity',
							'posts_per_page' => 20,
							'orderby'        => 'date',
							'order'          => 'DESC',
						) );

						if ( ! empty( $logs ) ) {
							echo '<table class="widefat fixed striped">';
							echo '<thead><tr><th>Date</th><th>User</th><th>Action</th><th>Details</th></tr></thead>';
							echo '<tbody>';
							foreach ( $logs as $log ) {
								$user = get_userdata( $log->post_author );
								echo '<tr>';
								echo '<td>' . get_the_date( 'Y-m-d H:i', $log ) . '</td>';
								echo '<td>' . ( $user ? esc_html( $user->display_name ) : 'Unknown' ) . '</td>';
								echo '<td>' . esc_html( $log->post_content ) . '</td>';
								echo '<td>' . esc_html( $log->post_title ) . '</td>';
								echo '</tr>';
							}
							echo '</tbody></table>';
						} else {
							echo '<p>No activity logs found.</p>';
						}
						echo '</div>';
						break;

					case 'users':
						echo '<h2>User Management</h2>';
						$users = get_users( array( 'number' => 20 ) );
						echo '<p>Showing last 20 users. <a href="' . admin_url( 'users.php' ) . '">View all in Backend</a></p>';
						echo '<table class="widefat fixed striped">';
						echo '<thead><tr><th>Username</th><th>Email</th><th>Role</th><th>Registered</th></tr></thead>';
						echo '<tbody>';
						foreach ( $users as $user ) {
							$roles = implode( ', ', $user->roles );
							echo '<tr>';
							echo '<td>' . esc_html( $user->user_login ) . '</td>';
							echo '<td>' . esc_html( $user->user_email ) . '</td>';
							echo '<td>' . esc_html( $roles ) . '</td>';
							echo '<td>' . esc_html( $user->user_registered ) . '</td>';
							echo '</tr>';
						}
						echo '</tbody></table>';
						break;

					case 'articles':
						echo '<h2>Published Articles Management</h2>';
						$articles = get_posts( array( 'post_type' => 'post', 'posts_per_page' => 10 ) );
						echo '<p>Showing last 10 articles. <a href="' . admin_url( 'edit.php' ) . '">Manage in Backend</a></p>';
						echo '<table class="widefat fixed striped">';
						echo '<thead><tr><th>Title</th><th>Author</th><th>Date</th><th>Status</th></tr></thead>';
						echo '<tbody>';
						foreach ( $articles as $article ) {
							echo '<tr>';
							echo '<td>' . esc_html( $article->post_title ) . '</td>';
							echo '<td>' . get_the_author_meta( 'display_name', $article->post_author ) . '</td>';
							echo '<td>' . get_the_date( '', $article ) . '</td>';
							echo '<td>' . esc_html( $article->post_status ) . '</td>';
							echo '</tr>';
						}
						echo '</tbody></table>';
						break;

					case 'design':
						echo '<h2>Design Customization</h2>';
						if ( isset( $_POST['jobs_design_save'] ) && check_admin_referer( 'jobs_design_action' ) ) {
							update_option( 'jobs_logo_url', sanitize_text_field( $_POST['jobs_logo_url'] ) );
							update_option( 'jobs_primary_color', sanitize_hex_color( $_POST['jobs_primary_color'] ) );
							echo '<div class="jobs-success-msg">Settings saved.</div>';
						}
						$logo = get_option( 'jobs_logo_url', '' );
						$color = get_option( 'jobs_primary_color', '#1d3469' );
						?>
						<form method="post" action="">
							<?php wp_nonce_field( 'jobs_design_action' ); ?>
							<p>
								<label>Site Logo URL:</label><br>
								<input type="text" name="jobs_logo_url" value="<?php echo esc_attr( $logo ); ?>" style="width: 100%; max-width: 400px;">
							</p>
							<p>
								<label>Primary Color:</label><br>
								<input type="text" name="jobs_primary_color" value="<?php echo esc_attr( $color ); ?>" style="width: 100%; max-width: 400px;">
							</p>
							<input type="submit" name="jobs_design_save" class="button button-primary" value="Save Changes">
						</form>
						<?php
						break;

					case 'ads':
						echo '<h2>External Ads & Google AdSense</h2>';
						if ( isset( $_POST['jobs_ads_save'] ) && check_admin_referer( 'jobs_ads_action' ) ) {
							// Allow simple scripts for ads
							$ads_code = current_user_can( 'unfiltered_html' ) ? $_POST['jobs_ads_code'] : wp_kses_post( $_POST['jobs_ads_code'] );
							update_option( 'jobs_ads_code', $ads_code );
							echo '<div class="jobs-success-msg">Ads settings saved.</div>';
						}
						$ads_code = get_option( 'jobs_ads_code', '' );
						?>
						<form method="post" action="">
							<?php wp_nonce_field( 'jobs_ads_action' ); ?>
							<p>
								<label>AdSense / Ads Code:</label><br>
								<textarea name="jobs_ads_code" rows="10" style="width: 100%;"><?php echo esc_textarea( $ads_code ); ?></textarea>
							</p>
							<input type="submit" name="jobs_ads_save" class="button button-primary" value="Save Changes">
						</form>
						<?php
						break;

					case 'support':
						echo '<h2>Technical Support</h2>';
						$tickets = get_posts( array(
							'post_type'      => 'job_notification', // Using notifications as tickets for now
							'posts_per_page' => 20,
							'orderby'        => 'date',
							'order'          => 'DESC',
						) );

						if ( ! empty( $tickets ) ) {
							echo '<table class="widefat fixed striped">';
							echo '<thead><tr><th>Date</th><th>User</th><th>Subject</th><th>Message</th></tr></thead>';
							echo '<tbody>';
							foreach ( $tickets as $ticket ) {
								$user = get_userdata( $ticket->post_author );
								echo '<tr>';
								echo '<td>' . get_the_date( 'Y-m-d H:i', $ticket ) . '</td>';
								echo '<td>' . ( $user ? esc_html( $user->display_name ) : 'Unknown' ) . '</td>';
								echo '<td>' . esc_html( $ticket->post_title ) . '</td>';
								echo '<td>' . esc_html( wp_trim_words( $ticket->post_content, 10 ) ) . '</td>';
								echo '</tr>';
							}
							echo '</tbody></table>';
						} else {
							echo '<p>No support tickets found.</p>';
						}
						break;

					case 'permissions':
						echo '<h2>Permissions & Roles Management</h2>';
						global $wp_roles;
						$roles = $wp_roles->roles;
						echo '<table class="widefat fixed striped">';
						echo '<thead><tr><th>Role</th><th>Display Name</th><th>Capabilities Count</th><th>Users Count</th></tr></thead>';
						echo '<tbody>';
						foreach ( $roles as $slug => $role ) {
							$user_query = new WP_User_Query( array( 'role' => $slug, 'number' => 1, 'fields' => 'ID' ) );
							$count = $user_query->get_total();
							echo '<tr>';
							echo '<td>' . esc_html( $slug ) . '</td>';
							echo '<td>' . esc_html( $role['name'] ) . '</td>';
							echo '<td>' . count( $role['capabilities'] ) . '</td>';
							echo '<td>' . intval( $count ) . '</td>';
							echo '</tr>';
						}
						echo '</tbody></table>';
						break;

					default:
						echo '<h2>' . ucfirst( $active_tab ) . '</h2>';
						echo '<p>Content for ' . esc_html( $active_tab ) . ' not found.</p>';
						break;
				}
				?>
			</div>
		</div>
		<style>
			.jobs-admin-panel-frontend {
				background: transparent;
				padding: 20px;
			}
			.jobs-admin-nav {
				display: flex;
				flex-wrap: wrap;
				border-bottom: 2px solid #eee;
				margin-bottom: 20px;
			}
			.jobs-nav-tab {
				padding: 10px 20px;
				text-decoration: none;
				color: #555;
				font-weight: 500;
				border-bottom: 2px solid transparent;
				margin-bottom: -2px;
				transition: color 0.2s, border-color 0.2s;
			}
			.jobs-nav-tab:hover, .jobs-nav-tab.active {
				color: var(--jobs-primary, #1d3469);
				border-bottom-color: var(--jobs-primary, #1d3469);
			}
			.jobs-admin-content {
				padding: 20px 0;
			}
			.jobs-dashboard-widgets {
				display: flex;
				gap: 20px;
				margin-top: 20px;
			}
			.jobs-card {
				background: rgba(255, 255, 255, 0.5);
				padding: 20px;
				border-radius: 8px;
				flex: 1;
				text-align: center;
				border: 1px solid #eee;
			}
			.jobs-big-number {
				font-size: 36px;
				font-weight: bold;
				color: var(--jobs-primary, #1d3469);
				margin: 10px 0 0;
			}
			table.widefat {
				width: 100%;
				border-collapse: collapse;
				margin-top: 10px;
			}
			table.widefat th, table.widefat td {
				padding: 10px;
				text-align: left;
				border-bottom: 1px solid #eee;
			}
			table.widefat th {
				background: #f9f9f9;
				font-weight: 600;
			}
		</style>
		<?php
		return ob_get_clean();
	}

}
