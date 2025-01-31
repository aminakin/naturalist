window.addEventListener("DOMContentLoaded", () => {
  const locationButtons = document.querySelectorAll(".location__btn-item");
  const locationGroup = document.querySelectorAll(".location__group");

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

  const locationAlphabetLetter = document.querySelectorAll(
    ".location-alphabet__letter"
  );

  locationAlphabetLetter.forEach((letterBtn) => {
    const letter = letterBtn.getAttribute("data-letter").toLowerCase();
    const lettersContainer = letterBtn.parentNode.parentNode;
    const locationContainer =
      lettersContainer.querySelector(".location-section");
    const locations = locationContainer.querySelectorAll(
      ".location-full__item"
    );

    letterBtn.addEventListener("click", function () {
      locationContainer.classList.remove("hidden");
      lettersContainer
        .querySelectorAll(".location-alphabet__letter")
        .forEach((letterBTN) => {
          letterBTN.classList.remove("active");
        });
      letterBtn.classList.add("active");

      locations.forEach((location) => {
        let text = location.textContent.toLowerCase();
        text = text.replace("республика ", "");
        text = text.replace("озеро ", "");
        text = text.replace("река ", "");

        if (text[0] == letter) {
          location.classList.remove("hidden");
        } else {
          location.classList.add("hidden");
        }
      });
    });
  });

  initAlphabet();

  function initAlphabet() {
    document.querySelectorAll(".location-alphabet").forEach((alphabet) => {
      alphabet
        .querySelector(".location-alphabet__letter:not(.disabled)")
        .click();
    });
  }

  if (window.isWater == "1") {
    document
      .querySelector('.location__btn-list a[data-btn="reservoirs"]')
      .click();
  }
});
