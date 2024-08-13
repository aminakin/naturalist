$('document').ready(function(){    
  var request;
  const EMAIL_REGEXP = /^(([^<>()[\].,;:\s@"]+(\.[^<>()[\].,;:\s@"]+)*)|(".+"))@(([^<>()[\].,;:\s@"]+\.)+[^<>()[\].,;:\s@"]{2,})$/iu;
  const messageSubscribeHTML = function(title, subtitle) {
    const messageSubscribe = `
      <div id="subscribe_result" class="modal modal_form">            
        <div class="modal__container">
            <button class="modal__close" data-modal-close>
                <svg class="icon icon_cross" viewbox="0 0 18 18" style="width: 1.8rem; height: 1.8rem;">
                    <use xlink:href="#cross" />
                </svg>
            </button>
            <p class="modal__title">
              ${title}
            </p>
            <p class="modal__subtitle">
              ${subtitle}
            </p>
        </div>
      </div>
    `
    return messageSubscribe
  }

  function isEmailValid(value) {
    return EMAIL_REGEXP.test(value);
  }


  $("#subscribe-form form").submit(function (event) {    
    var inputMail = $(this).find(".subscribe__input");
    var errorElem = $(this).find('.input__error');    
    if ($(inputMail).val() == "") {
      $(inputMail).addClass("error");
      $(errorElem).text('Это поле обязательно для заполнения. Пожалуйста, введите e-mail').show();
      event.preventDefault();
    } else if (!isEmailValid($(inputMail).val())) {
      $(inputMail).addClass("error");
      $(errorElem).text('Пожалуйста, введите правильный e-mail в формате example@example.ru').show();
      event.preventDefault();
    } else {
      event.preventDefault();
      $(inputMail).removeClass("error");
      $(errorElem).text('').hide();
      if (request) {
        request.abort();
      }
      var $form = $(this);
      var $inputs = $form.find("input, select, button, textarea");
      var serializedData = $form.serialize();
      $inputs.prop("disabled", true);
      request = $.ajax({
        url: "/ajax/forms/subscribe.php",
        type: "post",
        data: serializedData,
      });
      request.done(function (response, textStatus, jqXHR) {
        if (response == "success") {
          $("body").prepend(messageSubscribeHTML(
            "Успех!",
            "Спасибо за подписку! Теперь вы будете получать свежие новости, специальные предложения и интересные обновления от нас"
          ));
          window.modal.open('subscribe_result');
        } else {
            $(inputMail).addClass("error");
            $(errorElem).text('Вы уже подписаны на нашу рассылку.').show();
            //$(errorElem).text('Вы уже подписаны на нашу рассылку. Если вы хотите получать новости по другому адресу, \nпожалуйста, введите другой e-mail').show();
        }
      });
      request.fail(function (jqXHR, textStatus, errorThrown) {
        $("body").prepend(messageSubscribeHTML(
            "Ошибочка вышла",
            "Извините, произошла системная ошибка. Пожалуйста, повторите попытку позже или свяжитесь с нашей службой поддержки"
          ));        
        window.modal.open('subscribe_result');
      });
      request.always(function () {
        $inputs.prop("disabled", false);
      });
    }
  });
})