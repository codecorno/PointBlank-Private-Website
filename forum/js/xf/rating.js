!function($, window, document, _undefined)
{
	"use strict";

	XF.Rating = XF.Element.newHandler({
		options: {
			theme: 'fontawesome-stars',
			initialRating: null,
			ratingHref: null,
			readonly: false,
			deselectable: false,
			showSelected: true
		},

		ratingOverlay: null,

		init: function()
		{
			this.$target.barrating({
				theme: this.options.theme,
				initialRating: this.options.initialRating,
				readonly: this.options.readonly ? true : false,
				deselectable: this.options.deselectable ? true : false,
				showSelectedRating: this.options.showSelected ? true : false,
				onSelect: XF.proxy(this, 'ratingSelected')
			});

			if (this.options.showSelected)
			{
				this.$target.next('.br-widget').addClass('br-widget--withSelected');
			}

			if (this.options.initialRating)
			{
				this.$target.val(this.options.initialRating);
			}
		},

		ratingSelected: function(value, text, event)
		{
			if (this.options.readonly)
			{
				return;
			}

			if (!this.options.ratingHref)
			{
				return;
			}

			if (this.ratingOverlay)
			{
				this.ratingOverlay.destroy();
			}

			this.$target.barrating('clear');

			XF.ajax('get', this.options.ratingHref, {
				rating: value
			}, XF.proxy(this, 'loadOverlay'));
		},

		loadOverlay: function(data)
		{
			if (data.html)
			{
				var self = this;
				XF.setupHtmlInsert(data.html, function ($html, container)
				{
					var $overlay = XF.getOverlayHtml({
						html: $html,
						title: container.h1 || container.title
					});
					self.ratingOverlay = XF.showOverlay($overlay);
				});
			}
		}
	});

	XF.Element.register('rating', 'XF.Rating');
}
(jQuery, window, document);