<div id="jobs-top-bar" class="jobs-top-bar">
    <div class="jobs-bar-container">
        <!-- Modules Icon -->
        <div class="jobs-modules-trigger" id="jobs-modules-trigger" title="Application Modules">
             <span class="dashicons dashicons-grid-view"></span>
        </div>

        <!-- Notifications Trigger -->
        <div class="jobs-notifications-trigger" title="Notifications" style="margin-right: 15px; cursor: pointer;">
             <span class="dashicons dashicons-bell"></span>
        </div>

        <!-- User Avatar -->
         <div class="jobs-user-profile" id="jobs-user-trigger">
            <?php echo get_avatar( get_current_user_id(), 32 ); ?>
         </div>
    </div>

    <!-- Modules Dropdown -->
    <div id="jobs-modules-list" class="jobs-modules-list" style="display:none;">
        <ul>
            <?php
            $current_user = wp_get_current_user();
            $roles = ( array ) $current_user->roles;
            ?>

            <!-- Modules excluded for Job Seekers (Available for Employer, Reviewer, Admin) -->
            <?php if ( ! in_array( 'job_seeker', $roles ) ) : ?>
                <li data-module="job-posting">Job Posting</li>
                <li data-module="job-listings-history">Job Listings History</li>
            <?php endif; ?>

            <li data-module="public-profile">Public Profile</li>

            <?php if ( in_array( 'job_seeker', $roles ) ) : ?>
            <li data-module="applications-submitted">Applications Submitted</li>
            <li data-module="cv-resume">CV / Resume</li>
            <?php endif; ?>

             <?php if ( in_array( 'employer', $roles ) ) : ?>
            <li data-module="company-profile">Company Profile</li>
            <?php endif; ?>

            <?php if ( in_array( 'employer', $roles ) || in_array( 'reviewer', $roles ) || in_array( 'administrator', $roles ) ) : ?>
            <li data-module="job-requests">Job Requests</li>
            <?php endif; ?>

            <li data-module="favorites">Favorites</li>
            <li data-module="drafts">Drafts</li>
            <li data-module="support">Support</li>
            <li data-module="settings">Settings</li>
            <li data-module="terms-conditions">Terms & Conditions</li>
            <li data-module="articles">Articles</li>
        </ul>
    </div>

    <!-- User Dropdown -->
    <div id="jobs-user-menu" class="jobs-user-menu" style="display:none;">
        <ul>
            <li data-module="settings">Settings</li>
            <li><a href="<?php echo wp_logout_url(); ?>">Logout</a></li>
        </ul>
    </div>
</div>

<!-- Modal for module content -->
<div id="jobs-modal" class="jobs-modal" style="display:none;">
    <div class="jobs-modal-content" id="jobs-modal-content">
        <span class="jobs-close">&times;</span>
        <div id="jobs-modal-body"></div>
    </div>
</div>
