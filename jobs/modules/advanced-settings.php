<?php
/**
 * Module: advanced-settings.php
 */
$page_ids = get_option( 'jobs_page_ids', array() );
$admin_panel_url = isset( $page_ids['admin_panel'] ) ? get_permalink( $page_ids['admin_panel'] ) : home_url();
?>
<script>
window.location.href = "<?php echo esc_url( $admin_panel_url ); ?>";
</script>
<p>Redirecting to Admin Control Panel...</p>
