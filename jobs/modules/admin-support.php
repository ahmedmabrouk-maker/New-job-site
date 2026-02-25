<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$args = array(
    'post_type'      => 'job_notification',
    'post_status'    => 'publish',
    'posts_per_page' => 20,
);
$tickets = get_posts( $args );
?>
<div class="jobs-module-header">
    <h2>Technical Support</h2>
</div>
<div class="jobs-support-list">
    <?php if ( $tickets ) : ?>
        <table class="jobs-table widefat">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Subject</th>
                    <th>User</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $tickets as $ticket ) : ?>
                    <tr>
                        <td><?php echo get_the_date( 'Y-m-d H:i', $ticket->ID ); ?></td>
                        <td><?php echo esc_html( $ticket->post_title ); ?></td>
                        <td>
                            <?php
                            $author = get_userdata( $ticket->post_author );
                            echo $author ? esc_html( $author->display_name ) : 'Unknown';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No support tickets found.</p>
    <?php endif; ?>
</div>
