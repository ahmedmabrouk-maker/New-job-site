<?php
/**
 * Module: CV / Resume (Step-by-step Dropdown)
 */
$user_id = get_current_user_id();
$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );
if ( ! $cv_data ) $cv_data = array();
?>
<div class="jobs-module-container">
    <h2>CV / Resume</h2>
    <div class="jobs-tabs">
        <button class="jobs-tab-link active" data-tab="step-qualifications">Qualifications</button>
        <button class="jobs-tab-link" data-tab="step-experience">Experience</button>
        <button class="jobs-tab-link" data-tab="step-skills">Skills</button>
    </div>

    <form id="jobs-cv-form">
        <div id="step-qualifications" class="jobs-tab-content active" style="display:block;">
            <h3>Qualifications</h3>
            <textarea name="education" rows="5" placeholder="List your qualifications..."><?php echo esc_textarea( isset($cv_data['education']) ? $cv_data['education'] : '' ); ?></textarea>
            <div class="step-nav">
                <button type="button" class="jobs-btn next-step" data-next="step-experience">Next</button>
            </div>
        </div>

        <div id="step-experience" class="jobs-tab-content" style="display:none;">
            <h3>Experience</h3>
            <textarea name="experience" rows="5" placeholder="List your experience..."><?php echo esc_textarea( isset($cv_data['experience']) ? $cv_data['experience'] : '' ); ?></textarea>
            <div class="step-nav">
                <button type="button" class="jobs-btn prev-step" data-prev="step-qualifications">Back</button>
                <button type="button" class="jobs-btn next-step" data-next="step-skills">Next</button>
            </div>
        </div>

        <div id="step-skills" class="jobs-tab-content" style="display:none;">
            <h3>Skills</h3>
            <input type="text" name="skills" value="<?php echo esc_attr( isset($cv_data['skills']) ? $cv_data['skills'] : '' ); ?>" placeholder="Comma separated skills">
            <div class="step-nav">
                <button type="button" class="jobs-btn prev-step" data-prev="step-experience">Back</button>
                <button type="submit" class="jobs-btn">Save CV</button>
            </div>
        </div>
    </form>

    <div id="cv-message"></div>
    <hr>
    <button class="jobs-btn" onclick="window.print()">Download PDF (Print)</button>
</div>
<style>
.step-nav { margin-top: 15px; display: flex; justify-content: space-between; }
</style>
<script>
jQuery(document).ready(function($) {
    $('.jobs-tab-link').on('click', function() {
        $('.jobs-tab-link').removeClass('active');
        $(this).addClass('active');
        $('.jobs-tab-content').hide();
        $('#' + $(this).data('tab')).show();
    });

    $('.next-step').on('click', function() {
        var next = $(this).data('next');
        $('.jobs-tab-link[data-tab="' + next + '"]').click();
    });

    $('.prev-step').on('click', function() {
        var prev = $(this).data('prev');
        $('.jobs-tab-link[data-tab="' + prev + '"]').click();
    });

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
