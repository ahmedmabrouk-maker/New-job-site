<?php
/**
 * Module: settings.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
$hidden = get_user_meta( $user->ID, '_jobs_hide_public_profile', true );

?>

<div class="jobs-module-header">
	<h2>Settings</h2>
</div>

<!-- Public Profile Visibility -->
<div class="jobs-section">
	<h3>Public Profile Visibility</h3>
	<label>
		<input type="checkbox" id="jobs-toggle-profile" <?php checked( $hidden, 1 ); ?>> Hide my public profile
	</label>
</div>

<!-- Account Information -->
<div class="jobs-section">
	<h3>Account Information</h3>
	<form id="jobs-account-form" class="jobs-form">
		<div class="jobs-form-group">
			<label for="account_email">Email Address</label>
			<input type="email" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" required>
		</div>
		<div class="jobs-form-group">
			<label for="account_password">New Password (leave blank to keep current)</label>
			<input type="password" name="account_password" id="account_password" placeholder="New Password">
		</div>
		<button type="submit" class="jobs-submit-btn">Update Account</button>
		<div id="jobs-account-message"></div>
	</form>
</div>

<!-- Delete Account -->
<?php if ( ! in_array( 'administrator', (array) $user->roles ) ) : ?>
<div class="jobs-section" style="border-top: 1px solid #eee; padding-top: 20px; color: #dc3545;">
	<h3>Danger Zone</h3>
	<p>Once you delete your account, there is no going back. Please be certain.</p>
	<button id="jobs-delete-account-btn" class="button" style="background: #dc3545; color: #fff; border: none;">Delete Account</button>
</div>
<?php endif; ?>

<script>
jQuery(document).ready(function($) {
	// Toggle Profile Visibility
	$('#jobs-toggle-profile').on('change', function() {
		var hidden = $(this).is(':checked') ? 1 : 0;
		$.post(jobs_ajax.ajax_url, {
			action: 'jobs_toggle_public_profile',
			nonce: jobs_ajax.nonce,
			hidden: hidden
		}, function(response) {
			// Optional feedback
		});
	});

	// Update Account
	$('#jobs-account-form').on('submit', function(e) {
		e.preventDefault();
		$('#jobs-account-message').text('Updating...');

		$.post(jobs_ajax.ajax_url, {
			action: 'jobs_update_account_settings',
			nonce: jobs_ajax.nonce,
			email: $('#account_email').val(),
			password: $('#account_password').val()
		}, function(response) {
			$('#jobs-account-message').text(response.data);
		});
	});

	// Delete Account
	$('#jobs-delete-account-btn').on('click', function() {
		if(confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
			$.post(jobs_ajax.ajax_url, {
				action: 'jobs_delete_account',
				nonce: jobs_ajax.nonce
			}, function(response) {
				if(response.success) {
					window.location.href = '<?php echo home_url(); ?>';
				} else {
					alert(response.data);
				}
			});
		}
	});
});
</script>
