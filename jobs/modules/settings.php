<?php
/**
 * Module: settings.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
$is_admin = in_array( 'administrator', (array) $user->roles );
$last_update = get_user_meta( $user->ID, '_jobs_last_account_update', true );
$can_update = true;

if ( ! $is_admin && $last_update ) {
	$days_since = ( time() - $last_update ) / ( 60 * 60 * 24 );
	if ( $days_since < 30 ) {
		$can_update = false;
		$days_remaining = ceil( 30 - $days_since );
	}
}

$hide_profile = get_user_meta( $user->ID, '_jobs_hide_public_profile', true );

?>
<div class="jobs-module-header">
	<h2>Settings</h2>
</div>

<div class="jobs-settings-section">
	<h3>Public Profile Visibility</h3>
	<label class="switch">
		<input type="checkbox" id="jobs-hide-profile" <?php echo $hide_profile ? 'checked' : ''; ?> onchange="togglePublicProfile(this)">
		<span class="slider round"></span> Hide my public profile
	</label>
</div>

<hr>

<div class="jobs-settings-section">
	<h3>Account Details</h3>
	<?php if ( $can_update ) : ?>
		<form id="jobs-account-form" class="jobs-form">
			<div class="jobs-form-group">
				<label for="email">Email Address</label>
				<input type="email" name="email" value="<?php echo esc_attr( $user->user_email ); ?>" required>
			</div>
			<div class="jobs-form-group">
				<label for="password">New Password (leave blank to keep current)</label>
				<input type="password" name="password" placeholder="New Password">
			</div>
			<!-- Username cannot be changed easily in WP, usually readonly -->
			<div class="jobs-form-group">
				<label>Username</label>
				<input type="text" value="<?php echo esc_attr( $user->user_login ); ?>" disabled>
				<p class="description">Username cannot be changed.</p>
			</div>

			<button type="submit" class="jobs-submit-btn">Update Account</button>
			<div id="jobs-account-message"></div>
		</form>
	<?php else : ?>
		<p class="notice notice-warning">You can only update your account details once every 30 days. Please wait <?php echo $days_remaining; ?> more days.</p>
	<?php endif; ?>
</div>

<hr>

<div class="jobs-settings-section">
	<h3>Personal Activity Log</h3>
	<p>Recent activity associated with your account.</p>
	<?php
	// List recent posts (jobs/applications) by user
	$activities = get_posts( array(
		'author' => $user->ID,
		'post_type' => array( 'job', 'job_application', 'job_notification' ),
		'posts_per_page' => 5,
		'post_status' => 'any'
	) );
	if ( $activities ) {
		echo '<ul>';
		foreach ( $activities as $activity ) {
			echo '<li>' . ucfirst( str_replace( 'job_', '', $activity->post_type ) ) . ': ' . esc_html( $activity->post_title ) . ' (' . get_the_date( '', $activity->ID ) . ')</li>';
		}
		echo '</ul>';
	} else {
		echo '<p>No recent activity.</p>';
	}
	?>
</div>

<hr>

<div class="jobs-settings-section">
	<h3>Danger Zone</h3>
	<button class="button button-link-delete" style="color: red; border-color: red;" onclick="deleteAccount()">Delete Account</button>
</div>

<script>
function togglePublicProfile(checkbox) {
	var hidden = checkbox.checked ? 1 : 0;
	jQuery.post(jobs_ajax.ajax_url, {
		action: 'jobs_toggle_public_profile',
		hidden: hidden,
		nonce: jobs_ajax.nonce
	});
}

function deleteAccount() {
	if (confirm('Are you sure you want to permanently delete your account? This action cannot be undone.')) {
		jQuery.post(jobs_ajax.ajax_url, {
			action: 'jobs_delete_account',
			nonce: jobs_ajax.nonce
		}, function(response) {
			if (response.success) {
				alert(response.data);
				window.location.href = '<?php echo home_url(); ?>';
			} else {
				alert(response.data);
			}
		});
	}
}

jQuery(document).ready(function($) {
	$('#jobs-account-form').on('submit', function(e) {
		e.preventDefault();
		var formData = $(this).serialize();
		formData += '&action=jobs_update_account_settings&nonce=' + jobs_ajax.nonce;

		$('#jobs-account-message').text('Updating...').css('color', '#333');

		$.post(jobs_ajax.ajax_url, formData, function(response) {
			if (response.success) {
				$('#jobs-account-message').text(response.data).css('color', 'green');
				// Reload to update "last update" check if implemented in backend to block immediately
			} else {
				$('#jobs-account-message').text(response.data).css('color', 'red');
			}
		});
	});
});
</script>
