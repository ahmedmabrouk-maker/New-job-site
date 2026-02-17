<?php
/**
 * Module: job-listings-history.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
if ( ! in_array( 'employer', (array) $user->roles ) && ! in_array( 'administrator', (array) $user->roles ) ) {
	echo '<p>Access denied.</p>';
	return;
}

$args = array(
	'post_type'      => 'job',
	'post_status'    => array( 'publish', 'pending', 'expired' ), // draft is in Drafts module
	'author'         => $user->ID,
	'posts_per_page' => 10,
);

$query = new WP_Query( $args );
?>

<div class="jobs-module-header">
	<h2>Job Listings History</h2>
</div>

<?php if ( $query->have_posts() ) : ?>
	<div class="jobs-history-list">
		<?php while ( $query->have_posts() ) : $query->the_post();
			$status = get_post_status();
			$status_label = ucfirst( $status );
		?>
			<div class="jobs-history-item">
				<div class="jobs-history-info">
					<h3 class="jobs-history-title"><?php the_title(); ?></h3>
					<div class="jobs-history-meta">
						<span class="jobs-status-badge jobs-status-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status_label ); ?></span>
						<span class="jobs-date"><?php echo get_the_date(); ?></span>
					</div>
				</div>
				<div class="jobs-history-actions">
					<a href="<?php the_permalink(); ?>" target="_blank" class="button">View</a>
					<button class="button" onclick="loadJobsModule('job-posting', {job_id: <?php the_ID(); ?>})">Edit</button>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
<?php else : ?>
	<p>No active or pending job listings found.</p>
<?php endif; ?>
