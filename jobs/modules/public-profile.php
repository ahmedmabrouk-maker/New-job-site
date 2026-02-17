<?php
/**
 * Module: public-profile.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in.</p>';
	return;
}

$user = wp_get_current_user();
$user_id = $user->ID;
$hidden = get_user_meta( $user_id, '_jobs_hide_public_profile', true );

$roles = (array) $user->roles;
$is_employer = in_array( 'employer', $roles );
$is_seeker = in_array( 'job_seeker', $roles );

?>

<div class="jobs-module-header">
	<h2>My Public Profile</h2>
	<?php if ( $hidden ) : ?>
		<span class="jobs-badge jobs-badge-warning">Hidden</span>
	<?php else : ?>
		<span class="jobs-badge jobs-badge-success">Visible</span>
	<?php endif; ?>
</div>

<div class="jobs-profile-preview">
	<div class="jobs-profile-header">
		<?php echo get_avatar( $user_id, 80 ); ?>
		<h3><?php echo esc_html( $user->display_name ); ?></h3>
		<p><?php echo ucfirst( implode( ', ', $roles ) ); ?></p>
	</div>

	<?php if ( $is_employer ) :
		$company_data = get_user_meta( $user_id, '_jobs_company_data', true );
		if ( ! is_array( $company_data ) ) $company_data = array();
		?>
		<div class="jobs-profile-section">
			<h4>Company Details</h4>
			<?php if ( ! empty( $company_data['logo_url'] ) ) : ?>
				<img src="<?php echo esc_url( $company_data['logo_url'] ); ?>" alt="Company Logo" class="jobs-company-logo">
			<?php endif; ?>
			<p><strong>Name:</strong> <?php echo esc_html( $company_data['name'] ?? 'N/A' ); ?></p>
			<p><strong>Employees:</strong> <?php echo esc_html( $company_data['employee_count'] ?? 'N/A' ); ?></p>
			<p><strong>Address:</strong> <?php echo esc_html( $company_data['address'] ?? 'N/A' ); ?></p>
			<p><strong>About:</strong> <?php echo wpautop( esc_html( $company_data['description'] ?? '' ) ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( $is_seeker ) :
		$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );
		if ( ! is_array( $cv_data ) ) $cv_data = array();
		?>
		<div class="jobs-profile-section">
			<h4>CV / Resume</h4>

			<h5>Skills</h5>
			<p><?php echo esc_html( $cv_data['skills'] ?? 'None listed' ); ?></p>

			<h5>Experience</h5>
			<?php if ( ! empty( $cv_data['experience'] ) ) : ?>
				<ul>
				<?php foreach ( $cv_data['experience'] as $exp ) : ?>
					<li>
						<strong><?php echo esc_html( $exp['position'] ); ?></strong> at <?php echo esc_html( $exp['company'] ); ?> (<?php echo esc_html( $exp['years'] ); ?>)
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p>No experience listed.</p>
			<?php endif; ?>

			<h5>Education</h5>
			<?php if ( ! empty( $cv_data['education'] ) ) : ?>
				<ul>
				<?php foreach ( $cv_data['education'] as $edu ) : ?>
					<li>
						<strong><?php echo esc_html( $edu['degree'] ); ?></strong> - <?php echo esc_html( $edu['institution'] ); ?> (<?php echo esc_html( $edu['year'] ); ?>)
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p>No education listed.</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="jobs-share-link">
		<h4>Share Your Profile</h4>
		<input type="text" readonly value="<?php echo esc_url( home_url( '/?jobs_profile=' . $user_id ) ); ?>" onclick="this.select()">
		<p class="description">Copy this link to share your profile.</p>
	</div>
</div>

<style>
.jobs-profile-header { text-align: center; margin-bottom: 20px; }
.jobs-profile-header img { border-radius: 50%; }
.jobs-profile-section { margin-bottom: 20px; border-top: 1px solid #eee; padding-top: 20px; }
.jobs-badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; color: #fff; }
.jobs-badge-success { background: #28a745; }
.jobs-badge-warning { background: #ffc107; color: #333; }
.jobs-share-link input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
</style>
