<?php
/**
 * Module: applications-submitted.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to view your applications.</p>';
	return;
}

$user = wp_get_current_user();
if ( ! in_array( 'job_seeker', (array) $user->roles ) ) {
	echo '<p>Only Job Seekers can view applications.</p>';
	return;
}

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$args = array(
	'post_type'      => 'job_application',
	'post_status'    => array( 'publish', 'pending' ),
	'author'         => $user->ID,
	'posts_per_page' => 10,
	'paged'          => $paged,
);

$applications = new WP_Query( $args );

?>
<div class="jobs-module-header">
	<h2>My Applications</h2>
</div>

<div class="jobs-applications-list">
	<?php
	if ( $applications->have_posts() ) {
		while ( $applications->have_posts() ) {
			$applications->the_post();
			$app_id = get_the_ID();
			$job_id = get_post_meta( $app_id, '_job_id', true );
			$job_title = get_the_title( $job_id );
			$status = get_post_status(); // Might use custom status or meta for application status

			// If job doesn't exist anymore, handle gracefully
			if ( ! $job_title ) $job_title = 'Job Removed';

			?>
			<div class="jobs-application-item">
				<h3>Applied for: <?php echo esc_html( $job_title ); ?></h3>
				<p><strong>Date:</strong> <?php echo get_the_date(); ?></p>
				<p><strong>Status:</strong> <?php echo ucfirst( $status ); ?></p>
			</div>
			<?php
		}

		// Pagination
		echo '<div class="jobs-pagination">';
		echo paginate_links( array(
			'total' => $applications->max_num_pages,
			'current' => $paged,
		) );
		echo '</div>';

		wp_reset_postdata();
	} else {
		echo '<p>You haven\'t applied to any jobs yet.</p>';
	}
	?>
</div>
