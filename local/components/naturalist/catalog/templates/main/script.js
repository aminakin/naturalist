/* Каталог */
$(function () {
  // Показать ещё
  $(document).on(
    "click",
    "[data-catalog-container] [data-catalog-showmore]",
    function (event) {
      event.preventDefault();
      $(this).css("visibility", "hidden");
      var params = getUrlParams();
      var page = $(this).data("page");
      params["page"] = page;
      var url = setUrlParams(params);
      var showenElements = $(".catalog__list > div").length;

      var ajaxContainer = "[data-catalog-container] .catalog__list";
      var ajaxPagerContainer = "[data-catalog-container] .catalog__more";

      jQuery.ajax({
        type: "POST",
        url: url,
        dataType: "html",
        success: function (html) {
          showenElements += $(html).find(ajaxContainer + " > div").length;
          //sendDataToLocalStorage(page, showenElements);
          var updContentHtml = $(html).find(ajaxContainer).html();
          $(ajaxContainer).append(updContentHtml);

          var updPagerHtml = $(html).find(ajaxPagerContainer).html() ?? "";
          $(ajaxPagerContainer).html(updPagerHtml);

          window.objectsGallery();
          window.map.handleItemHover();

          if (updPagerHtml == "") {
            $("#same_items").show();
          }
        },
      });
    }
  );

  function sendDataToLocalStorage(page, items) {
    let data = {
      page: page,
      items: items,
    };
    console.log(data);
    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/setLocalStorage.php",
      data: data,
      dataType: "json",
      success: function (data) {
        console.log(data);
      },
    });
  }

  // Сортировка
  $(document).on("click", "[data-sort]", function (event) {
    event.preventDefault();

    var sort = $(this).data("sort");
    var order = $(this).data("type");

    var params = getUrlParams();
    delete params["page"];
    params["sort"] = sort;
    params["order"] = order;
    var url = setUrlParams(params);

    location.href = url;
  });

  // Фильтр - применение
  $(document).on("click", "[data-filter-set]", function (event) {
    event.preventDefault();

    $("[data-filter-set]").attr("disabled", "disabled");

    var frontFilter = $(this).data("filter-catalog-front-btn") ?? false;

    var parentFrom = $(this).parents("form");

    var params = getUrlParams();

    params['maxPrice'] = $(".max-price").val();
    params['mimPrice'] = $(".min-price").val();

    if ($('input[name="type"]:checked', parentFrom).length > 0) {
      var arTypes = [];
      $('input[name="type"]:checked', parentFrom).each(function (
        indx,
        element
      ) {
        arTypes.push($(element).val());
      });
      params["types"] = arTypes.join(",");
    } else {
      delete params["types"];
    }

    var name = $("input[data-autocomplete-result]", parentFrom).val()
      ? $("input[data-autocomplete-result]", parentFrom).val()
      : $('input[name="name"]', parentFrom).val();
    if (name) {
      params["name"] = name;
    } else {
      delete params["name"];
    }

    if ($('input[name="water"]:checked', parentFrom).length > 0) {
      var arWater = [];
      $('input[name="water"]:checked', parentFrom).each(function (
        indx,
        element
      ) {
        arWater.push($(element).val());
      });
      params["water"] = arWater.join(",");
    } else {
      delete params["water"];
    }

    if ($('input[name="services"]:checked', parentFrom).length > 0) {
      var arServices = [];
      $('input[name="services"]:checked', parentFrom).each(function (
        indx,
        element
      ) {
        arServices.push($(element).val());
      });
      params["services"] = arServices.join(",");
    } else {
      delete params["services"];
    }

    if ($('input[name="food"]:checked', parentFrom).length > 0) {
      var arFood = [];
      $('input[name="food"]:checked', parentFrom).each(function (
        indx,
        element
      ) {
        arFood.push($(element).val());
      });
      params["food"] = arFood.join(",");
    } else {
      delete params["food"];
    }

    if ($('input[name="restvariants"]:checked', parentFrom).length > 0) {
      var arRestvariants = [];
      $('input[name="restvariants"]:checked', parentFrom).each(function (
        indx,
        element
      ) {
        arRestvariants.push($(element).val());
      });
      params["restvariants"] = arRestvariants.join(",");
    } else {
      delete params["restvariants"];
    }

    if ($('input[name="objectcomforts"]:checked', parentFrom).length > 0) {
      var arObjectcomforts = [];
      $('input[name="objectcomforts"]:checked', parentFrom).each(function (
        indx,
        element
      ) {
        arObjectcomforts.push($(element).val());
      });
      params["objectcomforts"] = arObjectcomforts.join(",");
    } else {
      delete params["objectcomforts"];
    }

    if ($('input[name="features"]:checked', parentFrom).length > 0) {
      var arFeatures = [];
      $('input[name="features"]:checked', parentFrom).each(function (
        indx,
        element
      ) {
        arFeatures.push($(element).val());
      });
      params["features"] = arFeatures.join(",");
    } else {
      delete params["features"];
    }

    if ($('input[name="housetypes"]:checked', parentFrom).length > 0) {
      var arHousetypes = [];
      $('input[name="housetypes"]:checked', parentFrom).each(function (
        indx,
        element
      ) {
        arHousetypes.push($(element).val());
      });
      params["housetypes"] = arHousetypes.join(",");
    } else {
      delete params["housetypes"];
    }

    var dateFrom = $("[data-date-from]", parentFrom).text();
    var dateTo = $("[data-date-to]", parentFrom).text();
    var guests = $('input[name="guests-adults-count"]', parentFrom).val();
    var children = [];
    $(".guests__children input[data-guests-children]", parentFrom).each(
      function (indx, element) {
        var age = $(element).val();
        children.push(age);
      }
    );

    if (dateFrom.trim() != "Заезд" && dateTo.trim() != "Выезд" && guests > 0) {
      let arDateFrom = dateFrom.split(".");
      let arDateTo = dateTo.split(".");

      let transformDateFrom = new Date(
        arDateFrom[1] + "/" + arDateFrom[0] + "/" + arDateFrom[2]
      );
      let transformDateTo = new Date(
        arDateTo[1] + "/" + arDateTo[0] + "/" + arDateTo[2]
      );

      params["dateFrom"] = dateFrom;
      params["dateTo"] = dateTo;
      params["guests"] = guests;

      if (children.length > 0) {
        params["children"] = children.length;
        params["childrenAge"] = children;
      } else {
        deleteUrlParams(params, ["children", "childrenAge"]);
      }

      if (transformDateFrom > transformDateTo) {
        var error = "Дата выезда не должна быть раньше заезда";
        window.infoModal("Упс…", error);
        $("[data-filter-set]").removeAttr("disabled");
        return false;
      }
    }
    // else {
    //   var error = "Вы забыли указать даты заезда и выезда";
    //   window.infoModal("Ой…", error);
    //   $("[data-filter-set]").removeAttr("disabled");
    //   return false;
    // }

    deleteUrlParams(params, ["page"]);

    console.log(params);

    if (Object.keys(params).length > 0) {
      window.preloader.show();
      var url = setUrlParams(params);
      if (location.pathname == "/map/") {
        window.location = location.pathname + url;
      } else {
        window.location = "/catalog/" + url;
      }
    }
  });

  //Фильтр очистка автозаполнения
  $(document).on("change", '.filters input[name="name"]', function (event) {
    $(".filters input[data-autocomplete-result]").val("");
  });

  // Фильтр - сброс
  $(document).on("click", "[data-filter-reset]", function (event) {
    event.preventDefault();

    var params = getUrlParams();
    deleteUrlParams(params, [
      "types",
      "services",
      "food",
      "features",
      "name",
      "dateFrom",
      "dateTo",
      "guests",
      "children",
      "childrenAge",
      "page",
      "sort",
    ]);

    var url = setUrlParams(params);

    if (location.pathname.includes("catalog")) {
      window.location = location.origin + "/catalog/";
    } else {
      window.location = location.origin + "/map/";
    }
  });
});

