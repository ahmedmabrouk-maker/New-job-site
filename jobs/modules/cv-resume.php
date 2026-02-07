<?php
/**
 * Module: CV / Resume
 */
$user_id = get_current_user_id();
$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );
if ( ! $cv_data ) $cv_data = array();
?>
<div class="jobs-module-container">
    <h2>Update CV / Resume</h2>
    <form id="jobs-cv-form">
        <h3>Experience</h3>
        <textarea name="experience" rows="5" placeholder="List your experience..."><?php echo esc_textarea( isset($cv_data['experience']) ? $cv_data['experience'] : '' ); ?></textarea>

        <h3>Education</h3>
        <textarea name="education" rows="5" placeholder="List your education..."><?php echo esc_textarea( isset($cv_data['education']) ? $cv_data['education'] : '' ); ?></textarea>

        <h3>Skills</h3>
        <input type="text" name="skills" value="<?php echo esc_attr( isset($cv_data['skills']) ? $cv_data['skills'] : '' ); ?>" placeholder="Comma separated skills">

        <button type="submit" class="jobs-btn" style="margin-top:15px;">Save CV</button>
        <div id="cv-message"></div>
    </form>
</div>
<script>
jQuery(document).ready(function($) {
    $('#jobs-cv-form').on('submit', function(e) {
        e.preventDefault();
        var data = $(this).serialize();
        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: data + '&action=jobs_save_cv&nonce=' + jobs_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    $('#cv-message').html('<p class="success">CV Updated!</p>');
                }
            }
        });
    });
});
</script>
