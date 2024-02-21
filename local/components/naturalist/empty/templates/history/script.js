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
      url: "/ajax/handlers/addReview.php",
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
    var campingId = $(this).data("camping-id");
    var orderId = $(this).data("order-id");

    $('#review-form input[name="campingId"]').val(campingId);
    $('#review-form input[name="orderId"]').val(orderId);
  });

  $(document).on("click", "[data-review-add]", function (e) {
    e.preventDefault();

    var arCriterias = {};
    $("#review-form [data-rating-field]").each(function (indx, element) {
      var num = $(element).data("rating-field-num");
      var value = $(element).val();

      arCriterias[num] = value;
    });

    var name = $('#review-form input[name="name"]').val();
    var text = $('#review-form textarea[name="text"]').val();
    var campingId = $('#review-form input[name="campingId"]').val();
    var orderId = $('#review-form input[name="orderId"]').val();

    var params = {
      name: name,
      text: text,
      campingId: campingId,
      orderId: orderId,
      criterias: arCriterias,
    };

    reviews.add(params);
  });

  // Фильтр по статусу
  $(document).on("click", "[data-status]", function (event) {
    event.preventDefault();

    var status = $(this).data("status");

    var params = getUrlParams();
    params["status"] = status;
    var url = setUrlParams(params);

    location.href = url;
  });

  // Поиск
  $(document).on(
    "click",
    "#form-order-search [data-order-search]",
    function (event) {
      event.preventDefault();

      var params = getUrlParams();

      var orderNum = $('#form-order-search input[name="orderNum"]').val();
      if (orderNum) {
        params["orderNum"] = orderNum;
      } else {
        delete params["orderNum"];
      }

      var url = setUrlParams(params);
      window.location = url;
    }
  );

  /*var filesExt = ['jpg','jpeg','png','gif'];
    $(document).on('change', '.dropzone', function(){
        if($('#review-form input[name="files"]').get(0).files.length > 0) {
            var len = $('#review-form input[name="files"]').get(0).files.length;
			var error = "";
			if(len > 10) {
				var error = "Превышен лимит фото.";
			}
            for(var i = 0; i < len; i++) {
				var sizeFile = $('#review-form input[name="files"]').get(0).files[i].size;
				//var parts = $('#review-form input[name="files"]').get(0).files[i].val().split('.');
				if (5242880 < sizeFile) {
					var error = error + "<br> Максимальный размер файла - 5 Мб";
                    break;
				}
            }
			if(error.length > 0) {
				window.infoModal("Обнаружены ошибки", error);
			}

        }
    });*/
});
