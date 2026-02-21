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
	$cv_data = array();
}

$education = isset( $cv_data['education'] ) ? $cv_data['education'] : array();
$experience = isset( $cv_data['experience'] ) ? $cv_data['experience'] : array();
$courses = isset( $cv_data['courses'] ) ? $cv_data['courses'] : array();
$certifications = isset( $cv_data['certifications'] ) ? $cv_data['certifications'] : array();
$skills = isset( $cv_data['skills'] ) ? $cv_data['skills'] : '';

$visibility = isset( $cv_data['visibility'] ) ? $cv_data['visibility'] : array(
	'education' => 1, 'experience' => 1, 'courses' => 1, 'certifications' => 1, 'skills' => 1
);
?>

<div class="jobs-module-header">
	<h2>CV / Resume</h2>
	<a href="<?php echo esc_url( add_query_arg( 'jobs_pdf_resume', '1', home_url() ) ); ?>" target="_blank" class="button button-primary">Download PDF</a>
</div>

<form id="jobs-cv-form" class="jobs-form">

	<!-- Education Section -->
	<div class="jobs-section">
		<div class="jobs-section-header">
			<h3>Education</h3>
			<label><input type="checkbox" name="visibility[education]" value="1" <?php checked( isset($visibility['education']) && $visibility['education'] ); ?>> Show in Profile</label>
			<button type="button" class="button button-small" onclick="addEducationField()">+ Add</button>
		</div>
		<div id="jobs-education-fields">
			<?php if ( ! empty( $education ) ) : ?>
				<?php foreach ( $education as $index => $edu ) : ?>
					<div class="jobs-repeater-item">
						<input type="text" name="education[<?php echo $index; ?>][school]" placeholder="School/University" value="<?php echo esc_attr( $edu['school'] ); ?>">
						<input type="text" name="education[<?php echo $index; ?>][degree]" placeholder="Degree" value="<?php echo esc_attr( $edu['degree'] ); ?>">
						<input type="text" name="education[<?php echo $index; ?>][year]" placeholder="Year" value="<?php echo esc_attr( $edu['year'] ); ?>">
						<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<!-- Experience Section -->
	<div class="jobs-section">
		<div class="jobs-section-header">
			<h3>Experience</h3>
			<label><input type="checkbox" name="visibility[experience]" value="1" <?php checked( isset($visibility['experience']) && $visibility['experience'] ); ?>> Show in Profile</label>
			<button type="button" class="button button-small" onclick="addExperienceField()">+ Add</button>
		</div>
		<div id="jobs-experience-fields">
			<?php if ( ! empty( $experience ) ) : ?>
				<?php foreach ( $experience as $index => $exp ) : ?>
					<div class="jobs-repeater-item">
						<input type="text" name="experience[<?php echo $index; ?>][company]" placeholder="Company" value="<?php echo esc_attr( $exp['company'] ); ?>">
						<input type="text" name="experience[<?php echo $index; ?>][position]" placeholder="Position" value="<?php echo esc_attr( $exp['position'] ); ?>">
						<input type="text" name="experience[<?php echo $index; ?>][years]" placeholder="Years (e.g. 2020-2022)" value="<?php echo esc_attr( $exp['years'] ); ?>">
						<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<!-- Courses Section -->
	<div class="jobs-section">
		<div class="jobs-section-header">
			<h3>Courses</h3>
			<label><input type="checkbox" name="visibility[courses]" value="1" <?php checked( isset($visibility['courses']) && $visibility['courses'] ); ?>> Show in Profile</label>
			<button type="button" class="button button-small" onclick="addCourseField()">+ Add</button>
		</div>
		<div id="jobs-courses-fields">
			<?php if ( ! empty( $courses ) ) : ?>
				<?php foreach ( $courses as $index => $course ) : ?>
					<div class="jobs-repeater-item">
						<input type="text" name="courses[<?php echo $index; ?>][name]" placeholder="Course Name" value="<?php echo esc_attr( $course['name'] ); ?>">
						<input type="text" name="courses[<?php echo $index; ?>][year]" placeholder="Year" value="<?php echo esc_attr( $course['year'] ); ?>">
						<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<!-- Certifications Section -->
	<div class="jobs-section">
		<div class="jobs-section-header">
			<h3>Certifications</h3>
			<label><input type="checkbox" name="visibility[certifications]" value="1" <?php checked( isset($visibility['certifications']) && $visibility['certifications'] ); ?>> Show in Profile</label>
			<button type="button" class="button button-small" onclick="addCertificationField()">+ Add</button>
		</div>
		<div id="jobs-certifications-fields">
			<?php if ( ! empty( $certifications ) ) : ?>
				<?php foreach ( $certifications as $index => $cert ) : ?>
					<div class="jobs-repeater-item">
						<input type="text" name="certifications[<?php echo $index; ?>][name]" placeholder="Certification Name" value="<?php echo esc_attr( $cert['name'] ); ?>">
						<input type="text" name="certifications[<?php echo $index; ?>][year]" placeholder="Year" value="<?php echo esc_attr( $cert['year'] ); ?>">
						<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<!-- Skills Section -->
	<div class="jobs-section">
		<div class="jobs-section-header">
			<h3>Skills</h3>
			<label><input type="checkbox" name="visibility[skills]" value="1" <?php checked( isset($visibility['skills']) && $visibility['skills'] ); ?>> Show in Profile</label>
		</div>
		<textarea name="skills" placeholder="List your skills, separated by commas..."><?php echo esc_textarea( $skills ); ?></textarea>
	</div>

	<button type="submit" class="jobs-submit-btn">Save CV</button>
	<div id="jobs-cv-message"></div>
