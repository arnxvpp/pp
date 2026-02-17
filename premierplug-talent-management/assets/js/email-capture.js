(function($) {
    'use strict';

    if (typeof pptmEmailCapture === 'undefined') {
        return;
    }

    var popup = {
        el: null,
        shown: false,
        closed: false,

        init: function() {
            this.el = $('#pptm-email-popup');

            if (!this.el.length || !pptmEmailCapture.popup_enabled) {
                return;
            }

            if (this.checkCookie()) {
                return;
            }

            this.bindEvents();
            this.setupTrigger();
        },

        checkCookie: function() {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = cookies[i].trim();
                if (cookie.indexOf('pptm_popup_shown=') === 0) {
                    return true;
                }
            }
            return false;
        },

        setCookie: function() {
            var days = pptmEmailCapture.frequency_days;
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = 'pptm_popup_shown=1; expires=' + date.toUTCString() + '; path=/';
        },

        setupTrigger: function() {
            var self = this;
            var triggerType = pptmEmailCapture.trigger_type;
            var triggerValue = pptmEmailCapture.trigger_value;

            if (triggerType === 'exit') {
                $(document).on('mouseleave', function(e) {
                    if (e.clientY <= 0 && !self.shown && !self.closed) {
                        self.show();
                    }
                });
            } else if (triggerType === 'scroll') {
                var scrollFired = false;
                $(window).on('scroll', function() {
                    if (scrollFired || self.shown || self.closed) {
                        return;
                    }

                    var scrollPercent = ($(window).scrollTop() + $(window).height()) / $(document).height() * 100;

                    if (scrollPercent >= triggerValue) {
                        scrollFired = true;
                        self.show();
                    }
                });
            } else if (triggerType === 'time') {
                setTimeout(function() {
                    if (!self.shown && !self.closed) {
                        self.show();
                    }
                }, triggerValue * 1000);
            }
        },

        show: function() {
            this.shown = true;
            this.el.addClass('active');
            this.setCookie();
            $('body').css('overflow', 'hidden');

            if (window.gtag) {
                gtag('event', 'popup_shown', {
                    'event_category': 'email_capture'
                });
            }
        },

        hide: function() {
            this.el.removeClass('active');
            $('body').css('overflow', '');

            if (window.gtag) {
                gtag('event', 'popup_closed', {
                    'event_category': 'email_capture'
                });
            }
        },

        bindEvents: function() {
            var self = this;

            this.el.find('.pptm-popup-close').on('click', function() {
                self.closed = true;
                self.hide();
            });

            this.el.find('.pptm-popup-overlay').on('click', function() {
                self.closed = true;
                self.hide();
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.shown) {
                    self.closed = true;
                    self.hide();
                }
            });

            this.el.find('#pptm-popup-form').on('submit', function(e) {
                e.preventDefault();
                self.submitForm($(this));
            });
        },

        submitForm: function($form) {
            var self = this;
            var $email = $form.find('input[name="email"]');
            var $button = $form.find('.pptm-submit-button');
            var $message = $form.find('.pptm-form-message');

            $button.prop('disabled', true).text('Subscribing...');
            $message.removeClass('success error').hide();

            $.ajax({
                url: pptmEmailCapture.ajaxurl,
                type: 'POST',
                data: {
                    action: 'pptm_subscribe',
                    nonce: pptmEmailCapture.nonce,
                    email: $email.val()
                },
                success: function(response) {
                    if (response.success) {
                        $message.addClass('success').text(response.data.message).show();
                        $email.val('');

                        if (window.gtag) {
                            gtag('event', 'subscribe', {
                                'event_category': 'email_capture',
                                'method': 'popup'
                            });
                        }

                        setTimeout(function() {
                            self.hide();
                        }, 2000);
                    } else {
                        $message.addClass('error').text(response.data.message).show();
                    }
                },
                error: function() {
                    $message.addClass('error').text(pptmEmailCapture.messages.error).show();
                },
                complete: function() {
                    $button.prop('disabled', false).text(pptmEmailCapture.messages.button || 'Subscribe');
                }
            });
        }
    };

    $('.pptm-inline-email-form').each(function() {
        var $form = $(this);

        $form.on('submit', function(e) {
            e.preventDefault();

            var $email = $form.find('input[name="email"]');
            var $button = $form.find('.pptm-submit-button');
            var $message = $form.find('.pptm-form-message');

            $button.prop('disabled', true).text('Subscribing...');
            $message.removeClass('success error').hide();

            $.ajax({
                url: pptmEmailCapture.ajaxurl,
                type: 'POST',
                data: {
                    action: 'pptm_subscribe',
                    nonce: pptmEmailCapture.nonce,
                    email: $email.val()
                },
                success: function(response) {
                    if (response.success) {
                        $message.addClass('success').text(response.data.message).show();
                        $email.val('');

                        if (window.gtag) {
                            gtag('event', 'subscribe', {
                                'event_category': 'email_capture',
                                'method': 'inline'
                            });
                        }
                    } else {
                        $message.addClass('error').text(response.data.message).show();
                    }
                },
                error: function() {
                    $message.addClass('error').text('An error occurred. Please try again.').show();
                },
                complete: function() {
                    $button.prop('disabled', false).text($button.data('original-text') || 'Subscribe');
                }
            });
        });
    });

    $(document).ready(function() {
        popup.init();
    });

})(jQuery);
