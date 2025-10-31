var Order = function () {
  this.beforeCancel = function (orderId) {
    var data = {
      orderId: orderId,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/cancelOrderBefore.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR && a.PENALTY) {
          $("#cancel .reservation-penalty").show();
          $("#cancel .reservation-penalty > span").text(a.PENALTY);
        } else {
          $("#cancel .reservation-penalty").hide();
        }
      },
    });
  };
  this.cancel = function (orderId) {
    var data = {
      orderId: orderId,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/cancelOrder.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          window.modal.close('cancel-step');
          window.modal.open('cancel-done');
        } else {
          window.infoModal(ERROR_TITLE, a.ERROR);
        }
      },
    });
  };
  this.payment = function (orderId) {
    var data = {
      orderId: orderId,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/getPaymentUrl.php",
      data: data,
      dataType: "json",
      success: function (a) {
        if (!a.ERROR) {
          var link = a.LINK;
          location.href = link;
        }
      },
    });
  };
  this.getVaucher = function (orderId) {
    var data = {
      orderId: orderId,
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/pdf/createOrderPdf.php",
      data: data,
      dataType: "json",
      success: function (data) {
        console.log(data);
        if (!data.ERROR) {
          window.open(data.LINK, "blank");
        } else {
          alert(data.ERROR);
        }
      },
    });
  };
  this.addReason = function (orderId, checkedInputId, reasonText) {
    var data = {
        orderId: orderId,
        reason: checkedInputId,
        answer: reasonText
    };

    jQuery.ajax({
      type: "POST",
      url: "/ajax/handlers/addReasonCancelOrder.php",
      data: data,
      dataType: "json",
      success: function (data) {
        if (!data.ERROR) {
              window.modal.close('cancel-done');
              window.modal.open('cancel-finish');
        } else {
              console.log('/ajax/handlers/addReasonCancelOrder.php ' + data.ERROR);
        }
      },
    });
  };
};
var order = new Order();

$(function () {
  let getVaucherButtons = document.querySelectorAll(".profile__get-vaucher");
  if (getVaucherButtons.length) {
    getVaucherButtons.forEach((element) => {
      element.addEventListener("click", function () {
        order.getVaucher(element.dataset.id);
      });
    });
  }
  $(document).on("click", "[data-order-cancel]", function (e) {
      e.preventDefault();
      var orderId = $(this).data("id");

      let modalCancelTextBlock = document.querySelector('#cancel-step .modal__content-info');
      let cancelBlock = document.querySelector('.object-row[data-id="' + orderId + '"] .room__cancelation-tooltip ul');
      let modalCancelBtn = document.querySelector('#cancel-step [data-modal-cancel-order]');
      let modalCancelDoneBtn = document.querySelector('#cancel-done .modal__content-btn');

      if (modalCancelTextBlock && cancelBlock && modalCancelBtn) {
          modalCancelTextBlock.innerHTML = cancelBlock.outerHTML;
          modalCancelBtn.dataset.id = orderId;
          modalCancelDoneBtn.dataset.id = orderId;
      }

      window.modal.open('cancel-step');
  });

   $(document).on("click", "[data-modal-cancel-order]", function (e) {
      e.preventDefault();
      var orderId = $(this).data("id");
      order.cancel(orderId);
  });
  

  // Сортировка
  $(document).on("click", "[data-order-sort]", function (event) {
    event.preventDefault();

    var sort = $(this).data("order-sort");

    var params = getUrlParams();
    params["sort"] = sort;
    var url = setUrlParams(params);

    location.href = url;
  });

  // Фильтр
  $(document).on(
    "click",
    "#form-order-search [data-order-search]",
    function (event) {
      event.preventDefault();

      var params = getUrlParams();

      var orderNum = $('#form-order-search input[name="orderNum"]').val();
      if (orderNum) {
        params["orderNum"] = orderNum;
      } else {
        delete params["orderNum"];
      }

      var url = setUrlParams(params);
      window.location = url;
    }
  );

  // Получение ссылки на оплату
  $(document).on("click", "[data-payment]", function (e) {
    e.preventDefault();
    var orderId = $(this).data("id");
    order.payment(orderId);
  });

  let reasonItems = document.querySelectorAll('.reason-item');

  if (reasonItems) {
      reasonItems.forEach(item => {
          let textInput = document.querySelector('.text-input');

          if (textInput) {
              item.addEventListener('click', () => {
                  let input = item.querySelector('input');

                  if (input.getAttribute('id') == 'text') {
                      textInput.style.display = 'block';
                  } else {
                      textInput.style.display = 'none';
                  }
              });
          }
      });
  }

  $(document).on("click", "[data-modal-add-reason]", function (e) {
      e.preventDefault();

      let orderId = $(this).data('id');
      let reasonItems = document.querySelectorAll('.reason-item');
      let checkedInputId = 0;
      let reasonBlock = document.getElementById('free-text');
      let reasonText = reasonBlock.value;
      let emptyText = false;

      if (reasonItems) {
          reasonItems.forEach(item => {
              let checkedInput = item.querySelector('input');

              if (checkedInput.checked) {
                  checkedInputId = checkedInput.dataset.id;
              }

              if (checkedInput.getAttribute('id') == 'text' && checkedInput.checked && (!reasonText || reasonText == '')) {
                  reasonBlock.classList.add('empty-input');
                  emptyText = true;
              } else if (checkedInput.getAttribute('id') == 'text' && checkedInput.checked && reasonText != '') {
                  reasonBlock.classList.remove('empty-input');
                  emptyText = false;
              }
          });
      }

      if (!emptyText) {
          order.addReason(orderId, checkedInputId, reasonText);
      }
  });

  $(document).on("click", "#cancel-finish .modal__close", function(e) {
      setTimeout(function () {
          location.reload();
      }, 1500);
  });
});
