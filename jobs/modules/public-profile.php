<?php
/**
 * Module: public-profile.php
 */

if ( ! is_user_logged_in() ) {
	echo '<p>You must be logged in to view your profile.</p>';
	return;
}

$user = wp_get_current_user();
$roles = ( array ) $user->roles;
$user_id = $user->ID;
$avatar_url = get_avatar_url( $user_id, array( 'size' => 150 ) );

?>

<div class="jobs-module-header">
	<h2>Public Profile</h2>
</div>

<div class="jobs-profile-container">
	<div class="jobs-profile-header">
		<img src="<?php echo esc_url( $avatar_url ); ?>" alt="Avatar" class="jobs-profile-avatar">
		<h3><?php echo esc_html( $user->display_name ); ?></h3>
		<p class="jobs-profile-role"><?php echo ucfirst( $roles[0] ); ?></p>
	</div>

	<?php if ( in_array( 'employer', $roles ) ) : ?>
		<?php
		$company_data = get_user_meta( $user_id, '_jobs_company_data', true );
		if ( ! is_array( $company_data ) ) $company_data = array();
		?>
		<div class="jobs-profile-section">
			<h3>Company Details</h3>
			<?php if ( ! empty( $company_data['logo_url'] ) ) : ?>
				<img src="<?php echo esc_url( $company_data['logo_url'] ); ?>" alt="Company Logo" class="jobs-company-logo">
			<?php endif; ?>
			<p><strong>Company Name:</strong> <?php echo esc_html( isset( $company_data['name'] ) ? $company_data['name'] : 'N/A' ); ?></p>
			<p><strong>Employees:</strong> <?php echo esc_html( isset( $company_data['employee_count'] ) ? $company_data['employee_count'] : 'N/A' ); ?></p>
			<p><strong>Address:</strong> <?php echo esc_html( isset( $company_data['address'] ) ? $company_data['address'] : 'N/A' ); ?></p>
			<p><strong>Description:</strong><br><?php echo nl2br( esc_html( isset( $company_data['description'] ) ? $company_data['description'] : '' ) ); ?></p>
		</div>

	<?php elseif ( in_array( 'job_seeker', $roles ) ) : ?>
		<?php
		$cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );
		if ( ! is_array( $cv_data ) ) $cv_data = array();
		?>
		<div class="jobs-profile-section">
			<h3>Professional Summary</h3>

			<h4>Skills</h4>
			<p><?php echo esc_html( isset( $cv_data['skills'] ) ? $cv_data['skills'] : 'No skills listed.' ); ?></p>

			<h4>Experience</h4>
			<?php if ( ! empty( $cv_data['experience'] ) ) : ?>
				<ul class="jobs-list">
				<?php foreach ( $cv_data['experience'] as $exp ) : ?>
					<li>
						<strong><?php echo esc_html( $exp['position'] ); ?></strong> at <?php echo esc_html( $exp['company'] ); ?> (<?php echo esc_html( $exp['years'] ); ?>)
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p>No experience listed.</p>
			<?php endif; ?>

			<h4>Education</h4>
			<?php if ( ! empty( $cv_data['education'] ) ) : ?>
				<ul class="jobs-list">
				<?php foreach ( $cv_data['education'] as $edu ) : ?>
					<li>
						<strong><?php echo esc_html( $edu['degree'] ); ?></strong> from <?php echo esc_html( $edu['school'] ); ?> (<?php echo esc_html( $edu['year'] ); ?>)
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p>No education listed.</p>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<style>
.jobs-profile-container { text-align: left; }
.jobs-profile-header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
.jobs-profile-avatar { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; }
.jobs-company-logo { max-width: 150px; display: block; margin: 10px 0; }
.jobs-profile-section { margin-bottom: 20px; }
.jobs-list { list-style: none; padding: 0; }
.jobs-list li { margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #f9f9f9; }
</style>
