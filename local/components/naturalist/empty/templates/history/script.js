$(function () {
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
