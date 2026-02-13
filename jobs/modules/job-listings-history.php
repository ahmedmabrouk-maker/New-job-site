<?php
/**
 * Module: job-listings-history.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to view your job history.</p>';
	return;
}

$user_id = get_current_user_id();

$args = array(
	'post_type'      => 'job',
	'post_status'    => array( 'publish', 'pending', 'draft', 'trash' ), // Include relevant statuses
	'author'         => $user_id,
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$jobs_query = new WP_Query( $args );

?>

<div class="jobs-module-header">
	<h2>Job Listings History</h2>
</div>

<?php if ( $jobs_query->have_posts() ) : ?>
	<div class="jobs-history-list">
		<?php while ( $jobs_query->have_posts() ) : $jobs_query->the_post(); ?>
			<?php
			$status = get_post_status();
			$status_label = ucfirst( $status );
			$status_class = 'status-' . $status;
			?>
			<div class="jobs-history-item <?php echo esc_attr( $status_class ); ?>">
				<div class="jobs-history-title">
					<strong><?php the_title(); ?></strong>
				</div>
				<div class="jobs-history-meta">
					<span class="jobs-date">Posted: <?php echo get_the_date(); ?></span>
					<span class="jobs-status <?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $status_label ); ?></span>
				</div>
				<div class="jobs-history-actions">
					<a href="<?php echo get_permalink(); ?>" target="_blank" class="button">View</a>
					<!-- Edit/Delete actions could be added here -->
				</div>
			</div>
		<?php endwhile; ?>
	</div>
	<?php wp_reset_postdata(); ?>
<?php else : ?>
	<p>No job listings found.</p>
<?php endif; ?>

<style>
.jobs-history-item {
	border-bottom: 1px solid #eee;
	padding: 15px 0;
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.jobs-history-item:last-child {
	border-bottom: none;
}
.jobs-status {
	padding: 2px 8px;
	border-radius: 4px;
	font-size: 12px;
	color: #fff;
	margin-left: 10px;
}
.jobs-status.status-publish { background-color: #28a745; }
.jobs-status.status-pending { background-color: #ffc107; color: #333; }
.jobs-status.status-draft { background-color: #6c757d; }
.jobs-status.status-trash { background-color: #dc3545; }
</style>
