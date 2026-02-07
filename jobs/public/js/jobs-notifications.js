jQuery(document).ready(function($) {
    $('.jobs-notifications-trigger').on('click', function() {
        var module = 'notifications';

        // We can reuse the load logic from top-bar if exposed, or replicate it.
        // For consistency with asset loading, simulating a click on a hidden menu item is hacky but effective if item exists.
        // However, we can just perform the AJAX call and handle response exactly like top-bar.js

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
                    $('#jobs-modal-content').addClass('jobs-modal-dropdown').removeClass('jobs-modal-full'); // Notification is dropdown style

                    if (response.data.css_url) {
                         if (!$('link[href="' + response.data.css_url + '"]').length) {
                             $('<link>').attr({ rel: 'stylesheet', type: 'text/css', href: response.data.css_url }).appendTo('head');
                         }
                    }
                    if (response.data.js_url) {
                        if (!$('script[src="' + response.data.js_url + '"]').length) {
                             $.getScript(response.data.js_url);
                        }
                    }

                    $('#jobs-modal').fadeIn();
                }
            }
        });
    });
});
