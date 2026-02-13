(function( $ ) {
	'use strict';

	// Make loadJobsModule global so it can be called from inline onclick handlers
	window.loadJobsModule = function( moduleName ) {
		$('#jobs-module-container').css('display', 'flex');
		// Clear previous content or show loader
		$('#jobs-module-body').html('<div style="text-align:center; padding: 40px; color: #666;">Loading ' + moduleName.replace('-', ' ') + '...</div>');

		$.ajax({
			url: jobs_ajax.ajax_url,
			type: 'POST',
			data: {
				action: 'jobs_load_module',
				module: moduleName,
				nonce: jobs_ajax.nonce
			},
			success: function( response ) {
				if ( response.success ) {
					$('#jobs-module-body').html( response.data );
				} else {
					$('#jobs-module-body').html( '<div class="notice notice-error"><p>Error: ' + response.data + '</p></div>' );
				}
			},
			error: function() {
				$('#jobs-module-body').html( '<div class="notice notice-error"><p>Error loading module. Please try again.</p></div>' );
			}
		});
	};

	$(document).ready(function() {
		// Close modal when clicking outside content
		$(document).on('click', '.jobs-module-modal', function(e) {
			if (e.target === this) {
				$(this).fadeOut(200);
			}
		});

		// Close modal on escape key
		$(document).on('keydown', function(e) {
			if (e.key === 'Escape') {
				$('.jobs-module-modal').fadeOut(200);
				$('.jobs-dropdown-menu').removeClass('active');
			}
		});

		// Toggle dropdown
		$('.jobs-menu-trigger').on('click', function(e) {
			e.stopPropagation();
			$('.jobs-dropdown-menu').toggleClass('active');
		});

		// Close dropdown when clicking outside
		$(document).on('click', function(e) {
			if (!$(e.target).closest('.jobs-top-bar-content').length) {
				$('.jobs-dropdown-menu').removeClass('active');
			}
		});
	});

})( jQuery );
