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

if ( empty( $favorites ) || ! is_array( $favorites ) ) {
	echo '<div class="jobs-module-header"><h2>Favorites</h2></div>';
	echo '<p>You have no favorite jobs.</p>';
	return;
}

$args = array(
	'post_type'      => 'job',
	'post_status'    => 'publish',
	'post__in'       => $favorites,
	'posts_per_page' => 5,
	'orderby'        => 'post__in', // Maintain order or date? 'date' is better for "last five".
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$query = new WP_Query( $args );

?>
<div class="jobs-module-header">
	<h2>Favorites</h2>
</div>

<div class="jobs-favorites-list">
	<?php
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<div class="jobs-favorite-item">
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<p>
					<strong>Date:</strong> <?php echo get_the_date(); ?>
				</p>
				<button class="button" onclick="removeFavorite(<?php echo get_the_ID(); ?>, this)">Remove</button>
			</div>
			<?php
		}
		wp_reset_postdata();
	} else {
		echo '<p>No active favorite jobs found.</p>';
	}
	?>
</div>

<script>
function removeFavorite(jobId, btn) {
	jQuery.post(jobs_ajax.ajax_url, {
		action: 'jobs_toggle_favorite',
		job_id: jobId,
		nonce: jobs_ajax.nonce
	}, function(response) {
		if (response.success) {
			jQuery(btn).closest('.jobs-favorite-item').fadeOut();
		}
	});
}
</script>
