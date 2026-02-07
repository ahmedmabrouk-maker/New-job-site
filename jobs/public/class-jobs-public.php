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
		wp_enqueue_style( $this->plugin_name . '-global', plugin_dir_url( __FILE__ ) . 'css/jobs-global.css', array(), time() );

        // Conditionally load styles based on presence of shortcodes or role
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'jobs_search' ) ) {
            wp_enqueue_style( $this->plugin_name . '-search', plugin_dir_url( __FILE__ ) . 'css/jobs-search.css', array(), time() );
        }

        if ( is_user_logged_in() ) {
            wp_enqueue_style( $this->plugin_name . '-top-bar', plugin_dir_url( __FILE__ ) . 'css/jobs-top-bar.css', array(), time() );
        }
	}

	public function enqueue_scripts() {
        // Global script (dependency)
        wp_enqueue_script( $this->plugin_name . '-global', plugin_dir_url( __FILE__ ) . 'js/jobs-global.js', array( 'jquery' ), time(), true );

        wp_localize_script( $this->plugin_name . '-global', 'jobs_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'jobs-ajax-nonce' )
        ));

        // Conditionally load scripts
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'jobs_search' ) ) {
            wp_enqueue_script( $this->plugin_name . '-search', plugin_dir_url( __FILE__ ) . 'js/jobs-search.js', array( 'jquery', $this->plugin_name . '-global' ), time(), true );
        }

        if ( is_user_logged_in() ) {
            wp_enqueue_script( $this->plugin_name . '-top-bar', plugin_dir_url( __FILE__ ) . 'js/jobs-top-bar.js', array( 'jquery', $this->plugin_name . '-global' ), time(), true );
            wp_enqueue_script( $this->plugin_name . '-notifications', plugin_dir_url( __FILE__ ) . 'js/jobs-notifications.js', array( 'jquery', $this->plugin_name . '-top-bar' ), time(), true );
        }
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

        // Location Sorting
        if ( ! empty( $_POST['lat'] ) && ! empty( $_POST['lng'] ) ) {
            $lat = floatval( $_POST['lat'] );
            $lng = floatval( $_POST['lng'] );

            // Custom query to sort by distance using meta fields
            // add_filter( 'posts_join', array( $this, 'join_meta_for_distance' ) ); // Removed undefined callback
            add_filter( 'posts_orderby', function( $orderby ) use ( $lat, $lng ) {
                global $wpdb;
                // Simplified Haversine or Euclidean distance for sorting
                // We assume meta tables are joined as mt1 (lat) and mt2 (lng)
                // However, without a clean way to alias in WP_Query, we might need manual SQL or a simpler approximation.
                // A reliable way is to not use WP_Query filters but raw SQL, OR use a simpler "ORDER BY (lat - target)^2 + (lng - target)^2"
                // Let's use a simpler meta query approach if possible, but sorting by calculated value needs SQL.

                // Let's rely on a simpler sorting for now:
                // We'll trust the custom join we added.
                // To avoid complex SQL injection risks in this snippet, we'll assume a standard WP setup.

                // Actually, doing this reliably via 'posts_orderby' without 'posts_fields' and complex logic is hard.
                // Alternative: Get all posts, sort in PHP (not scalable but works for small datasets).
                // Or better: Use the standard meta_query to Filter, but we want to Sort.

                return $orderby; // Placeholder: Real distance sorting requires complex SQL logic
            } );

            // NOTE: Implementing robust Geo-sorting in pure WP without plugins (like GeoQuery) is heavy.
            // We will attempt a basic Euclidean distance sort via SQL injection in orderby if strictly required.
            // For this scope, we will capture the intent but might fallback to standard sort if complex.

            // Let's try a custom SQL approach for the orderby
             add_filter( 'posts_clauses', function( $clauses ) use ( $lat, $lng ) {
                global $wpdb;
                $clauses['join'] .= "
                    INNER JOIN {$wpdb->postmeta} AS lat_meta ON ({$wpdb->posts}.ID = lat_meta.post_id AND lat_meta.meta_key = '_job_latitude')
                    INNER JOIN {$wpdb->postmeta} AS lng_meta ON ({$wpdb->posts}.ID = lng_meta.post_id AND lng_meta.meta_key = '_job_longitude')
                ";
                $clauses['orderby'] = "
                    (POW(lat_meta.meta_value - {$lat}, 2) + POW(lng_meta.meta_value - {$lng}, 2)) ASC, " . $clauses['orderby'];
                return $clauses;
            } );
        }

        $query = new WP_Query( $args );

        // Remove filters to avoid affecting other queries
        // Since we used closures, we can't easily remove_filter unless we stored the closure.
        // But since this is an AJAX request ending immediately, it's safer.
        // However, best practice is to clean up.
        // We will skip explicit cleanup as the script dies after wp_send_json_success.

        ob_start();

        if ( $query->have_posts() ) {
            echo '<div class="jobs-grid">';
            while ( $query->have_posts() ) {
                $query->the_post();
                $this->render_job_card();
            }
            echo '</div>';

            // Pagination
            $total_pages = $query->max_num_pages;
            if ( $total_pages > 1 ) {
                $current_page = max( 1, $paged );
                echo '<div class="jobs-pagination">';

                // Previous
                if ( $current_page > 1 ) {
                    echo '<a href="#" class="page-link" data-page="' . ( $current_page - 1 ) . '">&laquo;</a>';
                }

                // Range (Circular/Limited logic simplified to sliding window)
                $start = max( 1, $current_page - 2 );
                $end = min( $total_pages, $current_page + 2 );

                // Adjust window if close to edges
                if ( $end - $start < 4 ) {
                    if ( $start == 1 ) {
                        $end = min( $total_pages, $start + 4 );
                    } elseif ( $end == $total_pages ) {
                        $start = max( 1, $end - 4 );
                    }
                }

                for ( $i = $start; $i <= $end; $i++ ) {
                    $active = ( $i == $current_page ) ? 'active' : '';
                    echo '<a href="#" class="page-link ' . $active . '" data-page="' . $i . '">' . $i . '</a>';
                }

                // Next
                if ( $current_page < $total_pages ) {
                    echo '<a href="#" class="page-link" data-page="' . ( $current_page + 1 ) . '">&raquo;</a>';
                }

                echo '</div>';
            }

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
                    <button class="jobs-btn-apply-toggle" data-job-id="<?php the_ID(); ?>">Apply Now</button>
                    <button class="jobs-btn-favorite" data-job-id="<?php the_ID(); ?>">Save</button>
                 <?php else : ?>
                    <a href="#" class="jobs-login-required">Login to Apply</a>
                 <?php endif; ?>
            </div>
            <?php if ( is_user_logged_in() ) : ?>
            <div class="jobs-quick-apply-form" id="quick-apply-<?php the_ID(); ?>" style="display:none;">
                <form class="jobs-apply-form" data-job-id="<?php the_ID(); ?>">
                    <h4>Quick Apply</h4>
                    <p>Apply with your profile.</p>
                    <button type="submit" class="jobs-btn-submit-application">Confirm Application</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function ajax_save_cv() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );
        if ( ! is_user_logged_in() ) wp_send_json_error( 'Login required.' );

        $user_id = get_current_user_id();
        $cv_data = array(
            'experience' => sanitize_textarea_field( $_POST['experience'] ),
            'education'  => sanitize_textarea_field( $_POST['education'] ),
            'skills'     => sanitize_text_field( $_POST['skills'] )
        );

        update_user_meta( $user_id, '_jobs_cv_data', $cv_data );
        wp_send_json_success( 'CV Saved.' );
    }

    public function ajax_update_settings() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );
        if ( ! is_user_logged_in() ) wp_send_json_error( 'Login required.' );

        $user_id = get_current_user_id();
        $email = sanitize_email( $_POST['user_email'] );
        $pass  = $_POST['user_pass'];
        $pass_confirm = $_POST['user_pass_confirm'];

        if ( ! is_email( $email ) ) wp_send_json_error( 'Invalid email.' );

        // Update Email
        if ( $email !== wp_get_current_user()->user_email ) {
            if ( email_exists( $email ) ) wp_send_json_error( 'Email already in use.' );
            wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
        }

        // Update Password
        if ( ! empty( $pass ) ) {
            if ( $pass !== $pass_confirm ) wp_send_json_error( 'Passwords do not match.' );
            wp_update_user( array( 'ID' => $user_id, 'user_pass' => $pass ) );
        }

        wp_send_json_success( 'Settings updated.' );
    }

    public function ajax_save_company() {
        check_ajax_referer( 'jobs-ajax-nonce', 'nonce' );
        if ( ! is_user_logged_in() ) wp_send_json_error( 'Login required.' );

        $user_id = get_current_user_id();
        $company_data = array(
            'company_name'        => sanitize_text_field( $_POST['company_name'] ),
            'company_description' => sanitize_textarea_field( $_POST['company_description'] ),
            'company_website'     => esc_url_raw( $_POST['company_website'] )
        );

        update_user_meta( $user_id, '_jobs_company_data', $company_data );
        wp_send_json_success( 'Company Profile Saved.' );
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
            'settings', 'advanced-settings', 'terms-conditions', 'articles',
            'notifications', 'analytics'
        );

        if ( ! in_array( $module, $allowed_modules ) ) {
            wp_send_json_error( 'Invalid module' );
        }

        $file_path = plugin_dir_path( dirname( __FILE__ ) ) . 'modules/' . $module . '.php';

        if ( file_exists( $file_path ) ) {
            ob_start();
            include $file_path;
            $content = ob_get_clean();

            // Look for optional module assets
            $css_url = '';
            $js_url  = '';

            $module_css_path = plugin_dir_path( dirname( __FILE__ ) ) . 'modules/css/' . $module . '.css';
            if ( file_exists( $module_css_path ) ) {
                $css_url = plugin_dir_url( dirname( __FILE__ ) ) . 'modules/css/' . $module . '.css';
            }

            $module_js_path = plugin_dir_path( dirname( __FILE__ ) ) . 'modules/js/' . $module . '.js';
            if ( file_exists( $module_js_path ) ) {
                $js_url = plugin_dir_url( dirname( __FILE__ ) ) . 'modules/js/' . $module . '.js';
            }

            wp_send_json_success( array(
                'html'    => $content,
                'css_url' => $css_url,
                'js_url'  => $js_url
            ) );
        } else {
            wp_send_json_error( 'Module file not found' );
        }
    }

}
