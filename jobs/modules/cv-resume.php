<?php
/**
 * Module: cv-resume.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
if ( ! in_array( 'job_seeker', (array) $user->roles ) ) {
	echo '<p>Access denied.</p>';
	return;
}

$cv_data = get_user_meta( $user->ID, '_jobs_cv_data', true );
$education = isset( $cv_data['education'] ) ? $cv_data['education'] : array();
$experience = isset( $cv_data['experience'] ) ? $cv_data['experience'] : array();
$skills = isset( $cv_data['skills'] ) ? $cv_data['skills'] : '';

?>

<div class="jobs-module-header">
	<h2>CV / Resume</h2>
</div>

<form id="jobs-cv-form" class="jobs-form">

	<!-- Education Section -->
	<div class="jobs-section">
		<h3>Education <button type="button" class="button button-small" onclick="jobsAddEducation()">Add</button></h3>
		<div id="jobs-education-container">
			<?php if ( ! empty( $education ) ) : ?>
				<?php foreach ( $education as $index => $edu ) : ?>
					<div class="jobs-repeater-item">
						<input type="text" name="education[<?php echo $index; ?>][institution]" placeholder="Institution" value="<?php echo esc_attr( $edu['institution'] ); ?>">
						<input type="text" name="education[<?php echo $index; ?>][degree]" placeholder="Degree" value="<?php echo esc_attr( $edu['degree'] ); ?>">
						<input type="text" name="education[<?php echo $index; ?>][year]" placeholder="Year" value="<?php echo esc_attr( $edu['year'] ); ?>">
						<button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<!-- Experience Section -->
	<div class="jobs-section">
		<h3>Experience <button type="button" class="button button-small" onclick="jobsAddExperience()">Add</button></h3>
		<div id="jobs-experience-container">
			<?php if ( ! empty( $experience ) ) : ?>
				<?php foreach ( $experience as $index => $exp ) : ?>
					<div class="jobs-repeater-item">
						<input type="text" name="experience[<?php echo $index; ?>][company]" placeholder="Company" value="<?php echo esc_attr( $exp['company'] ); ?>">
						<input type="text" name="experience[<?php echo $index; ?>][position]" placeholder="Position" value="<?php echo esc_attr( $exp['position'] ); ?>">
						<input type="text" name="experience[<?php echo $index; ?>][years]" placeholder="Years" value="<?php echo esc_attr( $exp['years'] ); ?>">
						<button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>

	<!-- Skills Section -->
	<div class="jobs-form-group">
		<label for="jobs-skills">Skills (comma separated)</label>
		<textarea name="skills" id="jobs-skills" rows="3"><?php echo esc_textarea( $skills ); ?></textarea>
	</div>

	<div class="jobs-form-actions">
		<button type="submit" class="jobs-submit-btn">Save CV</button>
		<a href="<?php echo esc_url( add_query_arg( 'jobs_pdf_resume', '1', home_url() ) ); ?>" target="_blank" class="button button-secondary">Download PDF</a>
	</div>
	<div id="jobs-cv-message"></div>
</form>

<script>
var eduCount = <?php echo count( $education ); ?>;
var expCount = <?php echo count( $experience ); ?>;

function jobsAddEducation() {
	var html = '<div class="jobs-repeater-item">' +
		'<input type="text" name="education[' + eduCount + '][institution]" placeholder="Institution">' +
		'<input type="text" name="education[' + eduCount + '][degree]" placeholder="Degree">' +
		'<input type="text" name="education[' + eduCount + '][year]" placeholder="Year">' +
		'<button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>' +
		'</div>';
	jQuery('#jobs-education-container').append(html);
	eduCount++;
}

function jobsAddExperience() {
	var html = '<div class="jobs-repeater-item">' +
		'<input type="text" name="experience[' + expCount + '][company]" placeholder="Company">' +
		'<input type="text" name="experience[' + expCount + '][position]" placeholder="Position">' +
		'<input type="text" name="experience[' + expCount + '][years]" placeholder="Years">' +
		'<button type="button" class="button" onclick="this.parentNode.remove()">Remove</button>' +
		'</div>';
	jQuery('#jobs-experience-container').append(html);
	expCount++;
}

jQuery(document).ready(function($) {
	$('#jobs-cv-form').on('submit', function(e) {
		e.preventDefault();
		$('#jobs-cv-message').text('Saving...');

		var formData = $(this).serialize();
		formData += '&action=jobs_save_cv_data&nonce=' + jobs_ajax.nonce;

		$.post(jobs_ajax.ajax_url, formData, function(response) {
			$('#jobs-cv-message').text(response.data);
		});
	});
});
</script>

<style>
.jobs-repeater-item {
	display: flex;
	gap: 10px;
	margin-bottom: 10px;
}
.jobs-section {
	margin-bottom: 30px;
	border-bottom: 1px solid #eee;
	padding-bottom: 20px;
}
</style>
