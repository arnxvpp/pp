(function($) {
    'use strict';

    $(document).ready(function() {
        $('.pptm-share-btn').on('click', function(e) {
            var $btn = $(this);
            var network = $btn.data('network');
            var postId = $btn.closest('.pptm-share-buttons').data('post-id');

            if (network === 'copy') {
                e.preventDefault();
                copyToClipboard($btn);
            } else {
                trackShare(postId, network);

                if (network !== 'email') {
                    e.preventDefault();
                    var url = $btn.attr('href');
                    openShareWindow(url, network);
                }
            }
        });

        function openShareWindow(url, network) {
            var width = 600;
            var height = 400;

            if (network === 'whatsapp' && /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                window.location.href = url;
                return;
            }

            var left = (screen.width / 2) - (width / 2);
            var top = (screen.height / 2) - (height / 2);

            window.open(
                url,
                'share-' + network,
                'width=' + width + ',height=' + height + ',top=' + top + ',left=' + left + ',toolbar=0,status=0'
            );
        }

        function copyToClipboard($btn) {
            var url = $btn.data('url');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(function() {
                    showCopySuccess($btn);
                }, function() {
                    fallbackCopyToClipboard(url, $btn);
                });
            } else {
                fallbackCopyToClipboard(url, $btn);
            }
        }

        function fallbackCopyToClipboard(text, $btn) {
            var $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();

            try {
                document.execCommand('copy');
                showCopySuccess($btn);
            } catch (err) {
                console.error('Copy failed', err);
            }

            $temp.remove();
        }

        function showCopySuccess($btn) {
            var originalText = $btn.find('span').text();

            $btn.addClass('copied');
            $btn.find('span').text(pptmSharing.copyText);

            setTimeout(function() {
                $btn.removeClass('copied');
                $btn.find('span').text(originalText);
            }, 2000);
        }

        function trackShare(postId, network) {
            if (!pptmSharing.ajaxurl) {
                return;
            }

            $.ajax({
                url: pptmSharing.ajaxurl,
                type: 'POST',
                data: {
                    action: 'pptm_track_share',
                    nonce: pptmSharing.nonce,
                    post_id: postId,
                    network: network
                },
                success: function(response) {
                    if (window.gtag) {
                        gtag('event', 'share', {
                            'method': network,
                            'content_type': 'article',
                            'item_id': postId
                        });
                    }

                    if (window.dataLayer) {
                        window.dataLayer.push({
                            'event': 'social_share',
                            'social_network': network,
                            'post_id': postId
                        });
                    }
                }
            });
        }
    });

})(jQuery);
