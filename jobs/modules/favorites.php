<?php
/**
 * Module: favorites.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
$favorites = get_user_meta( $user->ID, '_jobs_favorites', true );

if ( ! is_array( $favorites ) || empty( $favorites ) ) {
	echo '<p>No favorites saved yet.</p>';
	return;
}

// Remove duplicates and ensure IDs are valid
$favorites = array_unique( array_map( 'intval', $favorites ) );

$args = array(
	'post_type'      => 'job',
	'post_status'    => array( 'publish', 'closed' ),
	'post__in'       => $favorites,
	'posts_per_page' => 5, // Request says "Displays the last five saved job listings"
	'orderby'        => 'post__in', // Maintain order as saved (appended)
);

$query = new WP_Query( $args );

?>

<div class="jobs-module-header">
	<h2>Favorites (Last 5)</h2>
</div>

<?php if ( $query->have_posts() ) : ?>
	<div class="jobs-favorites-list">
		<?php while ( $query->have_posts() ) : $query->the_post();
			$status = get_post_status();
			$status_label = ucfirst( $status );
		?>
			<div class="jobs-favorite-item">
				<div class="jobs-favorite-info">
					<h3 class="jobs-favorite-title"><?php the_title(); ?></h3>
					<span class="jobs-date">Posted: <?php echo get_the_date(); ?></span>
					<span class="jobs-status-badge jobs-status-<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status_label ); ?></span>
				</div>
				<div class="jobs-favorite-actions">
					<a href="<?php the_permalink(); ?>" target="_blank" class="button">View</a>
					<button class="button button-small" onclick="jobsRemoveFavorite(<?php the_ID(); ?>)">Remove</button>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<script>
	function jobsRemoveFavorite(jobId) {
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_toggle_favorite',
			nonce: jobs_ajax.nonce,
			job_id: jobId
		}, function(response) {
			if(response.success) {
				loadJobsModule('favorites'); // Refresh list
			}
		});
	}
	</script>
<?php else : ?>
	<p>Your favorite jobs are no longer available.</p>
<?php endif; ?>
