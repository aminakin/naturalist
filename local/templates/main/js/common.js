const SUCCESS_TITLE = "Успех!";
const ERROR_TITLE = "Ошибка";

window.scrollToElement = function (scrollOnLoadSelector) {
  $("html, body").animate(
    {
      scrollTop: $(scrollOnLoadSelector).offset().top,
    },
    2000
  );
};
window.pluralize = function (count, words) {
  var cases = [2, 0, 1, 1, 1, 2];
  return words[
    count % 100 > 4 && count % 100 < 20 ? 2 : cases[Math.min(count % 10, 5)]
  ];
};
window.getUrlParams = function () {
  var params = window.location.search
    .replace("?", "")
    .split("&")
    .reduce(function (arr, current) {
      if (current.length > 0) {
        var splitted = current.split("=");
        arr[decodeURIComponent(splitted[0])] = decodeURIComponent(splitted[1]);
        return arr;
      }
    }, {});

  return params ? params : {};
};
window.setUrlParams = function (params) {
  var arr = [];
  for (var key in params) arr.push(key + "=" + params[key]);

  return arr.length > 0
    ? "?" + arr.join("&")
    : window.location.origin + window.location.pathname;
};
window.deleteUrlParams = function (arParams, arDeleting) {
  for (var key in arDeleting) {
    delete arParams[arDeleting[key]];
  }
};

window.setLocalStorageCatalog = function (event) {
  event.preventDefault();
  let page = $("[data-catalog-container] [data-catalog-showmore]").data("page");
  let showenElements = $(".catalog__list > div").length;
  let data = {
    page: page,
    items: showenElements,
  };
  jQuery.ajax({
    type: "POST",
    url: "/ajax/handlers/setLocalStorage.php",
    data: data,
    dataType: "json",
    success: function (data) {
      // console.log(data);
    },
  });
  window.open(event.target.getAttribute("href"));
  //location.href = event.target.getAttribute("href");
};

window.getChildrenOrderTitle = function (order) {
  switch (order) {
    case 1:
      return "первого";
    case 2:
      return "второго";
    case 3:
      return "третьего";
    case 4:
      return "четвертого";
    case 5:
      return "пятого";
    case 6:
      return "шестого";
    case 7:
      return "седьмого";
    case 8:
      return "восьмого";
    case 9:
      return "девятого";
    case 10:
      return "десятого";
    case 11:
      return "одиннадцатого";
    case 12:
      return "двенадцатого";
    case 13:
      return "тринадцатого";
    case 14:
      return "четырнадцатого";
    case 15:
      return "пятнадцатого";
    case 16:
      return "шестнадцатого";
    case 17:
      return "семнадцатого";
    case 18:
      return "восемнадцатого";
    case 19:
      return "девятнадцатого";
    case 20:
      return "двадцатого";
    default:
      return order + "-го";
  }
};

var getTimeDate = function (editDate) {
  let parseDate = editDate.split(".");
  let newDate = new Date(parseDate[2], parseDate[1], parseDate[0]);
  newDate = newDate.getTime();
  return newDate;
};

