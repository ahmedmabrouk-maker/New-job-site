<?php

class Jobs_Shortcodes {

	public function __construct() {
		add_shortcode( 'jobs_search', array( $this, 'render_search' ) );
		add_shortcode( 'jobs_login', array( $this, 'render_login' ) );
		add_shortcode( 'jobs_register', array( $this, 'render_register' ) );
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_admin_panel' ) );

		add_action( 'init', array( $this, 'handle_registration' ) );
		add_action( 'init', array( $this, 'generate_pdf_resume' ) );
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

	public function generate_pdf_resume() {
		if ( isset( $_GET['jobs_pdf_resume'] ) && $_GET['jobs_pdf_resume'] == '1' ) {
			if ( ! is_user_logged_in() ) {
				wp_die( 'You must be logged in to view your resume.' );
			}

			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
			$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );

			if ( ! is_array( $cv_data ) ) {
				$cv_data = array();
			}

			$education = isset( $cv_data['education'] ) ? $cv_data['education'] : array();
			$experience = isset( $cv_data['experience'] ) ? $cv_data['experience'] : array();
			$skills = isset( $cv_data['skills'] ) ? $cv_data['skills'] : '';

			// Render HTML for PDF/Print
			?>
			<!DOCTYPE html>
			<html>
			<head>
				<title>Resume - <?php echo esc_html( $user->display_name ); ?></title>
				<style>
					body { font-family: 'Rubik', sans-serif; padding: 40px; color: #333; }
					h1 { color: #1d3469; border-bottom: 2px solid #1d3469; padding-bottom: 10px; }
					h2 { color: #1d3469; margin-top: 30px; border-bottom: 1px solid #ddd; }
					.section { margin-bottom: 20px; }
					.item { margin-bottom: 15px; }
					.item-title { font-weight: bold; font-size: 1.1em; }
					.item-subtitle { font-style: italic; color: #666; }
					@media print {
						body { padding: 0; }
					}
				</style>
				<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">
			</head>
			<body onload="window.print()">
				<h1><?php echo esc_html( $user->display_name ); ?></h1>
				<p>Email: <?php echo esc_html( $user->user_email ); ?></p>

				<?php if ( ! empty( $experience ) ) : ?>
				<div class="section">
					<h2>Experience</h2>
					<?php foreach ( $experience as $exp ) : ?>
						<div class="item">
							<div class="item-title"><?php echo esc_html( $exp['position'] ); ?></div>
							<div class="item-subtitle"><?php echo esc_html( $exp['company'] ); ?> | <?php echo esc_html( $exp['years'] ); ?></div>
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
							<div class="item-subtitle"><?php echo esc_html( $edu['school'] ); ?> | <?php echo esc_html( $edu['year'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<?php if ( ! empty( $skills ) ) : ?>
				<div class="section">
					<h2>Skills</h2>
					<p><?php echo esc_html( $skills ); ?></p>
				</div>
				<?php endif; ?>

			</body>
			</html>
			<?php
			exit;
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
		if ( ! current_user_can( 'manage_options' ) ) {
			return '<div class="jobs-admin-panel-frontend"><p>Access Denied.</p></div>';
		}

		ob_start();
		$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'dashboard';
		$base_url = get_permalink();
		?>
		<div class="jobs-admin-panel-frontend">
			<div class="jobs-admin-header">
				<h1>System Administration</h1>
				<nav class="jobs-admin-nav">
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'dashboard', $base_url ) ); ?>" class="<?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">Dashboard</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'reports', $base_url ) ); ?>" class="<?php echo $active_tab == 'reports' ? 'active' : ''; ?>">Reports</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'activity', $base_url ) ); ?>" class="<?php echo $active_tab == 'activity' ? 'active' : ''; ?>">Activity Logs</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'users', $base_url ) ); ?>" class="<?php echo $active_tab == 'users' ? 'active' : ''; ?>">Users</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'articles', $base_url ) ); ?>" class="<?php echo $active_tab == 'articles' ? 'active' : ''; ?>">Articles</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'design', $base_url ) ); ?>" class="<?php echo $active_tab == 'design' ? 'active' : ''; ?>">Design</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'support', $base_url ) ); ?>" class="<?php echo $active_tab == 'support' ? 'active' : ''; ?>">Support</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'permissions', $base_url ) ); ?>" class="<?php echo $active_tab == 'permissions' ? 'active' : ''; ?>">Permissions</a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'ads', $base_url ) ); ?>" class="<?php echo $active_tab == 'ads' ? 'active' : ''; ?>">Ads</a>
				</nav>
			</div>

			<div class="jobs-admin-body">
				<?php
				switch ( $active_tab ) {
					case 'dashboard':
						echo '<h2>Welcome, Administrator</h2>';
						echo '<p>Use the tabs above to manage the Jobs system.</p>';
						break;
					case 'reports':
						echo '<h2>Reports</h2>';
						$job_counts = wp_count_posts( 'job' );
						$user_counts = count_users();
						echo '<div class="jobs-stats">';
						echo '<div class="stat-box"><strong>' . intval( $job_counts->publish ) . '</strong> Active Jobs</div>';
						echo '<div class="stat-box"><strong>' . intval( $job_counts->pending ) . '</strong> Pending Jobs</div>';
						echo '<div class="stat-box"><strong>' . intval( wp_count_posts( 'job_application' )->publish ) . '</strong> Applications</div>'; // Assuming applications are 'publish' by default or internal
						echo '<div class="stat-box"><strong>' . intval( $user_counts['total_users'] ) . '</strong> Users</div>';
						echo '</div>';
						break;
					case 'activity':
						echo '<h2>Activity Logs</h2>';
						$logs = get_posts( array(
							'post_type' => 'job_activity',
							'posts_per_page' => 20,
							'orderby' => 'date',
							'order' => 'DESC'
						) );
						if ( $logs ) {
							echo '<ul class="jobs-activity-list">';
							foreach ( $logs as $log ) {
								echo '<li><strong>' . get_the_title( $log ) . '</strong> - ' . get_the_date( '', $log ) . '<br>' . $log->post_content . '</li>';
							}
							echo '</ul>';
						} else {
							echo '<p>No activity recorded yet.</p>';
						}
						break;
					case 'users':
						echo '<h2>User Management</h2>';
						echo '<p><a href="' . admin_url( 'users.php' ) . '" class="button">Manage Users in WordPress Dashboard</a></p>';
						break;
					case 'articles':
						echo '<h2>Articles</h2>';
						echo '<p><a href="' . admin_url( 'edit.php' ) . '" class="button">Manage Articles in WordPress Dashboard</a></p>';
						break;
					case 'design':
						echo '<h2>Design Customization</h2>';
						// We need a form here that submits to options.php or AJAX.
						// Since options.php redirects to wp-admin, AJAX is better for frontend admin.
						// Or just link to the backend settings page?
						// Request: "This panel will be used instead of the default WordPress dashboard."
						// So it should work here.
						// I will add a simple form and handle it with a check on init or admin_post?
						// Simpler: Just display the settings form pointing to options.php but with a redirect back?
						// options.php redirects to referrer usually.
						echo '<form method="post" action="options.php">';
						settings_fields( 'jobs_options_group' );
						do_settings_sections( 'jobs_admin_settings' );
						submit_button();
						echo '</form>';
						break;
					case 'support':
						echo '<h2>Support Tickets</h2>';
						$tickets = get_posts( array(
							'post_type' => 'job_notification',
							'posts_per_page' => 20,
						) );
						if ( $tickets ) {
							foreach ( $tickets as $ticket ) {
								echo '<div class="ticket"><strong>' . get_the_title($ticket) . '</strong><br>' . $ticket->post_content . '</div>';
							}
						} else {
							echo '<p>No support tickets.</p>';
						}
						break;
					case 'permissions':
						echo '<h2>Permissions</h2>';
						global $wp_roles;
						echo '<ul>';
						foreach ( $wp_roles->roles as $role_key => $role_details ) {
							echo '<li><strong>' . esc_html( $role_details['name'] ) . '</strong> (' . count( $role_details['capabilities'] ) . ' capabilities)</li>';
						}
						echo '</ul>';
						break;
					case 'ads':
						echo '<h2>Ads Management</h2>';
						// Placeholder for ad management
						echo '<p>AdSense integration settings would go here.</p>';
						break;
				}
				?>
			</div>

			<style>
				.jobs-admin-panel-frontend { background: transparent; color: #333; }
				.jobs-admin-header { border-bottom: 2px solid #1d3469; margin-bottom: 20px; }
				.jobs-admin-nav a { display: inline-block; padding: 10px 20px; text-decoration: none; color: #1d3469; font-weight: bold; }
				.jobs-admin-nav a.active { background: #1d3469; color: #fff; }
				.stat-box { display: inline-block; width: 200px; padding: 20px; background: rgba(255,255,255,0.8); margin: 10px; border: 1px solid #ddd; text-align: center; }
				.jobs-activity-list li { padding: 10px; border-bottom: 1px solid #eee; }
			</style>
		</div>
		<?php
		return ob_get_clean();
	}

}
