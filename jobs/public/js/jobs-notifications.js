jQuery(document).ready(function($) {
    $('.jobs-notifications-trigger').on('click', function() {
        // Trigger the Support module which will have the Inbox tab
        // Or specific notification logic.
        // For now, let's load the Support module directly.
        var module = 'support';

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
                    // ... (Asset injection logic skipped for brevity, assumed handled by top-bar.js generic loader if reused)
                    // Since this is a custom trigger, we might need to manually trigger the asset loader or reuse the existing click handler.
                    // Let's just simulate a click on the support menu item if it exists, or duplicate the loader logic.
                    // Simpler: Trigger click on the hidden support list item.

                    var supportItem = $('.jobs-modules-list li[data-module="support"]');
                    if (supportItem.length) {
                        supportItem.click();
                    } else {
                        // Fallback if support not in menu (should be there)
                        alert('Support module not available.');
                    }
                }
            }
        });
    });
});
