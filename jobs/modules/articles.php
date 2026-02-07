<?php
/**
 * Module: Articles (Redirect)
 */
// Redirect to blog home
?>
<script>
    window.location.href = '<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>';
</script>
<div class="jobs-module-container">
    <p>Redirecting to Articles...</p>
</div>
