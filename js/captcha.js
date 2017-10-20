
'use strict';

class Captcha {

	constructor(captcha) {
		this.captcha = captcha.find('input.captcha');
		this.captchaInput = captcha.find('input.captchaInput');
		this.refresh = captcha.find('button.refresh');
		this.validation = captcha.find('p.validation');

		this.setCaptcha();
		this.setRefresh();
	}

	setCaptcha() {
		let captcha = this.captcha;
		let alpha = new Array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0');
		
		var a = alpha[Math.floor(Math.random() * alpha.length)];
		var b = alpha[Math.floor(Math.random() * alpha.length)];
		var c = alpha[Math.floor(Math.random() * alpha.length)];
		var d = alpha[Math.floor(Math.random() * alpha.length)];
		var e = alpha[Math.floor(Math.random() * alpha.length)];
		var f = alpha[Math.floor(Math.random() * alpha.length)];
		var g = alpha[Math.floor(Math.random() * alpha.length)];
		
		let code =  a + ' ' + b + ' ' + ' ' + c + ' ' + d + ' ' + e + ' ' + f + ' ' + g;
		let colors = ["#B40404", "#beb1dd", "#b200ff", "#faff00", "#0000FF", "#FE2E9A", "#FF0080", "#2EFE2E"];

		captcha.val(code).css('color', colors[Math.floor(Math.random() * colors.length)]);

		return code;
	}

	setRefresh() {
		let self = this;
		this.refresh.on('click.refreshCaptcha', function(e) {
			e.preventDefault();
			self.appendLog();
			self.setCaptcha();
		});
	}

	appendLog(log=false) {
		return log ? this.validation.show() : this.validation.hide();
	}

	validateCaptcha() {
		this.appendLog();

		if (this.removeSpace(this.captcha.val()) !== $.trim(this.captchaInput.val()) || this.captchaInput.val() === '') {
			this.setCaptcha();
			this.appendLog(true);
			return false;
		}

		return true;
	}

	removeSpace(string) {
		return string.split(' ').join('');
	}

}