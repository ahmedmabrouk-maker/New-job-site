<?php
/**
 * Module: Job Requests (Review Queue for Reviewers / Admins)
 */

$current_user = wp_get_current_user();
$roles = ( array ) $current_user->roles;

if ( in_array( 'reviewer', $roles ) || in_array( 'administrator', $roles ) ) {
    ?>
    <div class="jobs-module-container">
        <h2>Job Review Queue</h2>
        <div id="jobs-review-list">
            <?php
            $args = array(
                'post_type'      => 'job',
                'post_status'    => 'pending',
                'posts_per_page' => -1
            );
            $pending_jobs = new WP_Query( $args );

            if ( $pending_jobs->have_posts() ) {
                echo '<ul class="jobs-review-list">';
                while ( $pending_jobs->have_posts() ) {
                    $pending_jobs->the_post();
                    $job_id = get_the_ID();
                    echo '<li id="job-review-' . $job_id . '">';
                    echo '<h3>' . get_the_title() . '</h3>';
                    echo '<p>Posted by: ' . get_the_author() . '</p>';
                    echo '<div class="job-excerpt">' . get_the_excerpt() . '</div>';
                    echo '<div class="jobs-actions">';
                    echo '<button class="jobs-btn jobs-btn-approve" data-id="' . $job_id . '">Approve</button>';
                    echo '<button class="jobs-btn jobs-btn-reject" data-id="' . $job_id . '">Reject</button>';
                    echo '</div>';
                    echo '</li>';
                }
                echo '</ul>';
                wp_reset_postdata();
            } else {
                echo '<p>No pending jobs found.</p>';
            }
            ?>
        </div>
    </div>
    <?php
} elseif ( in_array( 'employer', $roles ) ) {
     ?>
     <div class="jobs-module-container">
        <h2>Job Applications Received</h2>
        <?php
        // Get all jobs by this employer
        $my_jobs = get_posts( array(
            'post_type' => 'job',
            'posts_per_page' => -1,
            'author' => $current_user->ID
        ) );

        if ( empty( $my_jobs ) ) {
            echo '<p>You have not posted any jobs yet.</p>';
        } else {
            $job_ids = wp_list_pluck( $my_jobs, 'ID' );

            $apps = new WP_Query( array(
                'post_type' => 'application',
                'meta_query' => array(
                    array( 'key' => '_job_id', 'value' => $job_ids, 'compare' => 'IN' )
                )
            ) );

            if ( $apps->have_posts() ) {
                echo '<ul class="jobs-list">';
                while ( $apps->have_posts() ) {
                    $apps->the_post();
                    $job_id = get_post_meta( get_the_ID(), '_job_id', true );
                    $applicant_id = get_post_meta( get_the_ID(), '_applicant_id', true );
                    $applicant = get_userdata( $applicant_id );
                    $job_title = get_the_title( $job_id );

                    echo '<li>';
                    echo '<h3>Application for: ' . esc_html( $job_title ) . '</h3>';
                    echo '<p>Applicant: ' . esc_html( $applicant->display_name ) . ' (' . esc_html( $applicant->user_email ) . ')</p>';
                    echo '<p>Date: ' . get_the_date() . '</p>';
                    echo '</li>';
                }
                echo '</ul>';
                wp_reset_postdata();
            } else {
                 echo '<p>No applications received yet.</p>';
            }
        }
        ?>
     </div>
     <?php
}
?>
