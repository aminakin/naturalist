/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 8485:
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

/***/ }),

/***/ 2518:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var _typeof = (__webpack_require__(435)["default"]);

function _regeneratorRuntime() {
  "use strict";
  /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */

  module.exports = _regeneratorRuntime = function _regeneratorRuntime() {
    return exports;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  var exports = {},
      Op = Object.prototype,
      hasOwn = Op.hasOwnProperty,
      $Symbol = "function" == typeof Symbol ? Symbol : {},
      iteratorSymbol = $Symbol.iterator || "@@iterator",
      asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator",
      toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  function define(obj, key, value) {
    return Object.defineProperty(obj, key, {
      value: value,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }), obj[key];
  }

  try {
    define({}, "");
  } catch (err) {
    define = function define(obj, key, value) {
      return obj[key] = value;
    };
  }

  function wrap(innerFn, outerFn, self, tryLocsList) {
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator,
        generator = Object.create(protoGenerator.prototype),
        context = new Context(tryLocsList || []);
    return generator._invoke = function (innerFn, self, context) {
      var state = "suspendedStart";
      return function (method, arg) {
        if ("executing" === state) throw new Error("Generator is already running");

        if ("completed" === state) {
          if ("throw" === method) throw arg;
          return doneResult();
        }

        for (context.method = method, context.arg = arg;;) {
          var delegate = context.delegate;

          if (delegate) {
            var delegateResult = maybeInvokeDelegate(delegate, context);

            if (delegateResult) {
              if (delegateResult === ContinueSentinel) continue;
              return delegateResult;
            }
          }

          if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) {
            if ("suspendedStart" === state) throw state = "completed", context.arg;
            context.dispatchException(context.arg);
          } else "return" === context.method && context.abrupt("return", context.arg);
          state = "executing";
          var record = tryCatch(innerFn, self, context);

          if ("normal" === record.type) {
            if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue;
            return {
              value: record.arg,
              done: context.done
            };
          }

          "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg);
        }
      };
    }(innerFn, self, context), generator;
  }

  function tryCatch(fn, obj, arg) {
    try {
      return {
        type: "normal",
        arg: fn.call(obj, arg)
      };
    } catch (err) {
      return {
        type: "throw",
        arg: err
      };
    }
  }

  exports.wrap = wrap;
  var ContinueSentinel = {};

  function Generator() {}

  function GeneratorFunction() {}

  function GeneratorFunctionPrototype() {}

  var IteratorPrototype = {};
  define(IteratorPrototype, iteratorSymbol, function () {
    return this;
  });
  var getProto = Object.getPrototypeOf,
      NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype);
  var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype);

  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function (method) {
      define(prototype, method, function (arg) {
        return this._invoke(method, arg);
      });
    });
  }

  function AsyncIterator(generator, PromiseImpl) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);

      if ("throw" !== record.type) {
        var result = record.arg,
            value = result.value;
        return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) {
          invoke("next", value, resolve, reject);
        }, function (err) {
          invoke("throw", err, resolve, reject);
        }) : PromiseImpl.resolve(value).then(function (unwrapped) {
          result.value = unwrapped, resolve(result);
        }, function (error) {
          return invoke("throw", error, resolve, reject);
        });
      }

      reject(record.arg);
    }

    var previousPromise;

    this._invoke = function (method, arg) {
      function callInvokeWithMethodAndArg() {
        return new PromiseImpl(function (resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
    };
  }

  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];

    if (undefined === method) {
      if (context.delegate = null, "throw" === context.method) {
        if (delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method)) return ContinueSentinel;
        context.method = "throw", context.arg = new TypeError("The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);
    if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel;
    var info = record.arg;
    return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel);
  }

  function pushTryEntry(locs) {
    var entry = {
      tryLoc: locs[0]
    };
    1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal", delete record.arg, entry.completion = record;
  }

  function Context(tryLocsList) {
    this.tryEntries = [{
      tryLoc: "root"
    }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0);
  }

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) return iteratorMethod.call(iterable);
      if ("function" == typeof iterable.next) return iterable;

      if (!isNaN(iterable.length)) {
        var i = -1,
            next = function next() {
          for (; ++i < iterable.length;) {
            if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next;
          }

          return next.value = undefined, next.done = !0, next;
        };

        return next.next = next;
      }
    }

    return {
      next: doneResult
    };
  }

  function doneResult() {
    return {
      value: undefined,
      done: !0
    };
  }

  return GeneratorFunction.prototype = GeneratorFunctionPrototype, define(Gp, "constructor", GeneratorFunctionPrototype), define(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) {
    var ctor = "function" == typeof genFun && genFun.constructor;
    return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name));
  }, exports.mark = function (genFun) {
    return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun;
  }, exports.awrap = function (arg) {
    return {
      __await: arg
    };
  }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () {
    return this;
  }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) {
    void 0 === PromiseImpl && (PromiseImpl = Promise);
    var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl);
    return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) {
      return result.done ? result.value : iter.next();
    });
  }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () {
    return this;
  }), define(Gp, "toString", function () {
    return "[object Generator]";
  }), exports.keys = function (object) {
    var keys = [];

    for (var key in object) {
      keys.push(key);
    }

    return keys.reverse(), function next() {
      for (; keys.length;) {
        var key = keys.pop();
        if (key in object) return next.value = key, next.done = !1, next;
      }

      return next.done = !0, next;
    };
  }, exports.values = values, Context.prototype = {
    constructor: Context,
    reset: function reset(skipTempReset) {
      if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) {
        "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined);
      }
    },
    stop: function stop() {
      this.done = !0;
      var rootRecord = this.tryEntries[0].completion;
      if ("throw" === rootRecord.type) throw rootRecord.arg;
      return this.rval;
    },
    dispatchException: function dispatchException(exception) {
      if (this.done) throw exception;
      var context = this;

      function handle(loc, caught) {
        return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i],
            record = entry.completion;
        if ("root" === entry.tryLoc) return handle("end");

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc"),
              hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0);
            if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0);
          } else {
            if (!hasFinally) throw new Error("try statement without catch or finally");
            if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
          }
        }
      }
    },
    abrupt: function abrupt(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];

        if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null);
      var record = finallyEntry ? finallyEntry.completion : {};
      return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record);
    },
    complete: function complete(record, afterLoc) {
      if ("throw" === record.type) throw record.arg;
      return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel;
    },
    finish: function finish(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel;
      }
    },
    "catch": function _catch(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];

        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;

          if ("throw" === record.type) {
            var thrown = record.arg;
            resetTryEntry(entry);
          }

          return thrown;
        }
      }

      throw new Error("illegal catch attempt");
    },
    delegateYield: function delegateYield(iterable, resultName, nextLoc) {
      return this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      }, "next" === this.method && (this.arg = undefined), ContinueSentinel;
    }
  }, exports;
}

