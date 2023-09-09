(function ($) {
    'use strict';

    $('.init-categories .button').click(function (e) {
        e.preventDefault();
        dcms_generic_ajax_call('.init-categories', 'dcms_migrate_initial_category');
    });


// Reuse code generic ajax call with jquery
    function dcms_generic_ajax_call(selector_container, action_method) {
        $.ajax({
            async: true,
            url: dcms_lemans.ajaxurl,
            type: 'post',
            dataType: 'json',
            timeout: 60000,
            data: {
                action: action_method,
                nonce: dcms_lemans.nonce_lemans
            },
            beforeSend: function () {
                $(selector_container + ' .button').prop('disabled', true);
                $(selector_container + ' .msg-btn').text(dcms_lemans.sending);
                $(selector_container + ' .loading').removeClass('hide');
            }
        })
            .done(function (res) {
                $(selector_container + ' .msg-btn').removeClass('hide').text(res.message);
                console.log(res);
            })
            .always(function () {
                // $(selector_container + ' .button').prop('disabled', false);
                $(selector_container + ' .msg-btn').text('Revisa el archivo error log para ver el progreso');
                $(selector_container + ' .loading').addClass('hide');
            });
    }


    // Click process button
    $('#process-migration-products').click(function (e) {
        e.preventDefault();
        process_migration_step(1);
    });

    // Process every step
    function process_migration_step(step, total = null) {

        $.ajax({
            url: dcms_lemans.ajaxurl,
            type: 'post',
            data: {
                action: 'dcms_process_batch_ajax_migration',
                nonce: dcms_lemans.nonce_lemans,
                total,
                step,
            },
            dataType: 'json',
        })
            .done(function (res) {
                if (res.status === 0) {
                    $('.process-info').html(`<strong>Procesados ${res.count} de ${res.total}
                                        <br> Paso: ${res.step}</strong>`);
                    process_migration_step(res.step, res.total)
                } else {
                    $('.process-info').text('Finalizado');
                }
            });
    }


})(jQuery);

