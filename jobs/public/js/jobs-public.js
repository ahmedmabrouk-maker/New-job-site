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
                    $('#jobs-modal-body').html(response.data);
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

    // Close Modal
    $('.jobs-close, .jobs-modal').on('click', function(e) {
        if (e.target !== this && !$(e.target).hasClass('jobs-close')) return;
        $('#jobs-modal').fadeOut();
    });
});
