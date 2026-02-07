<?php

class Jobs_Meta_Boxes {

    public function init() {
        add_action( 'add_meta_boxes', array( $this, 'add_job_location_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_job_location_meta' ) );
    }

    public function add_job_location_meta_box() {
        add_meta_box(
            'jobs_location_meta',
            __( 'Job Location (Latitude & Longitude)', 'jobs' ),
            array( $this, 'render_location_meta_box' ),
            'job',
            'side',
            'default'
        );
    }

    public function render_location_meta_box( $post ) {
        wp_nonce_field( 'jobs_save_location_meta', 'jobs_location_nonce' );

        $latitude = get_post_meta( $post->ID, '_job_latitude', true );
        $longitude = get_post_meta( $post->ID, '_job_longitude', true );

        ?>
        <p>
            <label for="job_latitude"><?php _e( 'Latitude:', 'jobs' ); ?></label>
            <input type="text" id="job_latitude" name="job_latitude" value="<?php echo esc_attr( $latitude ); ?>" class="widefat">
        </p>
        <p>
            <label for="job_longitude"><?php _e( 'Longitude:', 'jobs' ); ?></label>
            <input type="text" id="job_longitude" name="job_longitude" value="<?php echo esc_attr( $longitude ); ?>" class="widefat">
        </p>
        <p class="description"><?php _e( 'Enter coordinates for location-aware search results.', 'jobs' ); ?></p>
        <?php
    }

    public function save_job_location_meta( $post_id ) {
        if ( ! isset( $_POST['jobs_location_nonce'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['jobs_location_nonce'], 'jobs_save_location_meta' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( isset( $_POST['post_type'] ) && 'job' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        if ( isset( $_POST['job_latitude'] ) ) {
            update_post_meta( $post_id, '_job_latitude', sanitize_text_field( $_POST['job_latitude'] ) );
        }

        if ( isset( $_POST['job_longitude'] ) ) {
            update_post_meta( $post_id, '_job_longitude', sanitize_text_field( $_POST['job_longitude'] ) );
        }
    }

}
