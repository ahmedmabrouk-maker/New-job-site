<?php
/**
 * Module: job-requests.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to view requests.</p>';
	return;
}

$user = wp_get_current_user();
$roles = ( array ) $user->roles;

// JOB SEEKER VIEW: Responses / Offers
if ( in_array( 'job_seeker', $roles ) ) {
	echo '<div class="jobs-module-header"><h2>Employer Responses & Offers</h2></div>';

	$args = array(
		'post_type'      => 'job_application',
		'post_status'    => array( 'publish', 'trash' ), // Showing processed applications
		'author'         => $user->ID,
		'posts_per_page' => -1,
	);
	$responses = new WP_Query( $args );

	if ( $responses->have_posts() ) {
		echo '<div class="jobs-requests-list">';
		while ( $responses->have_posts() ) {
			$responses->the_post();
			$post_obj = get_post();
			$status = get_post_status();
			$display_status = ( $status === 'trash' ) ? 'Rejected' : ucfirst( $status );

			$job_id = get_post_meta( get_the_ID(), '_job_id', true );
			if ( ! $job_id && $post_obj ) $job_id = $post_obj->post_parent;

			$job_title = get_the_title( $job_id );

			echo '<div class="jobs-request-item">';
			echo '<div class="jobs-request-info">';
			echo '<strong>Response for: ' . esc_html( $job_title ) . '</strong>';
			echo '<br><span class="jobs-status status-' . esc_attr( $status ) . '">Status: ' . esc_html( $display_status ) . '</span>';
			echo '</div>';
			echo '<div class="jobs-request-actions">';
			echo '<a href="' . get_permalink( $job_id ) . '" target="_blank" class="button button-small">View Job</a>';
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
		wp_reset_postdata();
	} else {
		echo '<p>No responses received yet.</p>';
	}
	echo '<hr>';
}

// REVIEWER / ADMIN VIEW: Pending Jobs
if ( in_array( 'reviewer', $roles ) || in_array( 'administrator', $roles ) ) {

	echo '<div class="jobs-module-header"><h2>Pending Job Approvals</h2></div>';

	$pending_jobs = new WP_Query( array(
		'post_type'      => 'job',
		'post_status'    => 'pending',
		'posts_per_page' => -1,
		'orderby'        => 'date',
		'order'          => 'ASC',
	) );

	if ( $pending_jobs->have_posts() ) {
		echo '<div class="jobs-requests-list">';
		while ( $pending_jobs->have_posts() ) {
			$pending_jobs->the_post();
			echo '<div class="jobs-request-item">';
			echo '<div class="jobs-request-info">';
			echo '<strong>' . get_the_title() . '</strong> by ' . get_the_author();
			echo '<br><span class="jobs-date">' . get_the_date() . '</span>';
			echo '</div>';
			echo '<div class="jobs-request-actions">';
			echo '<a href="' . get_permalink() . '" target="_blank" class="button button-small">View</a> ';
			echo '<button class="button button-small button-primary" onclick="approveJob(' . get_the_ID() . ')">Approve</button> ';
			echo '<button class="button button-small button-secondary" onclick="rejectJob(' . get_the_ID() . ')">Reject</button>';
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
		wp_reset_postdata();
	} else {
		echo '<p>No pending jobs to review.</p>';
	}

	echo '<hr>'; // Separator if they are also employers
}

// EMPLOYER VIEW: Applications for their jobs
if ( in_array( 'employer', $roles ) || in_array( 'administrator', $roles ) ) { // Admins can see this too if they post jobs

	echo '<div class="jobs-module-header"><h2>Job Applications Received</h2></div>';

	$user_id = $user->ID;
	$jobs = get_posts( array(
		'post_type'      => 'job',
		'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ),
		'author'         => $user_id,
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );

	if ( ! empty( $jobs ) ) {
		$applications = new WP_Query( array(
			'post_type'       => 'job_application',
			'post_parent__in' => $jobs,
			'posts_per_page'  => -1,
			'orderby'         => 'date',
			'order'           => 'DESC',
		) );

		if ( $applications->have_posts() ) {
			echo '<div class="jobs-requests-list">';
			while ( $applications->have_posts() ) {
				$applications->the_post();
				$post_obj = get_post();
				$job_id = $post_obj->post_parent;
				$applicant_id = $post_obj->post_author;
				$applicant = get_userdata( $applicant_id );
				$job_title = get_the_title( $job_id );

				echo '<div class="jobs-request-item">';
				echo '<div class="jobs-request-info">';
				echo '<strong>' . esc_html( $applicant->display_name ) . '</strong> applied for <strong>' . esc_html( $job_title ) . '</strong>';
				echo '<br><span class="jobs-date">' . get_the_date() . '</span>';
				echo '</div>';
				echo '<div class="jobs-request-actions">';
				echo '<button class="button button-small" onclick="viewApplication(' . get_the_ID() . ')">View</button>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p>No applications received yet.</p>';
		}
	} else {
		if ( ! in_array( 'administrator', $roles ) && ! in_array( 'reviewer', $roles ) ) {
			echo '<p>You have no job listings.</p>';
		}
	}
}

?>

<style>
.jobs-request-item { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #eee; }
.jobs-request-item:last-child { border-bottom: none; }
.jobs-request-actions { display: flex; gap: 5px; }
</style>

<script>
function approveJob(jobId) {
	if (confirm('Approve this job?')) {
		// AJAX call
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_approve_job',
			nonce: jobs_ajax.nonce,
			job_id: jobId
		}, function(response) {
			alert(response.data);
			loadJobsModule('job-requests'); // Reload module
		});
	}
}
function rejectJob(jobId) {
	if (confirm('Reject this job?')) {
		// AJAX call
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_reject_job',
			nonce: jobs_ajax.nonce,
			job_id: jobId
		}, function(response) {
			alert(response.data);
			loadJobsModule('job-requests'); // Reload module
		});
	}
}
function viewApplication(appId) {
	alert('View Application ' + appId + ' functionality to be implemented.');
}
</script>
