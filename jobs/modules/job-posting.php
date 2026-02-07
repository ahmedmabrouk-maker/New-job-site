<?php
/**
 * Module: Job Posting
 */
?>
<div class="jobs-module-container">
    <h2>Post a New Job</h2>
    <form id="jobs-posting-form" class="jobs-form">
        <div class="form-group">
            <label for="job_title">Job Title</label>
            <input type="text" name="job_title" id="job_title" required>
        </div>

        <div class="form-group">
            <label for="job_description">Description</label>
            <textarea name="job_description" id="job_description" rows="5" required></textarea>
        </div>

        <div class="form-group">
            <label for="job_specialization">Specialization</label>
            <?php
            $specializations = get_terms( array( 'taxonomy' => 'job_specialization', 'hide_empty' => false ) );
            echo '<select name="job_specialization" id="job_specialization" required>';
            echo '<option value="">Select Specialization</option>';
            foreach ( $specializations as $term ) {
                echo '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>';
            }
            echo '</select>';
            ?>
        </div>

        <div class="form-group">
             <label for="job_category">Category</label>
            <?php
            $categories = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => false ) );
            echo '<select name="job_category" id="job_category" required>';
            echo '<option value="">Select Category</option>';
            foreach ( $categories as $term ) {
                echo '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>';
            }
            echo '</select>';
            ?>
        </div>

        <div class="form-group">
            <label for="job_country">Country</label>
             <?php
            $countries = get_terms( array( 'taxonomy' => 'job_country', 'hide_empty' => false ) );
            echo '<select name="job_country" id="job_country" required>';
            echo '<option value="">Select Country</option>';
            foreach ( $countries as $term ) {
                echo '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>';
            }
            echo '</select>';
            ?>
        </div>

         <div class="form-group">
            <label for="job_city">City</label>
             <?php
            $cities = get_terms( array( 'taxonomy' => 'job_city', 'hide_empty' => false ) );
            echo '<select name="job_city" id="job_city" required>';
            echo '<option value="">Select City</option>';
            foreach ( $cities as $term ) {
                echo '<option value="' . esc_attr( $term->term_id ) . '">' . esc_html( $term->name ) . '</option>';
            }
            echo '</select>';
            ?>
        </div>

        <button type="submit" class="jobs-btn">Submit for Review</button>
        <div id="jobs-posting-message"></div>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('#jobs-posting-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=jobs_post_job&nonce=' + jobs_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    $('#jobs-posting-message').html('<p class="success">Job submitted successfully! It is now pending review.</p>');
                    $('#jobs-posting-form')[0].reset();
                } else {
                     $('#jobs-posting-message').html('<p class="error">' + response.data + '</p>');
                }
            }
        });
    });
});
</script>
