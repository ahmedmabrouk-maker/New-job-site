<?php
/**
 * Module: admin-dashboard.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}
?>
<h2>Dashboard</h2>
<p>Welcome to the Jobs System Admin Panel.</p>
