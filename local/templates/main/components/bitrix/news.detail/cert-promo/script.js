document.addEventListener("DOMContentLoaded", function () {
  let switchers = document.querySelectorAll(".sliders__switcher");
  let sliders = document.querySelectorAll(".sliders__one");

  if (window.innerWidth > 768) {
    let promoSwiper1 = new Swiper(".certs__swiper-one", {
      slidesPerView: 3,
      spaceBetween: 38,
      navigation: {
        nextEl: "#box .swiper-button-next",
        prevEl: "#box .swiper-button-prev",
      },
    });
    let promoSwiper2 = new Swiper(".certs__swiper-two", {
      slidesPerView: 3,
      spaceBetween: 38,
      navigation: {
        nextEl: "#elec .swiper-button-next",
        prevEl: "#elec .swiper-button-prev",
      },
    });
    let promoSwiper3 = new Swiper(".certs__video-slider", {
      slidesPerView: 3,
      spaceBetween: 50,
      navigation: {
        nextEl: "#video .swiper-button-next",
        prevEl: "#video .swiper-button-prev",
      },
    });
  } else {
    sliders.forEach((slider) => {
      slider
        .querySelector(".swiper-wrapper")
        .classList.remove("swiper-wrapper");
    });
    document
      .querySelector(".video-mob-wrap")
      .classList.remove("swiper-wrapper");
  }

  if (switchers.length) {
    switchers.forEach((element) => {
      element.addEventListener("click", () => {
        const target = element.getAttribute("slider");
        switchers.forEach((switcher) => {
          switcher.classList.remove("active");
        });
        sliders.forEach((slider) => {
          if (slider.id == target) {
            slider.classList.add("active");
          } else {
            slider.classList.remove("active");
          }
        });
        element.classList.add("active");
      });
    });
  }

  document
    .querySelector(".button.video-more")
    .addEventListener("click", (evt) => {
      evt.target.style.display = "none";
      document
        .querySelectorAll(".video-mob-wrap .swiper-slide")
        .forEach((element) => {
          element.style.display = "block";
        });
    });

  Fancybox.bind("[data-fancybox]", {
    // Your custom options
  });
});