module.exports = _regeneratorRuntime, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 435:
/***/ (function(module) {

function _typeof(obj) {
  "@babel/helpers - typeof";

  return (module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  }, module.exports.__esModule = true, module.exports["default"] = module.exports), _typeof(obj);
}

module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;

/***/ }),

/***/ 1117:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

// TODO(Babel 8): Remove this file.
var runtime = __webpack_require__(2518)();

module.exports = runtime; // Copied from https://github.com/facebook/regenerator/blob/main/packages/runtime/runtime.js#L736=

try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  if (typeof globalThis === "object") {
    globalThis.regeneratorRuntime = runtime;
  } else {
    Function("r", "regeneratorRuntime = r")(runtime);
  }
}

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
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";

;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/regenerator/index.js
var regenerator = __webpack_require__(1117);
var regenerator_default = /*#__PURE__*/__webpack_require__.n(regenerator);
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






function _asyncIterator(iterable) { var method, async, sync, retry = 2; for ("undefined" != typeof Symbol && (async = Symbol.asyncIterator, sync = Symbol.iterator); retry--;) { if (async && null != (method = iterable[async])) return method.call(iterable); if (sync && null != (method = iterable[sync])) return new AsyncFromSyncIterator(method.call(iterable)); async = "@@asyncIterator", sync = "@@iterator"; } throw new TypeError("Object is not async iterable"); }

