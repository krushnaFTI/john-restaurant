(function ($) {

    var Advanced = function ($container, options) {
        var obj = this;
        jQuery.extend(obj.options, options);

        if (!$container.children().length) {
            $container.html('<div class="bookly-loading"></div>');
            $.ajax({
                url: ajaxurl,
                data: obj.options.get_staff_advanced,
                xhrFields: {withCredentials: true},
                crossDomain: 'withCredentials' in new XMLHttpRequest(),
                success: function (response) {
                    $container.html(response.data.html);
                    init($container, obj);
                }
            });
        } else {
            init($container, obj);
        }

        function init($container, obj) {
            if ($container.data('init') != true) {
                var $form = $('form', $container),
                    $unsaved_changes = $('.bookly-js-unsaved-changes'),
                    $unsaved_changes_save = $('.bookly-js-save-changes',$unsaved_changes),
                    $unsaved_changes_ignore = $('.bookly-js-ignore-changes',$unsaved_changes),
                    has_changes = false
                ;

                $container
                    .on('change', 'select,input,textarea', function () {
                        has_changes = true;
                    })
                    .on('click', '.bookly-js-google-calendar-row a', function (e) {
                        var url = $(this).attr('href');
                        if (has_changes) {
                            e.preventDefault();
                            $unsaved_changes.booklyModal('show');
                            $unsaved_changes.data('url', url);
                        }
                    })
                    .on('click', '.bookly-js-outlook-calendar-row a', function (e) {
                        var url = $(this).attr('href');
                        if (has_changes) {
                            e.preventDefault();
                            $unsaved_changes.booklyModal('show');
                            $unsaved_changes.data('url', url);
                        }
                    })
                    .on('change', '[name=google_disconnect]', function () {
                        has_changes = true;
                        $('.bookly-js-google-calendars-list', $form).toggle(!this.checked);
                    }).on('change', '[name=outlook_disconnect]', function () {
                        has_changes = true;
                        $('.bookly-js-outlook-calendars-list', $form).toggle(!this.checked);
                    })
                    // Save staff member details.
                    .on('click', '#bookly-advanced-save', function (e) {
                        e.preventDefault();
                        let ladda = Ladda.create(this);
                        ladda.start();
                        saveAdvanced(function (response) {
                            ladda.stop();
                            if (response.success) {
                                obj.options.saving({success: [obj.options.l10n.saved]});
                            }
                        });
                    })
                    .on('change', '[name="zoom_personal"]', function () {
                        $('.bookly-js-zoom-keys', $form).toggle(this.value != '0');
                    })
                    .on('click', '[type="reset"]', function (e) {
                        $form[0].reset();
                        has_changes = false;
                        $('[name="zoom_personal"]:checked', $form).trigger('change');
                    });

                $unsaved_changes_ignore.off().on('click', function () {
                    window.location.href = $unsaved_changes.data('url');
                });
                $unsaved_changes_save.off().on('click', function () {
                    let ladda = Ladda.create(this);
                        ladda.start();
                    saveAdvanced(function (response) {
                        if (response.success) {
                            window.location.href = $unsaved_changes.data('url');
                        } else {
                            obj.options.saving({error: [response.data.error]});
                        }
                        ladda.stop();
                    });
                });

                function saveAdvanced(callback) {
                    var data = $form.serializeArray();
                    data.push({name: 'action', value: 'bookly_pro_update_staff_advanced'});
                    data.push({name: 'csrf_token', value: obj.options.get_staff_advanced.csrf_token});
                    data.push({name: 'staff_id', value: obj.options.get_staff_advanced.staff_id});
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data,
                        dataType: 'json',
                        xhrFields: {withCredentials: true},
                        crossDomain: 'withCredentials' in new XMLHttpRequest(),
                        success: function (response) {
                            has_changes = false;
                            callback(response);
                        }
                    });
                }
            }
            $container.data('init', true);
        }
    };

    Advanced.prototype.options = {
        get_staff_advanced : {
            action    : 'bookly_pro_get_staff_advanced',
            staff_id  : -1,
            csrf_token: ''
        },
        l10n        : {},
        booklyAlert: window.booklyAlert,
        saving: function (alerts) {
            $(document.body).trigger('staff.saving', [alerts]);
        },
    };

    window.BooklyStaffAdvanced = Advanced;
})(jQuery);