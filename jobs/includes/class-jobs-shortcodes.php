<?php

class Jobs_Shortcodes {

	public function __construct() {
		add_shortcode( 'jobs_search', array( $this, 'render_search' ) );
		add_shortcode( 'jobs_login', array( $this, 'render_login' ) );
		add_shortcode( 'jobs_register', array( $this, 'render_register' ) );
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_admin_panel' ) );

		add_action( 'init', array( $this, 'handle_registration' ) );
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
			wp_redirect( home_url() );
			exit;
		}

		ob_start();
		?>
		<div class="jobs-admin-panel-frontend">
			<div class="jobs-admin-sidebar">
				<ul class="jobs-admin-menu">
					<li class="active" data-tab="admin-dashboard">Dashboard</li>
					<li data-tab="admin-reports">Reports</li>
					<li data-tab="admin-activity">Activity Log</li>
					<li data-tab="admin-users">Users</li>
					<li data-tab="admin-articles">Articles</li>
					<li data-tab="admin-design">Design</li>
					<li data-tab="admin-support">Support</li>
					<li data-tab="admin-permissions">Permissions</li>
					<li data-tab="admin-ads">Ads</li>
				</ul>
			</div>
			<div class="jobs-admin-content-area">
				<div id="jobs-admin-module-content">
					<!-- Content loaded via AJAX -->
				</div>
			</div>
		</div>
		<script>
		jQuery(document).ready(function($) {
			function loadAdminTab(tab) {
				$('#jobs-admin-module-content').html('<div class="jobs-loader">Loading...</div>');
				$('.jobs-admin-menu li').removeClass('active');
				$('.jobs-admin-menu li[data-tab="' + tab + '"]').addClass('active');

				$.post(jobs_ajax.ajax_url, {
					action: 'jobs_load_module',
					module: tab,
					nonce: jobs_ajax.nonce
				}, function(response) {
					if (response.success) {
						$('#jobs-admin-module-content').html(response.data);
					} else {
						$('#jobs-admin-module-content').html('<p class="error">' + response.data + '</p>');
					}
				});
			}

			// Load dashboard by default
			loadAdminTab('admin-dashboard');

			$('.jobs-admin-menu li').on('click', function() {
				var tab = $(this).data('tab');
				loadAdminTab(tab);
			});
		});
		</script>
		<style>
			.jobs-admin-panel-frontend { display: flex; min-height: 600px; }
			.jobs-admin-sidebar { width: 250px; border-right: 1px solid rgba(0,0,0,0.1); padding-right: 20px; }
			.jobs-admin-menu { list-style: none; padding: 0; margin: 0; }
			.jobs-admin-menu li { padding: 15px 10px; cursor: pointer; border-radius: 5px; transition: background 0.3s; color: #1d3469; font-weight: 500; }
			.jobs-admin-menu li:hover, .jobs-admin-menu li.active { background: rgba(29, 52, 105, 0.1); }
			.jobs-admin-content-area { flex: 1; padding-left: 20px; }
			.jobs-loader { text-align: center; padding: 20px; color: #666; }
		</style>
		<?php
		return ob_get_clean();
	}

}
