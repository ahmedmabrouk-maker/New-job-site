jQuery(document).ready(function($) {
    // Live Job Search
    var searchTimeout;

    function performSearch() {
        var keyword = $('#jobs-search-input').val();
        var specialization = $('#jobs-filter-specialization').val();
        var category = $('#jobs-filter-category').val();
        var country = $('#jobs-filter-country').val();
        var city = $('#jobs-filter-city').val();

        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'jobs_search',
                nonce: jobs_ajax.nonce,
                keyword: keyword,
                specialization: specialization,
                category: category,
                country: country,
                city: city
            },
            beforeSend: function() {
                $('#jobs-search-results').addClass('loading');
            },
            success: function(response) {
                $('#jobs-search-results').removeClass('loading');
                if (response.success) {
                    $('#jobs-search-results').html(response.data);
                }
            }
        });
    }

    // Debounce text input
    $('#jobs-search-input').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });

    // Immediate search on select change
    $('#jobs-filter-specialization, #jobs-filter-category, #jobs-filter-country, #jobs-filter-city').on('change', function() {
        performSearch();
    });

    // Prevent form submit
    $('#jobs-search-form').on('submit', function(e) {
        e.preventDefault();
        performSearch();
    });

    // Initial search if fields have values (e.g. back button)
    if ($('#jobs-search-input').val() || $('#jobs-filter-specialization').val() || $('#jobs-filter-category').val() || $('#jobs-filter-country').val() || $('#jobs-filter-city').val()) {
         performSearch();
    }

    // Quick Apply Handler (Frontend)
    $(document).on('click', '.jobs-btn-apply', function(e) {
        e.preventDefault();
        var jobId = $(this).data('job-id');

        if (confirm('Apply for this job with your current profile?')) {
             $.ajax({
                url: jobs_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'jobs_apply_job',
                    nonce: jobs_ajax.nonce,
                    job_id: jobId
                },
                success: function(response) {
                    alert(response.data);
                }
            });
        }
    });

    // Favorite Toggle Handler
    $(document).on('click', '.jobs-btn-favorite', function(e) {
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
                     if (response.data.action === 'added') {
                         btn.text('Saved');
                         alert('Job added to favorites.');
                     } else {
                         btn.text('Save');
                         alert('Job removed from favorites.');
                     }
                } else {
                    alert(response.data);
                }
            }
        });
    });
});
