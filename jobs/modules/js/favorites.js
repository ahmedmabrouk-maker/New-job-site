jQuery(document).ready(function($) {
    $('.jobs-btn-favorite').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var jobId = btn.data('job-id');
        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'jobs_toggle_favorite',
                nonce: jobs_ajax.nonce,
                job_id: jobId
            },
            success: function(response) {
                if (response.success) {
                     // In the list view, we just remove the card
                     btn.closest('.job-card').fadeOut();
                }
            }
        });
    });
});
