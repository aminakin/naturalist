var Order = function () {
    this.add = function () {
        var arGuests = {};
        var error = 0;
        $('#form-order [data-guest-row]').each(function (indx, element) {
            var key = $(element).data('guest-row');
            if ($(element).find('input[name="surname"]').val() !== '') {
                var surname = $(element).find('input[name="surname"]').val();
            }
            if ($(element).find('input[name="name"]').val() !== '') {
                var name = $(element).find('input[name="name"]').val();
            }

            var lastname = $(element).find('input[name="lastname"]').val() ?? '';
            var save = $(element).find('input[name="save"]').prop('checked') ? 1 : 0;

            arGuests[key] = {
                surname: surname,
                name: name,
                lastname: lastname,
                save: save
            }
        });

        if ($('#form-order input[name="phone"]').val() !== '') {
            var phone = $('#form-order input[name="phone"]').val();
        }

        if ($('#form-order input[name="email"]').val() !== '') {
            var email = $('#form-order input[name="email"]').val();
        }

        var params = {
            phone: phone,
            email: email,
            guests: arGuests,
            childrenAge: $('#form-order input[name="childrenAge"]').val(),
            comment: $('#form-order textarea[name="comment"]').val(),
            dateFrom: $('#form-order input[name="date_from"]').val(),
            dateTo: $('#form-order input[name="date_to"]').val(),
            checksum: $('#form-order input[name="travelineChecksum"]').val() ?? false,
        }
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/addOrder.php',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                $('[data-order]').attr('disabled', 'disabled');
            },
            success: function (a) {
                if (!a.ERROR) {                    
                    if (a.REDIRECT_URL) {
                        location.href = a.REDIRECT_URL;
                    }

                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                    $('[data-order]').removeAttr('disabled');
                    $(document).on('click', '#info-modal [data-modal-close]', function (e) {
                        e.preventDefault();
                        history.back(1);
                    });
                }
            }
        });
    }
    this.getCancellationAmount = function () {
        var params = {
            service: $('#form-order input[name="service"]').val(),
            sectionId: $('#form-order input[name="sectionId"]').val(),
            externalId: $('#form-order input[name="externalId"]').val(),
            guests: $('#form-order input[name="guests"]').val(),
            childrenAge: $('#form-order input[name="childrenAge"]').val(),
            dateFrom: $('#form-order input[name="date_from"]').val(),
            dateTo: $('#form-order input[name="date_to"]').val(),
            externalElementId: $('#form-order input[name="externalElementId"]').val(),
            travelineCategoryId: $('#form-order input[name="travelineCategoryId"]').val() ?? false,
            checksum: $('#form-order input[name="travelineChecksum"]').val() ?? false,
        }
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/getCancellationAmount.php',
            data: data,
            dataType: 'json',
            success: function (a) {
                if (!a.ERROR) {
                    if (a.FREE && a.PENALTY > 0) {
                        $('#cancel .reservation-date > span').text(a.DATE);
                        $('#cancel .reservation-penalty > span').text(a.PENALTY);
                        $('#cancel [data-resevation-list-free]').show();
                    } else if(a.PENALTY > 0) {
                        $('#cancel .reservation-penalty > span').text(a.PENALTY);
                        $('#cancel [data-resevation-list]').show();
                    } else {
                        $('#cancel .reservation-penalty').text('Бесплатная отмена бронирования');
                        $('#cancel [data-resevation-list]').show();
                    }

                    window.modal.open('cancel');
                }
            }
        });
    }

    this.getCancellationAmountBnovo = function () {
        var params = {
            service: $('#form-order input[name="service"]').val(),
            sectionId: $('#form-order input[name="sectionId"]').val(),
            externalId: $('#form-order input[name="externalId"]').val(),
            guests: $('#form-order input[name="guests"]').val(),
            childrenAge: $('#form-order input[name="childrenAge"]').val(),
            dateFrom: $('#form-order input[name="date_from"]').val(),
            dateTo: $('#form-order input[name="date_to"]').val(),
            externalElementId: $('#form-order input[name="externalElementId"]').val(),
            tariffId: $('#form-order input[name="tariffId"]').val(),
            priceOneNight: $('#form-order input[name="priceOneNight"]').val(),
            price: $('#form-order input[name="price"]').val(),
        }
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/getCancellationAmountBnovo.php',
            data: data,
            dataType: 'json',
            success: function (a) {
                if (!a.ERROR) {
                    if (a.PROPERTY_CANCELLATION_RULES_VALUE) {
                        $('#cancelBnovo .reservation-date').text(a.PROPERTY_CANCELLATION_RULES_VALUE);
                    } else {
                        $('#cancelBnovo .reservation-date').hide();
                    }
                    if (a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE && a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE == 5) {
                        $('#cancelBnovo .reservation-penalty > span').text(params.price);
                        $('#cancelBnovo [data-resevation-list-free]').show();
                    } else if (a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE && a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE == 4) {
                        $('#cancelBnovo .reservation-penalty > span').text(params.priceOneNight);
                        $('#cancelBnovo [data-resevation-list-free]').show();
                    }  else if (a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE && a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE == 2) {
                        $('#cancelBnovo .reservation-penalty > span').text(params.price*(a.PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE/100));
                        $('#cancelBnovo [data-resevation-list-free]').show();
                    } else if(a.PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE > 0) {
                        $('#cancelBnovo .reservation-penalty > span').text(a.PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE);
                        $('#cancelBnovo [data-resevation-list-free]').show();
                    } else {
                        $('#cancelBnovo .reservation-penalty').text('Бесплатная отмена бронирования');
                        $('#cancelBnovo [data-resevation-list]').show();
                    }

                    window.modal.open('cancelBnovo');
                }
            }
        });
    }

    this.sendCoupon = function () {
        let data = {
            coupon: $('.coupon__input').val(),
            action: 'couponAdd'
        }        
        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/addOrder.php',
            data: data,
            dataType: 'json',            
            success: function (data) {
                if (data.STATUS == 'SUCCESS') {
                    location.reload();
                } else {
                    let elem = '<span class="coupon__error-message">'+data.MESSAGE+'</span>';
                    $('#form__coupons').after($(elem));
                    $('#form__coupons input').addClass('error');
                }                
            },
            error: function (data) {                
                location.reload();
            },
        });
    }

    this.removeCoupon = function (coupon) {
        let data = {
            coupon: coupon,
            action: 'couponDelete'
        }        
        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/addOrder.php',
            data: data,
            dataType: 'json',            
            success: function (data) {
                location.reload();
            },
            error: function (data) {
                location.reload();
            },
        });
    }
}
var order = new Order();

$(function () {    
    window.addEventListener('sendForm', event => {
        if(event.detail.form == 'form-order') {
            order.add();
        }
    });

    $(document).on('click', '[data-get-cancellation-amount]', function (e) {
        e.preventDefault();
        order.getCancellationAmount();
    });

    $(document).on('click', '[data-get-cancellation-amount-bnovo]', function (e) {
        e.preventDefault();
        order.getCancellationAmountBnovo();
    });

    $('#coupon_toggler').on('change', function(){
        if ($(this).is(':checked')) {
            $('#form__coupons').show();
        } else {
            $('#form__coupons').hide();
        }        
    })
});