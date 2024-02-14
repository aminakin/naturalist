"use strict";

class CertActivate {
  constructor() {
    this.bindEvents();
  }

  bindEvents() {
    this.codeInputHandler();
    this.getCodeHandler();
    this.formHandler();
  }

  sertNumber = document.querySelectorAll(".sertificate-number");
  getCodeButton = document.querySelector(".get-code");
  phoneElement = document.querySelector(
    '.form-active-sert input[name="phone"]'
  );
  codeInputBlock = document.querySelector(".code-input");
  codeInput = document.querySelector('input[name="code"]');
  userIdInput = document.querySelector('input[name="user_id"]');
  form = document.querySelector(".form-active-sert");
  modal = document.querySelector("#cert__popup-wrap");

  codeInputHandler() {
    this.sertNumber.forEach((input) => {
      input.addEventListener("keyup", function (event) {
        if (this.value.length == 4) {
          if (this.nextElementSibling !== null) {
            this.nextElementSibling.focus();
          }
        } else if (this.value.length == 0) {
          if (this.previousElementSibling !== null) {
            this.previousElementSibling.focus();
          }
        }
      });
    });
  }

  getCodeHandler() {
    if (this.getCodeButton !== null) {
      this.getCodeButton.addEventListener("click", () => this.getCode());
    }
  }

  getCode() {
    let _this = this;
    if (this.phoneElement.value == "") {
      this.phoneElement.focus();
      return;
    }
    let data = {
      login: this.phoneElement.value,
      type: "phone",
      email: "",
      name: "",
      last_name: "",
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/authGetCode.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          _this.getCodeButton.parentElement.style.display = "none";
          _this.codeInputBlock.style.display = "block";
        } else {
          window.infoModal("Ошибка!", a.ERROR);
        }
      },
    });
  }

  formHandler() {
    let _this = this;
    this.form.addEventListener("submit", function (evt) {
      evt.preventDefault();
      _this.activateCert(_this);
    });
  }

  activateCert(_this) {
    let cert = "";

    this.sertNumber.forEach((element, index) => {
      if (this.sertNumber[index + 1]) {
        cert += element.value + "-";
      } else {
        cert += element.value;
      }
    });

    let data = {
      userId: this.userIdInput.value,
      certCode: cert,
    };

    if (this.userIdInput.value == 0) {
      data.login = this.phoneElement.value;
      data.type = "phone";
      data.code = this.codeInput.value;
    }

    this.sendCertReauest(_this, data);
  }

  sendCertReauest(_this, data) {
    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/certActivate.php",
      data: data,
      dataType: "json",
      success: function (a) {
        console.log(a);
        if (a.ERROR_MESSAGE == "") {
          _this.showModal(a);
        } else {
          window.infoModal("Ошибка!", a.ERROR_MESSAGE);
        }
      },
    });
  }

  showModal(data) {
    this.modal.querySelector(".popop-sert-btn span").textContent = data.AMOUNT;
    this.modal.querySelector(".popop-sert-date span").textContent = data.DATE;
    window.modal.open("cert__popup-wrap");
    setTimeout(() => {
      location.href = "/catalog/";
    }, 3000);
  }
}
