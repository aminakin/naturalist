$(function () {
  $(".object[href]").on("click", function (evt) {
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
    evt.preventDefault();
    var n = document.createElement("a");
    (n.href = $(this).attr("href")),
      (n.target = "_blank"),
      (n.rel = "noopener"),
      n.click(),
      n.remove();
  });
});