function AsyncFromSyncIterator(s) { function AsyncFromSyncIteratorContinuation(r) { if (Object(r) !== r) return Promise.reject(new TypeError(r + " is not an object.")); var done = r.done; return Promise.resolve(r.value).then(function (value) { return { value: value, done: done }; }); } return AsyncFromSyncIterator = function AsyncFromSyncIterator(s) { this.s = s, this.n = s.next; }, AsyncFromSyncIterator.prototype = { s: null, n: null, next: function next() { return AsyncFromSyncIteratorContinuation(this.n.apply(this.s, arguments)); }, return: function _return(value) { var ret = this.s.return; return void 0 === ret ? Promise.resolve({ value: value, done: !0 }) : AsyncFromSyncIteratorContinuation(ret.apply(this.s, arguments)); }, throw: function _throw(value) { var thr = this.s.return; return void 0 === thr ? Promise.reject(value) : AsyncFromSyncIteratorContinuation(thr.apply(this.s, arguments)); } }, new AsyncFromSyncIterator(s); }

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
    this.files = [];
  }

  _createClass(Dropzone, [{
    key: "getDefaultFiles",
    value: function () {
      var _getDefaultFiles = _asyncToGenerator( /*#__PURE__*/regenerator_default().mark(function _callee() {
        var defaultFiles, _iteratorAbruptCompletion, _didIteratorError, _iteratorError, _iterator, _step, item, response, blob, file;

        return regenerator_default().wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                defaultFiles = JSON.parse(this.$elements.dropzone.dataset.dropzone || '[]');

                if (!defaultFiles.length) {
                  _context.next = 39;
                  break;
                }

                _iteratorAbruptCompletion = false;
                _didIteratorError = false;
                _context.prev = 4;
                _iterator = _asyncIterator(defaultFiles);

              case 6:
                _context.next = 8;
                return _iterator.next();

              case 8:
                if (!(_iteratorAbruptCompletion = !(_step = _context.sent).done)) {
                  _context.next = 21;
                  break;
                }

                item = _step.value;
                _context.next = 12;
                return fetch(item.image);

              case 12:
                response = _context.sent;
                _context.next = 15;
                return response.blob();

              case 15:
                blob = _context.sent;
                file = new File([blob], item.name, {
                  type: blob.type
                });
                this.files.push(file);

              case 18:
                _iteratorAbruptCompletion = false;
                _context.next = 6;
                break;

              case 21:
                _context.next = 27;
                break;

              case 23:
                _context.prev = 23;
                _context.t0 = _context["catch"](4);
                _didIteratorError = true;
                _iteratorError = _context.t0;

              case 27:
                _context.prev = 27;
                _context.prev = 28;

                if (!(_iteratorAbruptCompletion && _iterator.return != null)) {
                  _context.next = 32;
                  break;
                }

                _context.next = 32;
                return _iterator.return();

              case 32:
                _context.prev = 32;

                if (!_didIteratorError) {
                  _context.next = 35;
                  break;
                }

                throw _iteratorError;

              case 35:
                return _context.finish(32);

              case 36:
                return _context.finish(27);

              case 37:
                _context.next = 39;
                return this.createPreviews();

              case 39:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this, [[4, 23, 27, 37], [28,, 32, 36]]);
      }));

      function getDefaultFiles() {
        return _getDefaultFiles.apply(this, arguments);
      }

      return getDefaultFiles;
    }()
  }, {
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
    key: "handleError",
    value: function handleError() {
      var defaultLength = this.files.length;
      var error = false;
      var message = [];
      this.files = this.files.filter(function (item) {
        return item.size <= 5 * 1024 * 1024;
      });

      if (defaultLength > this.files.length) {
        message.push('Файлы больше 5 мб не загружены');
        error = true;
      }

      if (this.files.length > 10) {
        message.push('Количество файлов не должно превышать 10');
        error = true;
        this.files = this.files.filter(function (item, index) {
          return index < 10;
        });
      }

      var $errorEl = this.$elements.dropzone.querySelector('.dropzone__error');
      if ($errorEl && !error) $errorEl.remove();

      if (error) {
        if (!$errorEl) {
          this.$elements.dropzone.insertAdjacentHTML('beforeend', "<div class=\"dropzone__error\"></div>");
          $errorEl = this.$elements.dropzone.querySelector('.dropzone__error');
        }

        $errorEl.innerHTML = message.join('<br>');
      }
    }
  }, {
    key: "handleAdd",
    value: function () {
      var _handleAdd = _asyncToGenerator( /*#__PURE__*/regenerator_default().mark(function _callee2() {
        var dt;
        return regenerator_default().wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                dt = new DataTransfer();
                this.handleError();
                this.files.forEach(function (item) {
                  dt.items.add(item);
                });
                this.$elements.value.files = dt.files;
                _context2.next = 6;
                return this.createPreviews();

              case 6:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, this);
      }));

      function handleAdd() {
        return _handleAdd.apply(this, arguments);
      }

      return handleAdd;
    }()
  }, {
    key: "handleDrop",
    value: function handleDrop() {
      var _this = this;

      this.$elements.dropzone.addEventListener('drop', /*#__PURE__*/function () {
        var _ref = _asyncToGenerator( /*#__PURE__*/regenerator_default().mark(function _callee3(event) {
          return regenerator_default().wrap(function _callee3$(_context3) {
            while (1) {
              switch (_context3.prev = _context3.next) {
                case 0:
                  event.preventDefault();
                  _this.files = [].concat(_toConsumableArray(_this.files), _toConsumableArray(event.dataTransfer.files));
                  _context3.next = 4;
                  return _this.handleAdd();

                case 4:
                case "end":
                  return _context3.stop();
              }
            }
          }, _callee3);
        }));

        return function (_x) {
          return _ref.apply(this, arguments);
        };
      }());
    }
  }, {
    key: "handleChange",
    value: function handleChange() {
      var _this2 = this;

      this.$elements.add.addEventListener('change', /*#__PURE__*/function () {
        var _ref2 = _asyncToGenerator( /*#__PURE__*/regenerator_default().mark(function _callee4(event) {
          return regenerator_default().wrap(function _callee4$(_context4) {
            while (1) {
              switch (_context4.prev = _context4.next) {
                case 0:
                  _this2.files = [].concat(_toConsumableArray(_this2.files), _toConsumableArray(event.target.files));
                  _context4.next = 3;
                  return _this2.handleAdd();

                case 3:
                case "end":
                  return _context4.stop();
              }
            }
          }, _callee4);
        }));

        return function (_x2) {
          return _ref2.apply(this, arguments);
        };
      }());
    }
  }, {
    key: "createPreviews",
    value: function () {
      var _createPreviews = _asyncToGenerator( /*#__PURE__*/regenerator_default().mark(function _callee5() {
        var addPreview, result;
        return regenerator_default().wrap(function _callee5$(_context5) {
          while (1) {
            switch (_context5.prev = _context5.next) {
              case 0:
                this.$elements.item.classList.add('form__item_load');

                addPreview = function addPreview(item) {
                  return new Promise(function (resolve) {
                    var reader = new FileReader();

                    reader.onload = function () {
                      return resolve({
                        id: item.lastModified,
                        image: reader.result,
                        name: item.name,
                        size: item.size
                      });
                    };

                    reader.readAsDataURL(item);
                  });
                };

                _context5.next = 4;
                return Promise.all(this.files.map(function (item) {
                  return addPreview(item);
                }));

              case 4:
                result = _context5.sent;
                this.$elements.files.innerHTML = result.map(function (item) {
                  var size = item.size / 1048576;
                  var sizeParam = 'МБ';

                  if (size < 1) {
                    size = item.size / 1024;
                    sizeParam = 'КБ';
                  }

                  return "\n\t\t\t\t\t<li class=\"list__item\">\n\t\t\t\t\t\t<div class=\"list__item-image\" style=\"background-image: url(".concat(item.image, ");\"></div>\n\t\t\t\t\t\t<div class=\"list__item-content\">\n\t\t\t\t\t\t\t<div>").concat(item.name, "</div>\n\t\t\t\t\t\t\t<span>").concat(size.toFixed(1), " ").concat(sizeParam, "</span>\n\t\t\t\t\t\t\t<button type=\"button\" data-dropzone-remove=\"").concat(item.id, "\">\n\t\t\t\t\t\t\t\t<svg class=\"icon icon_cross\" viewBox=\"0 0 18 18\" style=\"width: 1.8rem; height: 1.8rem;\">\n\t\t\t\t\t\t\t\t\t<use xlink:href=\"#cross\"></use>\n\t\t\t\t\t\t\t\t</svg>\n\t\t\t\t\t\t\t</button>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</li>\n\t\t\t\t");
                }).join('');
                this.$elements.files.classList.toggle('list_full', this.files.length > 0);
                this.$elements.item.classList.remove('form__item_load');

              case 8:
              case "end":
                return _context5.stop();
            }
          }
        }, _callee5, this);
      }));

      function createPreviews() {
        return _createPreviews.apply(this, arguments);
      }

      return createPreviews;
    }()
  }, {
    key: "handleRemove",
    value: function handleRemove() {
      var _this3 = this;

      document.addEventListener('click', /*#__PURE__*/function () {
        var _ref3 = _asyncToGenerator( /*#__PURE__*/regenerator_default().mark(function _callee6(event) {
          var $el, imageId, dt;
          return regenerator_default().wrap(function _callee6$(_context6) {
            while (1) {
              switch (_context6.prev = _context6.next) {
                case 0:
                  $el = event.target;

                  if (!$el.matches('[data-dropzone-remove]')) {
                    _context6.next = 9;
                    break;
                  }

                  imageId = parseInt($el.dataset.dropzoneRemove);
                  dt = new DataTransfer();
                  _this3.files = _this3.files.filter(function (item) {
                    return item.lastModified !== imageId;
                  });

                  _this3.files.forEach(function (item) {
                    dt.items.add(item);
                  });

                  _this3.$elements.value.files = dt.files;
                  _context6.next = 9;
                  return _this3.createPreviews();

                case 9:
                case "end":
                  return _context6.stop();
              }
            }
          }, _callee6);
        }));

        return function (_x3) {
          return _ref3.apply(this, arguments);
        };
      }());
    }
  }, {
    key: "init",
    value: function () {
      var _init = _asyncToGenerator( /*#__PURE__*/regenerator_default().mark(function _callee7() {
        return regenerator_default().wrap(function _callee7$(_context7) {
          while (1) {
            switch (_context7.prev = _context7.next) {
              case 0:
                if (!this.$elements.dropzone) {
                  _context7.next = 7;
                  break;
                }

                _context7.next = 3;
                return this.getDefaultFiles();

              case 3:
                this.handleDrag();
                this.handleDrop();
                this.handleChange();
                this.handleRemove();

              case 7:
              case "end":
                return _context7.stop();
            }
          }
        }, _callee7, this);
      }));

      function init() {
        return _init.apply(this, arguments);
      }

      return init;
    }()
  }]);

  return Dropzone;
}();

