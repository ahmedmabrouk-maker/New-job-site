<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$args = array(
    'post_type'      => 'job_activity',
    'post_status'    => 'publish',
    'posts_per_page' => 20,
    'orderby'        => 'date',
    'order'          => 'DESC',
);
$activities = get_posts( $args );
?>
<div class="jobs-module-header">
    <h2>Activity Log</h2>
</div>
<div class="jobs-activity-list">
    <?php if ( $activities ) : ?>
        <table class="jobs-table widefat">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Activity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $activities as $activity ) : ?>
                    <tr>
                        <td><?php echo get_the_date( 'Y-m-d H:i', $activity->ID ); ?></td>
                        <td><?php echo esc_html( $activity->post_title ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No activity found.</p>
    <?php endif; ?>
</div>
