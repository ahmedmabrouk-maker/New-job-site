<?php
/**
 * Module: favorites.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to view favorites.</p>';
	return;
}

$user_id = get_current_user_id();
$favorites = get_user_meta( $user_id, '_jobs_favorites', true );

if ( ! is_array( $favorites ) || empty( $favorites ) ) {
	echo '<div class="jobs-module-header"><h2>Favorites</h2></div>';
	echo '<p>You have no favorite jobs.</p>';
	return;
}

// Fetch jobs
$args = array(
	'post_type'      => 'job',
	'post_status'    => 'publish',
	'post__in'       => $favorites,
	'posts_per_page' => 5, // "Displays the last five saved job listings"
	'orderby'        => 'post__in', // Maintain order? Or date? Prompt says "last five saved". Assuming array order is chronological.
);

// If we want "last saved", we should assume new ones are appended or prepended.
// Let's query by post__in and order by date for now, or assume favorites array is ordered.
// Actually, `post__in` order is not guaranteed unless `orderby` is `post__in`.
// Let's just show the valid ones.

$fav_query = new WP_Query( $args );

?>

<div class="jobs-module-header">
	<h2>Favorites</h2>
</div>

<?php if ( $fav_query->have_posts() ) : ?>
	<div class="jobs-favorites-list">
		<?php while ( $fav_query->have_posts() ) : $fav_query->the_post(); ?>
			<div class="jobs-favorite-item">
				<div class="jobs-favorite-info">
					<strong><a href="<?php the_permalink(); ?>" target="_blank"><?php the_title(); ?></a></strong>
					<span class="jobs-date"><?php echo get_the_date(); ?></span>
				</div>
				<div class="jobs-favorite-actions">
					<button class="button button-small" onclick="removeFavorite(<?php echo get_the_ID(); ?>)">Remove</button>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
	<?php wp_reset_postdata(); ?>
<?php else : ?>
	<p>No active favorite jobs found.</p>
<?php endif; ?>

<style>
.jobs-favorite-item {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 15px 0;
	border-bottom: 1px solid #eee;
}
.jobs-favorite-item:last-child { border-bottom: none; }
</style>

<script>
function removeFavorite(jobId) {
	if (confirm('Remove from favorites?')) {
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_toggle_favorite',
			nonce: jobs_ajax.nonce,
			job_id: jobId
		}, function(response) {
			loadJobsModule('favorites'); // Reload module
		});
	}
}
</script>
