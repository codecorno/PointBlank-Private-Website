!function($, window, document, _undefined)
{
	"use strict";

	XF.CodeBlock = XF.Element.newHandler({
		options: {
			lang: null
		},

		init: function()
		{
			var language = this.options.lang,
				$code = this.$target.find('code');

			if (!language || typeof Prism != 'object' || !$code.length)
			{
				return;
			}

			$code.addClass('language-' + language);

			Prism.plugins.customClass.map({});
			Prism.plugins.customClass.prefix('prism-');

			Prism.highlightElement($code[0]);
		}
	});

	XF.Element.register('code-block', 'XF.CodeBlock');
}
(jQuery, window, document);