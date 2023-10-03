/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 6449:
/***/ (function() {

var $navigationControl = document.querySelector('[data-navigation-control]');

if ($navigationControl) {
  $navigationControl.addEventListener('click', function () {
    $navigationControl.closest('.sidebar-navigation').classList.toggle('sidebar-navigation_show');
  });
  document.addEventListener('click', function (event) {
    var $el = event.target;

    if (!$el.classList.contains('sidebar-navigation') && !$el.closest('.sidebar-navigation')) {
      var $nav = document.querySelector('.sidebar-navigation_show');
      if ($nav) $nav.classList.remove('sidebar-navigation_show');
    }
  });
}

/***/ }),

/***/ 4096:
/***/ (function() {

document.addEventListener('click', function (event) {
  var $el = event.target;

  if ($el.matches('[data-rating-value]')) {
    var $root = $el.closest('[data-rating]');
    $root.querySelector('.list').classList.add('list_checked');
    $root.querySelector('[data-rating-field]').value = $el.dataset.ratingValue;
    var $stars = $root.querySelectorAll('[data-rating-value]');
    $stars.forEach(function ($item, index) {
      $item.classList.toggle('list__item_active', index >= Array.from($stars).indexOf($el));
    });
  }
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";

// EXTERNAL MODULE: ./src/js/components/scores-edit.js
var scores_edit = __webpack_require__(4096);
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return _arrayLikeToArray(arr);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return _arrayLikeToArray(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread();
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
;// CONCATENATED MODULE: ./src/js/components/dropzone.js




var Dropzone = /*#__PURE__*/function () {
  function Dropzone() {
    _classCallCheck(this, Dropzone);

    this.$elements = {
      item: document.querySelector('[data-dropzone-item]'),
      dropzone: document.querySelector('[data-dropzone]'),
      value: document.querySelector('[data-dropzone-value]'),
      files: document.querySelector('[data-dropzone-files]'),
      add: document.querySelector('[data-dropzone-add]')
    };
    this.filesInfo = JSON.parse(this.$elements.dropzone.dataset.dropzone);
    this.files = [];
  }

  _createClass(Dropzone, [{
    key: "handleDrag",
    value: function handleDrag() {
      this.$elements.dropzone.addEventListener('dragover', function (event) {
        event.preventDefault();
      });
      this.$elements.dropzone.addEventListener('dragleave', function (event) {
        event.preventDefault();
      });
      this.$elements.dropzone.addEventListener('dragend', function (event) {
        event.preventDefault();
      });
    }
  }, {
    key: "handleAdd",
    value: function handleAdd(files) {
      var _this = this;

      files.forEach(function (item) {
        var reader = new FileReader();
        reader.addEventListener('load', function () {
          _this.filesInfo.push({
            id: item.lastModified,
            image: reader.result,
            name: item.name,
            size: item.size
          });

          _this.$elements.dropzone.dataset.dropzone = JSON.stringify(_this.filesInfo);

          _this.createPreviews();
        }, false);
        reader.readAsDataURL(item);
      });
      var dt = new DataTransfer();
      this.files.forEach(function (item) {
        dt.items.add(item);
      });
      this.$elements.value.files = dt.files;
    }
  }, {
    key: "handleDrop",
    value: function handleDrop() {
      var _this2 = this;

      this.$elements.dropzone.addEventListener('drop', function (event) {
        event.preventDefault();
        _this2.files = [].concat(_toConsumableArray(_this2.files), _toConsumableArray(event.dataTransfer.files));

        _this2.handleAdd(Array.from(event.dataTransfer.files));
      });
    }
  }, {
    key: "handleChange",
    value: function handleChange() {
      var _this3 = this;

      this.$elements.add.addEventListener('change', function (event) {
        _this3.files = [].concat(_toConsumableArray(_this3.files), _toConsumableArray(event.target.files));

        _this3.handleAdd(Array.from(event.target.files));
      });
    }
  }, {
    key: "createPreviews",
    value: function createPreviews() {
      this.$elements.files.innerHTML = this.filesInfo.map(function (item) {
        var size = item.size / 1000000;
        var sizeParam = 'МБ';

        if (size < 1) {
          size = item.size / 1000;
          sizeParam = 'КБ';
        }

        return "\n\t\t\t\t\t<li class=\"list__item\">\n\t\t\t\t\t\t<div class=\"list__item-image\" style=\"background-image: url(".concat(item.image, ");\"></div>\n\t\t\t\t\t\t<div class=\"list__item-content\">\n\t\t\t\t\t\t\t<div>").concat(item.name, "</div><span>").concat(size.toFixed(1), " ").concat(sizeParam, "</span>\n\t\t\t\t\t\t\t<button type=\"button\" data-dropzone-remove=\"").concat(item.id, "\">\n\t\t\t\t\t\t\t\t<svg class=\"icon icon_cross\" viewBox=\"0 0 18 18\" style=\"width: 1.8rem; height: 1.8rem;\">\n\t\t\t\t\t\t\t\t\t<use xlink:href=\"#cross\"></use>\n\t\t\t\t\t\t\t\t</svg>\n\t\t\t\t\t\t\t</button>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</li>\n\t\t\t\t");
      }).join('');
      this.$elements.files.classList.toggle('list_full', this.filesInfo.length > 0);
    }
  }, {
    key: "handleRemove",
    value: function handleRemove() {
      var _this4 = this;

      document.addEventListener('click', function (event) {
        var $el = event.target;

        if ($el.matches('[data-dropzone-remove]')) {
          var imageId = parseInt($el.dataset.dropzoneRemove);
          _this4.filesInfo = _this4.filesInfo.filter(function (item) {
            return item.id !== imageId;
          });
          _this4.files = _this4.files.filter(function (item) {
            return item.lastModified !== imageId;
          });
          _this4.$elements.dropzone.dataset.dropzone = JSON.stringify(_this4.filesInfo);

          _this4.createPreviews();

          var dt = new DataTransfer();

          _this4.files.forEach(function (item) {
            dt.items.add(item);
          });

          _this4.$elements.value.files = dt.files;
        }
      });
    }
  }, {
    key: "init",
    value: function init() {
      if (this.$elements.dropzone) {
        this.createPreviews();
        this.handleDrag();
        this.handleDrop();
        this.handleChange();
        this.handleRemove();
      }
    }
  }]);

  return Dropzone;
}();

/* harmony default export */ var components_dropzone = (Dropzone);
;// CONCATENATED MODULE: ./src/js/components/review-clear.js
function reviewClear() {
  var $form = document.getElementById('review-form');
  $form.querySelectorAll('input').forEach(function ($input) {
    $input.value = '';
  });
  $form.querySelectorAll('textarea').forEach(function ($textarea) {
    $textarea.value = '';
  });
  $form.querySelectorAll('.scores-edit__list .list__item').forEach(function ($star) {
    $star.classList.remove('list__item_active');
  });
  $form.querySelector('[data-dropzone-item]').innerHTML = "\n\t\t<input class=\"dropzone-hide\" type=\"file\" multiple value=\"\" data-dropzone-value accept=\"image/*\">\n\t\t<label class=\"dropzone\" data-dropzone=\"[]\">\n\t\t\t<input type=\"file\" multiple value=\"\" data-dropzone-add accept=\"image/*\">\n\t\t\t<span class=\"dropzone__message\">\n\t\t\t\t<svg class=\"icon icon_dropzone\" viewBox=\"0 0 48 48\" style=\"width: 4.8rem; height: 4.8rem;\">\n\t\t\t\t\t<use xlink:href=\"#dropzone\"></use>\n\t\t\t\t</svg>\n\t\t\t\t<span>\u041F\u0435\u0440\u0435\u0442\u0430\u0449\u0438\u0442\u0435 \u0444\u043E\u0442\u043E \u0441\u044E\u0434\u0430<br> \u0438\u043B\u0438 <strong>\u0437\u0430\u0433\u0440\u0443\u0437\u0438\u0442\u0435 \u0441 \u043A\u043E\u043C\u043F\u044C\u044E\u0442\u0435\u0440\u0430</strong><br> \u0434\u043E&nbsp;10&nbsp;\u0444\u0430\u0439\u043B\u043E\u0432, \u043C\u0430\u043A\u0441\u0438\u043C\u0430\u043B\u044C\u043D\u044B\u0439 \u0440\u0430\u0437\u043C\u0435\u0440 1&nbsp;\u0444\u0430\u0439\u043B\u0430&nbsp;-&nbsp;5&nbsp;\u043C\u0431</span>\n\t\t\t</span>\n\t\t</label>\n\t\t<ul class=\"list list_upload\" data-dropzone-files></ul>\n\t";
}
// EXTERNAL MODULE: ./src/js/components/navigation-dropdown.js
var navigation_dropdown = __webpack_require__(6449);
;// CONCATENATED MODULE: ./src/js/pages/lk_reviews.js




window.clearReviewData = reviewClear;

window.addDropzone = function () {
  var dropzone = new components_dropzone();
  dropzone.init();
};
}();
/******/ })()
;