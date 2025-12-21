(function($) {
    'use strict';

    $(document).ready(function() {
        var $trigger = $('#pptm_popup_trigger');
        var $triggerValue = $('input[name="pptm_popup_trigger_value"]');
        var $triggerUnit = $('#pptm_trigger_unit');

        function updateTriggerUnit() {
            var triggerType = $trigger.val();

            if (triggerType === 'exit') {
                $triggerValue.closest('#pptm_trigger_value_container').hide();
            } else {
                $triggerValue.closest('#pptm_trigger_value_container').show();

                if (triggerType === 'scroll') {
                    $triggerUnit.text('%');
                    $triggerValue.attr('max', 100);
                } else if (triggerType === 'time') {
                    $triggerUnit.text('seconds');
                    $triggerValue.attr('max', 300);
                }
            }
        }

        $trigger.on('change', updateTriggerUnit);
        updateTriggerUnit();

        $('textarea.code').each(function() {
            if (typeof wp !== 'undefined' && wp.codeEditor) {
                var settings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
                settings.codemirror = _.extend(
                    {},
                    settings.codemirror,
                    {
                        mode: 'htmlmixed',
                        lineNumbers: true,
                        lineWrapping: true
                    }
                );
                wp.codeEditor.initialize($(this), settings);
            }
        });

        $('.pptm-settings-wrap form').on('submit', function() {
            var $form = $(this);
            var $button = $form.find('input[type="submit"]');

            $button.prop('disabled', true);

            setTimeout(function() {
                $button.prop('disabled', false);
            }, 3000);
        });
    });

})(jQuery);
