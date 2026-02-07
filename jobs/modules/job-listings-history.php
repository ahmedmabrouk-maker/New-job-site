<?php
/**
 * Module: Job Listings History (Employer View)
 */

$current_user = wp_get_current_user();
?>
<div class="jobs-module-container">
    <h2>Job Listings History</h2>
    <div id="jobs-listings-history">
        <?php
        $args = array(
            'post_type'      => 'job',
            'post_status'    => array( 'publish', 'pending', 'draft', 'trash' ),
            'author'         => $current_user->ID,
            'posts_per_page' => -1
        );
        $my_jobs = new WP_Query( $args );

        if ( $my_jobs->have_posts() ) {
            echo '<table class="jobs-table">';
            echo '<thead><tr><th>Job Title</th><th>Status</th><th>Applications</th><th>Date</th></tr></thead>';
            echo '<tbody>';
            while ( $my_jobs->have_posts() ) {
                $my_jobs->the_post();
                $job_id = get_the_ID();
                $status = get_post_status();

                // Count applications
                $app_count = new WP_Query( array(
                    'post_type' => 'application',
                    'meta_key'  => '_job_id',
                    'meta_value' => $job_id,
                    'fields' => 'ids'
                ) );
                $count = $app_count->found_posts;

                echo '<tr>';
                echo '<td><a href="' . get_permalink() . '">' . get_the_title() . '</a></td>';
                echo '<td><span class="jobs-status status-' . $status . '">' . ucfirst( $status ) . '</span></td>';
                echo '<td>' . $count . '</td>';
                echo '<td>' . get_the_date() . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            wp_reset_postdata();
        } else {
            echo '<p>You have not posted any jobs yet.</p>';
        }
        ?>
    </div>
</div>
<style>
.jobs-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
.jobs-table th, .jobs-table td { border: 1px solid #eee; padding: 10px; text-align: left; }
.jobs-status.status-publish { color: green; font-weight: bold; }
.jobs-status.status-pending { color: orange; font-weight: bold; }
.jobs-status.status-trash { color: red; }
</style>
