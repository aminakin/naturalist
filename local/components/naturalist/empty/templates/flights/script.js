$(function () {
  window.preloader.show();


  setTimeout(function(){
    let widgets = document.querySelectorAll('tp-cascoon')
    const flights_form = document.querySelector('.flights-form')
    flights_form.appendChild(widgets[0])
  },3000)

  for(let i = 1; i <= 4; i++){
    setTimeout(function(){
          let widgets = document.querySelectorAll('tp-cascoon')
          const popular_places__widget_items = document.querySelector('.popular-places__widget-items')
          popular_places__widget_items.appendChild(widgets[i])
        },
        3000)
  }

  setTimeout(function(){
    let widgets = document.querySelectorAll('tp-cascoon')
    const flights_map = document.querySelector('.flights-map')
    flights_map.appendChild(widgets[widgets.length - 1])
    window.preloader.hide();
  }, 3000)


  // Поиск объектов на главной
  $(document).on("click", "[data-main-search]", function (event) {
    event.preventDefault();
    window.preloader.show();

    $("[data-main-search]").attr("disabled", "disabled");

    var params = getUrlParams();

    var name = $("#form-main-search input[data-autocomplete-result]").val()
      ? $("#form-main-search input[data-autocomplete-result]").val()
      : $('#form-main-search input[name="name"]').val();
    if (name) {
      params["name"] = name;
    }

    var dateFrom = $("#form-main-search [data-date-from]").text();
    var dateTo = $("#form-main-search [data-date-to]").text();
    var guests = $("#form-main-search [data-guests-adults-count]").val();
    var children = [];
    $("#form-main-search .guests__children input[data-guests-children]").each(
      function (indx, element) {
        var age = $(element).val();
        children.push(age);
      }
    );

    if (dateFrom.trim() != "Заезд" && dateTo.trim() != "Выезд" && guests > 0) {
      params["dateFrom"] = dateFrom;
      params["dateTo"] = dateTo;
      params["guests"] = guests;

      let arDateFrom = dateFrom.split(".");
      let arDateTo = dateTo.split(".");

      let transformDateFrom = new Date(
        arDateFrom[1] + "/" + arDateFrom[0] + "/" + arDateFrom[2]
      );
      let transformDateTo = new Date(
        arDateTo[1] + "/" + arDateTo[0] + "/" + arDateTo[2]
      );

      if (transformDateFrom > transformDateTo) {
        var error = "Дата выезда не должна быть раньше заезда";
        window.infoModal("Упс…", error);
        $("[data-main-search]").removeAttr("disabled");
        return false;
      }

      if (children.length > 0) {
        params["children"] = children.length;
        params["childrenAge"] = children;
      }
      var url = "/catalog/" + setUrlParams(params);
      location.href = url;
    } else {
      var error = "Вы забыли указать даты заезда и выезда";
      window.infoModal("Ой...", error);
      $("[data-main-search]").removeAttr("disabled");
      window.preloader.hide();
    }
  });
});


  $(document).ready(function() {
  var swiper = new Swiper(' .gg .swiper-container', {
    loop: true,
    slidesPerView: 4,
    spaceBetween: 30,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    on: {
      slideChangeTransitionEnd: function () {
        // Получаем текущее значение трансформации
        var translateValue = this.getTranslate();

        // Проверяем, если значение меньше -3280px
        if (translateValue < -3280) {
          // Если меньше, устанавливаем его на -3280
          this.setTranslate(-3280);
        }
      }
    }
});

});

