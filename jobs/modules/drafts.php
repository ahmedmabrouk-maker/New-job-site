<?php
/**
 * Module: Drafts (Dropdown)
 */
$user_id = get_current_user_id();
?>
<div class="jobs-module-container">
    <h3>My Drafts</h3>
    <?php
    $args = array(
        'post_type'      => 'job',
        'post_status'    => 'draft',
        'author'         => $user_id,
        'posts_per_page' => 10
    );
    $drafts = new WP_Query( $args );

    if ( $drafts->have_posts() ) {
        echo '<ul class="jobs-draft-list">';
        while ( $drafts->have_posts() ) {
            $drafts->the_post();
            echo '<li class="draft-item" style="background-color: #e3f2fd; padding: 10px; margin-bottom: 5px; border-radius: 4px;">';
            echo '<strong>' . get_the_title() . '</strong>';
            echo '<br><small>Last modified: ' . get_the_modified_date() . '</small>';
            echo '</li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    } else {
        echo '<p>No saved drafts.</p>';
    }
    ?>
</div>
