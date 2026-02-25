<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$job_count = wp_count_posts( 'job' );
$user_counts = count_users();
$users = $user_counts['total_users'];
?>
<div class="jobs-module-header">
    <h2>System Reports</h2>
</div>
<div class="jobs-reports-grid">
    <div class="jobs-report-card">
        <h3>Active Jobs</h3>
        <p class="jobs-count"><?php echo intval( $job_count->publish ); ?></p>
    </div>
    <div class="jobs-report-card">
        <h3>Pending Jobs</h3>
        <p class="jobs-count"><?php echo intval( $job_count->pending ); ?></p>
    </div>
    <div class="jobs-report-card">
        <h3>Total Users</h3>
        <p class="jobs-count"><?php echo intval( $users ); ?></p>
    </div>
</div>
