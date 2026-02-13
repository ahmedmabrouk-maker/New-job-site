<?php
/**
 * Module: support.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to contact support.</p>';
	return;
}
?>

<div class="jobs-module-header">
	<h2>Support</h2>
</div>

<form id="jobs-support-form" class="jobs-form">
	<div class="jobs-form-group">
		<label for="support_subject">Subject</label>
		<input type="text" name="support_subject" id="support_subject" required>
	</div>

	<div class="jobs-form-group">
		<label for="support_message">Message</label>
		<textarea name="support_message" id="support_message" rows="5" required></textarea>
	</div>

	<button type="submit" class="jobs-submit-btn">Send Message</button>
	<div id="jobs-support-message"></div>
</form>

<script>
jQuery(document).ready(function($) {
	$('#jobs-support-form').on('submit', function(e) {
		e.preventDefault();

		var formData = {
			action: 'jobs_send_support_message',
			nonce: jobs_ajax.nonce,
			subject: $('#support_subject').val(),
			message: $('#support_message').val()
		};

		$('#jobs-support-message').text('Sending...').css('color', '#333');

		$.post(jobs_ajax.ajax_url, formData, function(response) {
			if (response.success) {
				$('#jobs-support-message').text(response.data).css('color', 'green');
				$('#jobs-support-form')[0].reset();
			} else {
				$('#jobs-support-message').text(response.data).css('color', 'red');
			}
		});
	});
});
</script>
