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
