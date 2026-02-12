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

	$(document).on('submit', '#jobs-posting-form', function(e) {
		e.preventDefault();
		var $form = $(this);
		var $message = $('#jobs-posting-message');
		var $btn = $form.find('button[type="submit"]');

		$btn.prop('disabled', true).text('Submitting...');
		$message.html('');

		var formData = $form.serialize();
		formData += '&action=jobs_submit_job&nonce=' + jobs_ajax.nonce;

		$.ajax({
			url: jobs_ajax.ajax_url,
			type: 'POST',
			data: formData,
			success: function(response) {
				if (response.success) {
					$message.html('<p style="color: green;">' + response.data + '</p>');
					$form[0].reset();
				} else {
					$message.html('<p style="color: red;">' + response.data + '</p>');
				}
			},
			error: function() {
				$message.html('<p style="color: red;">An error occurred.</p>');
			},
			complete: function() {
				$btn.prop('disabled', false).text('Submit Job');
			}
		});
	});

	// Handle Job Search
	var searchTimeout;
	$(document).on('submit change input', '#jobs-search-form', function(e) {
		if (e.type === 'submit') {
			e.preventDefault();
		}

		clearTimeout(searchTimeout);
		searchTimeout = setTimeout(function() {
			var $form = $('#jobs-search-form');
			var formData = $form.serialize();
			formData += '&action=jobs_search_jobs';
			if (typeof jobs_ajax !== 'undefined' && jobs_ajax.nonce) {
				formData += '&nonce=' + jobs_ajax.nonce;
			}

			$('#jobs-search-results').css('opacity', '0.5');

			$.ajax({
				url: jobs_ajax.ajax_url,
				type: 'POST',
				data: formData,
				success: function(response) {
					if (response.success) {
						$('#jobs-search-results').html(response.data);
					} else {
						$('#jobs-search-results').html('<p>' + response.data + '</p>');
					}
				},
				error: function() {
					$('#jobs-search-results').html('<p>Error loading jobs.</p>');
				},
				complete: function() {
					$('#jobs-search-results').css('opacity', '1');
				}
			});
		}, 500);
	});

	// Handle CV Save
	$(document).on('submit', '#jobs-cv-form', function(e) {
		e.preventDefault();
		var $form = $(this);
		var $message = $('#jobs-cv-message');
		var $btn = $form.find('button[type="submit"]');

		$btn.prop('disabled', true).text('Saving...');
		$message.html('');

		var formData = $form.serialize();
		formData += '&action=jobs_save_cv&nonce=' + jobs_ajax.nonce;

		$.ajax({
			url: jobs_ajax.ajax_url,
			type: 'POST',
			data: formData,
			success: function(response) {
				if (response.success) {
					$message.html('<p style="color: green;">' + response.data + '</p>');
				} else {
					$message.html('<p style="color: red;">' + response.data + '</p>');
				}
			},
			error: function() {
				$message.html('<p style="color: red;">An error occurred.</p>');
			},
			complete: function() {
				$btn.prop('disabled', false).text('Save CV');
			}
		});
	});

	// Handle Company Profile Save
	$(document).on('submit', '#jobs-company-form', function(e) {
		e.preventDefault();
		var $form = $(this);
		var $message = $('#jobs-company-message');
		var $btn = $form.find('button[type="submit"]');

		$btn.prop('disabled', true).text('Saving...');
		$message.html('');

		var formData = new FormData(this);
		formData.append('action', 'jobs_save_company_profile');
		formData.append('nonce', jobs_ajax.nonce);

		$.ajax({
			url: jobs_ajax.ajax_url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(response) {
				if (response.success) {
					$message.html('<p style="color: green;">' + response.data + '</p>');
				} else {
					$message.html('<p style="color: red;">' + response.data + '</p>');
				}
			},
			error: function() {
				$message.html('<p style="color: red;">An error occurred.</p>');
			},
			complete: function() {
				$btn.prop('disabled', false).text('Save Profile');
			}
		});
	});

	// Repeater Logic
	$(document).on('click', '.jobs-add-row', function() {
		var section = $(this).data('section');
		var container = $('#jobs-' + section + '-container');
		var template = $('#tmpl-' + section).html();
		var index = new Date().getTime();

		template = template.replace(/INDEX/g, index);
		container.append(template);
	});

	$(document).on('click', '.jobs-remove-row', function() {
		$(this).closest('.jobs-repeater-row').remove();
	});

})( jQuery );
