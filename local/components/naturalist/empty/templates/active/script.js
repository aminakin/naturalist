var Order = function() {
    this.beforeCancel = function(orderId) {
        var data = {
            orderId: orderId
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/cancelOrderBefore.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR && a.PENALTY) {
                    $('#cancel .reservation-penalty').show();
                    $('#cancel .reservation-penalty > span').text(a.PENALTY);
                } else {
                    $('#cancel .reservation-penalty').hide();
                }
            }
        });
    }
    this.cancel = function(orderId) {
        var data = {
            orderId: orderId
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/cancelOrder.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    //window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    
                    //window.modal.close('cancel');
                    window.modal.open('cancel-done');

                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                    
                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    }
    this.payment = function(orderId) {
        var data = {
            orderId: orderId
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/getPaymentUrl.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    var link = a.LINK;
                    location.href = link;
                }
            }
        });
    }
}
var order = new Order();

$(function() {
    /*
    $(document).on('change', '#cancel input[name="terms"]', function(e) {
        if($(this).prop('checked') == true) {
            $('[data-order-cancel]').attr('disabled', false);
        } else {
            $('[data-order-cancel]').attr('disabled', true);
        }
    });
    */

    // Отмена заказа
    /*
    $(document).on('click', '[data-before-cancel]', function(e) {
        e.preventDefault();
        var orderId = $(this).data('id');
        $('#cancel [data-order-cancel]').data('order-id', orderId);
        order.beforeCancel(orderId);
    });
    */
    $(document).on('click', '[data-order-cancel]', function(e) {
        e.preventDefault();
        var orderId = $(this).data('id');
        order.cancel(orderId);
    });

    // Сортировка
    $(document).on('click', '[data-order-sort]', function(event) {
        event.preventDefault();

        var sort = $(this).data('order-sort');

        var params = getUrlParams();
        params["sort"] = sort;
        var url = setUrlParams(params);

        location.href = url;
    });

    // Фильтр
    $(document).on('click', '#form-order-search [data-order-search]', function(event) {
        event.preventDefault();

        var params = getUrlParams();

        var orderNum = $('#form-order-search input[name="orderNum"]').val();
        if(orderNum) {
            params["orderNum"] = orderNum;
        } else {
            delete params["orderNum"];
        }

        var url = setUrlParams(params);
        window.location = url;
    });

    // Получение ссылки на оплату
    $(document).on('click', '[data-payment]', function(e) {
        e.preventDefault();
        var orderId = $(this).data('id');
        order.payment(orderId);
    });
});