/* harmony default export */ var components_dropzone = (Dropzone);
;// CONCATENATED MODULE: ./src/js/components/review_clear.js
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
  $form.querySelector('[data-dropzone-item]').innerHTML = "\n\t\t<input class=\"dropzone-hide\" type=\"file\" name=\"files\" multiple value=\"\" data-dropzone-value accept=\"image/*\">\n\t\t<label class=\"dropzone\" data-dropzone=\"[]\">\n\t\t\t<input type=\"file\" multiple value=\"\" data-dropzone-add accept=\"image/*\">\n\t\t\t<span class=\"dropzone__message\">\n\t\t\t\t<svg class=\"icon icon_dropzone\" viewBox=\"0 0 48 48\" style=\"width: 4.8rem; height: 4.8rem;\">\n\t\t\t\t\t<use xlink:href=\"#dropzone\"></use>\n\t\t\t\t</svg>\n\t\t\t\t<span>\u041F\u0435\u0440\u0435\u0442\u0430\u0449\u0438\u0442\u0435 \u0444\u043E\u0442\u043E \u0441\u044E\u0434\u0430<br> \u0438\u043B\u0438 <strong>\u0437\u0430\u0433\u0440\u0443\u0437\u0438\u0442\u0435 \u0441 \u043A\u043E\u043C\u043F\u044C\u044E\u0442\u0435\u0440\u0430</strong><br> \u0434\u043E&nbsp;10&nbsp;\u0444\u0430\u0439\u043B\u043E\u0432, \u043C\u0430\u043A\u0441\u0438\u043C\u0430\u043B\u044C\u043D\u044B\u0439 \u0440\u0430\u0437\u043C\u0435\u0440 1&nbsp;\u0444\u0430\u0439\u043B\u0430&nbsp;-&nbsp;5&nbsp;\u043C\u0431</span>\n\t\t\t</span>\n\t\t</label>\n\t\t<ul class=\"list list_upload\" data-dropzone-files></ul>\n\t";
}
// EXTERNAL MODULE: ./src/js/components/navigation_dropdown.js
var navigation_dropdown = __webpack_require__(8485);
;// CONCATENATED MODULE: ./src/js/pages/lk_reviews.js






window.clearReviewData = reviewClear;
window.addDropzone = /*#__PURE__*/_asyncToGenerator( /*#__PURE__*/regenerator_default().mark(function _callee() {
  var dropzone;
  return regenerator_default().wrap(function _callee$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          dropzone = new components_dropzone();
          _context.next = 3;
          return dropzone.init();

        case 3:
        case "end":
          return _context.stop();
      }
    }
  }, _callee);
}));
}();
/******/ })()
;