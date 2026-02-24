<?php
/**
 * Module: admin-design.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}
$logo_url = get_option( 'jobs_logo_url', '' );
$primary_color = get_option( 'jobs_primary_color', '#1d3469' );
?>
<h2>Design Customization</h2>
<form id="jobs-design-form">
	<p>
		<label>Site Logo URL</label><br>
		<input type="text" name="jobs_logo_url" value="<?php echo esc_attr( $logo_url ); ?>" class="regular-text" style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
	</p>
	<p>
		<label>Primary Color</label><br>
		<input type="text" name="jobs_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" class="regular-text" style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
	</p>
	<button type="submit" class="jobs-submit-btn" style="width: auto;">Save Changes</button>
	<span id="jobs-design-message" style="margin-left: 10px;"></span>
</form>

<script>
jQuery('#jobs-design-form').on('submit', function(e) {
	e.preventDefault();
	var formData = {
		action: 'jobs_save_design_settings',
		nonce: jobs_ajax.nonce,
		jobs_logo_url: jQuery('input[name="jobs_logo_url"]').val(),
		jobs_primary_color: jQuery('input[name="jobs_primary_color"]').val()
	};
	jQuery('#jobs-design-message').text('Saving...').css('color', '#666');
	jQuery.post(jobs_ajax.ajax_url, formData, function(response) {
		if (response.success) {
			jQuery('#jobs-design-message').text('Settings saved.').css('color', 'green');
		} else {
			jQuery('#jobs-design-message').text('Error saving settings.').css('color', 'red');
		}
	});
});
</script>
