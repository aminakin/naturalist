/* Каталог */
$(function() {
    // Показать ещё
    $(document).on('click', '[data-catalog-container] [data-catalog-showmore]', function(event) {
        event.preventDefault();

        var params = getUrlParams();
        var page = $(this).data('page');
        params["page"] = page;
        var url = setUrlParams(params);
        var showenElements = $('.catalog__list > div').length;

        var ajaxContainer = '[data-catalog-container] .catalog__list';
        var ajaxPagerContainer = '[data-catalog-container] .catalog__more';        
        
        jQuery.ajax({
            type: 'POST',
            url: url,
            dataType: 'html',
            success: function(html) {
                showenElements += $(html).find(ajaxContainer + ' > div').length;
                //sendDataToLocalStorage(page, showenElements);
                var updContentHtml = $(html).find(ajaxContainer).html();
                $(ajaxContainer).append(updContentHtml);

                var updPagerHtml = $(html).find(ajaxPagerContainer).html() ?? '';
                $(ajaxPagerContainer).html(updPagerHtml);

                window.objectsGallery();
                window.map.handleItemHover();
            }
        });        
    });

    function sendDataToLocalStorage(page, items) {
        let data = {
            page: page,
            items: items,
        }
        console.log(data);
        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/setLocalStorage.php',
            data: data,
            dataType: 'json',
            success: function(data) {
                console.log(data);
            }
        });
    }

    // Сортировка
    $(document).on('click', '[data-catalog-container] [data-sort]', function(event) {
        event.preventDefault();

        var sort = $(this).data('sort');
        var order = $(this).data('type');

        var params = getUrlParams();
        params["sort"] = sort;
        params["order"] = order;
        var url = setUrlParams(params);

        location.href = url;
    });

    // Фильтр - применение
    $(document).on('click', '[data-filter-set]', function(event) {
        event.preventDefault();

        $('[data-filter-set]').attr('disabled', 'disabled');

        var params = getUrlParams();

        if($('#form-catalog-filter input[name="type"]:checked').length > 0) {
            var arTypes = [];
            $('#form-catalog-filter input[name="type"]:checked').each(function(indx, element) {
                arTypes.push($(element).val());
            });
            params['types'] = arTypes.join(',');

        } else {
            delete params['types'];
        }

        if($('#form-catalog-filter input[name="services"]:checked').length > 0) {
            var arServices = [];
            $('#form-catalog-filter input[name="services"]:checked').each(function(indx, element) {
                arServices.push($(element).val());
            });
            params['services'] = arServices.join(',');

        } else {
            delete params['services'];
        }

        if($('#form-catalog-filter input[name="food"]:checked').length > 0) {
            var arFood = [];
            $('#form-catalog-filter input[name="food"]:checked').each(function(indx, element) {
                arFood.push($(element).val());
            });
            params['food'] = arFood.join(',');

        } else {
            delete params['food'];
        }

        if($('#form-catalog-filter input[name="features"]:checked').length > 0) {
            var arFeatures = [];
            $('#form-catalog-filter input[name="features"]:checked').each(function(indx, element) {
                arFeatures.push($(element).val());
            });
            params['features'] = arFeatures.join(',');

        } else {
            delete params['features'];
        }

        var name = $('#form-catalog-filter input[data-autocomplete-result]').val()
         ? $('#form-catalog-filter input[data-autocomplete-result]').val()
         : $('#form-catalog-filter input[name="name"]').val();
        if(name) {
            params["name"] = name;
        } else {
        	delete params["name"];
        }

        var dateFrom = $('#form-catalog-filter [data-date-from]').text();
        var dateTo = $('#form-catalog-filter [data-date-to]').text();
        var guests = $('#form-catalog-filter input[name="guests-adults-count"]').val();
        var children = [];
        $('#form-catalog-filter .guests__children input[data-guests-children]').each(function(indx, element) {
            var age = $(element).val()
            children.push(age);
        });
        if(dateFrom != "Заезд" && dateTo != "Выезд" && guests > 0) {
			let arDateFrom = dateFrom.split('.');
			let arDateTo = dateTo.split('.');

			let transformDateFrom = new Date(arDateFrom[1] + "/" + arDateFrom[0] + "/" + arDateFrom[2]);
            let transformDateTo = new Date(arDateTo[1] + "/" + arDateTo[0] + "/" + arDateTo[2]);
            console.log(transformDateFrom);
            console.log(transformDateTo);
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
                window.infoModal("Упс…", error);
                $('[data-filter-set]').removeAttr('disabled');
                return false;
            }
        } else {
        	var error = 'Вы забыли указать даты заезда и выезда';
            window.infoModal("Ой…", error);
            $('[data-filter-set]').removeAttr('disabled');
            return false;
        }

        deleteUrlParams(params, ['page']);

        if(Object.keys(params).length > 0) {
            var url = setUrlParams(params);
            window.location = url;
        }
    });

    //Фильтр очистка автозаполнения
     $(document).on('change', '#form-catalog-filter input[name="name"]', function(event) {
        $('#form-catalog-filter input[data-autocomplete-result]').val('');
    });

    // Фильтр - сброс
    $(document).on('click', '[data-filter-reset]', function(event) {
        event.preventDefault();

        var params = getUrlParams();
        deleteUrlParams(params, ['types', 'services', 'food', 'features', 'name', 'dateFrom', 'dateTo', 'guests', 'children', 'childrenAge', 'page', 'sort']);

        var url = setUrlParams(params);
        window.location = url;
    });
});

