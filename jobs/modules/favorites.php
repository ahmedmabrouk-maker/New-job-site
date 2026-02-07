<?php
/**
 * Module: Favorites
 */
$user_id = get_current_user_id();
$favorites = get_user_meta( $user_id, '_jobs_favorites', true );

if ( empty( $favorites ) || ! is_array( $favorites ) ) {
    echo '<div class="jobs-module-container"><h2>Favorites</h2><p>You have no saved jobs.</p></div>';
} else {
    ?>
    <div class="jobs-module-container">
        <h2>Favorites</h2>
        <div class="jobs-grid">
            <?php
            $args = array(
                'post_type' => 'job',
                'post_status' => 'publish', // Auto-update requirement: only show if published
                'post__in' => $favorites
            );
            $query = new WP_Query( $args );

            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    // Basic card render
                    ?>
                    <div class="job-card">
                        <h3 class="job-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="job-excerpt"><?php the_excerpt(); ?></div>
                        <div class="job-actions">
                            <a href="<?php the_permalink(); ?>" class="job-view-btn">View Job</a>
                            <button class="jobs-btn-favorite" data-job-id="<?php the_ID(); ?>">Remove</button>
                        </div>
                    </div>
                    <?php
                }
                wp_reset_postdata();
            } else {
                echo '<p>No active favorites found.</p>';
            }
            ?>
        </div>
    </div>
    <?php
}
