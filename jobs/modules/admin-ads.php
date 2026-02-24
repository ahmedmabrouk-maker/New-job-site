<?php
/**
 * Module: admin-ads.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}
$ads_code = get_option( 'jobs_ads_code', '' );
?>
<h2>External Ads & Google AdSense</h2>
<form id="jobs-ads-form">
	<p>
		<label>AdSense Code / External Ads Script</label><br>
		<textarea name="jobs_ads_code" rows="10" class="large-text code" style="width: 100%; max-width: 600px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;"><?php echo esc_textarea( $ads_code ); ?></textarea>
	</p>
	<button type="submit" class="jobs-submit-btn" style="width: auto;">Save Ads Settings</button>
	<span id="jobs-ads-message" style="margin-left: 10px;"></span>
</form>

<script>
jQuery('#jobs-ads-form').on('submit', function(e) {
	e.preventDefault();
	var formData = {
		action: 'jobs_save_ads_settings',
		nonce: jobs_ajax.nonce,
		jobs_ads_code: jQuery('textarea[name="jobs_ads_code"]').val()
	};
	jQuery('#jobs-ads-message').text('Saving...').css('color', '#666');
	jQuery.post(jobs_ajax.ajax_url, formData, function(response) {
		if (response.success) {
			jQuery('#jobs-ads-message').text('Settings saved.').css('color', 'green');
		} else {
			jQuery('#jobs-ads-message').text('Error saving settings.').css('color', 'red');
		}
	});
});
</script>
