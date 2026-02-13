<?php
/**
 * Module: drafts.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to view drafts.</p>';
	return;
}

$user_id = get_current_user_id();

$args = array(
	'post_type'      => 'job',
	'post_status'    => 'draft',
	'author'         => $user_id,
	'posts_per_page' => -1,
	'orderby'        => 'date',
	'order'          => 'DESC',
);

$drafts_query = new WP_Query( $args );

?>

<div class="jobs-module-header">
	<h2>Drafts</h2>
</div>

<?php if ( $drafts_query->have_posts() ) : ?>
	<div class="jobs-drafts-list">
		<?php while ( $drafts_query->have_posts() ) : $drafts_query->the_post(); ?>
			<div class="jobs-draft-item">
				<div class="jobs-draft-info">
					<strong><?php the_title( '', '', true ); ?></strong>
					<?php if ( get_the_title() === '' ) echo '<em>(No Title)</em>'; ?>
					<span class="jobs-date">Last modified: <?php echo get_the_modified_date(); ?></span>
				</div>
				<div class="jobs-draft-actions">
					<button class="button button-small" onclick="editDraft(<?php echo get_the_ID(); ?>)">Edit</button>
					<button class="button button-small" onclick="deleteDraft(<?php echo get_the_ID(); ?>)">Delete</button>
				</div>
			</div>
		<?php endwhile; ?>
	</div>
	<?php wp_reset_postdata(); ?>
<?php else : ?>
	<p>No drafts found.</p>
<?php endif; ?>

<style>
.jobs-draft-item {
	background-color: #fef9e7; /* Soft pastel yellow/cream */
	border: 1px solid #fae5b0;
	border-radius: 8px;
	padding: 15px;
	margin-bottom: 10px;
	display: flex;
	justify-content: space-between;
	align-items: center;
}
.jobs-draft-info strong { color: #8a6d3b; }
.jobs-draft-actions button { background-color: #fff; border: 1px solid #ddd; color: #555; cursor: pointer; }
.jobs-draft-actions button:hover { background-color: #eee; }
</style>

<script>
function editDraft(jobId) {
	// Ideally this would load the job-posting module populated with data
	alert('Edit functionality for draft ID ' + jobId + ' to be implemented.');
}
function deleteDraft(jobId) {
	if (confirm('Delete this draft?')) {
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_delete_draft',
			nonce: jobs_ajax.nonce,
			job_id: jobId
		}, function(response) {
			loadJobsModule('drafts');
		});
	}
}
</script>
