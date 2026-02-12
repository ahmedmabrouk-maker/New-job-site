<?php
$user = wp_get_current_user();
$roles = (array) $user->roles;
$args = array(
    'post_type' => 'job',
    'post_status' => 'pending',
    'posts_per_page' => -1,
);

// If not admin/reviewer, restrict to user
if (!in_array('administrator', $roles) && !in_array('reviewer', $roles)) {
    $args['author'] = $user->ID;
}

$query = new WP_Query($args);
?>
<div class="jobs-module-header">
    <h2 class="jobs-module-title">Job Requests (Pending Approval)</h2>
</div>
<?php if($query->have_posts()): ?>
    <ul class="jobs-list">
    <?php while($query->have_posts()): $query->the_post(); ?>
        <li class="jobs-list-item" style="border-bottom: 1px solid #eee; padding: 10px 0;">
            <strong><?php the_title(); ?></strong> by <?php the_author(); ?> - <?php echo get_the_date(); ?>
            <?php if(in_array('administrator', $roles) || in_array('reviewer', $roles)): ?>
                <button class="jobs-btn-small" style="margin-left: 10px;">Approve</button>
            <?php endif; ?>
        </li>
    <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No pending job requests.</p>
<?php endif; wp_reset_postdata(); ?>
