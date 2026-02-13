<?php
/**
 * Module: applications-submitted.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to view your applications.</p>';
	return;
}

$user = wp_get_current_user();
if ( ! in_array( 'job_seeker', (array) $user->roles ) && ! in_array( 'administrator', (array) $user->roles ) ) {
	echo '<p>This module is for Job Seekers only.</p>';
	return;
}

$args = array(
	'post_type'      => 'job_application',
	'post_status'    => array( 'publish', 'pending', 'draft' ),
	'author'         => $user->ID,
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$applications = new WP_Query( $args );

?>

<div class="jobs-module-header">
	<h2>Applications Submitted</h2>
</div>

<?php if ( $applications->have_posts() ) : ?>
	<div class="jobs-applications-list">
		<?php while ( $applications->have_posts() ) : $applications->the_post(); ?>
			<?php
			$job_id = get_post_meta( get_the_ID(), '_job_id', true ); // Using meta for explicit link, or post_parent
			if ( ! $job_id ) $job_id = $post->post_parent;

			$job_title = get_the_title( $job_id );
			$status = get_post_status();
			$date = get_the_date();
			?>
			<div class="jobs-application-item">
				<div class="jobs-application-info">
					<strong><?php echo esc_html( $job_title ); ?></strong>
					<span class="jobs-date">Applied on: <?php echo esc_html( $date ); ?></span>
				</div>
				<div class="jobs-application-status">
					<span class="jobs-status status-<?php echo esc_attr( $status ); ?>"><?php echo ucfirst( $status ); ?></span>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
	<?php wp_reset_postdata(); ?>
<?php else : ?>
	<p>You have not submitted any applications yet.</p>
<?php endif; ?>

<style>
.jobs-application-item {
	display: flex;
	justify-content: space-between;
	padding: 15px 0;
	border-bottom: 1px solid #eee;
}
.jobs-application-item:last-child { border-bottom: none; }
.jobs-status { padding: 4px 8px; border-radius: 4px; font-size: 12px; background: #eee; }
.jobs-status.status-publish { background: #d4edda; color: #155724; }
.jobs-status.status-pending { background: #fff3cd; color: #856404; }
</style>
