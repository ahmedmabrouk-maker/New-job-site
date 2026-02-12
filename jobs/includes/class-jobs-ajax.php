<?php

class Jobs_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_jobs_load_module', array( $this, 'load_module' ) );
		add_action( 'wp_ajax_jobs_submit_job', array( $this, 'submit_job' ) );
		add_action( 'wp_ajax_jobs_search_jobs', array( $this, 'search_jobs' ) );
		add_action( 'wp_ajax_nopriv_jobs_search_jobs', array( $this, 'search_jobs' ) );
		add_action( 'wp_ajax_jobs_save_cv', array( $this, 'save_cv' ) );
		add_action( 'wp_ajax_jobs_save_company_profile', array( $this, 'save_company_profile' ) );
	}

	public function submit_job() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in to post a job.' );
		}

		$user = wp_get_current_user();
		$roles = ( array ) $user->roles;
		if ( ! in_array( 'employer', $roles ) && ! in_array( 'reviewer', $roles ) && ! in_array( 'administrator', $roles ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		$title = isset( $_POST['job_title'] ) ? sanitize_text_field( $_POST['job_title'] ) : '';
		$description = isset( $_POST['job_description'] ) ? wp_kses_post( $_POST['job_description'] ) : '';

		if ( empty( $title ) ) {
			wp_send_json_error( 'Job title is required.' );
		}

		$specialization = isset( $_POST['job_specialization'] ) ? intval( $_POST['job_specialization'] ) : 0;
		$country = isset( $_POST['job_country'] ) ? intval( $_POST['job_country'] ) : 0;
		$city = isset( $_POST['job_city'] ) ? intval( $_POST['job_city'] ) : 0;

		$post_id = wp_insert_post( array(
			'post_title'   => $title,
			'post_content' => $description,
			'post_status'  => 'pending',
			'post_type'    => 'job',
			'post_author'  => $user->ID,
		) );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( $post_id->get_error_message() );
		}

		if ( $specialization ) wp_set_object_terms( $post_id, $specialization, 'job_specialization' );
		if ( $country ) wp_set_object_terms( $post_id, $country, 'job_country' );
		if ( $city ) wp_set_object_terms( $post_id, $city, 'job_city' );

		wp_send_json_success( 'Job submitted successfully for review.' );
	}

	public function search_jobs() {
		$s = isset( $_POST['s'] ) ? sanitize_text_field( $_POST['s'] ) : '';
		$specialization = isset( $_POST['job_specialization'] ) ? sanitize_text_field( $_POST['job_specialization'] ) : '';
		$country = isset( $_POST['job_country'] ) ? sanitize_text_field( $_POST['job_country'] ) : '';
		$city = isset( $_POST['job_city'] ) ? sanitize_text_field( $_POST['job_city'] ) : '';

		$args = array(
			'post_type' => 'job',
			'post_status' => 'publish',
			's' => $s,
			'tax_query' => array( 'relation' => 'AND' ),
		);

		if ( $specialization ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_specialization',
				'field' => 'slug',
				'terms' => $specialization,
			);
		}
		if ( $country ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_country',
				'field' => 'slug',
				'terms' => $country,
			);
		}
		if ( $city ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'job_city',
				'field' => 'slug',
				'terms' => $city,
			);
		}

		$query = new WP_Query( $args );

		ob_start();
		if ( $query->have_posts() ) {
			echo '<div class="jobs-results-grid">';
			while ( $query->have_posts() ) {
				$query->the_post();
				?>
				<div class="job-card">
					<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<div class="job-meta">
						<?php
						$terms = get_the_terms( get_the_ID(), 'job_specialization' );
						if ( $terms && ! is_wp_error( $terms ) ) {
							foreach ( $terms as $term ) {
								echo '<span class="job-capsule" style="background-color: #e0f7fa; color: #006064; padding: 2px 8px; border-radius: 12px; font-size: 12px; margin-right: 5px;">' . esc_html( $term->name ) . '</span> ';
							}
						}
						?>
					</div>
					<div class="job-excerpt">
						<?php the_excerpt(); ?>
					</div>
				</div>
				<?php
			}
			echo '</div>';
		} else {
			echo '<p>No jobs found.</p>';
		}
		wp_reset_postdata();

		$html = ob_get_clean();
		wp_send_json_success( $html );
	}

	public function save_cv() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) wp_send_json_error( 'Not logged in' );

		$user = wp_get_current_user();
		if ( ! in_array( 'job_seeker', (array) $user->roles ) ) {
			wp_send_json_error( 'Permission denied. Only Job Seekers can save a CV.' );
		}

		$data = isset( $_POST['cv_data'] ) ? $_POST['cv_data'] : array();
		$clean_data = map_deep( $data, 'sanitize_text_field' );

		update_user_meta( $user->ID, '_jobs_cv_data', $clean_data );
		wp_send_json_success( 'CV saved successfully.' );
	}

	public function save_company_profile() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );
		if ( ! is_user_logged_in() ) wp_send_json_error( 'Not logged in' );

		$user = wp_get_current_user();
		if ( ! in_array( 'employer', (array) $user->roles ) ) {
			wp_send_json_error( 'Permission denied. Only Employers can save a Company Profile.' );
		}

		$data = isset( $_POST['company_data'] ) ? $_POST['company_data'] : array();
		$clean_data = map_deep( $data, 'sanitize_text_field' );

		if ( ! empty( $_FILES['company_logo']['name'] ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			$attachment_id = media_handle_upload( 'company_logo', 0 );

			if ( is_wp_error( $attachment_id ) ) {
				wp_send_json_error( 'Error uploading logo: ' . $attachment_id->get_error_message() );
			} else {
				$clean_data['logo'] = wp_get_attachment_url( $attachment_id );
			}
		} else {
			$existing = get_user_meta( $user->ID, '_jobs_company_data', true );
			if ( isset( $existing['logo'] ) && ! empty( $existing['logo'] ) ) {
				$clean_data['logo'] = $existing['logo'];
			}
		}

		update_user_meta( $user->ID, '_jobs_company_data', $clean_data );
		wp_send_json_success( 'Company Profile saved successfully.' );
	}

	public function load_module() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! isset( $_POST['module'] ) ) {
			wp_send_json_error( 'No module specified' );
		}

		$module = sanitize_key( $_POST['module'] );

		$allowed_modules = array(
			'job-posting',
			'job-listings-history',
			'public-profile',
			'applications-submitted',
			'job-requests',
			'cv-resume',
			'company-profile',
			'favorites',
			'drafts',
			'support',
			'settings',
			'advanced-settings',
			'terms-conditions',
			'articles'
		);

		if ( ! in_array( $module, $allowed_modules ) ) {
			wp_send_json_error( 'Invalid module' );
		}

		$file_path = JOBS_PLUGIN_DIR . 'modules/' . $module . '.php';

		if ( file_exists( $file_path ) ) {
			ob_start();
			include $file_path;
			$content = ob_get_clean();
			wp_send_json_success( $content );
		} else {
			wp_send_json_error( 'Module not found' );
		}
	}
}
