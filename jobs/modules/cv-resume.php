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
$skills = isset( $cv_data['skills'] ) ? $cv_data['skills'] : '';
$courses = isset( $cv_data['courses'] ) ? $cv_data['courses'] : array();
$certifications = isset( $cv_data['certifications'] ) ? $cv_data['certifications'] : array();
?>

<div class="jobs-module-header">
	<h2>CV / Resume</h2>
	<a href="<?php echo esc_url( add_query_arg( 'jobs_pdf_resume', '1', home_url() ) ); ?>" target="_blank" class="button button-primary">Download PDF Resume</a>
</div>

<form id="jobs-cv-form" class="jobs-form">

	<!-- Education Section -->
	<div class="jobs-section">
		<div class="jobs-section-header" onclick="toggleSection(this)">
			<h3>Education</h3>
			<span class="dashicons dashicons-arrow-down-alt2"></span>
		</div>
		<div class="jobs-section-content">
			<button type="button" class="button button-small" onclick="addEducationField()">+ Add Education</button>
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
	</div>

	<!-- Experience Section -->
	<div class="jobs-section">
		<div class="jobs-section-header" onclick="toggleSection(this)">
			<h3>Experience</h3>
			<span class="dashicons dashicons-arrow-down-alt2"></span>
		</div>
		<div class="jobs-section-content">
			<button type="button" class="button button-small" onclick="addExperienceField()">+ Add Experience</button>
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
	</div>

	<!-- Courses Section -->
	<div class="jobs-section">
		<div class="jobs-section-header" onclick="toggleSection(this)">
			<h3>Courses</h3>
			<span class="dashicons dashicons-arrow-down-alt2"></span>
		</div>
		<div class="jobs-section-content">
			<button type="button" class="button button-small" onclick="addCourseField()">+ Add Course</button>
			<div id="jobs-course-fields">
				<?php if ( ! empty( $courses ) ) : ?>
					<?php foreach ( $courses as $index => $course ) : ?>
						<div class="jobs-repeater-item">
							<input type="text" name="courses[<?php echo $index; ?>][name]" placeholder="Course Name" value="<?php echo esc_attr( $course['name'] ); ?>">
							<input type="text" name="courses[<?php echo $index; ?>][provider]" placeholder="Provider" value="<?php echo esc_attr( $course['provider'] ); ?>">
							<input type="text" name="courses[<?php echo $index; ?>][year]" placeholder="Year" value="<?php echo esc_attr( $course['year'] ); ?>">
							<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Certifications Section -->
	<div class="jobs-section">
		<div class="jobs-section-header" onclick="toggleSection(this)">
			<h3>Certifications</h3>
			<span class="dashicons dashicons-arrow-down-alt2"></span>
		</div>
		<div class="jobs-section-content">
			<button type="button" class="button button-small" onclick="addCertificationField()">+ Add Certification</button>
			<div id="jobs-certification-fields">
				<?php if ( ! empty( $certifications ) ) : ?>
					<?php foreach ( $certifications as $index => $cert ) : ?>
						<div class="jobs-repeater-item">
							<input type="text" name="certifications[<?php echo $index; ?>][name]" placeholder="Certification Name" value="<?php echo esc_attr( $cert['name'] ); ?>">
							<input type="text" name="certifications[<?php echo $index; ?>][issuer]" placeholder="Issuer" value="<?php echo esc_attr( $cert['issuer'] ); ?>">
							<input type="text" name="certifications[<?php echo $index; ?>][year]" placeholder="Year" value="<?php echo esc_attr( $cert['year'] ); ?>">
							<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Skills Section -->
	<div class="jobs-section">
		<div class="jobs-section-header" onclick="toggleSection(this)">
			<h3>Skills</h3>
			<span class="dashicons dashicons-arrow-down-alt2"></span>
		</div>
		<div class="jobs-section-content">
			<textarea name="skills" placeholder="List your skills, separated by commas..."><?php echo esc_textarea( $skills ); ?></textarea>
		</div>
	</div>

	<button type="submit" class="jobs-submit-btn">Save CV</button>
	<div id="jobs-cv-message"></div>
</form>

<script>
var eduCount = <?php echo count( $education ); ?>;
var expCount = <?php echo count( $experience ); ?>;
var courseCount = <?php echo count( $courses ); ?>;
var certCount = <?php echo count( $certifications ); ?>;

function toggleSection(header) {
	var content = header.nextElementSibling;
	if (content.style.display === "none") {
		content.style.display = "block";
		header.querySelector('.dashicons').classList.remove('dashicons-arrow-right');
		header.querySelector('.dashicons').classList.add('dashicons-arrow-down-alt2');
	} else {
		content.style.display = "none";
		header.querySelector('.dashicons').classList.remove('dashicons-arrow-down-alt2');
		header.querySelector('.dashicons').classList.add('dashicons-arrow-right');
	}
}

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
		'<input type="text" name="courses[' + courseCount + '][provider]" placeholder="Provider">' +
		'<input type="text" name="courses[' + courseCount + '][year]" placeholder="Year">' +
		'<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>' +
		'</div>';
	jQuery('#jobs-course-fields').append(html);
	courseCount++;
}

function addCertificationField() {
	var html = '<div class="jobs-repeater-item">' +
		'<input type="text" name="certifications[' + certCount + '][name]" placeholder="Certification Name">' +
		'<input type="text" name="certifications[' + certCount + '][issuer]" placeholder="Issuer">' +
		'<input type="text" name="certifications[' + certCount + '][year]" placeholder="Year">' +
		'<button type="button" class="button button-small remove-row" onclick="this.parentElement.remove()">Remove</button>' +
		'</div>';
	jQuery('#jobs-certification-fields').append(html);
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
.jobs-section { margin-bottom: 20px; border: 1px solid #eee; border-radius: 8px; overflow: hidden; }
.jobs-section-header { background: #f9f9f9; padding: 10px 20px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; }
.jobs-section-header h3 { margin: 0; font-size: 16px; }
.jobs-section-content { padding: 20px; display: block; }
.jobs-repeater-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
.jobs-repeater-item input { flex: 1; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
</style>
