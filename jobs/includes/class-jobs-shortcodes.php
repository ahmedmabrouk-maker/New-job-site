<?php

class Jobs_Shortcodes {

	public function register_shortcodes() {
		add_shortcode( 'jobs_search', array( $this, 'render_search' ) );
		add_shortcode( 'jobs_login', array( $this, 'render_login' ) );
		add_shortcode( 'jobs_register', array( $this, 'render_register' ) );
		add_shortcode( 'jobs_admin_panel', array( $this, 'render_admin_panel' ) );
	}

    public function process_registration() {
        if ( isset( $_POST['jobs_register'] ) ) {
            $username = sanitize_user( $_POST['username'] );
            $email    = sanitize_email( $_POST['email'] );
            $password = $_POST['password'];
            $role     = sanitize_text_field( $_POST['role'] );

            if ( username_exists( $username ) || email_exists( $email ) ) {
                 // Error handling will be passed to the shortcode via query var or session if needed.
                 // For simplicity in this scope, we handle success redirect here.
                 // In a full implementation, we'd store errors in a transient/session.
            } else {
                $valid_roles = array( 'job_seeker', 'employer' );
                if ( ! in_array( $role, $valid_roles ) ) {
                    $role = 'job_seeker';
                }

                $user_id = wp_create_user( $username, $password, $email );
                if ( ! is_wp_error( $user_id ) ) {
                    $user = new WP_User( $user_id );
                    $user->set_role( $role );

                    // Auto login
                    wp_set_current_user( $user_id );
                    wp_set_auth_cookie( $user_id );

                    // Redirect to previous page or home
                     wp_redirect( home_url() );
                     exit;
                }
            }
        }
    }

	public function render_search( $atts ) {
		ob_start();
        $logo_url = get_option( 'jobs_logo_url' );
        $search_title = get_option( 'jobs_search_title', 'JOBS' );
		?>
		<div class="jobs-search-container">
			<div class="jobs-header-logo">
                <?php if ( $logo_url ) : ?>
                    <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $search_title ); ?>" class="jobs-logo">
                <?php else : ?>
				    <div class="site-logo"><?php echo esc_html( $search_title ); ?></div>
                <?php endif; ?>
			</div>
			<form class="jobs-search-form" action="" method="get" id="jobs-search-form">
				<input type="text" name="jobs_query" id="jobs-search-input" placeholder="Search jobs...">

                <?php
                // Specialization
                $specializations = get_terms( array( 'taxonomy' => 'job_specialization', 'hide_empty' => false ) );
                echo '<select name="specialization" id="jobs-filter-specialization"><option value="">Specialization</option>';
                if ( ! empty( $specializations ) && ! is_wp_error( $specializations ) ) {
                    foreach ( $specializations as $term ) {
                        echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                    }
                }
                echo '</select>';

                // Category
                $categories = get_terms( array( 'taxonomy' => 'job_category', 'hide_empty' => false ) );
                echo '<select name="category" id="jobs-filter-category"><option value="">Category</option>';
                if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
                    foreach ( $categories as $term ) {
                        echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                    }
                }
                echo '</select>';

                // Country
                $countries = get_terms( array( 'taxonomy' => 'job_country', 'hide_empty' => false ) );
                echo '<select name="country" id="jobs-filter-country"><option value="">Country</option>';
                 if ( ! empty( $countries ) && ! is_wp_error( $countries ) ) {
                    foreach ( $countries as $term ) {
                        echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                    }
                }
                echo '</select>';

                // City
                $cities = get_terms( array( 'taxonomy' => 'job_city', 'hide_empty' => false ) );
                echo '<select name="city" id="jobs-filter-city"><option value="">City</option>';
                 if ( ! empty( $cities ) && ! is_wp_error( $cities ) ) {
                    foreach ( $cities as $term ) {
                        echo '<option value="' . esc_attr( $term->slug ) . '">' . esc_html( $term->name ) . '</option>';
                    }
                }
                echo '</select>';
                ?>

				<button type="submit" id="jobs-search-submit">Search</button>
			</form>
		</div>

        <div class="jobs-search-results" id="jobs-search-results">
        <?php
        if ( isset( $_GET['jobs_query'] ) ) {
            $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
            $args = array(
                'post_type' => 'job',
                's' => sanitize_text_field( $_GET['jobs_query'] ),
                'paged' => $paged,
                'tax_query' => array( 'relation' => 'AND' )
            );

            if ( ! empty( $_GET['specialization'] ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'job_specialization',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $_GET['specialization'] ),
                );
            }
            if ( ! empty( $_GET['country'] ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'job_country',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $_GET['country'] ),
                );
            }
            if ( ! empty( $_GET['city'] ) ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'job_city',
                    'field'    => 'slug',
                    'terms'    => sanitize_text_field( $_GET['city'] ),
                );
            }

            $jobs_query = new WP_Query( $args );

            if ( $jobs_query->have_posts() ) {
                echo '<ul class="jobs-list">';
                while ( $jobs_query->have_posts() ) {
                    $jobs_query->the_post();
                    echo '<li>';
                    echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
                    echo '<div class="job-excerpt">' . get_the_excerpt() . '</div>';
                    echo '</li>';
                }
                echo '</ul>';

                // Pagination
                echo paginate_links( array(
                    'total' => $jobs_query->max_num_pages
                ) );

                wp_reset_postdata();
            } else {
                echo '<p>No jobs found.</p>';
            }
        }
        ?>
        </div>
		<?php
		return ob_get_clean();
	}

	public function render_login( $atts ) {
        if ( is_user_logged_in() ) {
            return '<p>You are already logged in.</p>';
        }
		ob_start();
        wp_login_form();
		return ob_get_clean();
	}

	public function render_register( $atts ) {
        if ( is_user_logged_in() ) {
            return '<p>You are already logged in.</p>';
        }

        // Ideally display errors from transient here
        $message = '';
        if ( isset( $_POST['jobs_register'] ) ) {
             // If we are here, it means registration failed (e.g. user exists) or hasn't redirected.
             // Re-check simple errors for display purposes since we are in the loop now.
            $username = sanitize_user( $_POST['username'] );
            $email    = sanitize_email( $_POST['email'] );
            if ( username_exists( $username ) || email_exists( $email ) ) {
                 $message = '<p class="error">Username or Email already exists.</p>';
            }
        }

		ob_start();
        echo $message;
		?>
        <form class="jobs-register-form" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role">
                <option value="job_seeker">Job Seeker</option>
                <option value="employer">Employer</option>
            </select>
            <button type="submit" name="jobs_register">Register</button>
        </form>
        <?php
		return ob_get_clean();
	}

	public function render_admin_panel( $atts ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return 'Access Denied';
		}
		ob_start();
		?>
		<div class="jobs-admin-panel">
			<h1>Admin Control Panel</h1>
			<nav>
				<ul>
					<li>Reports</li>
					<li>Activity Log</li>
                    <li>User Management</li>
                    <li>Articles</li>
                    <li>Design</li>
                    <li>Support</li>
                    <li>Permissions</li>
                    <li>Ads</li>
				</ul>
			</nav>
            <div class="jobs-admin-content">
                <!-- Content loaded here -->
            </div>
		</div>
		<?php
		return ob_get_clean();
	}

}
