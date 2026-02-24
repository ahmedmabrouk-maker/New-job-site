<?php
/**
 * Module: admin-articles.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}
?>
<h2>Published Articles Management</h2>
<p>
	<a href="<?php echo admin_url( 'post-new.php' ); ?>" class="button button-primary">Add New Article</a>
</p>
<table class="widefat fixed striped" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
	<thead>
		<tr style="background: rgba(29, 52, 105, 0.1); text-align: left;">
			<th style="padding: 10px;">Title</th>
			<th style="padding: 10px;">Author</th>
			<th style="padding: 10px;">Date</th>
			<th style="padding: 10px;">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$posts = get_posts( array( 'post_type' => 'post', 'numberposts' => 20 ) );
		if ( $posts ) {
			foreach ( $posts as $post ) {
				echo '<tr style="border-bottom: 1px solid #eee;">';
				echo '<td style="padding: 10px;">' . esc_html( $post->post_title ) . '</td>';
				echo '<td style="padding: 10px;">' . get_the_author_meta( 'display_name', $post->post_author ) . '</td>';
				echo '<td style="padding: 10px;">' . get_the_date( '', $post->ID ) . '</td>';
				echo '<td style="padding: 10px;"><a href="' . get_edit_post_link( $post->ID ) . '">Edit</a> | <a href="' . get_permalink( $post->ID ) . '" target="_blank">View</a></td>';
				echo '</tr>';
			}
		} else {
			echo '<tr><td colspan="4" style="padding: 10px;">No articles found.</td></tr>';
		}
		?>
	</tbody>
</table>
