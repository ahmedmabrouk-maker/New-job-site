<?php
/**
 * Module: job-listings-history.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
if ( in_array( 'job_seeker', (array) $user->roles ) ) {
	echo '<p>Access denied.</p>';
	return;
}

$args = array(
	'post_type'      => 'job',
	'post_status'    => array( 'publish', 'expired', 'pending' ),
	'posts_per_page' => 20,
	'paged'          => 1, // Simple pagination for now
);

if ( ! in_array( 'administrator', (array) $user->roles ) && ! in_array( 'reviewer', (array) $user->roles ) ) {
	$args['author'] = $user->ID;
}

$jobs = new WP_Query( $args );

?>
<div class="jobs-module-header">
	<h2>Job Listings History</h2>
</div>

<div class="jobs-history-list">
	<?php
	if ( $jobs->have_posts() ) {
		while ( $jobs->have_posts() ) {
			$jobs->the_post();
			$status = get_post_status();
			$status_label = ucfirst( $status );
			$color = '#333';
			if ( 'publish' === $status ) $color = 'green';
			if ( 'pending' === $status ) $color = 'orange';
			if ( 'expired' === $status ) $color = 'red';
			?>
			<div class="jobs-history-item">
				<h3><?php the_title(); ?></h3>
				<p>
					<strong>Status:</strong> <span style="color: <?php echo $color; ?>; font-weight: bold;"><?php echo esc_html( $status_label ); ?></span> |
					<strong>Date:</strong> <?php echo get_the_date(); ?>
				</p>
				<p>
					<?php if ( 'publish' === $status ) : ?>
						<a href="<?php the_permalink(); ?>" target="_blank">View Job</a>
					<?php endif; ?>
					<!-- Add edit/delete links if needed -->
				</p>
			</div>
			<?php
		}
		wp_reset_postdata();
	} else {
		echo '<p>No job listings found.</p>';
	}
	?>
</div>
