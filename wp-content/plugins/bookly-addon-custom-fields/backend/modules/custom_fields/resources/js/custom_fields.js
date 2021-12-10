jQuery(function($) {

    var $fields = $("#bookly-custom-fields"),
        $cf_per_service = $('#bookly_custom_fields_per_service'),
        $mergeRepeatingBlock = $('#bookly-js-merge-repeating'),
        $cf_merge_repeating = $('#bookly_custom_fields_merge_repeating')
    ;

    $fields.sortable({
        axis   : 'y',
        handle : '.bookly-js-handle'
    });

    $cf_merge_repeating.data('default', $cf_merge_repeating.val());

    $cf_per_service
        .data('default', $cf_per_service.val())
        .change(function() {
            $mergeRepeatingBlock.toggle(this.value == '1');
            $('.bookly-js-services').booklyDropdown(this.value == '1' ? 'show' : 'hide');
        });

    /**
     * Build initial fields.
     */
    restoreFields();

    /**
     * On "Add new field" button click.
     */
    $('#bookly-js-add-fields').on('click', 'button', function() {
        addField($(this).data('type'));
    });

    /**
     * On "Add new item" button click.
     */
    $fields.on('click', 'button', function() {
        addItem($(this).prev('ul'), $(this).data('type'));
    });

    /**
     * Delete field or checkbox/radio button/drop-down option.
     */
    $fields.on('click', '.bookly-js-delete', function(e) {
        e.preventDefault();
        $(this).closest('li').fadeOut('fast', function() { $(this).remove(); });
    });

    /**
     * Submit custom fields form.
     */
    $('#ajax-send-custom-fields').on('click', function(e) {
        e.preventDefault();
        var ladda = Ladda.create(this),
            data = [];
        ladda.start();
        $fields.children('li').each(function() {
            var $this = $(this),
                field = {};
            switch ($this.data('type')) {
                case 'checkboxes':
                case 'radio-buttons':
                case 'drop-down':
                    field.items = [];
                    $this.find('ul.bookly-items li').each(function() {
                        field.items.push($(this).find('input').val());
                    });
                case 'textarea':
                case 'text-field':
                case 'file':
                case 'text-content':
                case 'captcha':
                    field.type     = $this.data('type');
                    field.label    = $this.find('.bookly-label').val();
                    field.required = $this.find('.bookly-required').prop('checked');
                    field.id       = $this.data('bookly-field-id');
                    field.services = $this.find('.bookly-js-services').booklyDropdown('getSelected');
            }
            data.push(field);
        });
        $.ajax({
            type      : 'POST',
            url       : ajaxurl,
            xhrFields : { withCredentials: true },
            data      : {
                action: 'bookly_custom_fields_save_custom_fields',
                csrf_token: BooklyCustomFieldsL10n.csrf_token,
                fields: JSON.stringify(data),
                per_service: $cf_per_service.val(),
                merge_repeating: $cf_merge_repeating.val()
            },
            complete  : function() {
                ladda.stop();
                booklyAlert({success : [BooklyCustomFieldsL10n.saved]});
            }
        });
    });

    /**
     * On 'Reset' click.
     */
    $('button[type=reset]').on('click', function() {
        $fields.empty();
        restoreFields();
    });

    /**
     * Add new field.
     *
     * @param type
     * @param id
     * @param label
     * @param required
     * @param services
     * @returns {*|jQuery}
     */
    function addField(type, id, label, required, services) {
        var $new_field = $('ul#bookly-templates > li[data-type=' + type + ']').clone();
        // Set id, label and required.
        if (typeof id == 'undefined') {
            id = Math.floor((Math.random() * 100000) + 1);
        }
        if (typeof label == 'undefined') {
            label = '';
        }
        if (typeof required == 'undefined') {
            required = false;
        }
        $new_field
            .hide()
            .data('bookly-field-id', id)
            .find('.bookly-required').prop({
                id      : 'required-' + id,
                checked : required
            })
                .next('label').attr('for', 'required-' + id).end()
            .end()
            .find('.bookly-label').val(label).end()
            .find('.bookly-js-services')
                .booklyDropdown()
                .booklyDropdown($cf_per_service.val() == '1' ? 'show' : 'hide')
                .booklyDropdown('setSelected', services || [])
        ;
        // Add new field to the list.
        $fields.append($new_field);
        $new_field.fadeIn('fast');
        // Make it sortable.
        $new_field.find('ul.bookly-items').sortable({
            axis   : 'y',
            handle : '.bookly-js-handle'
        });
        // Set focus to label field.
        $new_field.find('.bookly-label').focus();

        return $new_field;
    }

    /**
     * Add new checkbox/radio button/drop-down option.
     *
     * @param $ul
     * @param type
     * @param value
     * @return {*|jQuery}
     */
    function addItem($ul, type, value) {
        var $new_item = $('ul#bookly-templates > li[data-type=' + type + ']').clone();
        if (typeof value != 'undefined') {
            $new_item.find('input').val(value);
        }
        $new_item.hide().appendTo($ul).fadeIn('fast').find('input').focus();

        return $new_item;
    }

    /**
     * Restore fields from BooklyCustomFieldsL10n.custom_fields.
     */
    function restoreFields() {
        if (BooklyCustomFieldsL10n.custom_fields) {
            var custom_fields = jQuery.parseJSON(BooklyCustomFieldsL10n.custom_fields);
            $.each(custom_fields, function (i, field) {
                var $new_field = addField(field.type, field.id, field.label, field.required, field.services);
                // add children
                if (field.items) {
                    $.each(field.items, function (i, value) {
                        addItem($new_field.find('ul.bookly-items'), field.type + '-item', value);
                    });
                }
            });
        }

        $cf_merge_repeating.val($cf_merge_repeating.data('default'));
        $cf_per_service.val($cf_per_service.data('default'));
        $cf_per_service.change();

        $(':focus').blur();
    }
});