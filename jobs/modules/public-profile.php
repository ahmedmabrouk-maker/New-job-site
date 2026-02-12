<?php
$user = wp_get_current_user();
?>
<div class="jobs-module-header">
    <h2 class="jobs-module-title">Public Profile</h2>
</div>
<div class="jobs-profile-info">
    <p><strong>Name:</strong> <?php echo esc_html($user->display_name); ?></p>
    <p><strong>Email:</strong> <?php echo esc_html($user->user_email); ?></p>
</div>
