/**
 * PremierPlug Talent Manager - Admin JavaScript
 */

(function($) {
    'use strict';

    const TalentAdmin = {

        portfolioItemIndex: 0,

        init: function() {
            this.portfolioItemIndex = $('#pptm-portfolio-items .pptm-portfolio-item').length;
            this.bindEvents();
        },

        bindEvents: function() {
            $('#pptm-add-portfolio-item').on('click', this.addPortfolioItem.bind(this));
            $(document).on('click', '.pptm-remove-portfolio-item', this.removePortfolioItem.bind(this));
            $(document).on('click', '.pptm-upload-media', this.uploadMedia.bind(this));
        },

        addPortfolioItem: function(e) {
            e.preventDefault();

            const index = this.portfolioItemIndex++;
            const template = `
                <div class="pptm-portfolio-item" data-index="${index}">
                    <div class="pptm-portfolio-item-header">
                        <h4>Portfolio Item</h4>
                        <button type="button" class="button pptm-remove-portfolio-item">Remove</button>
                    </div>
                    <table class="form-table">
                        <tr>
                            <th><label>Type</label></th>
                            <td>
                                <select name="pptm_portfolio[${index}][type]">
                                    <option value="image">Image</option>
                                    <option value="video">Video</option>
                                    <option value="audio">Audio</option>
                                    <option value="document">Document</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Title</label></th>
                            <td><input type="text" name="pptm_portfolio[${index}][title]" class="regular-text" /></td>
                        </tr>
                        <tr>
                            <th><label>File URL</label></th>
                            <td>
                                <input type="url" name="pptm_portfolio[${index}][url]" class="large-text" />
                                <button type="button" class="button pptm-upload-media">Upload/Select Media</button>
                            </td>
                        </tr>
                        <tr>
                            <th><label>Description</label></th>
                            <td><textarea name="pptm_portfolio[${index}][description]" rows="3" class="large-text"></textarea></td>
                        </tr>
                    </table>
                </div>
            `;

            $('#pptm-portfolio-items').append(template);
        },

        removePortfolioItem: function(e) {
            e.preventDefault();

            if (confirm('Are you sure you want to remove this portfolio item?')) {
                $(e.currentTarget).closest('.pptm-portfolio-item').remove();
            }
        },

        uploadMedia: function(e) {
            e.preventDefault();

            const button = $(e.currentTarget);
            const urlField = button.prev('input[type="url"]');

            const frame = wp.media({
                title: 'Select or Upload Media',
                button: {
                    text: 'Use this media'
                },
                multiple: false
            });

            frame.on('select', function() {
                const attachment = frame.state().get('selection').first().toJSON();
                urlField.val(attachment.url);
            });

            frame.open();
        }
    };

    $(document).ready(function() {
        TalentAdmin.init();
    });

})(jQuery);
