!function($, window, document, _undefined)
{
	"use strict";

	XF.Lightbox = XF.Element.newHandler({
		options: {
			lbAddClass: '',
			lbUniversal: 0,
			lbSingleImage: 0,
			lbContainerZoom: 0,
			lbTrigger: '.js-lbImage',
			lbContainer: '.js-lbContainer',
			lbMode: 'lg-slide',
			lbThumbnail: true,
			lbShowThumb: true,
			lbNewWindow: true,
			lbHideBarsDelay: 1000,
			lbPreload: 1
		},

		init: function()
		{
			this.initPlugins();

			if (this.options.lbSingleImage)
			{
				this.initSingleImageContainer();
			}
			else
			{
				this.initContainers();
			}

			$(document).on('xf:reinit', XF.proxy(this, 'checkReInit'));
		},

		initSingleImageContainer: function()
		{
			if (!this.options.lbSingleImage)
			{
				return;
			}

			var $container = this.$target;
			$(window).onPassive('resize', XF.proxy(this, 'handleContainerZoom'));
			$container.on('lightbox:init', XF.proxy(this, 'handleContainerZoom'));

			this._initContainer($container);
		},

		handleContainerZoom: function()
		{
			if (!this.options.lbSingleImage || !this.options.lbContainerZoom)
			{
				return;
			}

			var $container = this.$target,
				$image = $container.find('img[data-zoom-target=1]'),
				self = this;

			if ($image.parents('a').length)
			{
				return;
			}

			// timeout to allow animations to finish (e.g. quick edit)
			setTimeout(function()
			{
				if (!$image.prop('complete'))
				{
					$image.on('load', function()
					{
						if (self.isImageNaturalSize($(this)))
						{
							$container.removeClass('lbContainer--canZoom');
						}
						else
						{
							$container.addClass('lbContainer--canZoom');
						}
					});
				}
				else
				{
					if (self.isImageNaturalSize($image))
					{
						$container.removeClass('lbContainer--canZoom');
					}
					else
					{
						$container.addClass('lbContainer--canZoom');
					}
				}
			}, 500);
		},

		initContainers: function()
		{
			if (this.options.lbSingleImage)
			{
				return;
			}

			var $containers = this.options.lbUniversal ? this.$target : this.$target.find(this.options.lbContainer);
			var self = this;

			$containers.each(function()
			{
				self._initContainer($(this));
			});
		},

		_initContainer: function($container)
		{
			if ($container.data('lbInitialized'))
			{
				return;
			}

			$container.find(this.options.lbTrigger).on('click.xflbtrigger', function(e)
			{
				if (e.ctrlKey || e.altKey || e.metaKey || e.shiftKey)
				{
					// stop the LB from triggering
					e.stopImmediatePropagation();
					return true;
				}
			});

			var config = $.extend(this.getConfig(), {
				galleryId: $container.data('lb-id')
			});
			$container.lightGallery(config);
			$container.data('lbInitialized', true);

			var event = $.Event('lightbox:init');
			$container.trigger(event);

			$container.on('onAfterSlide.lg', XF.proxy(this, 'afterSlide'));
		},

		_reInitContainer: function($container)
		{
			if (!$container.data('lbInitialized'))
			{
				return;
			}

			var lightGallery = $container.data('lightGallery');
			if (lightGallery)
			{
				lightGallery.destroy(true);
			}

			$container.find(this.options.lbTrigger).off('click.xflbtrigger');

			$container.removeData('lbInitialized');
			this._initContainer($container);
		},

		checkReInit: function(e, el)
		{
			if (el == document)
			{
				return;
			}

			if (!this.$target.find(el).length)
			{
				return;
			}

			var $el = $(el),
				lbTrigger = this.options.lbTrigger,
				lbContainer = this.options.lbContainer;

			if (this.options.lbUniversal)
			{
				if ($el.is(lbTrigger) || $el.find(lbTrigger).length)
				{
					// reinit the one container we have
					this._reInitContainer(this.$target);
				}
			}
			else if ($el.is(lbContainer) || $el.find(lbContainer).length)
			{
				// new container, reinit all to pick this one up
				this.initContainers();
			}
			else if ($el.closest(lbContainer).length && ($el.is(lbTrigger) || $el.find(lbTrigger).length))
			{
				// should be an existing container but a new image
				this._reInitContainer($el.closest(lbContainer));
			}
		},

		getConfig: function()
		{
			return {
				addClass: $.trim('xfLb ' + this.options.lbAddClass),
				selector: this.options.lbTrigger,
				mode: this.options.lbMode,
				thumbnail: this.options.lbThumbnail,
				showThumbByDefault: this.options.lbShowThumb,
				hideBarsDelay: this.options.lbHideBarsDelay,
				newWindow: this.options.lbNewWindow,
				preload: this.options.lbPreload,
				getCaptionFromTitleOrAlt: false,
				share: false
			};
		},

		isImageNaturalSize: function($image)
		{
			var dims = {
				width: $image.width(),
				height: $image.height(),
				naturalWidth: $image.prop('naturalWidth'),
				naturalHeight: $image.prop('naturalHeight')
			};

			if (!dims.naturalWidth || !dims.naturalHeight)
			{
				// could be a failed image, ignore
				return true;
			}

			if (dims.width == dims.naturalWidth
				&& dims.height == dims.naturalHeight
			)
			{
				return true;
			}
			else
			{
				return false;
			}
		},

		afterSlide: function(e, prev, i)
		{
			var lightGallery = $(e.target).data('lightGallery'),
				curSlide = lightGallery.$slide.get(i),
				$curImage = $(curSlide).find('.lg-image');

			if (!$curImage.prop('complete'))
			{
				var self = this;
				$curImage.on('load', function()
				{
					var $image = $(this);
					self.setZoomIconState($image, lightGallery);
				});
			}
			else
			{
				this.setZoomIconState($curImage, lightGallery);
			}
		},

		setZoomIconState: function($image, lightGallery)
		{
			var $actualSize = lightGallery.$outer.find('#lg-actual-size');

			if (typeof $image.data('lbCanZoom') === 'undefined')
			{
				var self = this;

				// timeout to allow animations to finish
				setTimeout(function()
				{
					if (self.isImageNaturalSize($image))
					{
						$actualSize.addClass('lg-icon--dimmed');
						$image.data('lbCanZoom', false);
					}
					else
					{
						$actualSize.removeClass('lg-icon--dimmed');
						$image.data('lbCanZoom', true);
					}
				}, 500);
			}
			else
			{
				if (!$image.data('lbCanZoom'))
				{
					$actualSize.addClass('lg-icon--dimmed');
				}
				else
				{
					$actualSize.removeClass('lg-icon--dimmed');
				}
			}
		},

		initPlugins: function()
		{
			/**
			 * Plugin for LightGallery to display an icon which opens image in a new window.
			 */
			var newWindowDefaults = {
				newWindow: true
			};

			var NewWindow = function(element)
			{
				// You can access all lightgallery variables and functions like this.
				this.core = $(element).data('lightGallery');

				this.$el = $(element);
				this.core.s = $.extend({}, newWindowDefaults, this.core.s);

				this.init();

				return this;
			};

			NewWindow.prototype.init = function()
			{
				if (this.core.s.newWindow)
				{
					var href,
						$outer = this.core.$outer,
						$newWindow = $('<a id="lg-new-window" class="lg-icon" target="_blank"></a>');

					var running = false;
					this.$el.on('onSlideItemLoad.lg onAfterSlide.lg', function(e)
					{
						if (running)
						{
							return;
						}

						running = true;

						var $currentSlide = $outer.find('.lg-current');

						href = $currentSlide.find('.lg-image').attr('src');
						if (href)
						{
							$newWindow.attr('href', href);
							$newWindow.show();
						}
						else if (!$newWindow.attr('href'))
						{
							$newWindow.hide();
						}

						running = false;
					});

					var $toolbar = $outer.find('.lg-toolbar'),
						$position = $toolbar.find('#lg-actual-size');

					if ($position.length)
					{
						$newWindow.insertBefore($position);
					}
					else
					{
						$toolbar.append($newWindow);
					}
				}
			};

			/**
			 * Destroy function must be defined.
			 * lightgallery will automatically call your module destroy function
			 * before destroying the gallery
			 */
			NewWindow.prototype.destroy = function()
			{
				this.$el.off('onSlideItemLoad.lg onAfterSlide.lg');
			};

			/**
			 * Plugin for LightGallery to display an icon which opens image in a new window.
			 */
			var captionLoaderDefaults = {
				captionLoader: true
			};

			var CaptionLoader = function(element)
			{
				// You can access all lightgallery variables and functions like this.
				this.core = $(element).data('lightGallery');

				this.$el = $(element);
				this.core.s = $.extend({}, captionLoaderDefaults, this.core.s);

				this.init();

				return this;
			};

			CaptionLoader.prototype.init = function()
			{
				if (this.core.s.captionLoader)
				{
					this.$el.one('onAfterOpen.lg', XF.proxy(this, 'captionAfterOpen'));
					this.$el.on('onAfterAppendSubHtml.lg', XF.proxy(this, 'captionLoaded'));
				}
			};

			CaptionLoader.prototype.captionAfterOpen = function()
			{
				var $container = this.$el;

				if ($container.data('captionsProcessed'))
				{
					return;
				}

				var template = '<h4>{{title}}</h4><p><a href="{{href}}">{{desc}}</a></p>';

				// note: this refers to the lightgallery instance so can't access other options easily
				$container.find('.js-lbImage').each(function()
				{
					var $anchor = $(this),
						$image = $anchor.find('img'),
						$container = $anchor.closest('.js-lbContainer'),
						lbId = $container.data('lb-id'),

						caption = {
							title: $container.data('lb-caption-title') || $image.attr('alt') || $image.attr('title') || '',
							desc: $anchor.data('lb-caption-desc') || $container.data('lb-caption-desc') || '',
							href: $anchor.data('lb-caption-href') || (lbId ? (window.location.href.replace(/#.*$/,'') + '#' + lbId) : null)
						};

					$anchor.attr('data-sub-html', Mustache.render(template, caption));
				});

				$container.data('captionsProcessed', true);
			};

			CaptionLoader.prototype.captionLoaded = function()
			{
				var $outer = this.core.$outer;
				$outer.find(this.core.s.appendSubHtmlTo + ' a').on('click', XF.proxy(this, 'captionLinkClicked'));
			};

			CaptionLoader.prototype.captionLinkClicked = function(e)
			{
				setTimeout(function()
				{
					window.location.href = $(e.target).attr('href');
				}, 50);
			};

			/**
			 * Destroy function must be defined.
			 * lightgallery will automatically call your module destroy function
			 * before destroying the gallery
			 */
			CaptionLoader.prototype.destroy = function()
			{
				this.$el.off('onAfterOpen.lg onAfterAppendSubHtml.lg');
			};

			$.fn.lightGallery.modules.newwindow = NewWindow;
			$.fn.lightGallery.modules.captionloader = CaptionLoader;
		}
	});

	XF.Element.register('lightbox', 'XF.Lightbox');
}
(jQuery, window, document);