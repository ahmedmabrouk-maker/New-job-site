<?php
/**
 * Module: cv-resume.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to manage your CV.</p>';
	return;
}

$user_id = get_current_user_id();
$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );
if ( ! is_array( $cv_data ) ) {
	$cv_data = array(
		'education' => array(),
		'experience' => array(),
		'skills' => '',
	);
}

$education = isset( $cv_data['education'] ) ? $cv_data['education'] : array();
$experience = isset( $cv_data['experience'] ) ? $cv_data['experience'] : array();
$skills = isset( $cv_data['skills'] ) ? $cv_data['skills'] : '';

?>
<div class="jobs-module-header">
	<h2>CV / Resume</h2>
	<a href="<?php echo esc_url( add_query_arg( 'jobs_pdf_resume', '1', home_url() ) ); ?>" target="_blank" class="button button-secondary" style="float: right;">Download PDF</a>
</div>

<form id="jobs-cv-form" class="jobs-form">

	<h3>Education</h3>
	<div id="jobs-education-list">
		<?php
		if ( ! empty( $education ) ) {
			foreach ( $education as $index => $edu ) {
				echo '<div class="jobs-cv-item">';
				echo '<input type="text" name="education[' . $index . '][institution]" placeholder="Institution" value="' . esc_attr( isset($edu['institution']) ? $edu['institution'] : '' ) . '">';
				echo '<input type="text" name="education[' . $index . '][degree]" placeholder="Degree" value="' . esc_attr( isset($edu['degree']) ? $edu['degree'] : '' ) . '">';
				echo '<input type="text" name="education[' . $index . '][year]" placeholder="Year" value="' . esc_attr( isset($edu['year']) ? $edu['year'] : '' ) . '">';
				echo '</div>';
			}
		}
		?>
	</div>
	<button type="button" class="button" onclick="addEducationField()">+ Add Education</button>

	<h3>Experience</h3>
	<div id="jobs-experience-list">
		<?php
		if ( ! empty( $experience ) ) {
			foreach ( $experience as $index => $exp ) {
				echo '<div class="jobs-cv-item">';
				echo '<input type="text" name="experience[' . $index . '][company]" placeholder="Company" value="' . esc_attr( isset($exp['company']) ? $exp['company'] : '' ) . '">';
				echo '<input type="text" name="experience[' . $index . '][position]" placeholder="Position" value="' . esc_attr( isset($exp['position']) ? $exp['position'] : '' ) . '">';
				echo '<input type="text" name="experience[' . $index . '][duration]" placeholder="Duration" value="' . esc_attr( isset($exp['duration']) ? $exp['duration'] : '' ) . '">';
				echo '</div>';
			}
		}
		?>
	</div>
	<button type="button" class="button" onclick="addExperienceField()">+ Add Experience</button>

	<h3>Skills</h3>
	<div class="jobs-form-group">
		<textarea name="skills" rows="5" placeholder="List your skills..."><?php echo esc_textarea( $skills ); ?></textarea>
	</div>

	<div style="margin-top: 20px;">
		<button type="submit" class="jobs-submit-btn">Save CV</button>
		<div id="jobs-cv-message"></div>
	</div>
</form>

<script>
function addEducationField() {
	var index = document.querySelectorAll('#jobs-education-list .jobs-cv-item').length;
	var html = '<div class="jobs-cv-item" style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">' +
		'<input type="text" name="education[' + index + '][institution]" placeholder="Institution" style="display:block; width:100%; margin-bottom:5px;">' +
		'<input type="text" name="education[' + index + '][degree]" placeholder="Degree" style="display:block; width:100%; margin-bottom:5px;">' +
		'<input type="text" name="education[' + index + '][year]" placeholder="Year" style="display:block; width:100%; margin-bottom:5px;">' +
		'</div>';
	jQuery('#jobs-education-list').append(html);
}

function addExperienceField() {
	var index = document.querySelectorAll('#jobs-experience-list .jobs-cv-item').length;
	var html = '<div class="jobs-cv-item" style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">' +
		'<input type="text" name="experience[' + index + '][company]" placeholder="Company" style="display:block; width:100%; margin-bottom:5px;">' +
		'<input type="text" name="experience[' + index + '][position]" placeholder="Position" style="display:block; width:100%; margin-bottom:5px;">' +
		'<input type="text" name="experience[' + index + '][duration]" placeholder="Duration" style="display:block; width:100%; margin-bottom:5px;">' +
		'</div>';
	jQuery('#jobs-experience-list').append(html);
}

jQuery(document).ready(function($) {
	$('#jobs-cv-form').on('submit', function(e) {
		e.preventDefault();
		var formData = $(this).serialize();
		formData += '&action=jobs_save_cv_data&nonce=' + jobs_ajax.nonce;

		$('#jobs-cv-message').text('Saving...').css('color', '#333');

		$.post(jobs_ajax.ajax_url, formData, function(response) {
			if (response.success) {
				$('#jobs-cv-message').text(response.data).css('color', 'green');
			} else {
				$('#jobs-cv-message').text(response.data).css('color', 'red');
			}
		});
	});
});
</script>
