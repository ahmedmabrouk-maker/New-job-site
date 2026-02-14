<?php

class Jobs_Shortcodes {

	public function __construct() {
		add_shortcode( 'jobs_search', array( $this, 'render_search' ) );
		add_shortcode( 'jobs_login', array( $this, 'render_login' ) );
		add_shortcode( 'jobs_register', array( $this, 'render_register' ) );
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_admin_panel' ) );

		add_action( 'init', array( $this, 'handle_registration' ) );
		add_action( 'init', array( $this, 'handle_pdf_download' ) );
	}

	public function handle_pdf_download() {
		if ( isset( $_GET['jobs_pdf_resume'] ) && $_GET['jobs_pdf_resume'] == '1' ) {
			if ( ! is_user_logged_in() ) {
				wp_die( 'You must be logged in to view the resume.', 'Access Denied' );
			}

			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
			$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );
			if ( ! is_array( $cv_data ) ) $cv_data = array();

			?>
			<!DOCTYPE html>
			<html>
			<head>
				<title>Resume - <?php echo esc_html( $user->display_name ); ?></title>
				<style>
					body { font-family: 'Rubik', sans-serif; color: #333; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 40px; }
					.resume-header { text-align: center; border-bottom: 2px solid #1d3469; padding-bottom: 20px; margin-bottom: 30px; }
					.resume-name { font-size: 28px; font-weight: 700; color: #1d3469; margin: 0; }
					.resume-contact { font-size: 14px; color: #666; margin-top: 5px; }
					.resume-section { margin-bottom: 25px; }
					.resume-section h3 { color: #1d3469; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 15px; text-transform: uppercase; font-size: 16px; }
					.resume-item { margin-bottom: 15px; }
					.resume-item-title { font-weight: 700; font-size: 16px; }
					.resume-item-subtitle { font-style: italic; color: #555; }
					.resume-item-meta { font-size: 12px; color: #999; }
					@media print {
						body { font-size: 12pt; padding: 0; }
						.no-print { display: none; }
					}
				</style>
				<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
			</head>
			<body onload="window.print()">
				<div class="resume-container">
					<div class="resume-header">
						<h1 class="resume-name"><?php echo esc_html( $user->display_name ); ?></h1>
						<p class="resume-contact"><?php echo esc_html( $user->user_email ); ?></p>
					</div>

					<?php if ( ! empty( $cv_data['education'] ) ) : ?>
					<div class="resume-section">
						<h3>Education</h3>
						<?php foreach ( $cv_data['education'] as $edu ) : ?>
							<div class="resume-item">
								<div class="resume-item-title"><?php echo esc_html( isset($edu['school']) ? $edu['school'] : '' ); ?></div>
								<div class="resume-item-subtitle"><?php echo esc_html( isset($edu['degree']) ? $edu['degree'] : '' ); ?></div>
								<div class="resume-item-meta"><?php echo esc_html( isset($edu['year']) ? $edu['year'] : '' ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $cv_data['experience'] ) ) : ?>
					<div class="resume-section">
						<h3>Experience</h3>
						<?php foreach ( $cv_data['experience'] as $exp ) : ?>
							<div class="resume-item">
								<div class="resume-item-title"><?php echo esc_html( isset($exp['company']) ? $exp['company'] : '' ); ?></div>
								<div class="resume-item-subtitle"><?php echo esc_html( isset($exp['position']) ? $exp['position'] : '' ); ?></div>
								<div class="resume-item-meta"><?php echo esc_html( isset($exp['years']) ? $exp['years'] : '' ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $cv_data['courses'] ) ) : ?>
					<div class="resume-section">
						<h3>Courses</h3>
						<?php foreach ( $cv_data['courses'] as $crs ) : ?>
							<div class="resume-item">
								<div class="resume-item-title"><?php echo esc_html( isset($crs['name']) ? $crs['name'] : '' ); ?></div>
								<div class="resume-item-subtitle"><?php echo esc_html( isset($crs['provider']) ? $crs['provider'] : '' ); ?></div>
								<div class="resume-item-meta"><?php echo esc_html( isset($crs['year']) ? $crs['year'] : '' ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $cv_data['certifications'] ) ) : ?>
					<div class="resume-section">
						<h3>Certifications</h3>
						<?php foreach ( $cv_data['certifications'] as $cert ) : ?>
							<div class="resume-item">
								<div class="resume-item-title"><?php echo esc_html( isset($cert['name']) ? $cert['name'] : '' ); ?></div>
								<div class="resume-item-subtitle"><?php echo esc_html( isset($cert['authority']) ? $cert['authority'] : '' ); ?></div>
								<div class="resume-item-meta"><?php echo esc_html( isset($cert['year']) ? $cert['year'] : '' ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php endif; ?>

					<?php if ( ! empty( $cv_data['skills'] ) ) : ?>
					<div class="resume-section">
						<h3>Skills</h3>
						<p><?php echo esc_html( $cv_data['skills'] ); ?></p>
					</div>
					<?php endif; ?>
				</div>
			</body>
			</html>
			<?php
			exit;
		}
	}

	public function render_admin_panel( $atts ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return '<p>Access Denied. You do not have permission to view this page.</p>';
		}

		// Handle Design Save
		if ( isset( $_POST['jobs_design_save'] ) && isset( $_POST['jobs_admin_nonce'] ) ) {
			if ( wp_verify_nonce( $_POST['jobs_admin_nonce'], 'jobs_save_design' ) ) {
				update_option( 'jobs_logo_url', sanitize_text_field( $_POST['jobs_logo_url'] ) );
				update_option( 'jobs_primary_color', sanitize_text_field( $_POST['jobs_primary_color'] ) );
				echo '<div class="jobs-notice success"><p>Settings Saved.</p></div>';
			}
		}

		// Handle Ads Save
		if ( isset( $_POST['jobs_ads_save'] ) && isset( $_POST['jobs_admin_nonce'] ) ) {
			if ( wp_verify_nonce( $_POST['jobs_admin_nonce'], 'jobs_save_ads' ) ) {
				update_option( 'jobs_ads_code', wp_kses_post( $_POST['jobs_ads_code'] ) );
				echo '<div class="jobs-notice success"><p>Ads Settings Saved.</p></div>';
			}
		}

		// Enqueue styles if not already loaded (though typically done via wp_enqueue_scripts)
		wp_enqueue_style( 'jobs-admin-style', JOBS_PLUGIN_URL . 'assets/css/jobs-admin.css', array(), JOBS_VERSION, 'all' );

		ob_start();

		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dashboard';
		$base_url = get_permalink();

		?>
		<div class="jobs-admin-wrap">
			<h1>Jobs System Admin Panel</h1>

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
						$logo_url = get_option( 'jobs_logo_url', '' );
						$primary_color = get_option( 'jobs_primary_color', '#1d3469' );
						?>
						<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'design', $base_url ) ); ?>">
							<?php wp_nonce_field( 'jobs_save_design', 'jobs_admin_nonce' ); ?>
							<table class="form-table">
								<tr>
									<th scope="row"><label for="jobs_logo_url">Site Logo URL</label></th>
									<td>
										<input name="jobs_logo_url" type="text" id="jobs_logo_url" value="<?php echo esc_attr( $logo_url ); ?>" class="regular-text">
										<p class="description">Enter the URL of your logo.</p>
									</td>
								</tr>
								<tr>
									<th scope="row"><label for="jobs_primary_color">Primary Color</label></th>
									<td>
										<input name="jobs_primary_color" type="text" id="jobs_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" class="regular-text">
										<p class="description">Default: #1d3469</p>
									</td>
								</tr>
							</table>
							<p class="submit">
								<input type="submit" name="jobs_design_save" id="submit" class="button button-primary" value="Save Changes">
							</p>
						</form>
						<?php
						break;
					case 'support':
						echo '<h2>Technical Support</h2><p>Recent Support Tickets:</p>';
						$tickets = new WP_Query( array(
							'post_type' => 'job_notification',
							'post_status' => 'publish',
							'posts_per_page' => 10,
						));
						if ( $tickets->have_posts() ) {
							echo '<table class="form-table" style="background: #fff; border: 1px solid #eee;"><tbody>';
							while ( $tickets->have_posts() ) {
								$tickets->the_post();
								echo '<tr style="border-bottom: 1px solid #eee;">';
								echo '<td style="padding: 10px;"><strong>' . get_the_title() . '</strong><br>' . get_the_content() . '</td>';
								echo '<td style="padding: 10px;">' . get_the_date() . '</td>';
								echo '</tr>';
							}
							echo '</tbody></table>';
							wp_reset_postdata();
						} else {
							echo '<p>No support tickets found.</p>';
						}
						break;
					case 'permissions':
						echo '<h2>Permissions & Roles Management</h2><p>Manage user roles and capabilities.</p>';
						break;
					case 'ads':
						echo '<h2>External Ads & Google AdSense</h2>';
						$ads_code = get_option( 'jobs_ads_code', '' );
						?>
						<form method="post" action="<?php echo esc_url( add_query_arg( 'tab', 'ads', $base_url ) ); ?>">
							<?php wp_nonce_field( 'jobs_save_ads', 'jobs_admin_nonce' ); ?>
							<table class="form-table">
								<tr>
									<th scope="row"><label for="jobs_ads_code">AdSense / Ads Code</label></th>
									<td>
										<textarea name="jobs_ads_code" id="jobs_ads_code" rows="10" class="regular-text" style="width: 100%;"><?php echo esc_textarea( $ads_code ); ?></textarea>
										<p class="description">Paste your Google AdSense or other ad codes here.</p>
									</td>
								</tr>
							</table>
							<p class="submit">
								<input type="submit" name="jobs_ads_save" id="submit" class="button button-primary" value="Save Ads Settings">
							</p>
						</form>
						<?php
						break;
					default:
						echo '<p>Tab not found.</p>';
						break;
				}
				?>
			</div>

			<style>
				.jobs-admin-nav { margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 0; }
				.jobs-nav-tab { display: inline-block; padding: 10px 15px; text-decoration: none; border: 1px solid transparent; border-bottom: none; color: #1d3469; margin-bottom: -1px; }
				.jobs-nav-tab:hover { background: #f9f9f9; }
				.jobs-nav-tab.active { background: #fff; border: 1px solid #ccc; border-bottom: 1px solid #fff; font-weight: bold; }
				.jobs-admin-content { padding: 20px 0; }
			</style>
		</div>
		<?php
		return ob_get_clean();
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

}
