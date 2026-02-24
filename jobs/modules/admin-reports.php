<?php
/**
 * Module: admin-reports.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}

$total_jobs = wp_count_posts('job');
$total_applications = wp_count_posts('job_application');
$users = count_users();
?>
<h2>Reports System</h2>
<div class="jobs-report-cards">
	<div class="jobs-card">
		<h3>Total Jobs</h3>
		<p><?php echo isset($total_jobs->publish) ? $total_jobs->publish : 0; ?></p>
	</div>
	<div class="jobs-card">
		<h3>Pending Jobs</h3>
		<p><?php echo isset($total_jobs->pending) ? $total_jobs->pending : 0; ?></p>
	</div>
	<div class="jobs-card">
		<h3>Total Applications</h3>
		<p><?php echo isset($total_applications->publish) ? $total_applications->publish : 0; ?></p>
	</div>
	<div class="jobs-card">
		<h3>Total Users</h3>
		<p><?php echo isset($users['total_users']) ? $users['total_users'] : 0; ?></p>
	</div>
</div>
<style>
.jobs-report-cards { display: flex; gap: 20px; flex-wrap: wrap; }
.jobs-card { background: rgba(255,255,255,0.8); padding: 20px; border-radius: 10px; flex: 1; min-width: 200px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
.jobs-card h3 { margin-top: 0; font-size: 14px; color: #666; }
.jobs-card p { font-size: 24px; font-weight: bold; margin: 0; color: #1d3469; }
</style>
