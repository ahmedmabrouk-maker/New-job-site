<?php
/**
 * Module: Settings
 */
$current_user = wp_get_current_user();
?>
<div class="jobs-module-container">
    <h2>Account Settings</h2>
    <form id="jobs-settings-form">
        <label>Email</label>
        <input type="email" name="user_email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required>

        <label>New Password (leave blank to keep current)</label>
        <input type="password" name="user_pass" placeholder="New Password">

        <label>Confirm Password</label>
        <input type="password" name="user_pass_confirm" placeholder="Confirm Password">

        <button type="submit" class="jobs-btn" style="margin-top:15px;">Update Account</button>
        <div id="settings-message"></div>
    </form>
</div>
<script>
jQuery(document).ready(function($) {
    $('#jobs-settings-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: data + '&action=jobs_update_settings&nonce=' + jobs_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    $('#settings-message').html('<p class="success">Account updated!</p>');
                } else {
                    $('#settings-message').html('<p class="error">' + response.data + '</p>');
                }
            }
        });
    });
});
</script>
