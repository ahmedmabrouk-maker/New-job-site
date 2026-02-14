<?php
/**
 * Module: advanced-settings.php
 */
$page_ids = get_option( 'jobs_page_ids', array() );
$admin_url = isset( $page_ids['admin_panel'] ) ? get_permalink( $page_ids['admin_panel'] ) : home_url();

if ( ! $admin_url ) {
	$admin_url = home_url();
}
?>
<div class="jobs-module-header">
	<h2>Advanced Settings</h2>
</div>
<p>Redirecting to Admin Control Panel...</p>
<script>
	window.location.href = '<?php echo esc_url( $admin_url ); ?>';
</script>
