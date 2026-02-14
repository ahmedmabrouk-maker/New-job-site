<?php
/**
 * Module: articles.php
 */
$page_for_posts = get_option( 'page_for_posts' );
$blog_url = $page_for_posts ? get_permalink( $page_for_posts ) : home_url();
?>
<div class="jobs-module-header">
	<h2>Articles</h2>
</div>
<p>Redirecting to Articles...</p>
<script>
	window.location.href = '<?php echo esc_url( $blog_url ); ?>';
</script>
