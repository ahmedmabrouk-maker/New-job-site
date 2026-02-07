(function( $ ) {
	'use strict';

	// Make loadJobsModule global so it can be called from inline onclick handlers
	window.loadJobsModule = function( moduleName ) {
		$('#jobs-module-container').css('display', 'flex');
		$('#jobs-module-body').html('Loading ' + moduleName + '...');

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
					$('#jobs-module-body').html( '<p>Error: ' + response.data + '</p>' );
				}
			},
			error: function() {
				$('#jobs-module-body').html( '<p>Error loading module.</p>' );
			}
		});
	};

})( jQuery );
