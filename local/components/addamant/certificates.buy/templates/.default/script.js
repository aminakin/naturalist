"use strict";

class BuyCert {
  constructor() {
    this.bindEvents();
  }

  certs = document.querySelectorAll(".form__certs label");
  customPriceInput = document.querySelector(".nominal__custom-cost");

  bindEvents() {
    this.customPriceInputHandler();
  }

  customPriceInputHandler() {
    this.customPriceInput.addEventListener("focus", function () {
      this.previousElementSibling.checked = true;
    });
  }
}
