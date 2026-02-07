jQuery(document).ready(function($) {
    // Toggle Modules Dropdown
    $('#jobs-modules-trigger').on('click', function(e) {
        e.stopPropagation();
        $('#jobs-modules-list').toggle();
        $('#jobs-user-menu').hide();
    });

    // Toggle User Menu
    $('#jobs-user-trigger').on('click', function(e) {
        e.stopPropagation();
        $('#jobs-user-menu').toggle();
        $('#jobs-modules-list').hide();
    });

    // Close dropdowns on click outside
    $(document).on('click', function() {
        $('#jobs-modules-list').hide();
        $('#jobs-user-menu').hide();
    });

    // Close Modal
    $('.jobs-close, .jobs-modal').on('click', function(e) {
        if (e.target !== this && !$(e.target).hasClass('jobs-close')) return;
        $('#jobs-modal').fadeOut();
    });

    // Load Module
    $('.jobs-modules-list li').on('click', function() {
        var module = $(this).data('module');
        if (!module) return;

        // AJAX call to load module
        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'jobs_load_module',
                nonce: jobs_ajax.nonce,
                module: module
            },
            success: function(response) {
                if (response.success) {
                    $('#jobs-modal-body').html(response.data.html);

                    // Determine Modal Type (Default to Full, check for Dropdown hint)
                    // We can check the module name or look for a specific class in the returned HTML
                    var modalContent = $('#jobs-modal-content');
                    modalContent.removeClass('jobs-modal-full jobs-modal-dropdown');

                    var dropdownModules = ['cv-resume', 'favorites', 'drafts', 'support', 'settings', 'notifications'];

                    if (dropdownModules.includes(module)) {
                        modalContent.addClass('jobs-modal-dropdown');
                    } else {
                        modalContent.addClass('jobs-modal-full');
                    }

                    // Inject CSS if present
                    if (response.data.css_url) {
                         if (!$('link[href="' + response.data.css_url + '"]').length) {
                             $('<link>').attr({
                                 rel: 'stylesheet',
                                 type: 'text/css',
                                 href: response.data.css_url
                             }).appendTo('head');
                         }
                    }

                    // Inject JS if present
                    if (response.data.js_url) {
                        // Check if already loaded
                        if (!$('script[src="' + response.data.js_url + '"]').length) {
                             $.getScript(response.data.js_url);
                        }
                    }

                    $('#jobs-modal').fadeIn();
                } else {
                    alert('Error loading module: ' + (response.data || 'Unknown error'));
                }
            },
            error: function() {
                alert('AJAX Error');
            }
        });
    });
});
