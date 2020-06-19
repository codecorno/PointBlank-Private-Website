!function($, window, document, _undefined)
{
	"use strict";

	// ################################## TEL BOX HANDLER ###########################################

	XF.TelBox = XF.Element.newHandler({
		options: {
			telInput: '.js-telInput',
			dialCode: '.js-dialCode',
			intlNumb: '.js-intlNumb'
		},

		$telInput: null,
		$dialCode: null,
		$intlNumb: null,

		init: function()
		{
			var $target = this.$target,
				$telInput = $target.find(this.options.telInput),
				$dialCode = $target.find(this.options.dialCode),
				$intlNumb = $target.find(this.options.intlNumb);

			if (!$telInput.length)
			{
				console.error('No tel input found.');
				return;
			}

			if (!$dialCode.length)
			{
				console.error('No dial code hidden input found.');
				return;
			}

			if (!$intlNumb.length)
			{
				console.error('No international number hidden input found.');
				return;
			}

			$telInput.intlTelInput();
			$target.closest('form').on('submit', XF.proxy(this, 'beforeSubmit'));

			this.$telInput = $telInput;
			this.$dialCode = $dialCode;
			this.$intlNumb = $intlNumb;
		},

		beforeSubmit: function(e)
		{
			var $telInput = this.$telInput,
				$intlNumb = this.$intlNumb,
				$dialCode = this.$dialCode,
				intlNumb = $telInput.intlTelInput('getNumber'),
				countryData = $telInput.intlTelInput('getSelectedCountryData');

			$intlNumb.val(intlNumb);
			$dialCode.val(countryData['dialCode']);
		}
	});

	XF.Element.register('tel-box', 'XF.TelBox');
}
(jQuery, window, document);