<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$ads_code = get_option( 'jobs_ads_code', '' );
?>
<div class="jobs-module-header">
    <h2>External Ads Management</h2>
</div>
<form id="jobs-ads-form" class="jobs-form">
    <div class="jobs-form-group">
        <label for="jobs_ads_code">Ad Code (Google AdSense)</label>
        <textarea name="jobs_ads_code" id="jobs_ads_code" rows="10"><?php echo esc_textarea( $ads_code ); ?></textarea>
    </div>
    <button type="submit" class="jobs-submit-btn">Save Ads Settings</button>
    <div id="jobs-ads-message"></div>
</form>

<script>
jQuery(document).ready(function($) {
    $('#jobs-ads-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=jobs_save_ads_settings&nonce=' + jobs_ajax.nonce;

        $('#jobs-ads-message').text('Saving...').css('color', '#333');

        $.post(jobs_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                $('#jobs-ads-message').text(response.data).css('color', 'green');
            } else {
                $('#jobs-ads-message').text(response.data).css('color', 'red');
            }
        });
    });
});
</script>
