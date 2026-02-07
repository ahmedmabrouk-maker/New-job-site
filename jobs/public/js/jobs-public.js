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

    // --- Action Handlers (Moved from Modules) ---

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
                    if (response.success) {
                        alert(response.data);
                    } else {
                        alert(response.data);
                    }
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
                     // Update UI based on context
                     if (response.data.action === 'added') {
                         btn.text('Saved');
                         alert('Job added to favorites.');
                     } else {
                         btn.text('Save');
                         // If we are in the favorites module (list view), remove the card
                         if (btn.closest('.jobs-module-container').length) {
                             btn.closest('.job-card').fadeOut();
                         } else {
                             alert('Job removed from favorites.');
                         }
                     }
                } else {
                    alert(response.data);
                }
            }
        });
    });

    // Job Review Queue Handlers (Approve/Reject)
    $(document).on('click', '.jobs-btn-approve', function() {
        updateJobStatus($(this).data('id'), 'publish');
    });

    $(document).on('click', '.jobs-btn-reject', function() {
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
