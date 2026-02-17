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

// Reviewer/Admin: View Pending Jobs
if ( in_array( 'reviewer', $roles ) || in_array( 'administrator', $roles ) ) {
	?>
	<div class="jobs-module-header">
		<h2>Pending Job Requests (Approval)</h2>
	</div>
	<?php
	$args = array(
		'post_type'      => 'job',
		'post_status'    => 'pending',
		'posts_per_page' => 10,
	);
	$pending_jobs = new WP_Query( $args );

	if ( $pending_jobs->have_posts() ) {
		echo '<div class="jobs-requests-list">';
		while ( $pending_jobs->have_posts() ) {
			$pending_jobs->the_post();
			?>
			<div class="jobs-request-item">
				<div class="jobs-request-info">
					<h3><?php the_title(); ?></h3>
					<p><?php echo wp_trim_words( get_the_content(), 20 ); ?></p>
					<span class="jobs-meta">By: <?php the_author(); ?> | Date: <?php echo get_the_date(); ?></span>
				</div>
				<div class="jobs-request-actions">
					<button class="button button-primary" onclick="jobsApproveJob(<?php the_ID(); ?>)">Approve</button>
					<button class="button button-secondary" onclick="jobsRejectJob(<?php the_ID(); ?>)">Reject</button>
				</div>
			</div>
			<?php
		}
		echo '</div>';
		wp_reset_postdata();
	} else {
		echo '<p>No pending job requests.</p>';
	}
	?>
	<script>
	function jobsApproveJob(jobId) {
		if(!confirm('Approve this job?')) return;
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_approve_job',
			nonce: jobs_ajax.nonce,
			job_id: jobId
		}, function(response) {
			alert(response.data);
			if(response.success) loadJobsModule('job-requests');
		});
	}
	function jobsRejectJob(jobId) {
		if(!confirm('Reject this job?')) return;
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_reject_job',
			nonce: jobs_ajax.nonce,
			job_id: jobId
		}, function(response) {
			alert(response.data);
			if(response.success) loadJobsModule('job-requests');
		});
	}
	</script>
	<?php
}

// Employer: View Applications to their jobs
if ( in_array( 'employer', $roles ) ) {
	?>
	<div class="jobs-module-header">
		<h2>Applications Received</h2>
	</div>
	<?php
	// Get jobs posted by this employer
	$employer_jobs = get_posts( array(
		'post_type'   => 'job',
		'post_status' => array('publish', 'pending', 'expired'),
		'author'      => $user->ID,
		'fields'      => 'ids',
		'numberposts' => -1
	) );

	if ( ! empty( $employer_jobs ) ) {
		// Get applications for these jobs
		// Assuming applications are linked via meta key '_job_id' to the job post ID
		// Or maybe checking 'post_parent' if implemented that way?
		// The prompt doesn't specify how applications are linked, but usually it's meta.
		// Let's assume meta '_job_application_job_id'.

		$args_apps = array(
			'post_type'      => 'job_application',
			'post_status'    => 'publish', // internal status
			'meta_query'     => array(
				array(
					'key'     => '_job_application_job_id',
					'value'   => $employer_jobs,
					'compare' => 'IN'
				)
			),
			'posts_per_page' => 10
		);
		$applications = new WP_Query( $args_apps );

		if ( $applications->have_posts() ) {
			echo '<div class="jobs-applications-list">';
			while ( $applications->have_posts() ) {
				$applications->the_post();
				$job_id = get_post_meta( get_the_ID(), '_job_application_job_id', true );
				$job_title = get_the_title( $job_id );
				$applicant_email = get_post_meta( get_the_ID(), '_job_application_email', true );
				?>
				<div class="jobs-application-item">
					<h3>Application for: <?php echo esc_html( $job_title ); ?></h3>
					<p>Applicant: <?php the_title(); ?> (<?php echo esc_html( $applicant_email ); ?>)</p>
					<div class="jobs-application-content">
						<?php the_content(); ?>
					</div>
					<!-- Actions: Contact, Status Update could be added -->
				</div>
				<?php
			}
			echo '</div>';
			wp_reset_postdata();
		} else {
			echo '<p>No applications received yet.</p>';
		}
	} else {
		echo '<p>You have not posted any jobs yet.</p>';
	}
}
?>
