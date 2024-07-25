var Order = function () {
  this.add = function () {
    var arGuests = {};
    var error = 0;
    let _this = this;
    let orderForm = $("#form-order");
    let isAuth = orderForm.attr("is_auth");
    if (isAuth === "true") {
      $("#form-order [data-guest-row]").each(function (indx, element) {
        var key = $(element).data("guest-row");
        if ($(element).find('input[name="surname"]').val() !== "") {
          var surname = $(element).find('input[name="surname"]').val();
        }

        if ($(element).find('input[name="name"]').val() !== "") {
          var name = $(element).find('input[name="name"]').val();
        }

        var lastname = $(element).find('input[name="lastname"]').val() ?? "";
        var save = $(element).find('input[name="save"]').prop("checked")
          ? 1
          : 0;

        arGuests[key] = {
          surname: surname,
          name: name,
          lastname: lastname,
          save: save,
        };
      });

      if ($('#form-order input[name="phone"]').val() !== "") {
        var phone = $('#form-order input[name="phone"]').val();
      }

      if ($('#form-order input[name="email"]').val() !== "") {
        var email = $('#form-order input[name="email"]').val();
      }

      var params = {
        fromOrder: true,
        login: phone,
        type: "phone",
        phone: phone,
        email: email,
        guests: arGuests,
        adults: $('#form-order input[name="guests"]').val(),
        name: $('#form-order input[name="name"]').val(),
        last_name: $('#form-order input[name="surname"]').val(),
        childrenAge: $('#form-order input[name="childrenAge"]').val(),
        comment: $('#form-order textarea[name="comment"]').val(),
        dateFrom: $('#form-order input[name="date_from"]').val(),
        dateTo: $('#form-order input[name="date_to"]').val(),
        checksum:
          $('#form-order input[name="travelineChecksum"]').val() ?? false,
        paysystem: $('#form-order input[name="paysystem"]:checked').val(),
        userbalance: $('#form-order input[name="user_balance"]').val() ?? false,
      };
      var data = {
        params: params,
      };

      dataLayer.push({
        ecommerce: {
          currencyCode: "RUB",
          purchase: {
            products: [
              {
                id: window.orderData.prodID,
                name: window.orderData.prodName,
                price: window.orderData.price,
                category: window.orderData.sectionName,
                quantity: 1,
                position: 1,
              },
            ],
          },
        },
      });

      jQuery.ajax({
        type: "POST",
        url: "/ajax/handlers/addOrder.php",
        data: data,
        dataType: "json",
        beforeSend: function () {
          window.preloader.show();
          $("[data-order]").attr("disabled", "disabled");
        },
        success: function (a) {
          window.preloader.hide();
          if (!a.ERROR) {
            if (a.REDIRECT_URL && a.REDIRECT_URL != false) {
              location.href = a.REDIRECT_URL;
            } else if (a.PAYMENT_DATA) {
              _this.getYaPayUrl(a.PAYMENT_DATA);
            } else {
              console.log(a);
            }
          } else {
            window.infoModal(ERROR_TITLE, a.ERROR);
            $("[data-order]").removeAttr("disabled");
            $(document).on(
              "click",
              "#info-modal [data-modal-close]",
              function (e) {
                e.preventDefault();
                history.back(1);
              }
            );
          }
        },
      });
    } else {
      var auth = new Auth();
      auth.getCode(
        "phone",
        orderForm.find('[name="phone"]').val(),
        orderForm.find('[name="email"]').val(),
        true
      );
    }
  };
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
      travelineCategoryId:
        $('#form-order input[name="travelineCategoryId"]').val() ?? false,
      checksum: $('#form-order input[name="travelineChecksum"]').val() ?? false,
    };
    var data = {
      params: params,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/getCancellationAmount.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          if (a.FREE && a.PENALTY > 0) {
            $("#cancel .reservation-date > span").text(a.DATE);
            $("#cancel .reservation-penalty > span").text(a.PENALTY);
            $("#cancel [data-resevation-list-free]").show();
          } else if (a.PENALTY > 0) {
            $("#cancel .reservation-penalty > span").text(a.PENALTY);
            $("#cancel [data-resevation-list]").show();
          } else {
            $("#cancel .reservation-penalty").text(
              "Бесплатная отмена бронирования"
            );
            $("#cancel [data-resevation-list]").show();
          }

          window.modal.open("cancel");
        }
      },
    });
  };

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
    };
    var data = {
      params: params,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/getCancellationAmountBnovo.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          if (a.PROPERTY_CANCELLATION_RULES_VALUE) {
            $("#cancelBnovo .reservation-date").text(
              a.PROPERTY_CANCELLATION_RULES_VALUE
            );
          } else {
            $("#cancelBnovo .reservation-date").hide();
          }
          if (
            a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE &&
            a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE == 5
          ) {
            $("#cancelBnovo .reservation-penalty > span").text(params.price);
            $("#cancelBnovo [data-resevation-list-free]").show();
          } else if (
            a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE &&
            a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE == 4
          ) {
            $("#cancelBnovo .reservation-penalty > span").text(
              params.priceOneNight
            );
            $("#cancelBnovo [data-resevation-list-free]").show();
          } else if (
            a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE &&
            a.PROPERTY_CANCELLATION_FINE_TYPE_VALUE == 2
          ) {
            $("#cancelBnovo .reservation-penalty > span").text(
              params.price * (a.PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE / 100)
            );
            $("#cancelBnovo [data-resevation-list-free]").show();
          } else if (a.PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE > 0) {
            $("#cancelBnovo .reservation-penalty > span").text(
              a.PROPERTY_CANCELLATION_FINE_AMOUNT_VALUE
            );
            $("#cancelBnovo [data-resevation-list-free]").show();
          } else {
            $("#cancelBnovo .reservation-penalty").text(
              "Бесплатная отмена бронирования"
            );
            $("#cancelBnovo [data-resevation-list]").show();
          }

          window.modal.open("cancelBnovo");
        }
      },
    });
  };

  this.sendCoupon = function () {
    _this = this;
    let data = {
      coupon: $(".coupon__input").val(),
      action: "couponAdd",
    };
    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/addOrder.php",
      data: data,
      dataType: "json",
      success: function (data) {
        if (data.STATUS == "SUCCESS") {
          _this.setCookieValuesFromForm();
          if (window.innerWidth < 500) {
            location.href = "/order#coupon-block";
          } else {
            location.reload();
          }
        } else {
          let elem =
            '<span class="coupon__error-message">' + data.MESSAGE + "</span>";
          $("#form__coupons").after($(elem));
          $("#form__coupons input").addClass("error");
        }
      },
      error: function (data) {
        if (window.innerWidth < 500) {
          location.href = "/order#coupon-block";
        } else {
          location.reload();
        }
      },
    });
  };

  this.setCookieValuesFromForm = function () {
    let name = $('[name="name"]').val();
    let surname = $('[name="surname"]').val();
    let phone = $('[name="phone"]').val();
    let email = $('[name="email"]').val();
    let comment = $('[name="comment"]').val();

    if (name != "") {
      this.setCookie(coockiePrefix + "_orderName", name);
    }

    if (surname != "") {
      this.setCookie(coockiePrefix + "_orderSurname", surname);
    }

    if (phone != "") {
      this.setCookie(coockiePrefix + "_orderPhone", phone);
    }

    if (email != "") {
      this.setCookie(coockiePrefix + "_orderEmail", email);
    }

    if (comment != "") {
      this.setCookie(coockiePrefix + "_orderComment", comment);
    }
  };

  this.removeCoupon = function (coupon) {
    let data = {
      coupon: coupon,
      action: "couponDelete",
    };
    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/addOrder.php",
      data: data,
      dataType: "json",
      success: function (data) {
        if (window.innerWidth < 500) {
          location.href = "/order#coupon-block";
        } else {
          location.reload();
        }
      },
      error: function (data) {
        if (window.innerWidth < 500) {
          location.href = "/order#coupon-block";
        } else {
          location.reload();
        }
      },
    });
  };

  this.setCookie = function (name, value, options = {}) {
    options = {
      path: "/",
      ...options,
    };

    if (options.expires instanceof Date) {
      options.expires = options.expires.toUTCString();
    }

    let updatedCookie =
      encodeURIComponent(name) + "=" + encodeURIComponent(value);

    for (let optionKey in options) {
      updatedCookie += "; " + optionKey;
      let optionValue = options[optionKey];
      if (optionValue !== true) {
        updatedCookie += "=" + optionValue;
      }
    }

    document.cookie = updatedCookie;
  };

  this.getYaPayUrl = function (data) {
    fetch("/bitrix/services/yandexpay.pay/trading/orders", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then((response) => {
        return response.json();
      })
      .then((result) => {
        if (result.status === "success") {
          location.href = result.data.paymentUrl;
        } else {
          console.error("orders error - " + result.reasonCode + result.reason);
        }
      });
  };
};
var order = new Order();

$(function () {
  window.addEventListener("sendForm", (event) => {
    if (event.detail.form == "form-order") {
      order.add();
    }
  });

  $(document).on("click", "[data-get-cancellation-amount]", function (e) {
    e.preventDefault();
    order.getCancellationAmount();
  });

  $(document).on("click", "[data-get-cancellation-amount-bnovo]", function (e) {
    e.preventDefault();
    order.getCancellationAmountBnovo();
  });

  $("#coupon_toggler").on("change", function () {
    if ($(this).is(":checked")) {
      $("#form__coupons").show();
    } else {
      $("#form__coupons").hide();
    }
  });

  $(".coupon__input").on("input", function () {
    if ($(this).val() == "") {
      $(this).removeClass("error");
      $(".coupon__error-message").hide();
    }
  });

  if (window.location.hash) {
    var hash = window.location.hash;
    setTimeout(() => {
      if ($(hash).length) {
        console.log(1);
        $("html, body").animate(
          {
            scrollTop: $(hash).offset().top,
          },
          500
        );
      }
    }, 1000);
  }
});
