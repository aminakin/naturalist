$(function () {
  if (window.innerWidth < 450) {
    $(".object-row[href]").on("click", function () {
      if ($(evt.target).parent().hasClass("favorite")) {
        return;
      }
      location.href = $(this).attr("href");
    });
  }
});
