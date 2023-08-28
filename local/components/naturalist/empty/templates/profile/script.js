$(function() {
    $(document).on('click', '[data-name-save]', function(e) {
        e.preventDefault();

        var params = {
            surname: $('#form-name-save input[name="surname"]').val(),
            name: $('#form-name-save input[name="name"]').val(),
            lastname: $('#form-name-save input[name="lastname"]').val(),
        }
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateProfile.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        location.reload();
                    }

                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    });

    $(document).on('click', '[data-email-code-send]', function(e) {
        e.preventDefault();

        var email = $('#form-email-save input[name="email"]').val();
        if(!email) {
            window.infoModal("Ошибочка вышла", "Укажите E-mail");
            return;
        }

        var data = {
            type: 'email',
            value: email,
        }
        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateGetCode.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);

                } else {
                    window.infoModal("Упс…", a.ERROR);
                }
            }
        });
    });
    $(document).on('click', '[data-email-save]', function(e) {
        e.preventDefault();

        var email = $('#form-email-save input[name="email"]').val();
        if(!email) {
            window.infoModal("Ошибочка вышла", "Укажите E-mail");
            return;
        }
        var code = $('#form-email-save input[name="code"]').val();
        if(!code) {
            window.infoModal("Ошибочка вышла", "Укажите код");
            return;
        }

        var params = {
            email: $('#form-email-save input[name="email"]').val(),
            code: code,
        }
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateProfile.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        location.reload();
                    }

                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    });

    $(document).on('click', '[data-phone-code-send]', function(e) {
        e.preventDefault();

        var phone = $('#form-phone-save input[name="phone"]').val();
        if(!phone) {
            window.infoModal("Ошибочка вышла", "Укажите телефон");
            return;
        }

        var data = {
            type: 'phone',
            value: phone,
        }
        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateGetCode.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);

                } else {
                    window.infoModal("Упс…", a.ERROR);
                }
            }
        });
    });
    $(document).on('click', '[data-phone-save]', function(e) {
        e.preventDefault();

        var phone = $('#form-phone-save input[name="phone"]').val();
        if(!phone) {
            window.infoModal(ERROR_TITLE, 'Укажите телефон');
            return;
        }
        var code = $('#form-phone-save input[name="code"]').val();
        if(!code) {
            window.infoModal(ERROR_TITLE, 'Укажите код.');
            return;
        }

        var params = {
            phone: phone,
            code: code
        }
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateProfile.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        location.reload();
                    }

                } else {
                    window.infoModal("Ээээ…", a.ERROR);
                }
            }
        });
    });

    window.addEventListener('saveAvatar', event => {
        //var data = new FormData();

        /*fetch(event.detail.file)
            .then(res => res.blob())
            .then(blob => {
                const file = new File([blob], "avatar.jpeg")
            })*/

        //data.append('avatar', );

        var params = {
            avatar: event.detail.file
        }
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateProfile.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        location.reload();
                    }

                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    })

    $(document).on('click', '[data-avatar-remove]', function(e) {
        e.preventDefault();

        var params = {
            deleteAvatar: 1
        }
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateProfile.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        location.reload();
                    }

                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    });

    $(document).on('click', '[data-field-update]', function(e) {
        var fieldName = $(this).attr('name');
        var fieldValue = $(this).prop('checked') ? 1 : 0;

        var params = {};
        params[fieldName] = fieldValue;
        var data = {
            params: params
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateProfile.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    //window.infoModal(SUCCESS_TITLE, a.MESSAGE);

                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    });
});