var Auth = function () {
  this.getCode = function (
    type,
    phone = "",
    email = "",
    authFromOrder = false
  ) {
    let name = "";
    let last_name = "";

    if ($('#form-order input[name="name"]').length) {
      name = $('#form-order input[name="name"]').val();
    }

    if ($('#form-order input[name="surname"]').length) {
      last_name = $('#form-order input[name="surname"]').val();
    }

    if (type == "email" && email == "") {
      var login = jQuery('#form-auth-email input[name="email"]').val();
    } else if (type == "phone" && phone == "") {
      var login = jQuery('#form-auth-phone input[name="phone"]').val();
    }

    var data = {
      login: phone != "" ? phone : login,
      type: type,
      email: email,
      name: name,
      last_name: last_name,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/authGetCode.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          $('#code input[name="login"]').val(phone != "" ? phone : login);
          $('#code input[name="type"]').val(type);
          if (authFromOrder) {
            $('#code input[name="auth_from_order"]').val("Y");
          }
          window.modal.close("login-" + type);
          window.modal.open("code");
        } else {
          window.infoModal(ERROR_TITLE, a.ERROR);
        }
      },
    });
  };
  this.login = function () {
    var _this = this;
    var data = {
      type: jQuery('#form-login input[name="type"]').val(),
      code: jQuery('#form-login input[name="code"]').val(),
      login: jQuery('#form-login input[name="login"]').val(),
      page: jQuery('#form-login input[name="page"]').val(),
      auth_from_order: jQuery(
        '#form-login input[name="auth_from_order"]'
      ).val(),
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/auth.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          window.modal.close("code");
          ym(91071014, "reachGoal", "authorization");
          VK.Goal("complete_registration");
          if (a.NO_RELOAD != "Y") {
            if (a.RELOAD) {
              location.reload();
            }
            if (a.REDIRECT_URL) {
              location.href = a.REDIRECT_URL;
            }
          } else {
            _this.orderPageAuthHandler();
          }

          $("#form-login .field_error > span.field__error").remove();
          $('#form-login input[name="code"]')
            .parent()
            .removeClass("field_error");
        } else {
          window.infoModal("Ой...", a.ERROR);

          $('#form-login input[name="code"]').parent().addClass("field_error");
          $("#form-login .field_error").append(
            '<span class="field__error">' + a.ERROR + "</span>"
          );
        }
      },
    });
  };
  this.orderPageAuthHandler = function () {
    $("#form-order").attr("is_auth", "true");
    $("#form-order")
      .find("#order__confirm-data")
      .text("Оплатить банковской картой");
  };
  this.authSocnets = function (params) {
    var data = {
      params: params,
    };
    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/authSocnets.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          //window.infoModal(SUCCESS_TITLE, a.MESSAGE);
          ym(91071014, "reachGoal", "authorization");
          VK.Goal("complete_registration");
          if (a.RELOAD) {
            location.reload();
          }
          if (a.REDIRECT_URL) {
            location.href = a.REDIRECT_URL;
          }
        } else {
          window.infoModal(ERROR_TITLE, a.ERROR);
        }
      },
    });
  };
};
var auth = new Auth();
$(function () {
  window.addEventListener("sendForm", (event) => {
    if (
      event.detail.form == "form-auth-phone" ||
      event.detail.form == "form-auth-email"
    ) {
      var idForm = "#" + event.detail.form;
      console.log(idForm);
      var type = $(idForm + " .button.button_primary").data("type");
      auth.getCode(type);
    }
    if (event.detail.form == "form-login") {
      auth.login();
    }
  });
  $(document).on("click", "[data-auth-socnets]", function (e) {
    var type = $(this).data("type");

    if (type == "vk") {
      VK.Auth.login(function (response) {
        if (response.session) {
          // Пользователь успешно авторизовался
          var user = response.session.user;
          var params = {
            type: type,
            login: user.nickname ? user.nickname : user.id,
            name: user.first_name,
            lastname: user.last_name,
          };

          auth.authSocnets(params);
        }
      }, VK.access.PHOTOS);
    } else if (type == "telegram") {
      var domain = window.location.hostname;
      if (domain == "naturalistbx.idemcloud.ru") {
        var botId = "5741433095";
      } else {
        var botId = "5700016629";
      }
      var currentUrl = window.location.href;
      var url =
          "https://oauth.telegram.org/auth?bot_id=" +
          botId +
          "&origin=https%3A%2F%2F" +
          domain +
          "&embed=1&request_access=write&return_to=" + encodeURIComponent(currentUrl);
      location.href = url;
    }
  });
});

var Order = function () {
  this.addBasket = function (
    productId,
    price,
    guests,
    childrenAge,
    dateFrom,
    dateTo,
    externalId,
    externalService,
    tariffId,
    categoryId,
    prices,
    checksum,
    people,
    title,
    photo,
    bronevikOfferExternalId,
    uhotelsTariffId
  ) {
    var data = {
      productId: productId,
      price: price,
      guests: guests,
      childrenAge: childrenAge,
      dateFrom: dateFrom,
      dateTo: dateTo,
      externalId: externalId,
      externalService: externalService,
      tariffId: tariffId,
      categoryId: categoryId,
      prices: prices,
      checksum: checksum,
      people: people,
      title: title,
      photo: photo,
      bronevikOfferExternalId: bronevikOfferExternalId,
      uhotelsTariffId: uhotelsTariffId,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/addBasket.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          //window.infoModal(SUCCESS_TITLE, a.MESSAGE);
          //VK.Goal("purchase");
          location.href = "/order/";
        } else {
          window.infoModal(ERROR_TITLE, a.ERROR);
        }
      },
    });
  };
};
var order = new Order();
$(function () {
  $(document).on("click", "[data-add-basket]", function (e) {
    e.preventDefault();
    if (window.preloader == undefined) {
      window.preloader = new Preloader();
    }
    window.preloader.show();
    var productId = $(this).data("id");
    var price = $(this).data("price");
    var guests = $(this).data("guests");
    var childrenAge = $(this).data("children-age");
    var dateFrom = $(this).data("date-from");
    var dateTo = $(this).data("date-to");
    var externalId = $(this).data("external-id");
    var externalService = $(this).data("external-service");
    var tariffId = $(this).data("tariff-id");
    var categoryId = $(this).data("category-id");
    var prices = $(this).data("prices");
    var checksum = $(this).data("traveline-checksum");
    var people = $(this).data("people");
    var title = $(this).data("room-title");
    var photo = $(this).data("room-photo");
    var objectTItle = $(this).data("object-title");
    var sectionExternalId = $(this).data("section-external-id");
    var bronevikOfferExternalId = $(this).data("bronevik-offer-external-id");
    var uhotelsTariffId = $(this).data("uhotels-taariff");

    // dataLayer.push({
    //   ecommerce: {
    //     currencyCode: "RUB",
    //     add: {
    //       products: [
    //         {
    //           id: sectionExternalId,
    //           name: objectTItle,
    //           price: price,
    //           brand: externalService,
    //           quantity: 1,
    //           variant: title,
    //         },
    //       ],
    //     },
    //   },
    // });

    order.addBasket(
      productId,
      price,
      guests,
      childrenAge,
      dateFrom,
      dateTo,
      externalId,
      externalService,
      tariffId,
      categoryId,
      prices,
      checksum,
      people,
      title,
      photo,
      bronevikOfferExternalId,
      uhotelsTariffId
    );
  });
});

