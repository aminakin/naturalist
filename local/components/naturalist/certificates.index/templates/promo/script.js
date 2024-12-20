$(document).ready(function() {

    const step = ".certificates_index__question",
        stepPreviewText = ".certificates_index__question-preview-title",
        stepDetailText = ".certificates_index__question-text",
        stepsWrapper = ".certificates_index__questions";

    function rollUp(target, targetWrapper, targetPreviewText, targetDetailText) {
        target.closest(targetWrapper).find(targetPreviewText).slideDown()
        target.closest(targetWrapper).find(targetDetailText).slideUp()
    }

    function rollDown(target, targetWrapper, targetPreviewText, targetDetailText) {
        target.closest(targetWrapper).find(targetPreviewText).slideUp()
        target.closest(targetWrapper).find(targetDetailText).slideDown()
    }

    function rollUpAll(target, targetWrapper, targetPreviewText, targetDetailText) {
        $(target).each(function(index, element) {
            if (!$(element).hasClass("active")) {
                rollUp($(element), targetWrapper, targetPreviewText, targetDetailText)
            }
        })
    }

    $(step).click(function() {
        if (!$(this).hasClass("active")) {

            $(this).addClass("active").siblings().removeClass("active")

            rollDown($(this), step, stepPreviewText, stepDetailText)

            rollUpAll($(this).closest(stepsWrapper).find(step), step, stepPreviewText, stepDetailText)

        } else {
            $(this).removeClass("active")

            rollUp($(this), step, stepPreviewText, stepDetailText)

        }
    })

})