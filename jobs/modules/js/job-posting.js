jQuery(document).ready(function($) {
    $('#jobs-posting-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=jobs_post_job&nonce=' + jobs_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    $('#jobs-posting-message').html('<p class="success">Job submitted successfully! It is now pending review.</p>');
                    $('#jobs-posting-form')[0].reset();
                } else {
                     $('#jobs-posting-message').html('<p class="error">' + response.data + '</p>');
                }
            }
        });
    });
});
