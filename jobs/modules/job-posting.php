<?php
/**
 * Module: job-posting.php
 */

$specializations = get_terms( array( 'taxonomy' => 'job_specialization', 'hide_empty' => false ) );
$countries = get_terms( array( 'taxonomy' => 'job_country', 'hide_empty' => false ) );
$cities = get_terms( array( 'taxonomy' => 'job_city', 'hide_empty' => false ) );
?>
<div class="jobs-module-header">
    <h2 class="jobs-module-title">Post a Job</h2>
</div>
<form id="jobs-posting-form" class="jobs-form">
    <div class="jobs-form-group">
        <label for="job_title" class="jobs-label">Job Title</label>
        <input type="text" id="job_title" name="job_title" class="jobs-input" required>
    </div>

    <div class="jobs-form-group">
        <label for="job_specialization" class="jobs-label">Specialization</label>
        <select id="job_specialization" name="job_specialization" class="jobs-select">
            <option value="">Select Specialization</option>
            <?php
            if ( ! is_wp_error( $specializations ) ) {
                foreach ( $specializations as $term ) : ?>
                    <option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option>
                <?php endforeach;
            }
            ?>
        </select>
    </div>

    <div class="jobs-form-group">
        <label for="job_country" class="jobs-label">Country</label>
        <select id="job_country" name="job_country" class="jobs-select">
            <option value="">Select Country</option>
            <?php
            if ( ! is_wp_error( $countries ) ) {
                foreach ( $countries as $term ) : ?>
                    <option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option>
                <?php endforeach;
            }
            ?>
        </select>
    </div>

    <div class="jobs-form-group">
        <label for="job_city" class="jobs-label">City</label>
        <select id="job_city" name="job_city" class="jobs-select">
            <option value="">Select City</option>
            <?php
            if ( ! is_wp_error( $cities ) ) {
                foreach ( $cities as $term ) : ?>
                    <option value="<?php echo esc_attr( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option>
                <?php endforeach;
            }
            ?>
        </select>
    </div>

    <div class="jobs-form-group">
        <label for="job_description" class="jobs-label">Description</label>
        <textarea id="job_description" name="job_description" class="jobs-textarea" rows="5" required></textarea>
    </div>

    <div class="jobs-form-actions">
        <button type="submit" class="jobs-btn-primary">Submit Job</button>
    </div>
    <div id="jobs-posting-message" class="jobs-message"></div>
</form>
