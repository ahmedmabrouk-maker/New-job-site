<?php

class Jobs_Top_Bar {

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'render_top_bar' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'jobs-public-style', JOBS_PLUGIN_URL . 'assets/css/jobs-public.css', array(), JOBS_VERSION, 'all' );
		wp_enqueue_style( 'dashicons' );

		wp_enqueue_script( 'jobs-public-script', JOBS_PLUGIN_URL . 'assets/js/jobs-public.js', array( 'jquery' ), JOBS_VERSION, true );
		wp_localize_script( 'jobs-public-script', 'jobs_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'jobs_ajax_nonce' ),
		) );
	}

	public function render_top_bar() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;

		// Check if user has access to top bar
		// All defined roles should have access
		if ( ! array_intersect( $roles, array( 'job_seeker', 'employer', 'reviewer', 'administrator', 'system_administrator' ) ) ) {
			return;
		}

		$avatar_url = get_avatar_url( $user->ID );

		?>
		<div class="jobs-top-bar">
			<div class="jobs-top-bar-content">
				<div class="jobs-user-profile">
					<img src="<?php echo esc_url( $avatar_url ); ?>" alt="User Avatar" class="jobs-avatar">
					<span class="jobs-username"><?php echo esc_html( $user->display_name ); ?></span>
					<div class="jobs-settings-logout" style="margin-left: 10px;">
						<a href="<?php echo wp_logout_url( home_url() ); ?>" style="color: #1d3469; text-decoration: none; font-size: 12px;">Logout</a>
					</div>
				</div>
				<div class="jobs-menu-trigger" onclick="document.querySelector('.jobs-dropdown-menu').classList.toggle('active')">
					<span class="dashicons dashicons-menu"></span>
				</div>
			</div>
			<div class="jobs-dropdown-menu">
				<ul>
					<?php
					// Modules for non-Job Seekers (Employer, Reviewer, Admin)
					if ( ! in_array( 'job_seeker', $roles ) ) :
					?>
						<li><a href="#" onclick="loadJobsModule('job-posting'); return false;">Job Posting</a></li>
						<li><a href="#" onclick="loadJobsModule('job-listings-history'); return false;">Job Listings History</a></li>

						<?php if ( in_array( 'employer', $roles ) ) : ?>
							<li><a href="#" onclick="loadJobsModule('company-profile'); return false;">Company Profile</a></li>
						<?php endif; ?>

						<li><a href="#" onclick="loadJobsModule('job-requests'); return false;">Job Requests</a></li>
					<?php endif; ?>

					<li><a href="#" onclick="loadJobsModule('public-profile'); return false;">Public Profile</a></li>

					<?php if ( in_array( 'job_seeker', $roles ) ) : ?>
						<li><a href="#" onclick="loadJobsModule('applications-submitted'); return false;">Applications Submitted</a></li>
						<li><a href="#" onclick="loadJobsModule('cv-resume'); return false;">CV / Resume</a></li>
					<?php endif; ?>

					<li><a href="#" onclick="loadJobsModule('favorites'); return false;">Favorites</a></li>
					<li><a href="#" onclick="loadJobsModule('drafts'); return false;">Drafts</a></li>
					<li><a href="#" onclick="loadJobsModule('support'); return false;">Support</a></li>
					<li><a href="#" onclick="loadJobsModule('settings'); return false;">Settings</a></li>

					<?php if ( current_user_can( 'manage_options' ) ) : // Admins & System Admins ?>
						<li><a href="<?php echo admin_url( 'admin.php?page=jobs_admin' ); ?>">Advanced Settings</a></li>
					<?php endif; ?>

					<li><a href="#" onclick="loadJobsModule('terms-conditions'); return false;">Terms & Conditions</a></li>
					<li><a href="<?php echo home_url( '/articles' ); ?>">Articles</a></li>
				</ul>
			</div>

			<!-- Container for loading modules -->
			<div id="jobs-module-container" class="jobs-module-modal" style="display:none;">
				<div class="jobs-module-content">
					<span class="jobs-close-modal" onclick="document.getElementById('jobs-module-container').style.display='none'">&times;</span>
					<div id="jobs-module-body">Loading...</div>
				</div>
			</div>
		</div>
		<?php
	}
}
