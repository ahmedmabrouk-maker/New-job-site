<?php
if ( ! defined( 'ABSPATH' ) ) exit;

global $wp_roles;
$user_counts = count_users();
?>
<div class="jobs-module-header">
    <h2>Permissions & Roles Management</h2>
</div>
<table class="jobs-table widefat">
    <thead>
        <tr>
            <th>Role</th>
            <th>Capabilities Count</th>
            <th>Users</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $wp_roles->roles as $role_slug => $role_details ) : ?>
            <tr>
                <td><?php echo esc_html( $role_details['name'] ); ?></td>
                <td><?php echo count( $role_details['capabilities'] ); ?></td>
                <td><?php echo isset( $user_counts['avail_roles'][ $role_slug ] ) ? $user_counts['avail_roles'][ $role_slug ] : 0; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
