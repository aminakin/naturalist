/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
var __webpack_exports__ = {};

;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  Object.defineProperty(Constructor, "prototype", {
    writable: false
  });
  return Constructor;
}
;// CONCATENATED MODULE: ./src/js/components/autocomplete.js




function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { _defineProperty(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

var Autocomplete = /*#__PURE__*/function () {
  function Autocomplete() {
    _classCallCheck(this, Autocomplete);

    this.$roots = document.querySelectorAll('[data-autocomplete]'); // eslint-disable-next-line no-undef

    this.data = autocomplete;
  }

  _createClass(Autocomplete, [{
    key: "handleListChange",
    value: function handleListChange($field, $root) {
      var $list = $root.querySelector('[data-autocomplete-list]');
      var key = $field.dataset.autocompleteField;
      var value = $field.value.toLowerCase();
      var list = this.data.filter(function (item) {
        return item[key].toLowerCase().includes(value);
      });
      $list.innerHTML = "\n\t\t\t".concat(list.map(function (item) {
        return "<li class=\"list__item\"><button data-autocomplete-id=\"".concat(item.id, "\" type=\"button\">").concat(Autocomplete.handleHighlight(item, key, value), "</button></li>");
      }).join(''), "\n\t\t");
      $root.querySelector('[data-autocomplete-list]').classList.toggle('list_show', list.length > 0);
    }
  }, {
    key: "handleSelect",
    value: function handleSelect() {
      var _this = this;

      document.addEventListener('click', function (event) {
        var $el = event.target;

        if ($el.matches('[data-autocomplete-id]')) {
          var data = _this.data.find(function (item) {
            return item.id.toString() === $el.dataset.autocompleteId;
          });

          var $root = $el.closest('[data-autocomplete]');
          Object.entries(data).filter(function (item) {
            return item[0] !== 'id';
          }).forEach(function (item) {
            // eslint-disable-next-line prefer-destructuring
            $root.querySelector("[data-autocomplete-field=\"".concat(item[0], "\"]")).value = item[1];
          });
          Autocomplete.handleClear($root);
          $root.querySelectorAll('.field__input').forEach(function ($item) {
            window.validation.validate($item);
          });
        }
      });
    }
  }, {
    key: "init",
    value: function init() {
      var _this2 = this;

      if (this.$roots.length) {
        this.$roots.forEach(function ($root) {
          $root.querySelectorAll('[data-autocomplete-field]').forEach(function ($field) {
            $field.addEventListener('focus', function () {
              _this2.handleListChange($field, $root);

              _this2.$roots.forEach(function ($rootHide) {
                if ($root !== $rootHide) Autocomplete.handleClear($rootHide);
              });
            });
            $field.addEventListener('input', function () {
              _this2.handleListChange($field, $root);
            });
          });
        });
        document.addEventListener('click', function (event) {
          var $el = event.target;

          if (!$el.matches('[data-autocomplete-list]') && !$el.closest('[data-autocomplete-list]') && !$el.matches('[data-autocomplete-field]')) {
            _this2.$roots.forEach(function ($root) {
              Autocomplete.handleClear($root);
            });
          }
        });
      }

      this.handleSelect();
    }
  }], [{
    key: "handleHighlight",
    value: function handleHighlight(item, key, value) {
      var name = _objectSpread({}, item);

      if (value) name[key] = name[key].replace(new RegExp("(".concat(value, ")"), 'gim'), '<strong>$1</strong>');
      return "".concat(name.last, " ").concat(name.first, " ").concat(name.middle);
    }
  }, {
    key: "handleClear",
    value: function handleClear($root) {
      var $list = $root.querySelector('[data-autocomplete-list]');
      $list.innerHTML = '';
      $list.classList.remove('list_show');
    }
  }]);

  return Autocomplete;
}();

/* harmony default export */ var components_autocomplete = (Autocomplete);
;// CONCATENATED MODULE: ./src/js/pages/reservation.js

var reservation_autocomplete = new components_autocomplete();
reservation_autocomplete.init();
/******/ })()
;