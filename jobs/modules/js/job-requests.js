jQuery(document).ready(function($) {
    $('.jobs-btn-approve').on('click', function() {
        updateJobStatus($(this).data('id'), 'publish');
    });

    $('.jobs-btn-reject').on('click', function() {
        if(confirm('Are you sure you want to reject this job?')) {
            updateJobStatus($(this).data('id'), 'trash');
        }
    });

    function updateJobStatus(id, status) {
        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'jobs_update_job_status',
                nonce: jobs_ajax.nonce,
                job_id: id,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    $('#job-review-' + id).slideUp(function() { $(this).remove(); });
                } else {
                    alert(response.data);
                }
            }
        });
    }
});
