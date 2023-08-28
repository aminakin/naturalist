$(function() {
    // Поиск объектов
    $(document).on('click', '[data-favourites-search]', function(event) {
        event.preventDefault();

        var params = getUrlParams();

        var dateFrom = $('#form-favourites-search [data-date-from]').text();
        var dateTo = $('#form-favourites-search [data-date-to]').text();
        var guests = $('#form-favourites-search [data-guests-adults-count]').val();
        var children = [];
        $('#form-favourites-search .guests__children input[data-guests-children]').each(function(indx, element) {
            var age = $(element).val()
            children.push(age);
        });

        if(dateFrom != "Заезд" && dateTo != "Выезд" && guests > 0) {
			let arDateFrom = dateFrom.split('.');
			let arDateTo = dateTo.split('.');

			let transformDateFrom = new Date(arDateFrom[1] + "/" + arDateFrom[0] + "/" + arDateFrom[2]);
            let transformDateTo = new Date(arDateTo[1] + "/" + arDateTo[0] + "/" + arDateTo[2]);
            params["dateFrom"] = dateFrom;
            params["dateTo"] = dateTo;
            params["guests"] = guests;

            if(children.length > 0) {
                params["children"] = children.length;
                params["childrenAge"] = children;

            } else {
                deleteUrlParams(params, ['children', 'childrenAge']);
            }

            if(transformDateFrom > transformDateTo) {
                var error = 'Дата выезда не должна быть раньше заезда';
                window.infoModal('Упс…', error);
                return false;
            }

            var url = setUrlParams(params);
            location.href = url;
        } else {
            var error = 'Вы забыли указать даты заезда и выезда';
            window.infoModal('Ой...', error);
        }
    });
});