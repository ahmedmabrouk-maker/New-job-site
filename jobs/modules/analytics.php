<?php
/**
 * Module: Analytics (Full Page Modal)
 */
$current_user = wp_get_current_user();
$roles = ( array ) $current_user->roles;

if ( in_array( 'administrator', $roles ) || in_array( 'employer', $roles ) ) {
    ?>
    <div class="jobs-module-container">
        <h2>Analytics & Insights</h2>
        <div class="jobs-grid">
            <div class="job-card" style="text-align:center;">
                <h3>Job Views</h3>
                <p style="font-size: 2em; font-weight: bold;">1,245</p>
            </div>
            <div class="job-card" style="text-align:center;">
                <h3>Applications</h3>
                <p style="font-size: 2em; font-weight: bold;">42</p>
            </div>
            <div class="job-card" style="text-align:center;">
                <h3>Engagement Rate</h3>
                <p style="font-size: 2em; font-weight: bold;">3.4%</p>
            </div>
        </div>
        <p><em>Real-time analytics integration pending.</em></p>
    </div>
    <?php
} else {
    echo 'Access Denied';
}
