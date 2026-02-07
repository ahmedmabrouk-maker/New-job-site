<?php

class Jobs_Notifications {

    public function init() {
        // Trigger on Application Creation
        add_action( 'save_post_application', array( $this, 'notify_application_created' ), 10, 3 );

        // Trigger on Job Status Change (e.g. Pending -> Publish)
        add_action( 'transition_post_status', array( $this, 'notify_job_status_change' ), 10, 3 );
    }

    public function notify_application_created( $post_id, $post, $update ) {
        if ( $update ) return; // Only on creation

        $job_id = get_post_meta( $post_id, '_job_id', true );
        if ( ! $job_id ) return;

        $job = get_post( $job_id );
        if ( ! $job ) return;

        // Notify Employer
        $employer_id = $job->post_author;
        $this->send_notification( $employer_id, 'New application received for "' . $job->post_title . '".', 'info' );
    }

    public function notify_job_status_change( $new_status, $old_status, $post ) {
        if ( $post->post_type !== 'job' ) return;

        if ( $old_status == 'pending' && $new_status == 'publish' ) {
            // Notify Employer that job is live
            $this->send_notification( $post->post_author, 'Your job "' . $post->post_title . '" has been approved and is now live.', 'success' );
        }

        if ( $new_status == 'trash' && $old_status != 'trash' ) {
             // Notify Employer that job was rejected or deleted
             $this->send_notification( $post->post_author, 'Your job "' . $post->post_title . '" has been removed.', 'warning' );
        }
    }

    public function send_notification( $user_id, $message, $type = 'info' ) {
        $args = array(
            'post_type'   => 'job_notification',
            'post_title'  => 'Notification for User ' . $user_id,
            'post_content' => $message,
            'post_status' => 'publish',
            'post_author' => $user_id, // Assign to user so they can query it easily
            'meta_input'  => array(
                '_notification_type' => $type,
                '_read_status' => 'unread'
            )
        );
        wp_insert_post( $args );
    }

}
