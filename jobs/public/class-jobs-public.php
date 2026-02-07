<?php

class Jobs_Public {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'google-fonts-rubik', 'https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700&display=swap', array(), null );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/jobs-public.css', array(), time() );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/jobs-public.js', array( 'jquery' ), time(), true );

        wp_localize_script( $this->plugin_name, 'jobs_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'jobs-ajax-nonce' )
        ));
	}

    public function inject_top_bar() {
        if ( ! is_user_logged_in() ) {
            return;
        }

        $user = wp_get_current_user();
        if ( empty( $user->roles ) ) {
            return;
        }
        $roles = ( array ) $user->roles;

        if ( in_array( 'job_seeker', $roles ) || in_array( 'employer', $roles ) || in_array( 'reviewer', $roles ) || in_array( 'administrator', $roles ) ) {
             require plugin_dir_path( __FILE__ ) . 'partials/jobs-public-top-bar.php';
        }
    }

    public function ajax_search_jobs() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );

        $paged = isset( $_POST['paged'] ) ? intval( $_POST['paged'] ) : 1;
        $args = array(
            'post_type' => 'job',
            'post_status' => 'publish',
            's' => isset( $_POST['keyword'] ) ? sanitize_text_field( $_POST['keyword'] ) : '',
            'paged' => $paged,
            'tax_query' => array( 'relation' => 'AND' )
        );

        if ( ! empty( $_POST['specialization'] ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_specialization',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_POST['specialization'] ),
            );
        }

        if ( ! empty( $_POST['category'] ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_category',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_POST['category'] ),
            );
        }

        if ( ! empty( $_POST['country'] ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_country',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_POST['country'] ),
            );
        }

        if ( ! empty( $_POST['city'] ) ) {
            $args['tax_query'][] = array(
                'taxonomy' => 'job_city',
                'field'    => 'slug',
                'terms'    => sanitize_text_field( $_POST['city'] ),
            );
        }

        $query = new WP_Query( $args );

        ob_start();

        if ( $query->have_posts() ) {
            echo '<div class="jobs-grid">';
            while ( $query->have_posts() ) {
                $query->the_post();
                $this->render_job_card();
            }
            echo '</div>';
        } else {
            echo '<p class="no-jobs-found">No jobs found matching your criteria.</p>';
        }

        $content = ob_get_clean();
        wp_reset_postdata();

        wp_send_json_success( $content );
    }

    private function render_job_card() {
        ?>
        <div class="job-card">
            <h3 class="job-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

            <div class="job-meta">
                <?php
                $specializations = get_the_terms( get_the_ID(), 'job_specialization' );
                if ( $specializations && ! is_wp_error( $specializations ) ) {
                    foreach ( $specializations as $term ) {
                        echo '<span class="job-capsule capsule-specialization">' . esc_html( $term->name ) . '</span>';
                    }
                }

                $categories = get_the_terms( get_the_ID(), 'job_category' );
                if ( $categories && ! is_wp_error( $categories ) ) {
                     foreach ( $categories as $term ) {
                        echo '<span class="job-capsule capsule-category">' . esc_html( $term->name ) . '</span>';
                    }
                }

                $countries = get_the_terms( get_the_ID(), 'job_country' );
                if ( $countries && ! is_wp_error( $countries ) ) {
                     foreach ( $countries as $term ) {
                        echo '<span class="job-capsule capsule-location">' . esc_html( $term->name ) . '</span>';
                    }
                }
                ?>
            </div>

            <div class="job-excerpt">
                <?php the_excerpt(); ?>
            </div>
            <div class="job-actions">
                 <a href="<?php the_permalink(); ?>" class="job-view-btn">View Job</a>
                 <?php if ( is_user_logged_in() ) : ?>
                    <button class="jobs-btn-apply" data-job-id="<?php the_ID(); ?>">Apply Now</button>
                    <button class="jobs-btn-favorite" data-job-id="<?php the_ID(); ?>">Save</button>
                 <?php else : ?>
                    <a href="#" class="jobs-login-required">Login to Apply</a>
                 <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public function ajax_toggle_favorite() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'Login required.' );
        }

        $job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;
        if ( ! $job_id ) {
            wp_send_json_error( 'Invalid Job ID.' );
        }

        $user_id = get_current_user_id();
        $favorites = get_user_meta( $user_id, '_jobs_favorites', true );
        if ( ! is_array( $favorites ) ) {
            $favorites = array();
        }

        if ( in_array( $job_id, $favorites ) ) {
            $key = array_search( $job_id, $favorites );
            unset( $favorites[$key] );
            $action = 'removed';
        } else {
            $favorites[] = $job_id;
            $action = 'added';
        }

        update_user_meta( $user_id, '_jobs_favorites', array_values( $favorites ) );
        wp_send_json_success( array( 'action' => $action ) );
    }

    public function ajax_apply_job() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'You must be logged in to apply.' );
        }

        $job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;
        if ( ! $job_id ) {
            wp_send_json_error( 'Invalid Job ID.' );
        }

        // Check if already applied
        $user_id = get_current_user_id();
        $existing = new WP_Query( array(
            'post_type' => 'application',
            'meta_query' => array(
                'relation' => 'AND',
                array( 'key' => '_job_id', 'value' => $job_id ),
                array( 'key' => '_applicant_id', 'value' => $user_id )
            )
        ) );

        if ( $existing->have_posts() ) {
            wp_send_json_error( 'You have already applied for this job.' );
        }

        $application_data = array(
            'post_title'  => 'Application for Job #' . $job_id . ' by User #' . $user_id,
            'post_type'   => 'application',
            'post_status' => 'publish',
            'post_author' => $user_id
        );

        $app_id = wp_insert_post( $application_data );

        if ( is_wp_error( $app_id ) ) {
             wp_send_json_error( $app_id->get_error_message() );
        }

        update_post_meta( $app_id, '_job_id', $job_id );
        update_post_meta( $app_id, '_applicant_id', $user_id );

        wp_send_json_success( 'Application submitted successfully.' );
    }

    public function ajax_update_job_status() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
             wp_send_json_error( 'Access denied.' );
        }

        $user = wp_get_current_user();
        $roles = ( array ) $user->roles;
        if ( ! in_array( 'reviewer', $roles ) && ! in_array( 'administrator', $roles ) ) {
             wp_send_json_error( 'Access denied.' );
        }

        $job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;
        $status = sanitize_text_field( $_POST['status'] );

        if ( ! $job_id || ! in_array( $status, array( 'publish', 'trash' ) ) ) {
            wp_send_json_error( 'Invalid request.' );
        }

        $updated = wp_update_post( array(
            'ID' => $job_id,
            'post_status' => $status
        ) );

        if ( is_wp_error( $updated ) ) {
            wp_send_json_error( $updated->get_error_message() );
        }

        wp_send_json_success( 'Job status updated.' );
    }

    public function ajax_post_job() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( 'You must be logged in to post a job.' );
        }

        // Basic validation
        if ( empty( $_POST['job_title'] ) || empty( $_POST['job_description'] ) ) {
            wp_send_json_error( 'Title and description are required.' );
        }

        $user_id = get_current_user_id();

        $job_data = array(
            'post_title'    => sanitize_text_field( $_POST['job_title'] ),
            'post_content'  => wp_kses_post( $_POST['job_description'] ),
            'post_status'   => 'pending', // Pending review
            'post_type'     => 'job',
            'post_author'   => $user_id
        );

        $job_id = wp_insert_post( $job_data );

        if ( is_wp_error( $job_id ) ) {
            wp_send_json_error( $job_id->get_error_message() );
        }

        // Set Taxonomies
        if ( ! empty( $_POST['job_specialization'] ) ) {
            wp_set_post_terms( $job_id, array( intval( $_POST['job_specialization'] ) ), 'job_specialization' );
        }
        if ( ! empty( $_POST['job_category'] ) ) {
            wp_set_post_terms( $job_id, array( intval( $_POST['job_category'] ) ), 'job_category' );
        }
        if ( ! empty( $_POST['job_country'] ) ) {
            wp_set_post_terms( $job_id, array( intval( $_POST['job_country'] ) ), 'job_country' );
        }
        if ( ! empty( $_POST['job_city'] ) ) {
            wp_set_post_terms( $job_id, array( intval( $_POST['job_city'] ) ), 'job_city' );
        }

        wp_send_json_success( 'Job posted successfully.' );
    }

    public function load_module() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );

        $module = sanitize_text_field( $_POST['module'] );
        $allowed_modules = array(
            'job-posting', 'job-listings-history', 'public-profile',
            'applications-submitted', 'cv-resume', 'company-profile',
            'job-requests', 'favorites', 'drafts', 'support',
            'settings', 'advanced-settings', 'terms-conditions', 'articles'
        );

        if ( ! in_array( $module, $allowed_modules ) ) {
            wp_send_json_error( 'Invalid module' );
        }

        $file_path = plugin_dir_path( dirname( __FILE__ ) ) . 'modules/' . $module . '.php';

        if ( file_exists( $file_path ) ) {
            ob_start();
            include $file_path;
            $content = ob_get_clean();
            wp_send_json_success( $content );
        } else {
            wp_send_json_error( 'Module file not found' );
        }
    }

}
