$(function () {
  if (window.innerWidth < 450) {
    $(".object-row[href]").on("click", function (evt) {
      if (
        $(evt.target).parents(".favorite").length ||
        $(evt.target).hasClass("favorite") ||
        $(evt.target).parents(".swiper-button-prev").length ||
        $(evt.target).hasClass("swiper-button-prev") ||
        $(evt.target).parents(".swiper-button-next").length ||
        $(evt.target).hasClass("swiper-button-next")
      ) {
        return;
      }
      location.href = $(this).attr("href");
    });
  }
});
