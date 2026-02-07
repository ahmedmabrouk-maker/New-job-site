<?php
/**
 * Module: Company Profile
 */
$user_id = get_current_user_id();
$company_data = get_user_meta( $user_id, '_jobs_company_data', true );
if ( ! $company_data ) $company_data = array();
?>
<div class="jobs-module-container">
    <h2>Update Company Profile</h2>
    <form id="jobs-company-form">
        <label>Company Name</label>
        <input type="text" name="company_name" value="<?php echo esc_attr( isset($company_data['company_name']) ? $company_data['company_name'] : '' ); ?>">

        <label>Description</label>
        <textarea name="company_description" rows="5"><?php echo esc_textarea( isset($company_data['company_description']) ? $company_data['company_description'] : '' ); ?></textarea>

        <label>Website</label>
        <input type="text" name="company_website" value="<?php echo esc_attr( isset($company_data['company_website']) ? $company_data['company_website'] : '' ); ?>">

        <button type="submit" class="jobs-btn" style="margin-top:15px;">Save Profile</button>
        <div id="company-message"></div>
    </form>
</div>
<script>
jQuery(document).ready(function($) {
    $('#jobs-company-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: data + '&action=jobs_save_company&nonce=' + jobs_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    $('#company-message').html('<p class="success">Company Profile Updated!</p>');
                }
            }
        });
    });
});
</script>
