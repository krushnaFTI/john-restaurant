jQuery(function ($) {
    'use strict';
    let $staffList = $('#bookly-staff-list'),
        $modalBody = $('#bookly-staff-edit-modal .modal-body'),
        hash       = window.location.href.split('#'),
        staff_id   = BooklyL10nStaffEdit.activeStaffId
    ;

    if (BooklyL10nStaffEdit.activeStaffId != null) {
        $(document.body).trigger('bookly.staff.edit', [BooklyL10nStaffEdit.activeStaffId]);
    }

    $staffList
        .on('click', '[data-action="edit"]', function () {
            let data = $staffList.DataTable().row($(this).closest('td')).data();
            staff_id = data.id;
        });

    // Open advanced tab
    $modalBody
        .on('click', '#bookly-advanced-tab', function () {
            $('.tab-pane > div').hide();
            let $container = $('#bookly-advanced-container', $modalBody);
            new BooklyStaffAdvanced($container, {
                get_staff_advanced: {
                    action: 'bookly_pro_get_staff_advanced',
                    staff_id: staff_id,
                    csrf_token: BooklyStaffEditDialogL10n.csrfToken
                }
            });

            $('#bookly-advanced-save', $container).addClass('bookly-js-save');
            $container.show();
        })
});