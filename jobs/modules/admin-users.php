<?php
/**
 * Module: admin-users.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}
?>
<h2>User Management</h2>
<p>
	<a href="<?php echo admin_url( 'user-new.php' ); ?>" class="button button-primary">Add New User</a>
	<a href="<?php echo admin_url( 'users.php' ); ?>" class="button">Manage Users (WP Admin)</a>
</p>
<table class="widefat fixed striped" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
	<thead>
		<tr style="background: rgba(29, 52, 105, 0.1); text-align: left;">
			<th style="padding: 10px;">Username</th>
			<th style="padding: 10px;">Name</th>
			<th style="padding: 10px;">Email</th>
			<th style="padding: 10px;">Role</th>
			<th style="padding: 10px;">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$users = get_users();
		foreach ( $users as $user ) {
			$roles = implode( ', ', $user->roles );
			echo '<tr style="border-bottom: 1px solid #eee;">';
			echo '<td style="padding: 10px;">' . esc_html( $user->user_login ) . '</td>';
			echo '<td style="padding: 10px;">' . esc_html( $user->display_name ) . '</td>';
			echo '<td style="padding: 10px;">' . esc_html( $user->user_email ) . '</td>';
			echo '<td style="padding: 10px;">' . esc_html( $roles ) . '</td>';
			echo '<td style="padding: 10px;"><a href="' . get_edit_user_link( $user->ID ) . '">Edit</a></td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>