/* Объект */
$(function() {
    // Показать ещё (номера)
    $(document).on('click', '[data-object-container] [data-object-showmore]', function(event) {
        event.preventDefault();

        var params = getUrlParams();
        var page = $(this).data('page');
        params["page"] = page;
        var url = setUrlParams(params);

        var ajaxContainer = '[data-object-container] .rooms__list';
        var ajaxPagerContainer = '[data-object-container] .rooms__more';
        jQuery.ajax({
            type: 'POST',
            url: url,
            dataType: 'html',
            success: function(html) {
                var updContentHtml = $(html).find(ajaxContainer).html();
                $(ajaxContainer).append(updContentHtml);

                var updPagerHtml = $(html).find(ajaxPagerContainer).html() ?? '';
                $(ajaxPagerContainer).html(updPagerHtml);

                window.objectsGallery();
            }
        });
    });

    // Показать ещё (отзывы)
    $(document).on('click', '[data-object-reviews-container] [data-object-reviews-showmore]', function(event) {
        event.preventDefault();

        var params = getUrlParams();
        var page = $(this).data('page');
        params["reviewsPage"] = page;
        var url = setUrlParams(params);

        var ajaxContainer = '[data-object-reviews-container] .reviews__list';
        var ajaxPagerContainer = '[data-object-reviews-container] .reviews__more';
        jQuery.ajax({
            type: 'POST',
            url: url,
            dataType: 'html',
            success: function(html) {
                var updContentHtml = $(html).find(ajaxContainer).html();
                $(ajaxContainer).append(updContentHtml);

                var updPagerHtml = $(html).find(ajaxPagerContainer).html() ?? '';
                $(ajaxPagerContainer).html(updPagerHtml);

                window.objectsGallery();
            }
        });
    });

    // Сортировка (отзывы)
    $(document).on('click', '[data-object-reviews-container] [data-sort]', function(event) {
        event.preventDefault();

        var sort = $(this).data('sort');
        var order = $(this).data('type');

        var params = getUrlParams();
        params["sort"] = sort;
        //params["order"] = order;
        var url = setUrlParams(params);

        location.href = url+'#reviews-anchor';
    });

    // Фильтр (ajax)
    $(document).on('click', '[data-object-filter-set]', function(event) {
        event.preventDefault();

        var params = getUrlParams();
        var dateFrom = $.trim($('#form-object-filter [data-date-from]').text());
        var dateTo   = $.trim($('#form-object-filter [data-date-to]').text());
        var guests   = $('#form-object-filter input[name="guests-adults-count"]').val();
        var children = [];
        $('#form-object-filter .guests__children input[data-guests-children]').each(function(indx, element) {
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
                window.infoModal("Упс…", error);
                $('[data-object-filter-set]').removeAttr('disabled');
                return false;
            }

            var url = setUrlParams(params);
            var ajaxContainer = 'section.section_room';
            var ajaxContainerRelated = 'section.section_related';

            jQuery.ajax({
                type: 'POST',
                url: url,
                dataType: 'html',
                beforeSend: function () {
                    $('[data-object-filter-set]').attr('disabled', 'disabled');
                },
                success: function(html) {
                    $('[data-object-filter-set]').removeAttr('disabled');
                    var updContentHtml = $(html).find(ajaxContainer);

                    if(updContentHtml.length > 0) {
                        if($(ajaxContainer).length > 0) {
                            $(ajaxContainer).html(updContentHtml.html());
                        } else {
                            $('section.section_about').after(updContentHtml);
                        }

                        window.objectsGallery();
                        window.scrollToElement('#rooms-anchor');
                        window.history.replaceState(null, null, url);

                    } else {
                        var error = 'Не найдено номеров на выбранные даты';
                        window.infoModal("Ну вот....", error);
                    }

                    var updContentHtmlRelated = $(html).find(ajaxContainerRelated);
                    if(updContentHtmlRelated.length > 0) {
                        if($(ajaxContainerRelated).length > 0) {
                            $(ajaxContainerRelated).html(updContentHtmlRelated.html());
                        } else {
                            $('section.section_about').after(updContentHtmlRelated);
                        }
                        window.objectsGallery();
                        window.sliderRelated();
                    }
                }
            });

        } else {
            var error = 'Вы забыли указать даты заезда и выезда';
            window.infoModal('Ой...', error);
            $('[data-object-filter-set]').removeAttr('disabled');
        }
    });
});

var Review = function() {
    this.addLike = function(reviewId, value) {
        var data = {
            reviewId: reviewId,
            value: value
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/addReviewLike.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal("Ура!", a.MESSAGE);
                    if(a.RELOAD) {
                        setTimeout(function() {
                            location.reload()
                        }, 1500);
                    }

                } else {
                    if(a.ERROR == "Ошибка доступа."){
                        window.modal.open('login-phone');
                    } else {
                        window.infoModal("Упс…", a.ERROR);
                    }
                }
            }
        });
    }
    this.deleteLike = function(reviewId) {
        var data = {
            reviewId: reviewId
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/deleteReviewLike.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        setTimeout(function() {
                            location.reload()
                        }, 1500);
                    }

                } else {
                    if(a.ERROR == "Ошибка доступа."){
                        window.modal.open('login-phone');
                    } else {
                        window.infoModal("Упс…", a.ERROR);
                    }
                }
            }
        });
    }
}
var reviews = new Review();

$(function() {
    $(document).on('click', '[data-like-add]', function(e) {
        e.preventDefault();
        var reviewId = $(this).data('id');
        var value = $(this).data('value');
        reviews.addLike(reviewId, value);
    });
    $(document).on('click', '[data-like-delete]', function(e) {
        e.preventDefault();
        var reviewId = $(this).data('id');
        reviews.deleteLike(reviewId);
    });
});