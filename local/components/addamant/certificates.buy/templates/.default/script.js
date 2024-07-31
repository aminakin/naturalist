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
  cityRadio = document.querySelectorAll('input[name="city"]');
  addressInput = document.querySelector('input[name="address"]');
  deliveryInput = document.querySelectorAll('input[name="delivery"]');
  deliveryBlock = document.querySelector(".user-data__block.delivery");
  prodCostElement = document.querySelector(".summ__item.prod .summ__price");
  deliveryCostElement = document.querySelector(
    ".summ__item.del-var .summ__price"
  );
  summCostElement = document.querySelector(".summ__item.all .summ__price");
  certVariantsLabels = document.querySelectorAll(
    ".variant .form__el-variant label"
  );
  certVariants = document.querySelectorAll('input[name="cert_variant"]');
  certElVariantsLabels = document.querySelectorAll(
    ".el-variant .form__el-variant label"
  );
  certElVariants = document.querySelectorAll('input[name="cert_el_variant"]');
  certPocketsLabels = document.querySelectorAll(
    ".pocket .form__el-variant label"
  );
  certPockets = document.querySelectorAll('input[name="cert_pocket"]');
  submitForm = document.querySelector(".cert-buy__form");

  totalSumm = 0;
  prodSumm = 0;
  deliverySumm = 0;
  variantSumm = 0;
  pocketSumm = 0;

  ecommerceSend = false;

  innerDeliveries = [3, 6];
  outerDeliveries = [3, 4, 5];

  bindEvents() {
    this.customPriceInputHandler();
    this.certsSelectHandler();
    this.electroSelectHandler();
    this.fizSelectHandler();
    this.citySelectHandler();
    this.variantSelectHandler();
    this.pocketSelectHandler();
    this.elVariantSelectHandler();
    this.deliverySelectHandler();
    //this.submitHandler();
  }

  submitHandler() {
    let _this = this;
    this.submitForm.addEventListener("submit", function (evt) {
      if (!_this.ecommerceSend) {
        evt.preventDefault();
        dataLayer.push({
          ecommerce: {
            currencyCode: "RUB",
            purchase: {
              products: [
                {
                  id: "cert",
                  name: "Сертификат на " + _this.prodSumm,
                  price: _this.prodSumm,
                  category: "Сертификат",
                  quantity: 1,
                },
              ],
            },
          },
        });
        _this.ecommerceSend = true;
        _this.submitForm.submit();
      }
    });
  }

  certsSelectHandler() {
    let _this = this;
    this.certs.forEach((element) => {
      element.querySelector("input").addEventListener("change", function () {
        _this.elementsClassListRemove(_this.certs, "selected");
        if (this.checked) {
          _this.refreshSplitBadge(this.getAttribute("cost"));
          element.classList.add("selected");
        }
        _this.prodSumm = this.getAttribute("cost");
        _this.renderPrice(_this.prodCostElement, this.getAttribute("cost"));
        _this.calcSumm();
        if (!this.value != 15287) {
          _this.removeRequired(_this.customPriceInput);
        }
      });
    });
  }

  elementsClassListRemove(elements, className) {
    if (NodeList.prototype.isPrototypeOf(elements)) {
      elements.forEach((element) => {
        element.classList.remove(className);
      });
    } else {
      elements.classList.remove(className);
    }
  }

  citySelectHandler() {
    let _this = this;
    this.cityRadio.forEach((element) => {
      element.addEventListener("change", function () {
        if (this.checked) {
          if (this.getAttribute("location") == "inner") {
            _this.showInnerDeliveries();
          } else if (this.getAttribute("location") == "outer") {
            _this.showOuterDeliveries();
          }
          _this.deliverySumm = 0;
          _this.calcSumm();
        }
      });
    });
  }

  variantSelectHandler() {
    let _this = this;
    this.certVariants.forEach((element) => {
      element.addEventListener("change", function () {
        _this.elementsClassListRemove(_this.certVariantsLabels, "selected");
        if (this.checked) {
          element.parentElement.classList.add("selected");
          _this.variantSumm = +this.getAttribute("cost");
          _this.calcSumm();
        }
      });
    });
  }

  elVariantSelectHandler() {
    let _this = this;
    this.certElVariants.forEach((element) => {
      element.addEventListener("change", function () {
        _this.elementsClassListRemove(_this.certElVariantsLabels, "selected");
        if (this.checked) {
          element.parentElement.classList.add("selected");
          _this.variantSumm = 0;
          _this.pocketSumm = 0;
          _this.calcSumm();
        }
      });
    });
  }

  pocketSelectHandler() {
    let _this = this;
    this.certPockets.forEach((element) => {
      element.addEventListener("click", function () {
        _this.elementsClassListRemove(_this.certPocketsLabels, "selected");
        var $box = $(this);
        if ($box.is(":checked")) {
          element.parentElement.classList.add("selected");
          var group = "input:checkbox[name='" + $box.attr("name") + "']";
          $(group).prop("checked", false);
          $box.prop("checked", true);
          _this.pocketSumm = +this.getAttribute("cost");
          _this.calcSumm();
        } else {
          _this.pocketSumm = 0;
          _this.calcSumm();
          $box.prop("checked", false);
        }
      });
    });
  }

  deliverySelectHandler() {
    let _this = this;
    this.deliveryInput.forEach((element) => {
      element.addEventListener("change", function () {
        if (this.checked) {
          _this.deliverySumm = +this.getAttribute("cost");
          _this.calcSumm();
        }
        if (this.value == 3) {
          _this.removeRequired(_this.addressInput);
        } else {
          _this.setRequired(_this.addressInput);
        }
      });
    });
  }

  showInnerDeliveries() {
    let _this = this;
    this.deliveryBlock.style.display = "block";
    this.deliveryInput.forEach((element) => {
      element.checked = false;
      if (_this.innerDeliveries.includes(+element.value)) {
        element.parentElement.style.display = "block";
      } else {
        element.parentElement.style.display = "none";
      }
    });
  }

  showOuterDeliveries() {
    let _this = this;
    this.deliveryBlock.style.display = "block";
    this.deliveryInput.forEach((element) => {
      element.checked = false;
      if (_this.outerDeliveries.includes(+element.value)) {
        element.parentElement.style.display = "block";
      } else {
        element.parentElement.style.display = "none";
      }
    });
  }

  customPriceInputHandler() {
    let _this = this;
    this.customPriceInput.addEventListener("focus", function () {
      _this.elementsClassListRemove(_this.certs, "selected");
      this.previousElementSibling.checked = true;
      this.parentElement.classList.add("selected");
      _this.setRequired(this);
      this.setAttribute("placeholder", "    ₽");
    });
    this.customPriceInput.addEventListener(
      "mouseleave",
      this.checkCustomPrice.bind(this)
    );
    this.customPriceInput.addEventListener(
      "input",
      this.checkCustomPrice.bind(this)
    );
    this.customPriceInput.addEventListener(
      "blur",
      this.checkCustomPrice.bind(this)
    );
  }

  checkCustomPrice() {
    if (+this.customPriceInput.value === 0) {
      this.prodSumm = 0;
      this.calcSumm();
    } else if (this.customPriceInput.checkValidity()) {
      this.customPriceInput.setCustomValidity("");
      this.refreshSplitBadge(this.customPriceInput.value);
      this.prodSumm = this.customPriceInput.value;
      this.calcSumm();
    } else {
      this.customPriceInput.setCustomValidity("");
    }
    this.customPriceInput.reportValidity();
    this.renderPrice(this.prodCostElement, this.customPriceInput.value);
    this.customPriceInput.setAttribute("placeholder", "0000 ₽");
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
    this.elementsClassListRemove(this.fizSelect.parentElement, "selected");
    this.electroSelect.parentElement.classList.add("selected");
    // показываем/скрываем нужные блоки
    this.sectionFizVariants.style.display = "none";
    this.sectionFizPockets.style.display = "none";
    this.dataFiz.style.display = "none";
    this.cashPayment.style.display = "none";
    this.deliveryBlock.style.display = "none";
    this.sectionElectroVariants.style.display = "block";
    this.dataElectro.style.display = "block";

    // Обуняем доставку и вырианты
    this.deliverySumm = 0;
    this.variantSumm = 0;
    this.pocketSumm = 0;
    this.calcSumm();

    // Убираем чек с физических вариантов
    this.certVariants.forEach((element) => {
      element.checked = false;
    });
    this.certPockets.forEach((element) => {
      element.checked = false;
    });
    this.cityRadio.forEach((element) => {
      element.checked = false;
    });
    this.deliveryInput.forEach((element) => {
      element.checked = false;
    });

    // устанавливаем/удаляем обязательность нужных полей
    this.setRequired(this.certElVariants[0]);
    this.removeRequired(this.certVariants[0]);
    this.removeRequired(this.cityRadio[0]);
    this.removeRequired(this.addressInput);
    this.removeRequired(this.deliveryInput[0]);

    // расставляем порядковые номера для блоков
    this.setSectionNumbers();
  }

  showFiz() {
    this.elementsClassListRemove(this.electroSelect.parentElement, "selected");
    this.fizSelect.parentElement.classList.add("selected");
    // показываем/скрываем нужные блоки
    this.sectionFizVariants.style.display = "block";
    this.sectionFizPockets.style.display = "block";
    this.dataFiz.style.display = "block";
    this.cashPayment.style.display = "flex";
    this.sectionElectroVariants.style.display = "none";
    this.dataElectro.style.display = "none";

    // устанавливаем/удаляем обязательность нужных полей
    this.setRequired(this.cityRadio[0]);
    this.setRequired(this.addressInput);
    this.setRequired(this.deliveryInput[0]);
    this.setRequired(this.certVariants[0]);
    this.removeRequired(this.certElVariants[0]);

    // расставляем порядковые номера для блоков
    this.setSectionNumbers();
  }

  electroSelectHandler() {
    this.electroSelect.addEventListener("change", () => this.showElectro());
  }

  fizSelectHandler() {
    this.fizSelect.addEventListener("change", () => this.showFiz());
  }

  setRequired(element) {
    element.setAttribute("required", "true");
  }

  removeRequired(element) {
    element.removeAttribute("required");
  }

  calcDelivery() {}

  calcSumm() {
    this.totalSumm =
      +this.prodSumm +
      +this.deliverySumm +
      +this.variantSumm +
      +this.pocketSumm;
    this.renderPrice(
      this.deliveryCostElement,
      +this.deliverySumm + +this.variantSumm + +this.pocketSumm
    );
    if (+this.deliverySumm == 0) {
      this.deliveryCostElement.parentElement.querySelector(
        ".summ__text"
      ).textContent = "Упаковка:";
    } else {
      this.deliveryCostElement.parentElement.querySelector(
        ".summ__text"
      ).textContent = "Доставка и упаковка:";
    }
    this.renderPrice(this.summCostElement, this.totalSumm);
  }

  renderPrice(element, value) {
    element.textContent = new Intl.NumberFormat("ru-RU", {
      style: "currency",
      currency: "RUB",
      maximumFractionDigits: 0,
    }).format(value);
  }
}
