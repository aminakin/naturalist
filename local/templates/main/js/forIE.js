function isIE() {
      var ua = window.navigator.userAgent
      var msie = ua.indexOf('MSIE ') // IE 10 or older
      var trident = ua.indexOf('Trident/') //IE 11

      return msie > 0 || trident > 0
}     
      
if (isIE()) {
	if (localStorage.getItem('ieAlert') !== '1') {
		alert('Ваш браузер не поддерживается\n"Для корректной работы сайта необходимо использовать браузеры - Google Chrome, Safari, Mozilla Firefox, Opera, Яндекс браузер".')
		localStorage.setItem('ieAlert', '1')
	}
}