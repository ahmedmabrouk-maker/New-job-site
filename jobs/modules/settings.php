<?php
/**
 * Module: settings.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to access settings.</p>';
	return;
}

$user = wp_get_current_user();
$public_profile_hidden = get_user_meta( $user->ID, '_jobs_hide_public_profile', true );
?>

<div class="jobs-module-header">
	<h2>Settings</h2>
</div>

<!-- Privacy Settings -->
<div class="jobs-section">
	<h3>Privacy</h3>
	<label>
		<input type="checkbox" id="hide_public_profile" <?php checked( $public_profile_hidden, '1' ); ?> onchange="togglePublicProfile()">
		Hide Public Profile
	</label>
</div>

<!-- Account Settings -->
<div class="jobs-section">
	<h3>Change Account Details</h3>
	<p class="description">You can change your details once per month.</p>

	<?php
	$can_update = true;
	$next_update_msg = '';
	if ( ! in_array( 'administrator', (array) $user->roles ) ) {
		$last_update = get_user_meta( $user->ID, '_jobs_last_account_update', true );
		if ( $last_update ) {
			$days_since = ( current_time( 'timestamp' ) - $last_update ) / ( 60 * 60 * 24 );
			if ( $days_since < 30 ) {
				$can_update = false;
				$next_update = date_i18n( get_option( 'date_format' ), $last_update + ( 30 * 24 * 60 * 60 ) );
				$next_update_msg = 'You can update your account details again on ' . $next_update;
			}
		}
	}
	?>

	<?php if ( ! $can_update ) : ?>
		<p style="color: #f00;"><?php echo esc_html( $next_update_msg ); ?></p>
	<?php endif; ?>

	<form id="jobs-account-form" class="jobs-form">
		<div class="jobs-form-group">
			<label for="new_email">New Email</label>
			<input type="email" name="new_email" id="new_email" value="<?php echo esc_attr( $user->user_email ); ?>" <?php echo $can_update ? '' : 'readonly disabled'; ?>>
		</div>

		<div class="jobs-form-group">
			<label for="new_password">New Password (leave blank to keep current)</label>
			<input type="password" name="new_password" id="new_password" <?php echo $can_update ? '' : 'readonly disabled'; ?>>
		</div>

		<?php if ( $can_update ) : ?>
		<button type="submit" class="jobs-submit-btn">Update Details</button>
		<?php else : ?>
		<button type="button" class="jobs-submit-btn" disabled style="opacity: 0.5; cursor: not-allowed;">Update Details</button>
		<?php endif; ?>
		<div id="jobs-account-message"></div>
	</form>
</div>

<!-- Danger Zone -->
<div class="jobs-section" style="border-color: #f00;">
	<h3 style="color: #f00;">Danger Zone</h3>
	<button class="button button-secondary" style="color: #f00; border-color: #f00;" onclick="deleteAccount()">Delete Account</button>
</div>

<script>
function togglePublicProfile() {
	var isChecked = document.getElementById('hide_public_profile').checked;
	jQuery.post(jobs_ajax.ajax_url, {
		action: 'jobs_toggle_public_profile',
		nonce: jobs_ajax.nonce,
		hidden: isChecked ? 1 : 0
	});
}

function deleteAccount() {
	if (confirm('Are you sure you want to PERMANENTLY delete your account? This action cannot be undone.')) {
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_delete_account',
			nonce: jobs_ajax.nonce
		}, function(response) {
			if (response.success) {
				alert(response.data);
				window.location.reload(); // Should redirect to home or login
			} else {
				alert(response.data);
			}
		});
	}
}

jQuery(document).ready(function($) {
	$('#jobs-account-form').on('submit', function(e) {
		e.preventDefault();

		var formData = {
			action: 'jobs_update_account_settings',
			nonce: jobs_ajax.nonce,
			email: $('#new_email').val(),
			password: $('#new_password').val()
		};

		$('#jobs-account-message').text('Updating...').css('color', '#333');

		$.post(jobs_ajax.ajax_url, formData, function(response) {
			if (response.success) {
				$('#jobs-account-message').text(response.data).css('color', 'green');
			} else {
				$('#jobs-account-message').text(response.data).css('color', 'red');
			}
		});
	});
});
</script>

<style>
.jobs-section { margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
</style>