$(function () {
  $(document).on("click", "[data-favourite-add]", function (e) {
    e.preventDefault();

    var element = $(this);
    var elementId = $(this).data("id");
    var data = {
      elementId: elementId,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/addFavourite.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          if (a.RELOAD) {
            element.addClass("active");
            element.removeAttr("data-favourite-add");
            element.attr("data-favourite-remove", "");

            var favoritCount = $(
              ".list__item_favorite .list__item-icon span"
            ).text();
            var favoritCountNew = parseInt(favoritCount) + 1;
            console.log(favoritCountNew);
            if (favoritCountNew != 0) {
              $(".list__item_favorite .list__item-icon span")
                .show()
                .text(favoritCountNew);
            } else {
              $(".list__item_favorite .list__item-icon span").hide().text("");
            }
          }
        } else {
          window.infoModal(ERROR_TITLE, a.ERROR);
        }
      },
    });
  });

  $(document).on("click", "[data-favourite-remove]", function (e) {
    e.preventDefault();

    var element = $(this);
    var elementId = $(this).data("id");
    var data = {
      elementId: elementId,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/removeFavourite.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          if (a.RELOAD) {
            element.removeClass("active");
            element.removeAttr("data-favourite-remove");
            element.attr("data-favourite-add", "");

            var favoritCount = $(
              ".list__item_favorite .list__item-icon span"
            ).text();
            var favoritCountNew = parseInt(favoritCount) - 1;
            $(".list__item_favorite .list__item-icon span").text(
              favoritCountNew
            );
          }
        } else {
          window.infoModal(ERROR_TITLE, a.ERROR);
        }
      },
    });
  });

  window.addEventListener("sendForm", (event) => {
    if (event.detail.form == "form-feedback") {
      var name = $('#form-feedback input[name="name"]').val();
      if (!name) {
        window.infoModal(ERROR_TITLE, "Введите имя.");
        return;
      }
      var email = $('#form-feedback input[name="email"]').val();
      if (!email) {
        window.infoModal(ERROR_TITLE, "Введите почту.");
        return;
      }
      var message = $('#form-feedback textarea[name="message"]').val();
      if (!message) {
        window.infoModal(ERROR_TITLE, "Введите сообщение.");
        return;
      }

      var data = {
        name: name,
        email: email,
        message: message,
      };

      jQuery.ajax({
        type: "POST",
        url: "/ajax/forms/feedback.php",
        data: data,
        dataType: "json",
        success: function (a) {
          if (!a.ERROR) {
            window.infoModal(SUCCESS_TITLE, a.MESSAGE);
            //location.reload();
          } else {
            window.infoModal(ERROR_TITLE, a.ERROR);
          }
        },
      });
    }
  });

  window.addEventListener("sendForm", (event) => {
    if (event.detail.form == "form-corporat") {
      var name = $('#form-corporat input[name="name"]').val();
      if (!name) {
        window.infoModal(ERROR_TITLE, "Введите имя.");
        return;
      }
      var email = $('#form-corporat input[name="email"]').val();
      if (!email) {
        window.infoModal(ERROR_TITLE, "Введите почту.");
        return;
      }
      var phone = $('#form-corporat input[name="phone"]').val();
      if (!phone) {
        window.infoModal(ERROR_TITLE, "Введите телефон.");
        return;
      }

      var data = {
        name: name,
        email: email,
        phone: phone,
      };

      jQuery.ajax({
        type: "POST",
        url: "/ajax/forms/corporat.php",
        data: data,
        dataType: "json",
        success: function (a) {
          if (!a.ERROR) {
            window.modal.close("corporat");
            window.infoModal(SUCCESS_TITLE, a.MESSAGE);
          } else {
            window.infoModal(ERROR_TITLE, a.ERROR);
          }
        },
      });
    }
  });

  $(".show-more-seo").on("click", function (evt) {
    evt.preventDefault();
    $(".cert-index__seo-text").addClass("show-all-seo");
    $(this).hide();
  });

  $("a").on("click", function () {
    if (
      !this.getAttribute("href").includes("#") &&
      this.getAttribute("target") != "_blank" &&
      !this.getAttribute("data-fancybox") &&
      this.getAttribute("href") != "javascript: void(0)" &&
      !$(this).parents("#bx-panel").length &&
      !this.classList.contains("fancy")
    ) {
      setTimeout(() => {
        window.preloader.show();
      }, 1000);
    }
  });
});

// const hoverableElement = document.getElementById('list__item_catalog');
// const catalog_menu = document.getElementById('catalog_menu');
//
// hoverableElement.append(catalog_menu);
