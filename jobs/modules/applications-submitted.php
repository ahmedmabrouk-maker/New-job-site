<?php
/**
 * Module: Applications Submitted (Job Seeker View)
 */

$current_user = wp_get_current_user();
?>
<div class="jobs-module-container">
    <h2>Applications Submitted</h2>
    <div id="jobs-applications-list">
        <?php
        $args = array(
            'post_type'      => 'application',
            'post_status'    => 'publish',
            'author'         => $current_user->ID,
            'posts_per_page' => -1
        );
        $apps = new WP_Query( $args );

        if ( $apps->have_posts() ) {
            echo '<ul class="jobs-list">';
            while ( $apps->have_posts() ) {
                $apps->the_post();
                $job_id = get_post_meta( get_the_ID(), '_job_id', true );
                $job = get_post( $job_id );

                echo '<li>';
                if ( $job ) {
                    echo '<h3>' . esc_html( $job->post_title ) . '</h3>';
                    echo '<p>Applied on: ' . get_the_date() . '</p>';
                    echo '<p>Status: <span class="jobs-status">' . esc_html( $job->post_status ) . '</span></p>';
                } else {
                     echo '<h3>Unknown Job (Deleted)</h3>';
                }
                echo '</li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo '<p>You have not submitted any applications yet.</p>';
        }
        ?>
    </div>
</div>
