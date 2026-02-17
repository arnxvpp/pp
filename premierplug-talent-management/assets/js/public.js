/**
 * PremierPlug Talent Management - Public JavaScript
 */

(function($) {
    'use strict';

    if (typeof pptmData === 'undefined') {
        return;
    }

    $(document).ready(function() {

        // AJAX Talent Search
        $('#pptm-search-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $results = $('#pptm-search-results .pptm-talent-grid');
            var $loading = $('#pptm-search-loading');
            var $noResults = $('#pptm-search-no-results');

            var searchData = {
                action: 'pptm_search_talents',
                nonce: pptmData.nonce,
                search: $('#pptm-search-keyword').val(),
                category: $('#pptm-search-category').val(),
                skill: $('#pptm-search-skill').val()
            };

            // Show loading
            $results.empty();
            $noResults.hide();
            $loading.show();

            $.ajax({
                url: pptmData.ajaxurl,
                type: 'POST',
                data: searchData,
                success: function(response) {
                    $loading.hide();

                    if (response.success && response.data.length > 0) {
                        $.each(response.data, function(index, talent) {
                            var card = createTalentCard(talent);
                            $results.append(card);
                        });
                    } else {
                        $noResults.show();
                    }
                },
                error: function() {
                    $loading.hide();
                    $noResults.show();
                }
            });
        });

        // Create talent card HTML
        function createTalentCard(talent) {
            var $card = $('<div>', {
                'class': 'pptm-talent-card'
            });

            if (talent.thumbnail) {
                var $image = $('<div>', {
                    'class': 'pptm-talent-image'
                }).append(
                    $('<a>', {
                        'href': talent.permalink
                    }).append(
                        $('<img>', {
                            'src': talent.thumbnail,
                            'alt': talent.title
                        })
                    )
                );
                $card.append($image);
            }

            var $content = $('<div>', {
                'class': 'pptm-talent-content'
            });

            $content.append(
                $('<h3>', {
                    'class': 'pptm-talent-title'
                }).append(
                    $('<a>', {
                        'href': talent.permalink,
                        'text': talent.title
                    })
                )
            );

            if (talent.categories && talent.categories.length > 0) {
                $content.append(
                    $('<div>', {
                        'class': 'pptm-talent-categories',
                        'text': talent.categories.join(', ')
                    })
                );
            }

            if (talent.excerpt) {
                $content.append(
                    $('<div>', {
                        'class': 'pptm-talent-excerpt',
                        'html': talent.excerpt
                    })
                );
            }

            $content.append(
                $('<a>', {
                    'href': talent.permalink,
                    'class': 'pptm-talent-link',
                    'text': 'View Profile'
                })
            );

            $card.append($content);

            return $card;
        }

        // Auto-submit search on filter change
        $('#pptm-search-category, #pptm-search-skill').on('change', function() {
            $('#pptm-search-form').submit();
        });

    });

})(jQuery);
