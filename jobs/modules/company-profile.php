<?php
/**
 * Module: company-profile.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to manage your company profile.</p>';
	return;
}

$current_user = wp_get_current_user();
if ( ! in_array( 'employer', (array) $current_user->roles ) && ! in_array( 'administrator', (array) $current_user->roles ) ) {
	echo '<p>You do not have permission to manage a company profile.</p>';
	return;
}

$user_id = get_current_user_id();
$company_data = get_user_meta( $user_id, '_jobs_company_data', true );
if ( ! is_array( $company_data ) ) {
	$company_data = array();
}

$company_name = isset( $company_data['name'] ) ? $company_data['name'] : '';
$company_address = isset( $company_data['address'] ) ? $company_data['address'] : '';
$employee_count = isset( $company_data['employee_count'] ) ? $company_data['employee_count'] : '';
$company_description = isset( $company_data['description'] ) ? $company_data['description'] : '';
$company_logo = isset( $company_data['logo_url'] ) ? $company_data['logo_url'] : '';
?>

<div class="jobs-module-header">
	<h2>Company Profile</h2>
</div>

<form id="jobs-company-form" class="jobs-form" enctype="multipart/form-data">

	<div class="jobs-form-group">
		<label for="company_name">Company Name</label>
		<input type="text" name="company_name" id="company_name" value="<?php echo esc_attr( $company_name ); ?>" required>
	</div>

	<div class="jobs-form-group">
		<label for="company_logo">Company Logo</label>
		<?php if ( $company_logo ) : ?>
			<div class="jobs-current-logo">
				<img src="<?php echo esc_url( $company_logo ); ?>" alt="Current Logo" style="max-width: 100px; max-height: 100px; display: block; margin-bottom: 10px;">
			</div>
		<?php endif; ?>
		<input type="file" name="company_logo" id="company_logo" accept="image/*">
	</div>

	<div class="jobs-form-group">
		<label for="employee_count">Employee Count</label>
		<input type="number" name="employee_count" id="employee_count" value="<?php echo esc_attr( $employee_count ); ?>">
	</div>

	<div class="jobs-form-group">
		<label for="company_address">Address</label>
		<input type="text" name="company_address" id="company_address" value="<?php echo esc_attr( $company_address ); ?>">
	</div>

	<div class="jobs-form-group">
		<label for="company_description">Company Details</label>
		<textarea name="company_description" id="company_description" rows="5"><?php echo esc_textarea( $company_description ); ?></textarea>
	</div>

	<button type="submit" class="jobs-submit-btn">Save Profile</button>
	<div id="jobs-company-message"></div>
</form>

<script>
jQuery(document).ready(function($) {
	$('#jobs-company-form').on('submit', function(e) {
		e.preventDefault();

		var formData = new FormData(this);
		formData.append('action', 'jobs_save_company_data');
		formData.append('nonce', jobs_ajax.nonce);

		$('#jobs-company-message').text('Saving...').css('color', '#333');

		$.ajax({
			url: jobs_ajax.ajax_url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				if (response.success) {
					$('#jobs-company-message').text(response.data).css('color', 'green');
					// Ideally reload the module or update the logo preview, but for now just show message
				} else {
					$('#jobs-company-message').text(response.data).css('color', 'red');
				}
			},
			error: function() {
				$('#jobs-company-message').text('Error saving profile.').css('color', 'red');
			}
		});
	});
});
</script>
