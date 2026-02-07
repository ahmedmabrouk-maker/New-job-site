<?php

class Jobs_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

    public function enqueue_styles() {
        // Enqueue admin styles if needed
    }

    public function enqueue_scripts() {
        // Enqueue admin scripts if needed
    }

    public function register_settings() {
        register_setting( 'jobs_options', 'jobs_logo_url' );
        register_setting( 'jobs_options', 'jobs_search_title' );

        add_settings_section(
            'jobs_general_section',
            'General Settings',
            null,
            'jobs-settings'
        );

        add_settings_field(
            'jobs_logo_url',
            'Logo URL',
            array( $this, 'render_logo_field' ),
            'jobs-settings',
            'jobs_general_section'
        );

         add_settings_field(
            'jobs_search_title',
            'Search Engine Title',
            array( $this, 'render_search_title_field' ),
            'jobs-settings',
            'jobs_general_section'
        );
    }

    public function render_logo_field() {
        $logo = get_option( 'jobs_logo_url' );
        echo '<input type="text" name="jobs_logo_url" value="' . esc_attr( $logo ) . '" class="regular-text">';
        echo '<p class="description">Enter the full URL of your logo image.</p>';
    }

     public function render_search_title_field() {
        $title = get_option( 'jobs_search_title' );
        echo '<input type="text" name="jobs_search_title" value="' . esc_attr( $title ) . '" class="regular-text">';
    }

    public function add_admin_menu() {
        add_menu_page(
            'Jobs Settings',
            'Jobs',
            'manage_options',
            'jobs-settings',
            array( $this, 'render_settings_page' ),
            'dashicons-businessman',
            6
        );
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>Jobs Plugin Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'jobs_options' );
                do_settings_sections( 'jobs-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

}
