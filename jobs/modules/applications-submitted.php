<?php
$user_id = get_current_user_id();
$args = array(
    'post_type' => 'application',
    'author' => $user_id,
    'posts_per_page' => -1,
);
$query = new WP_Query($args);
?>
<div class="jobs-module-header">
    <h2 class="jobs-module-title">Applications Submitted</h2>
</div>
<?php if($query->have_posts()): ?>
    <ul class="jobs-list">
    <?php while($query->have_posts()): $query->the_post(); ?>
        <li class="jobs-list-item" style="border-bottom: 1px solid #eee; padding: 10px 0;">
            <strong><?php the_title(); ?></strong> - <?php echo get_the_date(); ?>
        </li>
    <?php endwhile; ?>
    </ul>
<?php else: ?>
    <p>No applications submitted.</p>
<?php endif; wp_reset_postdata(); ?>
