!function($, window, document, _undefined)
{
	"use strict";

	XF.Carousel = XF.Element.newHandler({
		options: {
			items: null,
			pause: 4000
		},
		breakpoints: {
			2: [700]
		},

		$items: null,

		init: function()
		{
			if (!$.fn.lightSlider)
			{
				console.error('Lightslider must be loaded');
				return;
			}

			this.$items = this.$target.children();

			var options = this.options,
				responsive = [];

			if (options.items === null)
			{
				if (this.$target.attr('class').match(/--show(\d+)/))
				{
					options.items = parseInt(RegExp.$1, 10);
				}
				else
				{
					options.items = 1;
				}
			}

			if (this.breakpoints[options.items])
			{
				var breakpoint = this.breakpoints[options.items];

				for (var i = 0; i < breakpoint.length; i++)
				{
					responsive.push({
						breakpoint: breakpoint[i],
						settings: {
							item: (options.items - i - 1)
						}
					});
				}
			}

			var effectiveItems = options.items,
				width = $(window).width(),
				bp;

			for (bp in responsive)
			{
				if (width < responsive[bp].breakpoint)
				{
					effectiveItems = responsive[bp].settings.item;
				}
			}

			if (this.$items.length <= effectiveItems)
			{
				return;
			}

			this.slider = this.$target.lightSlider({
				item: options.items,
				addClass: 'carousel-scrollContainer',
				slideMargin: 0,
				galleryMargin: 0,
				controls: false,
				auto: true,
				pause: options.pause,
				speed: 400,
				pauseOnHover: true,
				loop: true,
				rtl: XF.isRtl(),
				enableDrag: false,
				responsive: responsive
			});

			var self = this;
			$(window).on('resize', function()
			{
				self.$target.css('height', '');
				self.slider.refresh();
			});
		}
	});

	XF.Element.register('carousel', 'XF.Carousel');
}
(jQuery, window, document);