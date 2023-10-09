/******/ (function() { // webpackBootstrap
var __webpack_exports__ = {};
var moduleReserv = document.querySelector('[data-module-reserv]');

if (moduleReserv) {
  moduleReserv.addEventListener('change', function (event) {
    document.querySelector('[data-module-reserv-other]').classList.toggle('form__item_show', event.target.value === 'other');
  });
}
/******/ })()
;