<?php
/**
 * Module: job-requests.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
$roles = (array) $user->roles;
$is_admin = in_array( 'administrator', $roles );
$is_reviewer = in_array( 'reviewer', $roles );
$is_employer = in_array( 'employer', $roles );

if ( ! $is_admin && ! $is_reviewer && ! $is_employer ) {
	echo '<p>Access denied.</p>';
	return;
}

?>
<div class="jobs-module-header">
	<h2>Job Requests</h2>
</div>

<?php
// Section 1: Reviewers/Admins - Pending Jobs Approval
if ( $is_admin || $is_reviewer ) {
	echo '<h3>Pending Job Approvals</h3>';
	$pending_jobs = new WP_Query( array(
		'post_type'      => 'job',
		'post_status'    => 'pending',
		'posts_per_page' => 10,
	) );

	if ( $pending_jobs->have_posts() ) {
		while ( $pending_jobs->have_posts() ) {
			$pending_jobs->the_post();
			$job_id = get_the_ID();
			$author = get_userdata( get_the_author_meta( 'ID' ) );
			?>
			<div class="jobs-request-item" id="job-request-<?php echo $job_id; ?>">
				<h4><?php the_title(); ?></h4>
				<p><strong>Employer:</strong> <?php echo esc_html( $author ? $author->display_name : 'Unknown' ); ?></p>
				<p><strong>Date:</strong> <?php echo get_the_date(); ?></p>
				<p><?php echo wp_trim_words( get_the_content(), 20 ); ?></p>
				<div class="jobs-actions">
					<button class="button button-primary" onclick="approveJob(<?php echo $job_id; ?>)">Approve</button>
					<button class="button button-secondary" onclick="rejectJob(<?php echo $job_id; ?>)">Reject</button>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
	} else {
		echo '<p>No pending jobs to review.</p>';
	}
}

// Section 2: Employers - View Applications
if ( $is_employer ) {
	echo '<h3>Applications Received</h3>';
	// Get employer's jobs
	$my_jobs = get_posts( array(
		'post_type'   => 'job',
		'post_status' => 'publish',
		'author'      => $user->ID,
		'fields'      => 'ids',
		'numberposts' => -1,
	) );

	if ( ! empty( $my_jobs ) ) {
		// Get applications for these jobs
		// Need to query by meta key '_job_id'? Or parent? usually meta key.
		// Assuming applications store job ID in meta. I need to verify how applications are stored.
		// Since I haven't implemented application submission logic yet, I should probably assume `_job_id` meta key.

		$applications = new WP_Query( array(
			'post_type'      => 'job_application',
			'post_status'    => 'publish', // or pending if they have statuses
			'meta_query'     => array(
				array(
					'key'     => '_job_id',
					'value'   => $my_jobs,
					'compare' => 'IN',
				),
			),
			'posts_per_page' => 20,
		) );

		if ( $applications->have_posts() ) {
			while ( $applications->have_posts() ) {
				$applications->the_post();
				$app_id = get_the_ID();
				$job_id = get_post_meta( $app_id, '_job_id', true );
				$job_title = get_the_title( $job_id );
				$applicant_id = get_the_author_meta( 'ID' );
				$applicant = get_userdata( $applicant_id );
				?>
				<div class="jobs-application-item">
					<h4>Application for: <?php echo esc_html( $job_title ); ?></h4>
					<p><strong>Applicant:</strong> <?php echo esc_html( $applicant ? $applicant->display_name : 'Unknown' ); ?></p>
					<p><strong>Date:</strong> <?php echo get_the_date(); ?></p>
					<p><a href="#" class="button">View Profile/CV</a></p> <!-- Placeholder -->
				</div>
				<?php
			}
			wp_reset_postdata();
		} else {
			echo '<p>No applications received yet.</p>';
		}
	} else {
		echo '<p>You have no active job listings.</p>';
	}
}
?>

<script>
function approveJob(jobId) {
	if (!confirm('Approve this job?')) return;
	jQuery.post(jobs_ajax.ajax_url, {
		action: 'jobs_approve_job',
		job_id: jobId,
		nonce: jobs_ajax.nonce
	}, function(response) {
		if (response.success) {
			jQuery('#job-request-' + jobId).fadeOut();
		} else {
			alert(response.data);
		}
	});
}

function rejectJob(jobId) {
	if (!confirm('Reject this job?')) return;
	jQuery.post(jobs_ajax.ajax_url, {
		action: 'jobs_reject_job',
		job_id: jobId,
		nonce: jobs_ajax.nonce
	}, function(response) {
		if (response.success) {
			jQuery('#job-request-' + jobId).fadeOut();
		} else {
			alert(response.data);
		}
	});
}
</script>
