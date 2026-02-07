jQuery(document).ready(function($) {

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

    // Pagination Click
    $(document).on('click', '.jobs-pagination .page-link', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        performSearch(page);
    });

    // Modified performSearch to accept page
    var searchTimeout;

    function performSearch(page = 1) {
        var keyword = $('#jobs-search-input').val();
        var specialization = $('#jobs-filter-specialization').val();
        var category = $('#jobs-filter-category').val();
        var country = $('#jobs-filter-country').val();
        var city = $('#jobs-filter-city').val();
        var lat = $('input[name="lat"]').val();
        var lng = $('input[name="lng"]').val();

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
                city: city,
                lat: lat,
                lng: lng,
                paged: page
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

    // Initial search if fields have values (e.g. back button)
    if ($('#jobs-search-input').val() || $('#jobs-filter-specialization').val() || $('#jobs-filter-category').val() || $('#jobs-filter-country').val() || $('#jobs-filter-city').val()) {
         performSearch();
    } else {
        // Attempt geolocation
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                $('#jobs-search-form').append('<input type="hidden" name="lat" value="' + position.coords.latitude + '">');
                $('#jobs-search-form').append('<input type="hidden" name="lng" value="' + position.coords.longitude + '">');
                performSearch(); // Re-trigger search with location
            });
        }
    }

    // Quick Apply Toggle
    $(document).on('click', '.jobs-btn-apply-toggle', function(e) {
        e.preventDefault();
        var jobId = $(this).data('job-id');
        $('#quick-apply-' + jobId).slideToggle();
    });

    // Quick Apply Submit
    $(document).on('submit', '.jobs-apply-form', function(e) {
        e.preventDefault();
        var form = $(this);
        var jobId = form.data('job-id');
        var btn = form.find('button[type="submit"]');

        btn.prop('disabled', true).text('Applying...');

        $.ajax({
            url: jobs_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'jobs_apply_job',
                nonce: jobs_ajax.nonce,
                job_id: jobId
            },
            success: function(response) {
                btn.prop('disabled', false).text('Confirm Application');
                if (response.success) {
                    alert(response.data);
                    $('#quick-apply-' + jobId).slideUp();
                } else {
                    alert(response.data);
                }
            }
        });
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
