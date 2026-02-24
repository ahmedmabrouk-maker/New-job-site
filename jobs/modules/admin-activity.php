<?php
/**
 * Module: admin-activity.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}
?>
<h2>Activity Logs</h2>
<div class="jobs-tabs-container">
	<button class="jobs-tab-btn active" onclick="showActivity('general', this)">General Activity</button>
	<button class="jobs-tab-btn" onclick="showActivity('admin', this)">Admin Activity</button>
</div>

<div id="activity-general" class="jobs-activity-content">
	<h3>General Activity Log</h3>
	<p>Recent user activities will appear here.</p>
	<?php
	// Placeholder for activity log display
	// In a real implementation, we would query a custom table or CPT.
	// For now, let's list latest jobs as a proxy.
	$args = array(
		'post_type' => 'job',
		'posts_per_page' => 5,
		'post_status' => 'any',
	);
	$jobs = get_posts($args);
	if ($jobs) {
		echo '<ul>';
		foreach ($jobs as $job) {
			echo '<li>' . esc_html($job->post_title) . ' (' . $job->post_status . ') - ' . get_the_author_meta('display_name', $job->post_author) . ' - ' . get_the_date('', $job->ID) . '</li>';
		}
		echo '</ul>';
	} else {
		echo '<p><i>No recent jobs.</i></p>';
	}
	?>
</div>

<div id="activity-admin" class="jobs-activity-content" style="display:none;">
	<h3>Admin Activity Log</h3>
	<p>Recent admin activities will appear here.</p>
</div>

<script>
function showActivity(type, btn) {
	jQuery('.jobs-activity-content').hide();
	jQuery('#activity-' + type).show();
	jQuery('.jobs-tab-btn').removeClass('active');
	jQuery(btn).addClass('active');
}
</script>
<style>
.jobs-tabs-container { margin-bottom: 20px; border-bottom: 1px solid #ddd; }
.jobs-tab-btn { background: none; border: none; padding: 10px 20px; cursor: pointer; border-bottom: 2px solid transparent; font-weight: bold; color: #666; font-size: 14px; }
.jobs-tab-btn:hover { color: #1d3469; }
.jobs-tab-btn.active { border-bottom-color: #1d3469; color: #1d3469; }
</style>
