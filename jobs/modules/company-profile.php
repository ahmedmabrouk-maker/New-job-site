<?php
/**
 * Module: company-profile.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
if ( ! in_array( 'employer', (array) $user->roles ) ) {
	echo '<p>Access denied.</p>';
	return;
}

$company_data = get_user_meta( $user->ID, '_jobs_company_data', true );
if ( ! is_array( $company_data ) ) $company_data = array();

$name = isset( $company_data['name'] ) ? $company_data['name'] : '';
$employees = isset( $company_data['employee_count'] ) ? $company_data['employee_count'] : '';
$address = isset( $company_data['address'] ) ? $company_data['address'] : '';
$description = isset( $company_data['description'] ) ? $company_data['description'] : '';
$logo_url = isset( $company_data['logo_url'] ) ? $company_data['logo_url'] : '';

?>

<div class="jobs-module-header">
	<h2>Company Profile</h2>
</div>

<form id="jobs-company-form" class="jobs-form" enctype="multipart/form-data">

	<div class="jobs-form-group">
		<label for="company_name">Company Name</label>
		<input type="text" name="company_name" id="company_name" value="<?php echo esc_attr( $name ); ?>" required>
	</div>

	<div class="jobs-form-group">
		<label for="employee_count">Number of Employees</label>
		<input type="text" name="employee_count" id="employee_count" value="<?php echo esc_attr( $employees ); ?>">
	</div>

	<div class="jobs-form-group">
		<label for="company_address">Address</label>
		<input type="text" name="company_address" id="company_address" value="<?php echo esc_attr( $address ); ?>">
	</div>

	<div class="jobs-form-group">
		<label for="company_description">Description</label>
		<textarea name="company_description" id="company_description" rows="5"><?php echo esc_textarea( $description ); ?></textarea>
	</div>

	<div class="jobs-form-group">
		<label for="company_logo">Company Logo</label>
		<?php if ( $logo_url ) : ?>
			<img src="<?php echo esc_url( $logo_url ); ?>" alt="Company Logo" style="max-width: 100px; display: block; margin-bottom: 10px;">
		<?php endif; ?>
		<input type="file" name="company_logo" id="company_logo" accept="image/*">
	</div>

	<div class="jobs-form-actions">
		<button type="submit" class="jobs-submit-btn">Save Profile</button>
	</div>
	<div id="jobs-company-message"></div>
</form>

<script>
jQuery(document).ready(function($) {
	$('#jobs-company-form').on('submit', function(e) {
		e.preventDefault();
		$('#jobs-company-message').text('Saving...');

		var formData = new FormData(this);
		formData.append('action', 'jobs_save_company_data');
		formData.append('nonce', jobs_ajax.nonce);

		$.ajax({
			url: jobs_ajax.ajax_url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				$('#jobs-company-message').text(response.data);
				if(response.success) {
					// Optional: Reload to see new logo
				}
			}
		});
	});
});
</script>