/* Объект */
$(function () {
  // Показать ещё (номера)
  $(document).on(
    "click",
    "[data-object-container] [data-object-showmore]",
    function (event) {
      event.preventDefault();
      $(this).css("visibility", "hidden");
      var params = getUrlParams();
      var page = $(this).data("page");
      params["page"] = page;
      var url = setUrlParams(params);

      var ajaxContainer = "[data-object-container] .rooms__list";
      var ajaxPagerContainer = "[data-object-container] .rooms__more";
      jQuery.ajax({
        type: "POST",
        url: url,
        dataType: "html",
        success: function (html) {
          var updContentHtml = $(html).find(ajaxContainer).html();
          $(ajaxContainer).append(updContentHtml);

          var updPagerHtml = $(html).find(ajaxPagerContainer).html() ?? "";
          $(ajaxPagerContainer).html(updPagerHtml);

          window.objectsGallery();
        },
      });
    }
  );

  // Показать ещё (отзывы)
  $(document).on(
    "click",
    "[data-object-reviews-container] [data-object-reviews-showmore]",
    function (event) {
      event.preventDefault();

      var params = getUrlParams();
      var page = $(this).data("page");
      params["reviewsPage"] = page;
      var url = setUrlParams(params);

      var ajaxContainer = "[data-object-reviews-container] .reviews__list";
      var ajaxPagerContainer = "[data-object-reviews-container] .reviews__more";
      jQuery.ajax({
        type: "POST",
        url: url,
        dataType: "html",
        success: function (html) {
          var updContentHtml = $(html).find(ajaxContainer).html();
          $(ajaxContainer).append(updContentHtml);

          var updPagerHtml = $(html).find(ajaxPagerContainer).html() ?? "";
          $(ajaxPagerContainer).html(updPagerHtml);

          window.objectsGallery();
        },
      });
    }
  );

  // Сортировка (отзывы)
  $(document).on(
    "click",
    "[data-object-reviews-container] [data-sort]",
    function (event) {
      event.preventDefault();

      var sort = $(this).data("sort");
      var order = $(this).data("type");

      var params = getUrlParams();
      params["sort"] = sort;
      //params["order"] = order;
      var url = setUrlParams(params);

      location.href = url + "#reviews-anchor";
    }
  );

  // Фильтр (ajax)
  $(document).on("click", "[data-object-filter-set]", function (event) {
    event.preventDefault();

    var frontFilter = $(this).data("filter-catalog-front-btn") ?? false;

    var parentFrom = $(this).parents("form");

    var params = getUrlParams();
    var dateFrom = $.trim($("[data-date-from]", parentFrom).text());
    var dateTo = $.trim($("[data-date-to]", parentFrom).text());
    var guests = $('input[name="guests-adults-count"]', parentFrom).val();
    var children = [];
    $(".guests__children input[data-guests-children]", parentFrom).each(
      function (indx, element) {
        var age = $(element).val();
        children.push(age);
      }
    );

    if (dateFrom.trim() != "Заезд" && dateTo.trim() != "Выезд" && guests > 0) {
      let arDateFrom = dateFrom.split(".");
      let arDateTo = dateTo.split(".");

      let transformDateFrom = new Date(
        arDateFrom[1] + "/" + arDateFrom[0] + "/" + arDateFrom[2]
      );
      let transformDateTo = new Date(
        arDateTo[1] + "/" + arDateTo[0] + "/" + arDateTo[2]
      );
      params["dateFrom"] = dateFrom;
      params["dateTo"] = dateTo;
      params["guests"] = guests;

      if (children.length > 0) {
        params["children"] = children.length;
        params["childrenAge"] = children;
      } else {
        deleteUrlParams(params, ["children", "childrenAge"]);
      }

      if (transformDateFrom > transformDateTo) {
        var error = "Дата выезда не должна быть раньше заезда";
        window.infoModal("Упс…", error);
        $("[data-object-filter-set]").removeAttr("disabled");
        return false;
      }

      var url = setUrlParams(params);
      var ajaxContainer = "section.section_room";
      var ajaxContainerRelated = "section.section_related";

      jQuery.ajax({
        type: "POST",
        url: url,
        dataType: "html",
        beforeSend: function () {
          window.preloader.show();
          $("[data-object-filter-set]").attr("disabled", "disabled");
        },
        success: function (html) {
          window.preloader.hide();
          $("[data-object-filter-set]").removeAttr("disabled");
          var updContentHtml = $(html).find(ajaxContainer);

          if (updContentHtml.length > 0) {
            if ($(ajaxContainer).length > 0) {
              $(ajaxContainer).html(updContentHtml.html());
            } else {
              $("section.section_about").after(updContentHtml);
            }

            window.objectsGallery();
            window.scrollToElement("#rooms-anchor");
            window.history.replaceState(null, null, url);
          } else {
            var error = $(html).find(".search-error").text();
            window.infoModal("Ну вот....", error);
          }

          var updContentHtmlRelated = $(html).find(ajaxContainerRelated);
          if (updContentHtmlRelated.length > 0) {
            if ($(ajaxContainerRelated).length > 0) {
              $(ajaxContainerRelated).html(updContentHtmlRelated.html());
            } else {
              $("section.section_about").after(updContentHtmlRelated);
            }
            window.objectsGallery();
            window.sliderRelated();
          }
        },
      });
    } else {
      var error = "Вы забыли указать даты заезда и выезда";
      window.infoModal("Ой...", error);
      $("[data-object-filter-set]").removeAttr("disabled");
    }
  });
});

