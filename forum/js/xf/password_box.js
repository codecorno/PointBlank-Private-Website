!function($, window, document, _undefined)
{
	"use strict";

	XF.PasswordStrength = XF.Element.newHandler({
		options: {},

		$password: null,
		$meter: null,
		$meterText: null,

		language: {},

		init: function()
		{
			this.$password = this.$target.find('.js-password');
			this.$meter = this.$target.find('.js-strengthMeter');
			this.$meterText = this.$target.find('.js-strengthText');

			this.language = $.parseJSON($('.js-zxcvbnLanguage').first().html()) || {};

			this.$password.on('input', XF.proxy(this, 'input'));
		},

		input: function()
		{
			var password = this.$password.val(),
				result = zxcvbn(password),
				score = result.score, value,
				message = result.feedback.warning || '';

			// note: the messages in this file are translated elsewhere

			if (password)
			{
				value = (score + 1) * 20;

				if (score >= 4)
				{
					message = 'This is a very strong password';
				}
				else if (score >= 3)
				{
					message = 'This is a reasonably strong password';
				}
				else if (!message)
				{
					message = 'The chosen password could be stronger';
				}
			}
			else
			{
				message = 'Entering a password is required';
				value = 0;
			}

			this.$meter.val(value);
			this.$meterText.text(this.language[message] || '');
		}
	});

	XF.Element.register('password-hide-show', 'XF.PasswordHideShow');
	XF.Element.register('password-strength', 'XF.PasswordStrength');
}
(jQuery, window, document);