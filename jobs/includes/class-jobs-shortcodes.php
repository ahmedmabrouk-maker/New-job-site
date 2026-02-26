<?php

class Jobs_Shortcodes {

	public function __construct() {
		add_shortcode( 'jobs_search', array( $this, 'render_search' ) );
		add_shortcode( 'jobs_login', array( $this, 'render_login' ) );
		add_shortcode( 'jobs_register', array( $this, 'render_register' ) );
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_admin_panel' ) );

		add_action( 'init', array( $this, 'handle_registration' ) );
	}

	public function render_admin_panel( $atts ) {
		if ( ! current_user_can( 'administrator' ) ) {
			return '<p style="text-align:center; padding: 50px;">Access Denied. You must be an administrator to view this page.</p>';
		}

		ob_start();
		?>
		<div class="jobs-admin-panel-container">
			<div class="jobs-admin-header">
				<h1>System Admin Control Panel</h1>
			</div>

			<div class="jobs-admin-tabs">
				<button class="jobs-admin-tab active" onclick="loadAdminModule('admin-dashboard', this)">Dashboard</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-reports', this)">Reports</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-activity', this)">Activity</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-users', this)">Users</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-articles', this)">Articles</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-design', this)">Design</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-search', this)">Search Settings</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-support', this)">Support</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-permissions', this)">Permissions</button>
				<button class="jobs-admin-tab" onclick="loadAdminModule('admin-ads', this)">Ads</button>
			</div>

			<div id="jobs-admin-content" class="jobs-admin-content">
				<!-- Content loaded via AJAX -->
				<p>Loading...</p>
			</div>
		</div>

		<script type="text/javascript">
		function loadAdminModule(moduleName, tabElement) {
			// Update active tab
			if (tabElement) {
				var tabs = document.querySelectorAll('.jobs-admin-tab');
				tabs.forEach(function(tab) {
					tab.classList.remove('active');
				});
				tabElement.classList.add('active');
			}

			var contentDiv = document.getElementById('jobs-admin-content');
			contentDiv.innerHTML = '<p>Loading...</p>';

			jQuery.post(jobs_ajax.ajax_url, {
				action: 'jobs_load_module',
				module: moduleName,
				nonce: jobs_ajax.nonce
			}, function(response) {
				if (response.success) {
					contentDiv.innerHTML = response.data;
				} else {
					contentDiv.innerHTML = '<p class="error">Error loading module: ' + response.data + '</p>';
				}
			});
		}

		// Load dashboard by default
		document.addEventListener('DOMContentLoaded', function() {
			loadAdminModule('admin-dashboard', document.querySelector('.jobs-admin-tab.active'));
		});
		</script>

		<style>
			.jobs-admin-panel-container {
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px;
				font-family: 'Rubik', sans-serif;
			}
			.jobs-admin-header h1 {
				color: #1d3469;
				text-align: center;
				margin-bottom: 30px;
			}
			.jobs-admin-tabs {
				display: flex;
				flex-wrap: wrap;
				justify-content: center;
				gap: 10px;
				margin-bottom: 30px;
			}
			.jobs-admin-tab {
				background: transparent;
				border: 2px solid #1d3469;
				color: #1d3469;
				padding: 10px 20px;
				border-radius: 25px;
				cursor: pointer;
				font-family: 'Rubik', sans-serif;
				font-weight: 500;
				transition: all 0.3s ease;
			}
			.jobs-admin-tab:hover, .jobs-admin-tab.active {
				background: #1d3469;
				color: #fff;
			}
			.jobs-admin-content {
				background: transparent; /* Transparent as requested */
				min-height: 400px;
			}
		</style>
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
