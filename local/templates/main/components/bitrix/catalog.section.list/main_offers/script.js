$(function () {
  // Показать ещё
  $(document).on(
    "click",
    "[data-offers-container] [data-main-offers-showmore]",
    function (event) {
      event.preventDefault();
      $(this).css("visibility", "hidden");
      var params = getUrlParams();
      var page = $(this).data("page");
      var tab = $(
        "[data-offers-container] .list__item_active .list__link"
      ).data("offers-tab-switch");
      params["tab"] = tab;
      params["page"] = page;
      var url = setUrlParams(params);

      var ajaxContainer = "[data-offers-container] .objects__list";
      var ajaxPagerContainer = "[data-offers-container] .objects__more";
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

  // Табы
  $(document).on(
    "click",
    ".list__item:not(.list__item_active) [data-offers-tab-switch]",
    function (event) {
      event.preventDefault();

      var tab = $(this).data("offers-tab-switch");

      $("[data-offers-container] .list__item").removeClass("list__item_active");
      $(this).parent().addClass("list__item_active");

      var params = getUrlParams();
      params["tab"] = tab;
      var url = setUrlParams(params);

      var ajaxContainer = "[data-offers-container] .objects__list";
      var ajaxPagerContainer = "[data-offers-container] .objects__more";
      jQuery.ajax({
        type: "POST",
        url: url,
        dataType: "html",
        success: function (html) {
          var updContentHtml = $(html).find(ajaxContainer).html();
          $(ajaxContainer).html(updContentHtml);

          var updPagerHtml = $(html).find(ajaxPagerContainer).html() ?? "";
          $(ajaxPagerContainer).html(updPagerHtml);

          window.objectsGallery();
        },
      });
    }
  );

  $(".object[href]").on("click", function (evt) {
    if (
      $(evt.target).parents(".favorite").length ||
      $(evt.target).hasClass("favorite") ||
      $(evt.target).parents(".swiper-button-prev").length ||
      $(evt.target).hasClass("swiper-button-prev") ||
      $(evt.target).parents(".swiper-button-next").length ||
      $(evt.target).hasClass("swiper-button-next")
    ) {
      console.log($(evt.target).parent());
      return;
    }
    evt.preventDefault();
    var n = document.createElement("a");
    (n.href = $(this).attr("href")),
      (n.target = "_blank"),
      (n.rel = "noopener"),
      n.click(),
      n.remove();
  });
});
