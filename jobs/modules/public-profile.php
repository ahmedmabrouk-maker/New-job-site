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

?>
<div class="jobs-module-header">
	<h2>Public Profile</h2>
	<p>This is how your profile appears to others.</p>
</div>

<?php
if ( in_array( 'job_seeker', $roles ) ) {
	$cv_data = get_user_meta( $user->ID, '_jobs_cv_data', true );
	if ( empty( $cv_data ) ) {
		echo '<p>No CV data found. Please update your CV/Resume.</p>';
	} else {
		echo '<div class="jobs-profile-section">';
		echo '<h3>' . esc_html( $user->display_name ) . '</h3>';
		echo '<p>Email: ' . esc_html( $user->user_email ) . '</p>';

		if ( ! empty( $cv_data['skills'] ) ) {
			echo '<h4>Skills</h4>';
			echo '<p>' . nl2br( esc_html( $cv_data['skills'] ) ) . '</p>';
		}

		if ( ! empty( $cv_data['education'] ) ) {
			echo '<h4>Education</h4>';
			foreach ( $cv_data['education'] as $edu ) {
				echo '<div class="jobs-profile-item">';
				echo '<strong>' . esc_html( $edu['institution'] ) . '</strong> - ' . esc_html( $edu['degree'] ) . ' (' . esc_html( $edu['year'] ) . ')';
				echo '</div>';
			}
		}

		if ( ! empty( $cv_data['experience'] ) ) {
			echo '<h4>Experience</h4>';
			foreach ( $cv_data['experience'] as $exp ) {
				echo '<div class="jobs-profile-item">';
				echo '<strong>' . esc_html( $exp['company'] ) . '</strong> - ' . esc_html( $exp['position'] ) . ' (' . esc_html( $exp['duration'] ) . ')';
				echo '</div>';
			}
		}
		echo '</div>';
	}
} elseif ( in_array( 'employer', $roles ) ) {
	$company_data = get_user_meta( $user->ID, '_jobs_company_data', true );
	if ( empty( $company_data ) ) {
		echo '<p>No company data found. Please update your Company Profile.</p>';
	} else {
		echo '<div class="jobs-profile-section">';
		if ( ! empty( $company_data['logo_url'] ) ) {
			echo '<img src="' . esc_url( $company_data['logo_url'] ) . '" alt="Company Logo" style="max-width: 150px; margin-bottom: 20px;">';
		}
		echo '<h3>' . esc_html( $company_data['name'] ) . '</h3>';
		echo '<p><strong>Employees:</strong> ' . esc_html( $company_data['employee_count'] ) . '</p>';
		echo '<p><strong>Address:</strong> ' . esc_html( $company_data['address'] ) . '</p>';
		echo '<h4>Description</h4>';
		echo '<p>' . nl2br( esc_html( $company_data['description'] ) ) . '</p>';
		echo '</div>';
	}
} else {
	echo '<p>Profile not available for this role.</p>';
}
?>
