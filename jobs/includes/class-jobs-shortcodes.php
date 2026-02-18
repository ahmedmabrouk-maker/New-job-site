<?php

class Jobs_Shortcodes {

	public function __construct() {
		add_shortcode( 'jobs_search', array( $this, 'render_search' ) );
		add_shortcode( 'jobs_login', array( $this, 'render_login' ) );
		add_shortcode( 'jobs_register', array( $this, 'render_register' ) );

		add_action( 'init', array( $this, 'handle_registration' ) );
		add_action( 'init', array( $this, 'handle_pdf_resume' ) );
	}

	public function handle_pdf_resume() {
		if ( isset( $_GET['jobs_pdf_resume'] ) && $_GET['jobs_pdf_resume'] == '1' ) {
			if ( ! is_user_logged_in() ) {
				wp_die( 'You must be logged in to view your resume.' );
			}

			$user = wp_get_current_user();
			$cv_data = get_user_meta( $user->ID, '_jobs_cv_data', true );

			if ( empty( $cv_data ) ) {
				wp_die( 'No resume data found.' );
			}

			// Render printable HTML
			?>
			<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<title><?php echo esc_html( $user->display_name ); ?> - Resume</title>
				<link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap" rel="stylesheet">
				<style>
					body { font-family: 'Rubik', sans-serif; color: #333; line-height: 1.6; max-width: 800px; margin: 40px auto; padding: 20px; }
					h1 { color: #1d3469; border-bottom: 2px solid #1d3469; padding-bottom: 10px; }
					h2 { color: #1d3469; margin-top: 30px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
					.section { margin-bottom: 20px; }
					.item { margin-bottom: 15px; }
					.item-title { font-weight: bold; font-size: 1.1em; }
					.item-meta { color: #666; font-style: italic; }
					@media print {
						.no-print { display: none; }
						body { margin: 0; padding: 20px; }
					}
				</style>
			</head>
			<body>
				<div class="no-print" style="text-align: right; margin-bottom: 20px;">
					<button onclick="window.print()" style="padding: 10px 20px; background: #1d3469; color: white; border: none; cursor: pointer;">Print / Save as PDF</button>
				</div>

				<h1><?php echo esc_html( $user->display_name ); ?></h1>
				<p><?php echo esc_html( $user->user_email ); ?></p>

				<?php if ( ! empty( $cv_data['skills'] ) ) : ?>
					<div class="section">
						<h2>Skills</h2>
						<p><?php echo nl2br( esc_html( $cv_data['skills'] ) ); ?></p>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $cv_data['education'] ) ) : ?>
					<div class="section">
						<h2>Education</h2>
						<?php foreach ( $cv_data['education'] as $edu ) : ?>
							<div class="item">
								<div class="item-title"><?php echo esc_html( $edu['institution'] ); ?></div>
								<div class="item-meta"><?php echo esc_html( $edu['degree'] ); ?> (<?php echo esc_html( $edu['year'] ); ?>)</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $cv_data['experience'] ) ) : ?>
					<div class="section">
						<h2>Experience</h2>
						<?php foreach ( $cv_data['experience'] as $exp ) : ?>
							<div class="item">
								<div class="item-title"><?php echo esc_html( $exp['company'] ); ?></div>
								<div class="item-meta"><?php echo esc_html( $exp['position'] ); ?> (<?php echo esc_html( $exp['duration'] ); ?>)</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<script>
					// Optional: Auto print
					// window.print();
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

}
