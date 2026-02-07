<?php
/**
 * Module: Advanced Settings (Full Page Redirect)
 */
// In a real scenario, we might redirect via JS or show a link if headers sent
if ( current_user_can( 'manage_options' ) ) {
    ?>
    <script>
        window.location.href = '<?php echo admin_url( 'admin.php?page=jobs-settings' ); ?>';
    </script>
    <div class="jobs-module-container">
        <p>Redirecting to Admin Panel...</p>
    </div>
    <?php
} else {
    echo 'Access Denied';
}
