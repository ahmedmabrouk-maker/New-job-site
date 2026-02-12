<?php
$user_id = get_current_user_id();
$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );
if ( ! is_array( $cv_data ) ) $cv_data = array();

$education = isset($cv_data['education']) && is_array($cv_data['education']) ? $cv_data['education'] : array();
$experience = isset($cv_data['experience']) && is_array($cv_data['experience']) ? $cv_data['experience'] : array();
$skills = isset($cv_data['skills']) ? $cv_data['skills'] : '';
$courses = isset($cv_data['courses']) ? $cv_data['courses'] : '';
$certifications = isset($cv_data['certifications']) ? $cv_data['certifications'] : '';
?>
<div class="jobs-module-header">
    <h2 class="jobs-module-title">CV / Resume</h2>
</div>
<form id="jobs-cv-form" class="jobs-form">

    <!-- Education Section -->
    <div class="jobs-section">
        <h3 class="jobs-section-title">Education <button type="button" class="jobs-btn-small jobs-add-row" data-section="education">+ Add</button></h3>
        <div id="jobs-education-container">
            <?php foreach($education as $index => $edu): ?>
                <div class="jobs-repeater-row">
                    <input type="text" name="cv_data[education][<?php echo $index; ?>][school]" value="<?php echo esc_attr($edu['school'] ?? ''); ?>" placeholder="School/University" class="jobs-input">
                    <input type="text" name="cv_data[education][<?php echo $index; ?>][degree]" value="<?php echo esc_attr($edu['degree'] ?? ''); ?>" placeholder="Degree" class="jobs-input">
                    <input type="text" name="cv_data[education][<?php echo $index; ?>][date]" value="<?php echo esc_attr($edu['date'] ?? ''); ?>" placeholder="Date" class="jobs-input">
                    <button type="button" class="jobs-btn-small jobs-remove-row">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <script type="text/template" id="tmpl-education">
            <div class="jobs-repeater-row">
                <input type="text" name="cv_data[education][INDEX][school]" placeholder="School/University" class="jobs-input">
                <input type="text" name="cv_data[education][INDEX][degree]" placeholder="Degree" class="jobs-input">
                <input type="text" name="cv_data[education][INDEX][date]" placeholder="Date" class="jobs-input">
                <button type="button" class="jobs-btn-small jobs-remove-row">Remove</button>
            </div>
        </script>
    </div>

    <!-- Experience Section -->
    <div class="jobs-section">
        <h3 class="jobs-section-title">Experience <button type="button" class="jobs-btn-small jobs-add-row" data-section="experience">+ Add</button></h3>
        <div id="jobs-experience-container">
             <?php foreach($experience as $index => $exp): ?>
                <div class="jobs-repeater-row">
                    <input type="text" name="cv_data[experience][<?php echo $index; ?>][company]" value="<?php echo esc_attr($exp['company'] ?? ''); ?>" placeholder="Company" class="jobs-input">
                    <input type="text" name="cv_data[experience][<?php echo $index; ?>][position]" value="<?php echo esc_attr($exp['position'] ?? ''); ?>" placeholder="Position" class="jobs-input">
                    <input type="text" name="cv_data[experience][<?php echo $index; ?>][date]" value="<?php echo esc_attr($exp['date'] ?? ''); ?>" placeholder="Date" class="jobs-input">
                    <textarea name="cv_data[experience][<?php echo $index; ?>][description]" placeholder="Description" class="jobs-textarea" rows="2"><?php echo esc_textarea($exp['description'] ?? ''); ?></textarea>
                    <button type="button" class="jobs-btn-small jobs-remove-row">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <script type="text/template" id="tmpl-experience">
            <div class="jobs-repeater-row">
                <input type="text" name="cv_data[experience][INDEX][company]" placeholder="Company" class="jobs-input">
                <input type="text" name="cv_data[experience][INDEX][position]" placeholder="Position" class="jobs-input">
                <input type="text" name="cv_data[experience][INDEX][date]" placeholder="Date" class="jobs-input">
                <textarea name="cv_data[experience][INDEX][description]" placeholder="Description" class="jobs-textarea" rows="2"></textarea>
                <button type="button" class="jobs-btn-small jobs-remove-row">Remove</button>
            </div>
        </script>
    </div>

    <!-- Skills -->
    <div class="jobs-section">
        <h3 class="jobs-section-title">Skills</h3>
        <textarea name="cv_data[skills]" class="jobs-textarea" rows="5"><?php echo esc_textarea( $skills ); ?></textarea>
    </div>

    <!-- Courses -->
    <div class="jobs-section">
        <h3 class="jobs-section-title">Courses</h3>
        <textarea name="cv_data[courses]" class="jobs-textarea" rows="5"><?php echo esc_textarea( $courses ); ?></textarea>
    </div>

    <!-- Certifications -->
    <div class="jobs-section">
        <h3 class="jobs-section-title">Certifications</h3>
        <textarea name="cv_data[certifications]" class="jobs-textarea" rows="5"><?php echo esc_textarea( $certifications ); ?></textarea>
    </div>

    <div class="jobs-form-actions">
        <button type="submit" class="jobs-btn-primary">Save CV</button>
    </div>
    <div id="jobs-cv-message" class="jobs-message"></div>
</form>
