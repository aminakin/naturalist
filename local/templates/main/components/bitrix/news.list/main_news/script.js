$(function() {
    $(document).on('click', '[data-news-container] [data-main-news-showmore]', function(event) {
        event.preventDefault();

        $('[data-news-container] .news-preview__item-hidden').removeClass('news-preview__item-hidden');
        if($('[data-news-container] .news-preview__item-hidden').length == 0) {
            $('.news-preview__show').remove();
        }
    });
});