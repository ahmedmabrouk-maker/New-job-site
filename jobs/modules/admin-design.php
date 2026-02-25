<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$logo_url = get_option( 'jobs_logo_url', '' );
$primary_color = get_option( 'jobs_primary_color', '#1d3469' );
?>
<div class="jobs-module-header">
    <h2>Design Customization</h2>
</div>
<form id="jobs-design-form" class="jobs-form">
    <div class="jobs-form-group">
        <label for="jobs_logo_url">Site Logo URL</label>
        <input type="text" name="jobs_logo_url" id="jobs_logo_url" value="<?php echo esc_attr( $logo_url ); ?>">
    </div>
    <div class="jobs-form-group">
        <label for="jobs_primary_color">Primary Color</label>
        <input type="text" name="jobs_primary_color" id="jobs_primary_color" value="<?php echo esc_attr( $primary_color ); ?>">
    </div>
    <button type="submit" class="jobs-submit-btn">Save Design Settings</button>
    <div id="jobs-design-message"></div>
</form>

<script>
jQuery(document).ready(function($) {
    $('#jobs-design-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=jobs_save_design_settings&nonce=' + jobs_ajax.nonce;

        $('#jobs-design-message').text('Saving...').css('color', '#333');

        $.post(jobs_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                $('#jobs-design-message').text(response.data).css('color', 'green');
            } else {
                $('#jobs-design-message').text(response.data).css('color', 'red');
            }
        });
    });
});
</script>
