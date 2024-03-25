$(function () {
  if (window.innerWidth < 450) {
    $(".object[href]").on("click", function () {
      location.href = $(this).attr("href");
    });
  }
});
