<?php
/**
 * Module: admin-permissions.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}
?>
<h2>Permissions & Roles Management</h2>
<table class="widefat fixed striped" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
	<thead>
		<tr style="background: rgba(29, 52, 105, 0.1); text-align: left;">
			<th style="padding: 10px;">Role</th>
			<th style="padding: 10px;">Capabilities Count</th>
			<th style="padding: 10px;">Users Count</th>
		</tr>
	</thead>
	<tbody>
		<?php
		global $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->roles;
		foreach ( $roles as $role_slug => $role_details ) {
			$user_query = new WP_User_Query( array( 'role' => $role_slug ) );
			$user_count = $user_query->get_total();

			echo '<tr style="border-bottom: 1px solid #eee;">';
			echo '<td style="padding: 10px;">' . esc_html( $role_details['name'] ) . ' (' . esc_html( $role_slug ) . ')</td>';
			echo '<td style="padding: 10px;">' . count( $role_details['capabilities'] ) . ' capabilities</td>';
			echo '<td style="padding: 10px;">' . $user_count . ' users</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>
