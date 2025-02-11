class BlogersSliderStories {
    constructor() {

        this.blogersItemsJson = JSON.parse(objectStories);
        this.blogerReviewsId = document.querySelector("#object__stories");
        this.obZuck = null;

        this.initZuck();
    }

    getBlogersItemsForZuck() {
        let storiesJsItems = [];

        for (const key in this.blogersItemsJson) {
            const bloger = this.blogersItemsJson[key];

            let blogerItem = {
                id: bloger["name"] + key,
                photo: bloger["preview"],
                name: bloger["name"],
                items: [
                    {
                        id: bloger["name"] + "_1",
                        type: "video",
                        length: 0,
                        src: bloger["video"],
                        preview: "",
                        link: "",
                        linkText: false,
                        time: false,
                    },
                ],
            };
            storiesJsItems.push(blogerItem);
        }

        return storiesJsItems;
    }

    initZuck() {
        this.obZuck = Zuck(this.blogerReviewsId, {
            backNative: false,
            previousTap: true,
            avatars: false,
            stories: this.getBlogersItemsForZuck(),
            localStorage: true,
            language: {
                unmute: "Нажмите, чтобы отключить звук",
                keyboardTip: "Нажмите пробел, чтобы увидеть следующее",
                visitLink: "Посетить ссылку",
                time: {
                    ago: "назад",
                    hour: "час назад",
                    hours: "часов назад",
                    minute: "минуту назад",
                    minutes: "минут назад",
                    fromnow: "сейчас",
                    seconds: "секунд назад",
                    yesterday: "вчера",
                    tomorrow: "завтра",
                    days: "дней назад",
                },
            },
        });
    }
}

document.addEventListener("DOMContentLoaded", function () {
    if (typeof objectStories !== undefined) {
        new BlogersSliderStories();
    }

    let faqItems = $(".faq__item");
åß
    if (faqItems.length) {
        faqItems.each(function () {
            $(this).on("click", function () {
                $(this).find(".faq__item-content").slideToggle();
                $(this).toggleClass("opened");
            });
        });
    }


    /* кастомные кнопки слайдера на сторисах */
    if ($('#object__stories-prev').length && $('#object__stories-next').length) {

        $('#object__stories-prev').on('click', function () {
            $('#object__stories').animate({
                scrollLeft: '-=100'
            }, 300, 'swing');
        });

        $('#object__stories-next').on('click', function () {
            $('#object__stories').animate({
                scrollLeft: '+=100'
            }, 300, 'swing');
        });
    }
});
