<?php
/**
 * Module: admin-support.php
 */
if ( ! current_user_can( 'administrator' ) ) {
	echo 'Access Denied';
	return;
}
?>
<h2>Technical Support</h2>
<p>Support tickets from users.</p>
<?php
$args = array(
	'post_type' => 'job_notification',
	'posts_per_page' => 20,
	'post_status' => 'any',
);
$tickets = get_posts($args);

if ($tickets) {
	echo '<ul class="jobs-support-list">';
	foreach ($tickets as $ticket) {
		echo '<li>';
		echo '<strong>' . esc_html($ticket->post_title) . '</strong>';
		echo '<p>' . esc_html($ticket->post_content) . '</p>';
		echo '<small>From User ID: ' . $ticket->post_author . ' | ' . get_the_date('', $ticket->ID) . '</small>';
		echo '</li>';
	}
	echo '</ul>';
} else {
	echo '<p>No support tickets found.</p>';
}
?>
<style>
.jobs-support-list { list-style: none; padding: 0; }
.jobs-support-list li { background: rgba(255,255,255,0.8); padding: 15px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
.jobs-support-list li p { margin: 5px 0; color: #555; }
</style>