</form>

<script>
var eduCount = <?php echo count( $education ); ?>;
var expCount = <?php echo count( $experience ); ?>;
var courseCount = <?php echo count( $courses ); ?>;
var certCount = <?php echo count( $certifications ); ?>;

function addEducationField() {
	var html = '<div class="jobs-repeater-item">' +
		'<input type="text" name="education[' + eduCount + '][school]" placeholder="School/University">' +
		'<input type="text" name="education[' + eduCount + '][degree]" placeholder="Degree">' +
		'<input type="text" name="education[' + eduCount + '][year]" placeholder="Year">' +
		'<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>' +
		'</div>';
	jQuery('#jobs-education-fields').append(html);
	eduCount++;
}

function addExperienceField() {
	var html = '<div class="jobs-repeater-item">' +
		'<input type="text" name="experience[' + expCount + '][company]" placeholder="Company">' +
		'<input type="text" name="experience[' + expCount + '][position]" placeholder="Position">' +
		'<input type="text" name="experience[' + expCount + '][years]" placeholder="Years">' +
		'<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>' +
		'</div>';
	jQuery('#jobs-experience-fields').append(html);
	expCount++;
}

function addCourseField() {
	var html = '<div class="jobs-repeater-item">' +
		'<input type="text" name="courses[' + courseCount + '][name]" placeholder="Course Name">' +
		'<input type="text" name="courses[' + courseCount + '][year]" placeholder="Year">' +
		'<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>' +
		'</div>';
	jQuery('#jobs-courses-fields').append(html);
	courseCount++;
}

function addCertificationField() {
	var html = '<div class="jobs-repeater-item">' +
		'<input type="text" name="certifications[' + certCount + '][name]" placeholder="Certification Name">' +
		'<input type="text" name="certifications[' + certCount + '][year]" placeholder="Year">' +
		'<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>' +
		'</div>';
	jQuery('#jobs-certifications-fields').append(html);
	certCount++;
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

<style>
.jobs-section { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
.jobs-section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.jobs-section-header h3 { margin: 0; }
.jobs-repeater-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
.jobs-repeater-item input { flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
</style>
