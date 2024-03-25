$(function () {
  if (window.innerWidth < 450) {
    $(".object[href]").on("click", function (evt) {
      if ($(evt.target).parent().hasClass("favorite")) {
        return;
      }
      location.href = $(this).attr("href");
    });
  }
});
