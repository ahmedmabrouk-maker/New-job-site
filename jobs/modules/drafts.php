<?php
/**
 * Module: drafts.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();

$args = array(
	'post_type'      => 'job',
	'post_status'    => 'draft',
	'author'         => $user->ID,
	'posts_per_page' => 10,
);

$query = new WP_Query( $args );

?>

<div class="jobs-module-header">
	<h2>Drafts</h2>
</div>

<?php if ( $query->have_posts() ) : ?>
	<div class="jobs-drafts-list">
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>
			<div class="jobs-draft-item">
				<div class="jobs-draft-info">
					<h3 class="jobs-draft-title"><?php the_title(); ?></h3>
					<span class="jobs-date">Created: <?php echo get_the_date(); ?></span>
				</div>
				<div class="jobs-draft-actions">
					<button class="button" onclick="loadJobsModule('job-posting', {job_id: <?php the_ID(); ?>})">Edit</button>
					<button class="button button-small" onclick="jobsDeleteDraft(<?php the_ID(); ?>)">Delete</button>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</div>
	<script>
	function jobsDeleteDraft(jobId) {
		if(!confirm('Are you sure you want to delete this draft?')) return;
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_delete_draft',
			nonce: jobs_ajax.nonce,
			job_id: jobId
		}, function(response) {
			if(response.success) {
				loadJobsModule('drafts'); // Refresh list
			} else {
				alert(response.data);
			}
		});
	}
	</script>
<?php else : ?>
	<p>No drafts found.</p>
<?php endif; ?>

<style>
/* Soft Pastel Colors for Drafts */
.jobs-draft-item {
	background-color: #fff9e6; /* Pastel Yellow */
	border: 1px solid #ffeeba;
	border-radius: 8px;
	padding: 20px;
	margin-bottom: 15px;
	display: flex;
	justify-content: space-between;
	align-items: center;
	transition: background-color 0.2s;
}
.jobs-draft-item:hover {
	background-color: #fff3cd;
}
.jobs-draft-title {
	margin: 0;
	color: #856404;
	font-size: 18px;
}
.jobs-draft-actions a {
	margin-right: 10px;
}
</style>
