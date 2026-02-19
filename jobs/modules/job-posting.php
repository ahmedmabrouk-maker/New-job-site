<?php
/**
 * Module: job-posting.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to post a job.</p>';
	return;
}

$current_user = wp_get_current_user();
if ( ! in_array( 'employer', (array) $current_user->roles ) && ! in_array( 'administrator', (array) $current_user->roles ) && ! in_array( 'reviewer', (array) $current_user->roles ) ) {
	echo '<p>You do not have permission to post jobs.</p>';
	return;
}

$job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;
$title = '';
$description = '';
$specialization_id = 0;
$country_id = 0;
$city_id = 0;
$is_edit = false;

if ( $job_id ) {
	$post = get_post( $job_id );
	if ( $post && ( $post->post_author == $current_user->ID || current_user_can( 'administrator' ) ) ) {
		$title = $post->post_title;
		$description = $post->post_content;
		$is_edit = true;

		$specs = get_the_terms( $job_id, 'job_specialization' );
		if ( $specs && ! is_wp_error( $specs ) && ! empty( $specs ) ) $specialization_id = $specs[0]->term_id;

		$countries = get_the_terms( $job_id, 'job_country' );
		if ( $countries && ! is_wp_error( $countries ) && ! empty( $countries ) ) $country_id = $countries[0]->term_id;

		$cities = get_the_terms( $job_id, 'job_city' );
		if ( $cities && ! is_wp_error( $cities ) && ! empty( $cities ) ) $city_id = $cities[0]->term_id;
	} else {
		$job_id = 0; // Invalid job or permission
	}
}
?>

<div class="jobs-module-header">
	<h2><?php echo $is_edit ? 'Edit Job' : 'Post a New Job'; ?></h2>
</div>

<form id="jobs-posting-form" class="jobs-form">
	<?php if ( $is_edit ) : ?>
		<input type="hidden" name="job_id" id="job_id" value="<?php echo esc_attr( $job_id ); ?>">
	<?php endif; ?>

	<div class="jobs-form-group">
		<label for="job_title">Job Title</label>
		<input type="text" name="job_title" id="job_title" value="<?php echo esc_attr( $title ); ?>" required>
	</div>

	<div class="jobs-form-group">
		<label for="job_description">Job Description</label>
		<textarea name="job_description" id="job_description" rows="5" required><?php echo esc_textarea( $description ); ?></textarea>
	</div>

	<div class="jobs-form-group">
		<label for="job_specialization">Specialization</label>
		<select name="job_specialization" id="job_specialization" required>
			<option value="">Select Specialization</option>
			<?php
			$terms = get_terms( array( 'taxonomy' => 'job_specialization', 'hide_empty' => false ) );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$selected = ( $term->term_id == $specialization_id ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $term->term_id ) . '" ' . $selected . '>' . esc_html( $term->name ) . '</option>';
				}
			}
			?>
		</select>
	</div>

	<div class="jobs-form-group">
		<label for="job_country">Country</label>
		<select name="job_country" id="job_country" required>
			<option value="">Select Country</option>
			<?php
			$terms = get_terms( array( 'taxonomy' => 'job_country', 'hide_empty' => false ) );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$selected = ( $term->term_id == $country_id ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $term->term_id ) . '" ' . $selected . '>' . esc_html( $term->name ) . '</option>';
				}
			}
			?>
		</select>
	</div>

	<div class="jobs-form-group">
		<label for="job_city">City</label>
		<select name="job_city" id="job_city" required>
			<option value="">Select City</option>
			<?php
			$terms = get_terms( array( 'taxonomy' => 'job_city', 'hide_empty' => false ) );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					$selected = ( $term->term_id == $city_id ) ? 'selected' : '';
					echo '<option value="' . esc_attr( $term->term_id ) . '" ' . $selected . '>' . esc_html( $term->name ) . '</option>';
				}
			}
			?>
		</select>
	</div>

	<button type="submit" class="jobs-submit-btn"><?php echo $is_edit ? 'Update Job' : 'Submit Job'; ?></button>
	<div id="jobs-posting-message"></div>
</form>

<script>
jQuery(document).ready(function($) {
	$('#jobs-posting-form').on('submit', function(e) {
		e.preventDefault();

		var formData = {
			action: 'jobs_handle_job_posting',
			nonce: jobs_ajax.nonce,
			job_title: $('#job_title').val(),
			job_description: $('#job_description').val(),
			job_specialization: $('#job_specialization').val(),
			job_country: $('#job_country').val(),
			job_city: $('#job_city').val()
		};

		if ($('#job_id').length) {
			formData.job_id = $('#job_id').val();
		}

		$('#jobs-posting-message').text('Submitting...').css('color', '#333');

		$.post(jobs_ajax.ajax_url, formData, function(response) {
			if (response.success) {
				$('#jobs-posting-message').text(response.data).css('color', 'green');
				if (!$('#job_id').length) {
					$('#jobs-posting-form')[0].reset();
				}
			} else {
				$('#jobs-posting-message').text(response.data).css('color', 'red');
			}
		});
	});
});
</script>
