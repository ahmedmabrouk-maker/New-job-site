<?php
/**
 * Module: advanced-settings.php
 */

if ( ! is_user_logged_in() || ! current_user_can( 'administrator' ) ) {
	echo '<p>Access denied.</p>';
	return;
}

$page_ids = get_option( 'jobs_page_ids', array() );
$admin_page_id = isset( $page_ids['jobs_admin_panel'] ) ? $page_ids['jobs_admin_panel'] : 0;
$admin_url = $admin_page_id ? get_permalink( $admin_page_id ) : home_url();

?>
<div class="jobs-module-header">
	<h2>Advanced Settings</h2>
</div>
<p>Redirecting to Admin Control Panel...</p>

<script>
window.location.href = '<?php echo esc_url( $admin_url ); ?>';
</script>
