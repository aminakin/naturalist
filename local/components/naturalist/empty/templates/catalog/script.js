$(function () {
  if (window.innerWidth < 450) {
    $(".object-row[href]").on("click", function () {
      location.href = $(this).attr("href");
    });
  }
});