var Review = function () {
  this.addLike = function (reviewId, value) {
    var data = {
      reviewId: reviewId,
      value: value,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/addReviewLike.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          window.infoModal("Ура!", a.MESSAGE);
          if (a.RELOAD) {
            setTimeout(function () {
              location.reload();
            }, 1500);
          }
        } else {
          if (a.ERROR == "Ошибка доступа.") {
            window.modal.open("login-phone");
          } else {
            window.infoModal("Упс…", a.ERROR);
          }
        }
      },
    });
  };
  this.deleteLike = function (reviewId) {
    var data = {
      reviewId: reviewId,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/deleteReviewLike.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          window.infoModal(SUCCESS_TITLE, a.MESSAGE);
          if (a.RELOAD) {
            setTimeout(function () {
              location.reload();
            }, 1500);
          }
        } else {
          if (a.ERROR == "Ошибка доступа.") {
            window.modal.open("login-phone");
          } else {
            window.infoModal("Упс…", a.ERROR);
          }
        }
      },
    });
  };
};
var reviews = new Review();

$(function () {
  $(document).on("click", "[data-like-add]", function (e) {
    e.preventDefault();
    var reviewId = $(this).data("id");
    var value = $(this).data("value");
    reviews.addLike(reviewId, value);
  });
  $(document).on("click", "[data-like-delete]", function (e) {
    e.preventDefault();
    var reviewId = $(this).data("id");
    reviews.deleteLike(reviewId);
  });
});

$(document).ready(function () {
  
  
  let minPrice = $(".min-price").data("price-value");
  let maxPrice = $(".max-price").data("price-value");
  console.log(minPrice);
  $(".slider-range").slider({
    range: true,
    values: [minPrice, maxPrice],
    min: minPrice,
    max: maxPrice,
    slide: function (event, ui) {
      $('.min-price').val(ui.values[0]);
      $('.max-price').val(ui.values[1]);
    }
  });
  $(".slider-range").on("slidechange", function (event, ui) {
    let minPrice = ui.values[0];
    let maxPrice = ui.values[1];
  });

  $('.button.price').on("click", function () {
    $(this).toggleClass('active');
    if($(this).hasClass('active')){
      $('.catalog_sorter .price-filter').css('display', 'block');
    }else{
      $('.catalog_sorter .price-filter').css('display', 'none');
    }
  }); 

  $('.sort__btn').on("click", function () {
    $(this).toggleClass('active');
    if($(this).hasClass('active')){
      $('.sort__list').css('display', 'block');
    }else{
      $('.sort__list').css('display', 'none');
    }
  });

  $('.sort__btn span').html($('span.list__link span').html());
});
