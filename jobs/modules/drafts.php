<?php
/**
 * Module: drafts.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to view drafts.</p>';
	return;
}

$user = wp_get_current_user();

$args = array(
	'post_type'      => 'job',
	'post_status'    => 'draft',
	'author'         => $user->ID,
	'posts_per_page' => 20,
);

$query = new WP_Query( $args );

?>
<div class="jobs-module-header">
	<h2>Drafts</h2>
</div>

<div class="jobs-drafts-list">
	<?php
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			// Pastel color styling inline or via class
			?>
			<div class="jobs-draft-item">
				<div class="jobs-draft-info">
					<h3 style="margin-top:0; color: #555;"><?php the_title(); ?></h3>
					<p style="margin:0; font-size: 13px; color: #777;">Last Modified: <?php echo get_the_modified_date(); ?></p>
				</div>
				<div class="jobs-draft-actions">
					<a href="#" class="button button-small" onclick="alert('Edit functionality would open the post editor or a frontend form.'); return false;">Edit</a>
					<button class="button button-small button-link-delete" onclick="deleteDraft(<?php echo get_the_ID(); ?>, this)">Delete</button>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
	} else {
		echo '<p>No drafts found.</p>';
	}
	?>
</div>

<script>
function deleteDraft(jobId, btn) {
	if (!confirm('Are you sure you want to delete this draft?')) return;

	jQuery.post(jobs_ajax.ajax_url, {
		action: 'jobs_delete_draft',
		job_id: jobId,
		nonce: jobs_ajax.nonce
	}, function(response) {
		if (response.success) {
			jQuery(btn).closest('.jobs-draft-item').fadeOut();
		} else {
			alert(response.data);
		}
	});
}
</script>
