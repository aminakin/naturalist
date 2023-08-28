var Review = function() {
    this.update = function(reviewId, params) {
        var params = params || [];

        var data = new FormData();
        data.append('reviewId', reviewId);
        data.append('params', JSON.stringify(params));
        if($('#form-review input[name="files"]').get(0).files.length > 0) {
            var len = $('#form-review input[name="files"]').get(0).files.length;
            for(var i = 0; i < len; i++) {
                data.append('files[]', $('#form-review input[name="files"]').get(0).files[i]);
            }
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/updateReview.php',
            data: data,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        location.reload();
                    }
                    
                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    }
    this.delete = function(reviewId) {
        var data = {
            reviewId: reviewId
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/deleteReview.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        location.reload();
                    }
                    
                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    }
    this.deletePhoto = function(reviewId, photoId) {
        var data = {
            reviewId: reviewId,
            photoId: photoId
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/handlers/deleteReviewPhoto.php',
            data: data,
            dataType: 'json',
            success: function(a) {
                if(!a.ERROR) {
                    window.infoModal(SUCCESS_TITLE, a.MESSAGE);
                    if(a.RELOAD) {
                        location.reload();
                    }
                    
                } else {
                    window.infoModal(ERROR_TITLE, a.ERROR);
                }
            }
        });
    }
}
var reviews = new Review();
$(function() {
    $(document).on('click', '[data-review-edit]', function(e) {
        e.preventDefault();

        var reviewId = $(this).data('id');
        var data = {
            reviewId: reviewId
        }

        jQuery.ajax({
            type: 'POST',
            url: '/ajax/getReviewModal.php',
            data: data,
            dataType: 'html',
            success: function(html) {
                var htmlData = $(html).html();
                $('#form-review').html(htmlData);
                window.addDropzone();
            }
        });
    });

    $(document).on('click', '[data-review-update]', function(e) {
        e.preventDefault();
        var reviewId = $(this).data('id');

        var name = $('#form-review input[name="name"]').val();
        var text = $('#form-review textarea[name="text"]').val();
        var campingId = $('#form-review input[name="campingId"]').val();
        var orderId = $('#form-review input[name="orderId"]').val();

        var arCriterias = {};
        $('#form-review [data-rating-field]').each(function(indx, element) {
            var num = $(element).data('rating-field-num');
            var value = $(element).val();

            arCriterias[num] = value;
        });

        var params = {
            name: name,
            text: text,
            campingId: campingId,
            orderId: orderId,
            criterias: arCriterias
        }

        reviews.update(reviewId, params);
    });

    $(document).on('click', '[data-review-delete]', function(e) {
        e.preventDefault();
        var reviewId = $(this).data('id');
        reviews.delete(reviewId);
    });

    $(document).on('click', '[data-review-photo-delete]', function(e) {
        e.preventDefault();
        var reviewId = $(this).data('id');
        var photoId = $(this).data('photo-id');
        reviews.deletePhoto(reviewId, photoId);
    });
});