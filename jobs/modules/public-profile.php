<?php
/**
 * Module: Public Profile (View)
 */
$user_id = isset( $_GET['user_id'] ) ? intval( $_GET['user_id'] ) : get_current_user_id();
$user_info = get_userdata( $user_id );
$roles = ( array ) $user_info->roles;

?>
<div class="jobs-module-container">
    <h2>Public Profile: <?php echo esc_html( $user_info->display_name ); ?></h2>

    <?php if ( in_array( 'employer', $roles ) ) :
        $company_data = get_user_meta( $user_id, '_jobs_company_data', true );
        ?>
        <div class="profile-section">
            <h3>Company Details</h3>
            <p><strong>Name:</strong> <?php echo esc_html( isset($company_data['company_name']) ? $company_data['company_name'] : 'N/A' ); ?></p>
            <p><strong>Website:</strong> <?php echo esc_html( isset($company_data['company_website']) ? $company_data['company_website'] : 'N/A' ); ?></p>
            <p><strong>About:</strong><br><?php echo nl2br( esc_html( isset($company_data['company_description']) ? $company_data['company_description'] : '' ) ); ?></p>
        </div>
    <?php elseif ( in_array( 'job_seeker', $roles ) ) :
        $cv_data = get_user_meta( $user_id, '_jobs_cv_data', true );
        ?>
        <div class="profile-section">
            <h3>Resume</h3>
            <h4>Experience</h4>
            <p><?php echo nl2br( esc_html( isset($cv_data['experience']) ? $cv_data['experience'] : 'No experience listed.' ) ); ?></p>

            <h4>Education</h4>
            <p><?php echo nl2br( esc_html( isset($cv_data['education']) ? $cv_data['education'] : 'No education listed.' ) ); ?></p>

            <h4>Skills</h4>
            <div class="job-meta">
                <?php
                $skills = isset($cv_data['skills']) ? explode(',', $cv_data['skills']) : array();
                foreach($skills as $skill) {
                    echo '<span class="job-capsule capsule-category">' . esc_html( trim($skill) ) . '</span>';
                }
                ?>
            </div>
        </div>
    <?php endif; ?>
</div>
