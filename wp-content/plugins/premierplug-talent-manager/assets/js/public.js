/**
 * PremierPlug Talent Manager - Public JavaScript
 * Handles filtering, AJAX, and form submissions
 */

(function($) {
    'use strict';

    const TalentManager = {

        init: function() {
            this.bindEvents();
            this.initLightbox();
        },

        bindEvents: function() {
            $('#pptm-apply-filters').on('click', this.applyFilters.bind(this));
            $('#pptm-clear-filters').on('click', this.clearFilters.bind(this));
            $('#pptm-search').on('keyup', _.debounce(this.applyFilters.bind(this), 500));
            $('#pptm-inquiry-form').on('submit', this.submitInquiry.bind(this));

            $(document).on('click', '.pptm-pagination a', this.handlePagination.bind(this));
        },

        applyFilters: function(e) {
            if (e) {
                e.preventDefault();
            }

            const search = $('#pptm-search').val();
            const segments = [];
            $('input[name="segments[]"]:checked').each(function() {
                segments.push($(this).val());
            });
            const availability = $('#pptm-availability').val();

            const data = {
                action: 'pptm_filter_talents',
                nonce: pptmData.filter_nonce,
                search: search,
                segments: segments,
                availability: availability,
                per_page: 12,
                page: 1
            };

            this.loadTalents(data);
        },

        clearFilters: function(e) {
            e.preventDefault();

            $('#pptm-search').val('');
            $('input[name="segments[]"]').prop('checked', false);
            $('#pptm-availability').val('');

            this.applyFilters();
        },

        handlePagination: function(e) {
            e.preventDefault();

            const url = $(e.currentTarget).attr('href');
            const page = new URL(url).searchParams.get('paged') || 1;

            const search = $('#pptm-search').val();
            const segments = [];
            $('input[name="segments[]"]:checked').each(function() {
                segments.push($(this).val());
            });
            const availability = $('#pptm-availability').val();

            const data = {
                action: 'pptm_filter_talents',
                nonce: pptmData.filter_nonce,
                search: search,
                segments: segments,
                availability: availability,
                per_page: 12,
                page: page
            };

            this.loadTalents(data);

            $('html, body').animate({
                scrollTop: $('#talent-grid-section').offset().top - 100
            }, 500);
        },

        loadTalents: function(data) {
            $('#pptm-loading').show();
            $('#pptm-talent-grid').css('opacity', '0.5');

            $.ajax({
                url: pptmData.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        $('#pptm-talent-grid').html(response.data.html);
                        $('#pptm-results-count').text(response.data.found + ' Talents Found');

                        if (response.data.max_pages > 1) {
                            this.updatePagination(data.page, response.data.max_pages);
                        } else {
                            $('.pptm-pagination').remove();
                        }

                        if (typeof AOS !== 'undefined') {
                            AOS.refresh();
                        }
                    }

                    $('#pptm-loading').hide();
                    $('#pptm-talent-grid').css('opacity', '1');
                }.bind(this),
                error: function() {
                    $('#pptm-loading').hide();
                    $('#pptm-talent-grid').css('opacity', '1');
                    alert('Failed to load talents. Please try again.');
                }
            });
        },

        updatePagination: function(currentPage, maxPages) {
            let paginationHtml = '<div class="pptm-pagination">';

            if (currentPage > 1) {
                paginationHtml += '<a href="?paged=' + (currentPage - 1) + '">←</a>';
            }

            for (let i = 1; i <= maxPages; i++) {
                if (i === parseInt(currentPage)) {
                    paginationHtml += '<span class="current">' + i + '</span>';
                } else {
                    paginationHtml += '<a href="?paged=' + i + '">' + i + '</a>';
                }
            }

            if (currentPage < maxPages) {
                paginationHtml += '<a href="?paged=' + (parseInt(currentPage) + 1) + '">→</a>';
            }

            paginationHtml += '</div>';

            $('.pptm-pagination').remove();
            $('#talent-grid-section .gutter-container').append(paginationHtml);
        },

        submitInquiry: function(e) {
            e.preventDefault();

            const form = $(e.currentTarget);
            const submitBtn = form.find('button[type="submit"]');
            const responseDiv = $('#pptm-inquiry-response');

            submitBtn.prop('disabled', true).text('Sending...');

            const data = {
                action: 'pptm_submit_inquiry',
                nonce: pptmData.inquiry_nonce,
                talent_id: form.find('input[name="talent_id"]').val(),
                name: form.find('input[name="name"]').val(),
                email: form.find('input[name="email"]').val(),
                phone: form.find('input[name="phone"]').val(),
                organization: form.find('input[name="organization"]').val(),
                country: form.find('select[name="country"]').val(),
                message: form.find('textarea[name="message"]').val()
            };

            $.ajax({
                url: pptmData.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        responseDiv.removeClass('error').addClass('success');
                        responseDiv.html('<p>' + response.data.message + '</p>').slideDown();
                        form[0].reset();
                    } else {
                        responseDiv.removeClass('success').addClass('error');
                        responseDiv.html('<p>' + response.data.message + '</p>').slideDown();
                    }

                    submitBtn.prop('disabled', false).text('Submit');

                    setTimeout(function() {
                        responseDiv.slideUp();
                    }, 5000);
                },
                error: function() {
                    responseDiv.removeClass('success').addClass('error');
                    responseDiv.html('<p>An error occurred. Please try again.</p>').slideDown();
                    submitBtn.prop('disabled', false).text('Submit');
                }
            });
        },

        initLightbox: function() {
            $(document).on('click', '.portfolio-image-link', function(e) {
                e.preventDefault();
            });
        }
    };

    $(document).ready(function() {
        TalentManager.init();
    });

})(jQuery);
