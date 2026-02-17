<?php
/**
 * Module: applications-submitted.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
if ( ! in_array( 'job_seeker', (array) $user->roles ) ) {
	echo '<p>Access denied.</p>';
	return;
}

$args = array(
	'post_type'      => 'job_application',
	'post_status'    => array( 'publish', 'pending', 'draft' ), // internal statuses
	'author'         => $user->ID,
	'posts_per_page' => 10,
);

$applications = new WP_Query( $args );

?>

<div class="jobs-module-header">
	<h2>Applications Submitted</h2>
</div>

<?php if ( $applications->have_posts() ) : ?>
	<div class="jobs-applications-list">
		<?php while ( $applications->have_posts() ) : $applications->the_post();
			$job_id = get_post_meta( get_the_ID(), '_job_application_job_id', true );
			$job = get_post( $job_id );
			$job_title = $job ? $job->post_title : 'Unknown Job';
			$status = get_post_status(); // application status if managed via post status, or meta
			// Assuming post_status for now
			$status_label = ucfirst( $status );
		?>
			<div class="jobs-application-item">
				<div class="jobs-application-info">
					<h3 class="jobs-application-title">Applied for: <?php echo esc_html( $job_title ); ?></h3>
					<span class="jobs-date">Submitted on: <?php echo get_the_date(); ?></span>
					<span class="jobs-status-badge jobs-status-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status_label ); ?></span>
				</div>
				<div class="jobs-application-details">
					<!-- Maybe link to view application details or edit? -->
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
<?php else : ?>
	<p>You have not submitted any applications yet.</p>
<?php endif; ?>
