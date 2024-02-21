var Review = function () {
  this.add = function (params) {
    var params = params || [];

    var data = new FormData();
    data.append("params", JSON.stringify(params));
    if ($('#review-form input[name="files"]').get(0).files.length > 0) {
      var len = $('#review-form input[name="files"]').get(0).files.length;
      for (var i = 0; i < len; i++) {
        data.append(
          "files[]",
          $('#review-form input[name="files"]').get(0).files[i]
        );
      }
    }

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/addCertReview.php",
      data: data,
      processData: false,
      contentType: false,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          window.infoModal("Ура!", a.MESSAGE);
          if (a.RELOAD) {
            location.reload();
          }
        } else {
          window.infoModal("Упс…", a.ERROR);
        }
      },
    });
  };
};
var reviews = new Review();
$(function () {
  $(document).on("click", "[data-before-review-add]", function (e) {
    var orderId = $(this).data("order-id");
    $('#review-form input[name="orderId"]').val(orderId);
  });

  $(document).on("click", "[data-review-add]", function (e) {
    e.preventDefault();

    var name = $('#review-form input[name="name"]').val();
    var text = $('#review-form textarea[name="text"]').val();
    var orderId = $('#review-form input[name="orderId"]').val();
    var rating = $('#review-form input[name="RATING"]').val();

    var params = {
      name: name,
      text: text,
      orderId: orderId,
      rating: rating,
    };

    reviews.add(params);
  });
});
