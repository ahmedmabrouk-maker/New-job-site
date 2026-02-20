<?php

class Jobs_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_jobs_load_module', array( $this, 'load_module' ) );
		add_action( 'wp_ajax_jobs_handle_job_posting', array( $this, 'handle_job_posting' ) );
		add_action( 'wp_ajax_jobs_save_cv_data', array( $this, 'save_cv_data' ) );
		add_action( 'wp_ajax_jobs_save_company_data', array( $this, 'save_company_data' ) );
		add_action( 'wp_ajax_jobs_approve_job', array( $this, 'approve_job' ) );
		add_action( 'wp_ajax_jobs_reject_job', array( $this, 'reject_job' ) );
		add_action( 'wp_ajax_jobs_toggle_favorite', array( $this, 'toggle_favorite' ) );
		add_action( 'wp_ajax_jobs_delete_draft', array( $this, 'delete_draft' ) );
		add_action( 'wp_ajax_jobs_send_support_message', array( $this, 'send_support_message' ) );
		add_action( 'wp_ajax_jobs_toggle_public_profile', array( $this, 'toggle_public_profile' ) );
		add_action( 'wp_ajax_jobs_update_account_settings', array( $this, 'update_account_settings' ) );
		add_action( 'wp_ajax_jobs_delete_account', array( $this, 'delete_account' ) );
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

	public function handle_job_posting() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		if ( ! current_user_can( 'employer' ) && ! current_user_can( 'administrator' ) && ! current_user_can( 'reviewer' ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		$title = sanitize_text_field( $_POST['job_title'] );
		$description = wp_kses_post( $_POST['job_description'] );
		$specialization = intval( $_POST['job_specialization'] );
		$country = intval( $_POST['job_country'] );
		$city = intval( $_POST['job_city'] );

		if ( empty( $title ) || empty( $description ) ) {
			wp_send_json_error( 'Title and Description are required.' );
		}

		$post_id = wp_insert_post( array(
			'post_title'   => $title,
			'post_content' => $description,
			'post_status'  => 'pending',
			'post_type'    => 'job',
			'post_author'  => get_current_user_id(),
		) );

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( 'Error creating job post.' );
		}

		if ( $specialization ) {
			wp_set_post_terms( $post_id, array( $specialization ), 'job_specialization' );
		}
		if ( $country ) {
			wp_set_post_terms( $post_id, array( $country ), 'job_country' );
		}
		if ( $city ) {
			wp_set_post_terms( $post_id, array( $city ), 'job_city' );
		}

		// Save geolocation meta if needed later (placeholder)
		update_post_meta( $post_id, '_job_latitude', '' );
		update_post_meta( $post_id, '_job_longitude', '' );

		wp_send_json_success( 'Job posted successfully! Waiting for approval.' );
	}

	public function save_cv_data() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		$user_id = get_current_user_id();

		// Sanitize education
		$education = isset( $_POST['education'] ) ? $_POST['education'] : array();
		$clean_education = array();
		if ( is_array( $education ) ) {
			foreach ( $education as $edu ) {
				$clean_education[] = array_map( 'sanitize_text_field', $edu );
			}
		}

		// Sanitize experience
		$experience = isset( $_POST['experience'] ) ? $_POST['experience'] : array();
		$clean_experience = array();
		if ( is_array( $experience ) ) {
			foreach ( $experience as $exp ) {
				$clean_experience[] = array_map( 'sanitize_text_field', $exp );
			}
		}

		// Sanitize skills
		$skills = isset( $_POST['skills'] ) ? sanitize_text_field( $_POST['skills'] ) : '';

		// Sanitize courses
		$courses = isset( $_POST['courses'] ) ? $_POST['courses'] : array();
		$clean_courses = array();
		if ( is_array( $courses ) ) {
			foreach ( $courses as $course ) {
				$clean_courses[] = array_map( 'sanitize_text_field', $course );
			}
		}

		// Sanitize certifications
		$certifications = isset( $_POST['certifications'] ) ? $_POST['certifications'] : array();
		$clean_certifications = array();
		if ( is_array( $certifications ) ) {
			foreach ( $certifications as $cert ) {
				$clean_certifications[] = array_map( 'sanitize_text_field', $cert );
			}
		}

		$cv_data = array(
			'education'  => $clean_education,
			'experience' => $clean_experience,
			'skills'     => $skills,
			'courses'    => $clean_courses,
			'certifications' => $clean_certifications,
		);

		update_user_meta( $user_id, '_jobs_cv_data', $cv_data );

		wp_send_json_success( 'CV saved successfully!' );
	}

	public function save_company_data() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		$user_id = get_current_user_id();
		$current_data = get_user_meta( $user_id, '_jobs_company_data', true );
		if ( ! is_array( $current_data ) ) {
			$current_data = array();
		}

		$company_name = sanitize_text_field( $_POST['company_name'] );
		$employee_count = sanitize_text_field( $_POST['employee_count'] );
		$company_address = sanitize_text_field( $_POST['company_address'] );
		$company_description = sanitize_textarea_field( $_POST['company_description'] );

		// Handle Logo Upload
		$logo_url = isset( $current_data['logo_url'] ) ? $current_data['logo_url'] : '';
		if ( ! empty( $_FILES['company_logo']['name'] ) ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );

			$attachment_id = media_handle_upload( 'company_logo', 0 );

			if ( is_wp_error( $attachment_id ) ) {
				wp_send_json_error( 'Error uploading logo: ' . $attachment_id->get_error_message() );
			} else {
				$logo_url = wp_get_attachment_url( $attachment_id );
			}
		}

		$new_data = array(
			'name'           => $company_name,
			'employee_count' => $employee_count,
			'address'        => $company_address,
			'description'    => $company_description,
			'logo_url'       => $logo_url,
		);

		update_user_meta( $user_id, '_jobs_company_data', $new_data );

		wp_send_json_success( 'Company profile saved successfully!' );
	}

	public function approve_job() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() || ( ! current_user_can( 'administrator' ) && ! current_user_can( 'reviewer' ) ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		$job_id = intval( $_POST['job_id'] );
		$post = get_post( $job_id );

		if ( ! $post || 'job' !== $post->post_type ) {
			wp_send_json_error( 'Invalid job.' );
		}

		wp_update_post( array(
			'ID'          => $job_id,
			'post_status' => 'publish',
		) );

		wp_send_json_success( 'Job approved successfully.' );
	}

	public function reject_job() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() || ( ! current_user_can( 'administrator' ) && ! current_user_can( 'reviewer' ) ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		$job_id = intval( $_POST['job_id'] );
		$post = get_post( $job_id );

		if ( ! $post || 'job' !== $post->post_type ) {
			wp_send_json_error( 'Invalid job.' );
		}

		wp_update_post( array(
			'ID'          => $job_id,
			'post_status' => 'trash',
		) );

		wp_send_json_success( 'Job rejected.' );
	}

	public function toggle_favorite() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		$job_id = intval( $_POST['job_id'] );
		$user_id = get_current_user_id();
		$favorites = get_user_meta( $user_id, '_jobs_favorites', true );

		if ( ! is_array( $favorites ) ) {
			$favorites = array();
		}

		if ( in_array( $job_id, $favorites ) ) {
			$favorites = array_diff( $favorites, array( $job_id ) );
			$action = 'removed';
		} else {
			$favorites[] = $job_id;
			$action = 'added';
		}

		update_user_meta( $user_id, '_jobs_favorites', $favorites );

		wp_send_json_success( array( 'action' => $action ) );
	}

	public function delete_draft() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		$job_id = intval( $_POST['job_id'] );
		$post = get_post( $job_id );

		if ( ! $post || 'job' !== $post->post_type ) {
			wp_send_json_error( 'Invalid job.' );
		}

		if ( $post->post_author != get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'Permission denied.' );
		}

		wp_delete_post( $job_id, true ); // Force delete

		wp_send_json_success( 'Draft deleted.' );
	}

	public function send_support_message() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		$subject = sanitize_text_field( $_POST['subject'] );
		$message = sanitize_textarea_field( $_POST['message'] );
		$user = wp_get_current_user();

		if ( empty( $subject ) || empty( $message ) ) {
			wp_send_json_error( 'Subject and message are required.' );
		}

		// Send email to admin
		$to = get_option( 'admin_email' );
		$email_subject = '[Jobs Support] ' . $subject;
		$body = "User: " . $user->display_name . " (" . $user->user_email . ")\n\n" . $message;
		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );

		wp_mail( $to, $email_subject, $body, $headers );

		// Create notification post
		$notif_id = wp_insert_post( array(
			'post_title'   => 'Support Request: ' . $subject,
			'post_content' => $message,
			'post_status'  => 'publish',
			'post_type'    => 'job_notification',
			'post_author'  => $user->ID,
		) );

		wp_send_json_success( 'Message sent successfully.' );
	}

	public function toggle_public_profile() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		$hidden = intval( $_POST['hidden'] );
		update_user_meta( get_current_user_id(), '_jobs_hide_public_profile', $hidden );

		wp_send_json_success();
	}

	public function update_account_settings() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		$user = wp_get_current_user();
		$new_email = sanitize_email( $_POST['email'] );
		$new_password = $_POST['password'];

		$user_data = array( 'ID' => $user->ID );

		if ( is_email( $new_email ) && $new_email !== $user->user_email ) {
			if ( email_exists( $new_email ) ) {
				wp_send_json_error( 'Email already in use.' );
			}
			$user_data['user_email'] = $new_email;
		}

		if ( ! empty( $new_password ) ) {
			$user_data['user_pass'] = $new_password;
		}

		if ( count( $user_data ) > 1 ) {
			$user_id = wp_update_user( $user_data );
			if ( is_wp_error( $user_id ) ) {
				wp_send_json_error( $user_id->get_error_message() );
			}
			wp_send_json_success( 'Account details updated.' );
		} else {
			wp_send_json_success( 'No changes made.' );
		}
	}

	public function delete_account() {
		check_ajax_referer( 'jobs_ajax_nonce', 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( 'You must be logged in.' );
		}

		$user = wp_get_current_user();

		if ( in_array( 'administrator', (array) $user->roles ) ) {
			wp_send_json_error( 'Admins cannot delete their account this way.' );
		}

		require_once( ABSPATH . 'wp-admin/includes/user.php' );

		if ( wp_delete_user( $user->ID ) ) {
			wp_send_json_success( 'Account deleted.' );
		} else {
			wp_send_json_error( 'Error deleting account.' );
		}
	}
}
