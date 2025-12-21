jQuery(document).ready(function($) {
    var modal = $('#add-type-modal');
    var closeBtn = $('.close');
    var cancelBtn = $('.cancel-modal');

    $('.add-new-type').on('click', function() {
        var category = $(this).data('category');
        $('#modal-category').val(category);

        var categoryLabel = '';
        if (category === 'articles') categoryLabel = 'Article Type';
        if (category === 'talent_types') categoryLabel = 'Talent Type';
        if (category === 'service_types') categoryLabel = 'Service Type';

        modal.find('h2').text('Add New ' + categoryLabel);
        modal.show();

        $('#type_id').val('').focus();
        $('#label').val('');
        $('#singular').val('');
        $('#slug').val('');
        $('#icon').val('dashicons-admin-post');
    });

    closeBtn.on('click', function() {
        modal.hide();
    });

    cancelBtn.on('click', function() {
        modal.hide();
    });

    $(window).on('click', function(event) {
        if (event.target == modal[0]) {
            modal.hide();
        }
    });

    $('#label').on('blur', function() {
        var label = $(this).val();
        if (label && !$('#type_id').val()) {
            $('#type_id').val(label.toLowerCase().replace(/[^a-z0-9]+/g, '_'));
        }
        if (label && !$('#slug').val()) {
            $('#slug').val(label.toLowerCase().replace(/[^a-z0-9]+/g, '-'));
        }
    });

    $('#singular').on('blur', function() {
        var singular = $(this).val();
        if (singular && !$('#type_id').val()) {
            $('#type_id').val(singular.toLowerCase().replace(/[^a-z0-9]+/g, '_'));
        }
        if (singular && !$('#slug').val()) {
            $('#slug').val(singular.toLowerCase().replace(/[^a-z0-9]+/g, '-') + 's');
        }
    });
});
