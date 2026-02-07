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
             <a href="<?php the_permalink(); ?>" class="job-view-btn">View Job</a>
        </div>
        <?php
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
