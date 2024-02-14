"use strict";

class BuyCert {
  constructor() {
    this.bindEvents();
    this.setSectionNumbers();
  }

  certs = document.querySelectorAll(".form__certs label");
  customPriceInput = document.querySelector(".nominal__custom-cost");
  splitBadge = document.querySelector(".split-badge");
  sectionNumbers = document.querySelectorAll(".form__number");
  sectionElectroVariants = document.querySelector(".form__block.el-variant");
  sectionFizVariants = document.querySelector(".form__block.variant");
  sectionFizPockets = document.querySelector(".form__block.pocket");
  dataElectro = document.querySelector(".user-data__block.electro");
  dataFiz = document.querySelector(".user-data__block.fiz");
  electroSelect = document.querySelector('input[value="electro"]');
  fizSelect = document.querySelector('input[value="fiz"]');
  cashPayment = document.querySelector('label[cash="Y"]');

  bindEvents() {
    this.customPriceInputHandler();
    this.certsSelectHandler();
    this.electroSelectHandler();
    this.fizSelectHandler();
  }

  certsSelectHandler() {
    let _this = this;
    this.certs.forEach((element) => {
      element.querySelector("input").addEventListener("change", function () {
        if (this.checked) {
          _this.refreshSplitBadge(this.getAttribute("cost"));
        }
      });
    });
  }

  customPriceInputHandler() {
    let _this = this;
    this.customPriceInput.addEventListener("focus", function () {
      this.previousElementSibling.checked = true;
    });
    this.customPriceInput.addEventListener("blur", function () {
      _this.refreshSplitBadge(this.value);
      this.reportValidity();
    });
  }

  refreshSplitBadge(amount) {
    let splitBadgeHtml = `
      <yandex-pay-badge
        merchant-id="d82873ad-61ce-4050-b05e-1f4599f0bb7b"
        type="bnpl"
        amount="${amount}"
        size="l"
        variant="detailed"
        theme="light"
        color="primary"
      />
    `;
    this.splitBadge.innerHTML = splitBadgeHtml;
  }

  setSectionNumbers() {
    let index = 1;
    this.sectionNumbers.forEach((element) => {
      if (element.offsetParent !== null) {
        element.textContent = index;
        index += 1;
      }
    });
  }

  showElectro() {
    this.sectionFizVariants.style.display = "none";
    this.sectionFizPockets.style.display = "none";
    this.dataFiz.style.display = "none";
    this.cashPayment.style.display = "none";
    this.sectionElectroVariants.style.display = "block";
    this.dataElectro.style.display = "block";
    this.setSectionNumbers();
  }

  showFiz() {
    this.sectionFizVariants.style.display = "block";
    this.sectionFizPockets.style.display = "block";
    this.dataFiz.style.display = "block";
    this.cashPayment.style.display = "flex";
    this.sectionElectroVariants.style.display = "none";
    this.dataElectro.style.display = "none";
    this.setSectionNumbers();
  }

  electroSelectHandler() {
    this.electroSelect.addEventListener("change", () => this.showElectro());
  }

  fizSelectHandler() {
    this.fizSelect.addEventListener("change", () => this.showFiz());
  }
}
