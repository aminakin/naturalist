window.onload = function () {
  let sliders = document.querySelectorAll(".swiper-slide");
  let showMore = document.querySelector(".cert-index__more");
  let hideShowMore = true;

  function checkVisible() {
    sliders.forEach((element) => {
      hideShowMore = true;
      let style = window.getComputedStyle(element);
      if (style.display == "none") {
        hideShowMore = false;
      }
    });
  }

  showMore.addEventListener("click", function (evt) {
    evt.preventDefault();
    let count = 0;
    sliders.forEach((element) => {
      let style = window.getComputedStyle(element);
      if (style.display == "none" && count < 2) {
        element.style.display = "block";
        count += 1;
      }
    });
    checkVisible();
    if (hideShowMore) {
      this.style.display = "none";
    }
  });
};
