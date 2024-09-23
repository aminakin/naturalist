window.addEventListener("DOMContentLoaded", () => {
  const locationButtons = document.querySelectorAll(".location__btn-item");
  const locationGroup = document.querySelectorAll(".location__group");
  const btnAll = document.querySelector(".btn-all");
  const linkAll = document.querySelector(".link-all");

  locationButtons.forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      btnAction();

      if (this.classList.contains("active")) {
        locationGroup.forEach((group) => {
          group.classList.remove("active");
          if (
            button.getAttribute("data-btn") == group.getAttribute("data-group")
          ) {
            if (button.getAttribute("data-btn") == "reservoirs") {
              btnAll.setAttribute("href", "/regions/?water");
              linkAll.setAttribute("href", "/regions/?water");
            } else {
              btnAll.setAttribute("href", "/regions/");
              linkAll.setAttribute("href", "/regions/");
            }
            group.classList.add("active");
            button.classList.add("active");
          }
        });
      }
    });
  });

  function btnAction() {
    locationButtons.forEach((button) => {
      if (!button.classList.contains("active")) {
        button.classList.add("active");
      } else {
        button.classList.remove("active");
      }
    });
  }

  if (window.innerWidth < 620) {
    locationGroup.forEach((group) => {
      group.querySelectorAll(".location__item").forEach((item, index) => {
        if (index > 5) {
          item.style.display = "none";
        }
      });
    });
  }
});
