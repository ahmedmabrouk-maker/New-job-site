<?php
/**
 * Module: Notifications (Dropdown)
 */
$current_user = wp_get_current_user();
?>
<div class="jobs-module-container">
    <h3>Notifications</h3>
    <?php
    $args = array(
        'post_type'      => 'job_notification',
        'post_status'    => 'publish',
        'author'         => $current_user->ID,
        'posts_per_page' => 5
    );
    $notifications = new WP_Query( $args );

    if ( $notifications->have_posts() ) {
        echo '<ul class="jobs-notification-list">';
        while ( $notifications->have_posts() ) {
            $notifications->the_post();
            $type = get_post_meta( get_the_ID(), '_notification_type', true );
            echo '<li class="notification-item type-' . esc_attr( $type ) . '">';
            echo '<strong>' . get_the_content() . '</strong><br>';
            echo '<small>' . get_the_date() . '</small>';
            echo '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo '<p>No new notifications.</p>';
    }
    ?>
    <div style="text-align:center; margin-top:10px;">
        <a href="#" onclick="jQuery('.jobs-modules-list li[data-module=\'support\']').click(); return false;">View All in Inbox</a>
    </div>
</div>
<style>
.notification-item { padding: 8px 0; border-bottom: 1px solid #eee; font-size: 0.9rem; }
.notification-item:last-child { border-bottom: none; }
</style>
