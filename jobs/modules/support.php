<?php
/**
 * Module: Support (Includes Notifications Inbox)
 */
$current_user = wp_get_current_user();
?>
<div class="jobs-module-container">
    <h2>Support & Notifications</h2>

    <div class="jobs-tabs">
        <button class="jobs-tab-link active" data-tab="inbox">Inbox</button>
        <button class="jobs-tab-link" data-tab="contact">Contact Admin</button>
    </div>

    <div id="inbox" class="jobs-tab-content active" style="display:block;">
        <h3>Notifications</h3>
        <?php
        $args = array(
            'post_type'      => 'job_notification',
            'post_status'    => 'publish',
            'author'         => $current_user->ID,
            'posts_per_page' => 20
        );
        $notifications = new WP_Query( $args );

        if ( $notifications->have_posts() ) {
            echo '<ul class="jobs-notification-list">';
            while ( $notifications->have_posts() ) {
                $notifications->the_post();
                $type = get_post_meta( get_the_ID(), '_notification_type', true );
                echo '<li class="notification-item type-' . esc_attr( $type ) . '">';
                echo '<h4>' . get_the_content() . '</h4>'; // Content is the message
                echo '<span class="date">' . get_the_date() . ' ' . get_the_time() . '</span>';
                echo '</li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p>No notifications.</p>';
        }
        ?>
    </div>

    <div id="contact" class="jobs-tab-content" style="display:none;">
        <h3>Contact Administration</h3>
        <form>
            <textarea placeholder="Your message..." rows="5" style="width:100%;"></textarea>
            <button type="button" class="jobs-btn">Send Message</button>
        </form>
    </div>
</div>
<style>
.jobs-tab-link { padding: 10px 20px; cursor: pointer; background: #eee; border: none; }
.jobs-tab-link.active { background: var(--jobs-primary-color); color: #fff; }
.notification-item { padding: 10px; border-bottom: 1px solid #eee; }
.notification-item.type-success { border-left: 4px solid green; }
.notification-item.type-warning { border-left: 4px solid red; }
</style>
<script>
jQuery(document).ready(function($) {
    $('.jobs-tab-link').on('click', function() {
        $('.jobs-tab-link').removeClass('active');
        $(this).addClass('active');
        $('.jobs-tab-content').hide();
        $('#' + $(this).data('tab')).show();
    });
});
</script>
