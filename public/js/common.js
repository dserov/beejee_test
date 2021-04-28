function show_message(msg, status) {
    var cls = "errorbox";
    if (status == 'true')
        cls = "allokbox";

    $('#message-layer-text').html(msg);
    $('#message-layer-text').attr('class', cls);
    // появление и исчезание блока
    $('#message-layer').hide()
        .clearQueue()
        .click(function () {
            $(this).hide();
            $(this).clearQueue();
        })
        .toggle(200);
    if (status == 'true')
        $('#message-layer').delay(3000).toggle(200);
}

function show_message_permanent(msg, status) {
    var cls = "errorbox";
    if (status === "true")
        cls = "allokbox";

    $('#message-layer-text').html(msg);
    $('#message-layer-text').attr('class', cls);
    // появление и исчезание блока
    $('#message-layer').hide()
        .clearQueue()
        .click(function () {
            $(this).hide();
            $(this).clearQueue();
        })
        .toggle(200);
}

$(document).ready(function (e) {
    $('#order_by').on('change', function (event) {
        window.location.href = '?order_by=' + $(this).val();
    });

    $('#order_direction').on('change', function (event) {
        window.location.href = '?order_direction=' + $(this).val();
    });

    $('.status').on('change', function (event) {
        let data = {
            id: $(this).data('id'),
            status_code: $(this).is(":checked")
        };
        let posting = $.post("/todo/update",
            JSON.stringify(data),
            function (otvet, textStatus, jqXHR) {
                show_message('Сохранено!', 'true');
            });
        posting.fail(function (jqXHR, textStatus, errorThrown) {
            setTimeout(function () {
                window.location.reload();
            }, 1000);
        });
    });

    $.ajaxSetup({
        dataType: 'json',
        beforeSend: function (jqXHR, settings) {
            $('#loading-layer').show();
        },
        complete: function (jqXHR, settings) {
            $('#loading-layer').hide();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            let errorMessage = textStatus;
            if (jqXHR.responseJSON && jqXHR.responseJSON.error)
                errorMessage = jqXHR.responseJSON.error;
            if (jqXHR.responseText) {
                let data = JSON.parse(jqXHR.responseText);
                if (typeof data !== undefined)
                    errorMessage = data.error;
            }
            show_message('Ошибка: ' + errorMessage + '<br> Код возврата: ' + jqXHR.statusText + ' (' + jqXHR.status + ')', 'false');
        }
    });
});
