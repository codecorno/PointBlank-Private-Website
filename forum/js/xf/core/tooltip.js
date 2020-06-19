/** @param {jQuery} $ jQuery Object */
!function($, window, document)
{
	"use strict";

	// ################################## TOOLTIP CORE ###########################################

	XF.TooltipElement = XF.create({
		options: {
			baseClass: 'tooltip',
			extraClass: 'tooltip--basic',
			html: false,
			inViewport: true,
			loadRequired: false,
			loadParams: null,
			placement: 'top'
		},

		content: null,
		$tooltip: null,
		shown: false,
		shownFully: false,
		placement: null,
		positioner: null,
		loadRequired: false,
		loading: false,
		contentApplied: false,
		setupCallbacks: null,

		__construct: function(content, options, positioner)
		{
			this.setupCallbacks = []; // needs to be set here to be local

			this.options = $.extend(true, {}, this.options, options);
			this.content = content;
			this.loadRequired = this.options.loadRequired;

			if (positioner)
			{
				this.setPositioner(positioner);
			}
		},

		setPositioner: function(positioner)
		{
			this.positioner = positioner;
		},

		setLoadRequired: function(required)
		{
			this.loadRequired = required;
		},

		addSetupCallback: function(callback)
		{
			if (this.$tooltip)
			{
				// already setup, run now
				callback(this.$tooltip);
			}
			else
			{
				this.setupCallbacks.push(callback);
			}
		},

		show: function()
		{
			if (this.shown)
			{
				return;
			}

			this.shown = true;

			if (this.loadRequired)
			{
				this.loadContent();
				return;
			}

			var $tooltip = this.getTooltip(),
				self = this;

			this.reposition();

			$(window).on('resize.tooltip-' + $tooltip.xfUniqueId(), XF.proxy(this, 'reposition'));

			$tooltip
				.trigger('tooltip:shown')
				.stop()
				.css({
					visibility: '',
					display: 'none'
				})
				.fadeIn('fast', function() { self.shownFully = true; });
		},

		hide: function()
		{
			if (!this.shown)
			{
				return;
			}

			this.shown = false;
			this.shownFully = false;

			var $tooltip = this.$tooltip;
			if ($tooltip)
			{
				$tooltip
					.stop()
					.fadeOut('fast')
					.trigger('tooltip:hidden');

				$(window).off('resize.tooltip-' + $tooltip.xfUniqueId());
			}
		},

		toggle: function()
		{
			this.shown ? this.hide() : this.show();
		},

		destroy: function()
		{
			if (this.$tooltip)
			{
				this.$tooltip.remove();
			}
		},

		isShown: function()
		{
			return this.shown;
		},

		isShownFully: function()
		{
			return this.shown && this.shownFully;
		},

		requiresLoad: function()
		{
			return this.loadRequired;
		},

		getPlacement: function()
		{
			return XF.rtlFlipKeyword(this.options.placement);
		},

		reposition: function()
		{
			var positioner = this.positioner;

			if (!positioner)
			{
				console.error('No tooltip positioner');
				return;
			}

			if (this.loadRequired)
			{
				return;
			}

			var targetDims,
				forceInViewport = this.options.inViewport;

			if (positioner instanceof $)
			{
				targetDims = positioner.dimensions(true);

				if (positioner.closest('.overlay').length)
				{
					forceInViewport = true;
				}
			}
			else if (typeof positioner[0] !== 'undefined' && typeof positioner[1] !== 'undefined')
			{
				// a single [x, y] point
				targetDims = {
					top: positioner[1],
					right: positioner[0],
					bottom: positioner[1],
					left: positioner[0]
				};
			}
			else if (typeof positioner.right !== 'undefined' && typeof positioner.bottom !== 'undefined')
			{
				// positioner is already t/r/b/l format
				targetDims = positioner;
			}
			else
			{
				console.error('Positioner is not in correct format', positioner);
			}

			targetDims.width = targetDims.right - targetDims.left;
			targetDims.height = targetDims.bottom - targetDims.top;

			var $tooltip = this.getTooltip(),
				placement = this.getPlacement(),
				baseClass = this.options.baseClass,
				originalPlacement = placement,
				constraintDims;

			if (forceInViewport)
			{
				var $window = $(window),
					vwHeight = $window.height(),
					vwWidth = $window.width(),
					vwTop = $window.scrollTop(),
					vwLeft = $window.scrollLeft(),
					stickyHeaders;

				if (stickyHeaders = XF.Element.getHandlers('sticky-header'))
				{
					if (stickyHeaders[0].$target.hasClass(stickyHeaders[0].options.stickyClass))
					{
						vwTop += stickyHeaders[0].$target.outerHeight();
					}
				}

				constraintDims = {
					top: vwTop,
					left: vwLeft,
					right: vwLeft + vwWidth,
					bottom: vwTop + vwHeight,
					width: vwWidth,
					height: vwHeight
				};
			}
			else
			{
				constraintDims = $('body').dimensions();
			}

			if (this.placement)
			{
				$tooltip.removeClass(baseClass + '--' + this.placement);
			}

			$tooltip.addClass(baseClass + '--' + placement).css({
				visibility: 'hidden',
				display: 'block',
				top: '',
				bottom: '',
				left: '',
				right: '',
				'padding-left': '',
				'padding-right': '',
				'padding-top': '',
				'padding-bottom': ''
			});

			var tooltipWidth = $tooltip.outerWidth(),
				tooltipHeight = $tooltip.outerHeight();

			// can we fit this in the right position? if not, flip it
			// if still can't fit horizontally, go vertical
			if (placement == 'top' && targetDims.top - tooltipHeight < constraintDims.top)
			{
				placement = 'bottom';
			}
			else if (placement == 'bottom' && targetDims.bottom + tooltipHeight > constraintDims.bottom)
			{
				if (targetDims.top - tooltipHeight >= constraintDims.top)
				{
					// only flip this back to the top if we have space within the constraints
					placement = 'top';
				}
			}
			else if (placement == 'left' && targetDims.left - tooltipWidth < constraintDims.left)
			{
				if (targetDims.right + tooltipWidth > constraintDims.right)
				{
					if (targetDims.top - tooltipHeight < constraintDims.top)
					{
						placement = 'bottom';
					}
					else
					{
						placement = 'top';
					}
				}
				else
				{
					placement = 'right';
				}
			}
			else if (placement == 'right' && targetDims.right + tooltipWidth > constraintDims.right)
			{
				if (targetDims.left - tooltipWidth < constraintDims.left)
				{
					if (targetDims.top - tooltipHeight < constraintDims.top)
					{
						placement = 'bottom';
					}
					else
					{
						placement = 'top';
					}
				}
				else
				{
					placement = 'left';
				}
			}
			if (placement != originalPlacement)
			{
				$tooltip.removeClass(baseClass + '--' + originalPlacement).addClass(baseClass + '--' + placement);
			}

			// figure out how to place the edges
			var position = {
				top: '',
				right: '',
				bottom: '',
				left: ''
			};
			switch (placement)
			{
				case 'top':
					position.bottom = $(window).height() - targetDims.top;
					position.left = targetDims.left + targetDims.width / 2 - tooltipWidth / 2;
					break;

				case 'bottom':
					position.top = targetDims.bottom;
					position.left = targetDims.left + targetDims.width / 2 - tooltipWidth / 2;
					break;

				case 'left':
					position.top = targetDims.top + targetDims.height / 2 - tooltipHeight / 2;
					position.right = $(window).width() - targetDims.left;
					break;

				case 'right':
				default:
					position.top = targetDims.top + targetDims.height / 2 - tooltipHeight / 2;
					position.left = targetDims.right;
			}

			$tooltip.css(position);

			var tooltipDims = $tooltip.dimensions(true),
				delta = { top: 0, left: 0 },
				$arrow = $tooltip.find('.' + baseClass + '-arrow'),
				tooltipShifted = null;

			// Check to see if we're outside of the constraints on the opposite edge from our positioned side
			// and if we are, push us down to that position and move the arrow to be positioned nicely.
			// We will only move the top positioning when doing left/right and left using top/bottom.
			if (placement == 'left' || placement == 'right')
			{
				if (tooltipDims.top < constraintDims.top)
				{
					delta.top = constraintDims.top - tooltipDims.top;
					tooltipShifted = 'down';
				}
				else if (tooltipDims.bottom > constraintDims.bottom)
				{
					delta.top = constraintDims.bottom - tooltipDims.bottom;
					tooltipShifted = 'up';
				}

				$arrow.css({
					left: '',
					top: (50 - (100 * delta.top / tooltipDims.top)) + '%'
				});
			}
			else
			{
				if (tooltipDims.left < constraintDims.left)
				{
					delta.left = constraintDims.left - tooltipDims.left;
					tooltipShifted = 'left';
				}
				else if (tooltipDims.left + tooltipWidth > constraintDims.right)
				{
					delta.left = constraintDims.right - (tooltipDims.left + tooltipWidth);
					tooltipShifted = 'right';
				}

				var arrowLeft = parseInt(tooltipWidth / 100 * (50 - (100 * delta.left / tooltipWidth)), 0),
					arrowRealLeft = arrowLeft + parseInt($arrow.css('margin-left')),
					arrowRealRight = arrowRealLeft + $arrow.outerWidth(),
					tooltipLeftOffset = parseInt($tooltip.css('padding-left'), 10),
					tooltipRightOffset = parseInt($tooltip.css('padding-right'), 10),
					shiftDiff;

				// detect if the arrow is going to spill out of the main container and adjust the container
				// padding to keep the width the same but to shift it
				if (arrowRealLeft < tooltipLeftOffset)
				{
					shiftDiff = tooltipLeftOffset - arrowRealLeft;
					$tooltip.css({
						'padding-left':  Math.max(0, tooltipLeftOffset - shiftDiff),
						'padding-right':  tooltipRightOffset + shiftDiff
					});
				}
				else if (arrowRealRight > tooltipWidth - tooltipRightOffset)
				{
					shiftDiff = arrowRealRight - (tooltipWidth - tooltipRightOffset);
					$tooltip.css({
						'padding-left':  tooltipRightOffset + shiftDiff,
						'padding-right':  Math.max(0, tooltipRightOffset - shiftDiff)
					});
				}

				$arrow.css({
					top: '',
					left: arrowLeft
				});
			}

			if (delta.left)
			{
				$tooltip.css('left', position.left + delta.left);
			}
			else if (delta.top)
			{
				$tooltip.css('top', position.top + delta.top);
			}

			this.placement = placement;

			if (this.shown && !this.loadRequired)
			{
				$tooltip.css('visibility', '');
			}
		},

		attach: function()
		{
			this.getTooltip();
		},

		getTooltip: function()
		{
			if (!this.$tooltip)
			{
				var $tooltip = this.getTemplate();
				$tooltip.appendTo('body');
				this.$tooltip = $tooltip;

				if (!this.loadRequired)
				{
					this.applyTooltipContent();
				}
			}

			return this.$tooltip;
		},

		applyTooltipContent: function()
		{
			if (this.contentApplied || this.loadRequired)
			{
				return false;
			}

			var $tooltip = this.getTooltip(),
				$contentHolder = $tooltip.find('.' + this.options.baseClass + '-content'),
				content = this.content;

			if ($.isFunction(content))
			{
				content = content();
			}

			if (this.options.html)
			{
				$contentHolder.html(content);
				$contentHolder.find('img').on('load', XF.proxy(this, 'reposition'));
			}
			else
			{
				$contentHolder.text(content);
			}

			var setup = this.setupCallbacks;
			for (var i = 0; i < setup.length; i++)
			{
				setup[i]($tooltip);
			}

			XF.activate($tooltip);

			this.contentApplied = true;
			return true;
		},


		loadContent: function()
		{
			if (!this.loadRequired || this.loading)
			{
				return;
			}

			var content = this.content;

			var self = this,
				onLoad = function(newContent)
				{
					self.content = newContent;
					self.loadRequired = false;
					self.loading = false;
					self.applyTooltipContent();

					if (self.shown)
					{
						self.shown = false; // make sure the show works
						self.show();
					}
				};

			if (!$.isFunction(content))
			{
				onLoad('');
				return;
			}

			this.loading = true;
			content(onLoad, this.options.loadParams);
		},

		getTemplate: function()
		{
			var extra = this.options.extraClass ? (' ' + this.options.extraClass) : '',
				baseClass = this.options.baseClass;

			return $($.parseHTML('<div class="' + baseClass + extra + '" role="tooltip">'
				+ '<div class="' + baseClass + '-arrow"></div>'
				+ '<div class="' + baseClass + '-content"></div>'
				+ '</div>'));
		}
	});

	XF.TooltipTrigger = XF.create({
		options: {
			delayIn: 200,
			delayInLoading: 800,
			delayOut: 200,
			trigger: 'hover focus',
			maintain: false,
			clickHide: null,
			onShow: null,
			onHide: null
		},

		$target: null,
		tooltip: null,

		delayTimeout: null,
		delayTimeoutType: null,
		stopFocusBefore: null,
		clickTriggered: false,

		$covers: null,

		__construct: function($target, tooltip, options)
		{
			this.options = $.extend(true, {}, this.options, options);
			this.$target = $target;
			this.tooltip = tooltip;

			if (this.options.trigger == 'auto')
			{
				this.options.trigger = 'hover focus' + ($target.is('span') ? ' touchclick' : '');
			}

			tooltip.setPositioner($target);
			tooltip.addSetupCallback(XF.proxy(this, 'onTooltipSetup'));

			$target.xfUniqueId();
			XF.TooltipTrigger.cache[$target.attr('id')] = this;
		},

		init: function()
		{
			var $target = this.$target,
				actOnClick = false,
				actOnTouchClick = false,
				self = this,
				supportsPointerEvents = XF.supportsPointerEvents(),
				pointerEnter = supportsPointerEvents ? 'pointerenter' : 'mouseenter',
				pointerLeave = supportsPointerEvents ? 'pointerleave' : 'mouseleave';

			if (this.options.clickHide === null)
			{
				this.options.clickHide = $target.is('a');
			}

			var triggers = this.options.trigger.split(' ');
			for (var i = 0; i < triggers.length; i++)
			{
				switch (triggers[i])
				{
					case 'hover':
						$target.on(pointerEnter + '.tooltip', XF.proxy(this, 'mouseEnter'))
							.on(pointerLeave + '.tooltip', XF.proxy(this, 'leave'));
						break;

					case 'focus':
						$target.on({
							'focusin.tooltip': XF.proxy(this, 'focusEnter'),
							'focusout.tooltip': XF.proxy(this, 'leave')
						});
						break;

					case 'click':
						actOnClick = true;

						$target.onPointer('click.tooltip', XF.proxy(this, 'click'));
						$target.onPointer('auxclick.tooltip contextmenu.tooltip', function()
						{
							self.cancelShow();
							self.stopFocusBefore = Date.now() + 2000;
						});
						break;

					case 'touchclick':
						actOnTouchClick = true;
						$target.onPointer('click.tooltip', function(e)
						{
							if (XF.isEventTouchTriggered(e))
							{
								self.click(e);
							}
						});
						break;

					case 'touchhold':
						actOnTouchClick = true;

						$target.data('threshold', this.options.delayIn);

						$target.onPointer({
							'touchstart.tooltip': function(e)
							{
								$target.data('tooltip:touching', true);
							},
							'touchend.tooltip': function(e)
							{
								setTimeout(function()
								{
									$target.removeData('tooltip:touching');
								}, 50);
							},
							'taphold.tooltip': function(e)
							{
								$target.data('tooltip:taphold', true);
								if (XF.isEventTouchTriggered(e))
								{
									self.click(e);
								}
							},
							'contextmenu.tooltip': function(e)
							{
								if ($target.data('tooltip:touching'))
								{
									e.preventDefault();
								}
							}
						});
						break;
				}
			}

			if (actOnClick && actOnTouchClick)
			{
				console.error('Cannot have touchclick and click triggers');
			}

			if (!actOnClick && this.options.clickHide)
			{
				$target.onPointer('click.tooltip auxclick.tooltip contextmenu.tooltip', function(e)
				{
					if (actOnTouchClick && XF.isEventTouchTriggered(e))
					{
						// other event already triggered
						return;
					}

					self.hide();
					self.stopFocusBefore = Date.now() + 2000;
				});
			}

			$target.on({
				'tooltip:show': XF.proxy(this, 'show'),
				'tooltip:hide': XF.proxy(this, 'hide'),
				'tooltip:reposition': XF.proxy(this, 'reposition')
			});
		},

		reposition: function()
		{
			this.tooltip.reposition();
		},

		click: function(e)
		{
			if (e.button > 0 || e.ctrlKey || e.shiftKey || e.metaKey || e.altKey)
			{
				// non-primary clicks should prevent any hover behavior
				this.cancelShow();
				return;
			}

			if (this.tooltip.isShown())
			{
				if (!this.tooltip.isShownFully())
				{
					// a click before the tooltip has finished animating or loading, so act as if the click triggered
					e.preventDefault();
					this.clickShow(e);
					return;
				}

				this.hide();
			}
			else
			{
				e.preventDefault();
				this.clickShow(e);
			}
		},

		clickShow: function(e)
		{
			this.clickTriggered = true;

			var self = this;

			setTimeout(function()
			{
				var $covers = self.addCovers();

				if (XF.isEventTouchTriggered(e))
				{
					$covers.addClass('is-active');
				}
				else
				{
					$(document).on('click.tooltip-' + self.$target.xfUniqueId(), XF.proxy(self, 'docClick'));
				}
			}, 0);

			this.show();
		},

		addCovers: function()
		{
			if (this.$covers)
			{
				this.$covers.remove();
			}

			var dimensions = this.$target.dimensions(true);
			var boxes = [];

			// above
			boxes.push({
				top: 0,
				height: dimensions.top,
				left: 0,
				right: 0
			});

			// left
			boxes.push({
				top: dimensions.top,
				height: dimensions.height,
				left: 0,
				width: dimensions.left
			});

			// right
			boxes.push({
				top: dimensions.top,
				height: dimensions.height,
				left: dimensions.right,
				right: 0
			});

			// below
			boxes.push({
				top: dimensions.bottom,
				height: $('html').height() - dimensions.bottom,
				left: 0,
				right: 0
			});

			var $covers = $(),
				$box;
			for (var i = 0; i < boxes.length; i++)
			{
				$box = $('<div class="tooltipCover" />').css(boxes[i]);
				$covers = $covers.add($box);
			}

			$covers.on('click', XF.proxy(this, 'hide'));

			this.tooltip.getTooltip().before($covers);
			this.$covers = $covers;

			XF.setRelativeZIndex($covers, this.$target);

			return $covers;
		},

		docClick: function(e)
		{
			var $clicked,
				$covers = this.$covers,
				pageX = e.pageX,
				pageY = e.pageY,
				$window = $(window);

			if (!$covers)
			{
				return;
			}

			if (e.screenX == 0 && e.screenY == 0)
			{
				var dimensions = $(e.target).dimensions();
				pageX = dimensions.left;
				pageY = dimensions.top;
			}

			$covers.addClass('is-active');
			$clicked = $(document.elementFromPoint(pageX - $window.scrollLeft(), pageY - $window.scrollTop()));
			$covers.removeClass('is-active');

			if ($clicked.is($covers))
			{
				this.hide();
			}
		},

		mouseEnter: function(e)
		{
			if (XF.isEventTouchTriggered(e))
			{
				// make touch tooltips only trigger on click
				return;
			}

			this.enter();
		},

		focusEnter: function(e)
		{
			if (Date.now() - XF.pageDisplayTime < 100)
			{
				return;
			}

			if (XF.isEventTouchTriggered(e))
			{
				// touch focus is likely a long press so don't trigger a tooltip for that
				// (make touch tooltips only trigger on click)
				return;
			}

			// there are situations where a focus event happens and we don't want it to trigger a display
			if (!this.stopFocusBefore || Date.now() >= this.stopFocusBefore)
			{
				this.enter();
			}
		},

		enter: function()
		{
			if (this.isShown() && this.clickTriggered)
			{
				// already shown by a click, don't trigger anything else
				return;
			}

			this.clickTriggered = false;

			var delay = this.tooltip.requiresLoad() ? this.options.delayInLoading : this.options.delayIn;
			if (!delay)
			{
				this.show();
				return;
			}

			if (this.delayTimeoutType !== 'enter')
			{
				this.resetDelayTimer();
			}

			if (!this.delayTimeoutType && !this.isShown())
			{
				this.delayTimeoutType = 'enter';

				var self = this;
				this.delayTimeout = setTimeout(function()
				{
					self.delayTimeoutType = null;
					self.show();
				}, delay);
			}
		},

		leave: function()
		{
			if (this.clickTriggered)
			{
				// when click toggled, only an explicit other action closes this
				return;
			}

			var delay = this.options.delayOut;
			if (!delay)
			{
				this.hide();
				return;
			}

			if (this.delayTimeoutType !== 'leave')
			{
				this.resetDelayTimer();
			}

			if (!this.delayTimeoutType && this.isShown())
			{
				this.delayTimeoutType = 'leave';

				var self = this;
				this.delayTimeout = setTimeout(function()
				{
					self.delayTimeoutType = null;
					self.hide();
				}, delay);
			}
		},

		show: function()
		{
			var self = this;
			$(window)
				.off('focus.tooltip-' + this.$target.xfUniqueId())
				.on('focus.tooltip-' + this.$target.xfUniqueId(), function(e)
				{
					self.stopFocusBefore = Date.now() + 250;
				});

			XF.setRelativeZIndex(this.tooltip.getTooltip(), this.$target);

			if (this.options.onShow)
			{
				var cb = this.options.onShow;
				cb(this, this.tooltip);
			}

			this.tooltip.show();
		},

		cancelShow: function()
		{
			if (this.delayTimeoutType === 'enter')
			{
				this.resetDelayTimer();
			}
			else if (!this.tooltip.isShownFully())
			{
				this.hide();
			}
		},

		hide: function()
		{
			this.tooltip.hide();
			this.resetDelayTimer();
			this.clickTriggered = false;

			if (this.$covers)
			{
				this.$covers.remove();
				this.$covers = null;
			}

			$(document).off('click.tooltip-' + this.$target.xfUniqueId());

			if (this.options.onHide)
			{
				var cb = this.options.onHide;
				cb(this, this.tooltip);
			}
		},

		toggle: function()
		{
			this.isShown() ? this.hide() : this.show();
		},

		isShown: function()
		{
			return this.tooltip.isShown();
		},

		wasClickTriggered: function()
		{
			return this.clickTriggered;
		},

		resetDelayTimer: function()
		{
			if (this.delayTimeoutType)
			{
				clearTimeout(this.delayTimeout);
				this.delayTimeoutType = null;
			}
		},

		addMaintainElement: function($el)
		{
			if ($el.data('tooltip-maintain'))
			{
				return;
			}

			var triggers = this.options.trigger.split(' ');
			for (var i = 0; i < triggers.length; i++)
			{
				switch (triggers[i])
				{
					case 'hover':
						$el.on('mouseenter.tooltip', XF.proxy(this, 'enter'));
						$el.on('mouseleave.tooltip', XF.proxy(this, 'leave'));
						break;

					case 'focus':
						$el.on('focusin.tooltip', XF.proxy(this, 'enter'));
						$el.on('focusout.tooltip', XF.proxy(this, 'leave'));
						break;
				}
			}

			$el.data('tooltip-maintain', true);
		},

		removeMaintainElement: function($el)
		{
			$el.off('.tooltip');
			$el.data('tooltip-maintain', false);
		},

		onTooltipSetup: function($tooltip)
		{
			if (this.options.maintain)
			{
				this.addMaintainElement($tooltip);

				var self = this;

				$tooltip.on('menu:opened', function(e, $menu)
				{
					self.addMaintainElement($menu);
				});
				$tooltip.on('menu:closed', function(e, $menu)
				{
					self.removeMaintainElement($menu);
				});
			}
		}
	});
	XF.TooltipTrigger.cache = {};

	XF.TooltipOptions = {
		base: {
			// tooltip options
			baseClass: 'tooltip',
			extraClass: 'tooltip--basic',
			html: false,
			inViewport: true,
			placement: 'top',

			// trigger options
			clickHide: null,
			delayIn: 200,
			delayOut: 200,
			maintain: false,
			trigger: 'hover focus'
		},
		tooltip: [
			'baseClass',
			'extraClass',
			'html',
			'placement'
		],
		trigger: [
			'clickHide',
			'delayIn',
			'delayOut',
			'maintain',
			'trigger'
		],
		extract: function(keys, values)
		{
			var o = {};
			for (var i = 0; i < keys.length; i++)
			{
				o[keys[i]] = values[keys[i]];
			}

			return o;
		},
		extractTooltip: function(values) { return this.extract(this.tooltip, values); },
		extractTrigger: function(values) { return this.extract(this.trigger, values); }
	};

	// ################################## BASIC TOOLTIP ###########################################

	XF.Tooltip = XF.Element.newHandler({
		options: $.extend(true, {}, XF.TooltipOptions.base, {
			content: null
		}),

		trigger: null,
		tooltip: null,

		init: function()
		{
			var tooltipContent = this.getContent(),
				tooltipOptions = XF.TooltipOptions.extractTooltip(this.options),
				triggerOptions = XF.TooltipOptions.extractTrigger(this.options);

			this.tooltip = new XF.TooltipElement(tooltipContent, tooltipOptions);
			this.trigger = new XF.TooltipTrigger(this.$target, this.tooltip, triggerOptions);

			this.trigger.init();
		},

		getContent: function()
		{
			if (this.options.content)
			{
				return this.options.content;
			}
			else
			{
				var $target = this.$target,
					title = $target.attr('data-original-title') || $target.attr('title') || '';

				$target.attr('data-original-title', title).removeAttr('title');

				return title;
			}
		}
	});

	// ############################## ELEMENT TOOLTIPS ###############################

	XF.ElementTooltip = XF.extend(XF.Tooltip, {
		__backup: {
			'getContent': '_getContent',
			'init': '_init'
		},

		options: $.extend({}, XF.Tooltip.prototype.options, {
			element: null,
			showError: true,
			noTouch: true,
			shortcut: null
		}),

		$element: null,

		init: function()
		{
			if (this.options.shortcut)
			{
				this.setupShortcut(this.options.shortcut);
			}

			if (this.options.noTouch && XF.Feature.has('touchevents'))
			{
				return;
			}

			var element = this.options.element,
				showError = this.options.showError;

			if (!element)
			{
				if (showError)
				{
					console.error('No element specified for the element tooltip');
				}
				return;
			}

			var $element = XF.findRelativeIf(element, this.$target);
			if (!$element.length)
			{
				if (showError)
				{
					console.error('Element tooltip could not find ' + element);
				}

				return;
			}

			this.$element = $element;
			this.$target.removeAttr('title');
			this.options.html = true;
			this._init();
		},

		setupShortcut: function(shortcut)
		{
			if (shortcut == 'node-description')
			{
				if (!this.options.element)
				{
					this.options.element = '< .js-nodeMain | .js-nodeDescTooltip';
				}

				this.options.showError = false;
				this.options.maintain = true;
				this.options.placement = 'right';
				this.options.extraClass = 'tooltip--basic tooltip--description';
			}
		},

		getContent: function()
		{
			return this.$element.clone().contents();
		}
	});

	// ################################## PREVIEW TOOLTIP ###########################################

	XF.PreviewTooltip = XF.Element.newHandler({
		options: {
			delay: 600,
			previewUrl: null
		},

		trigger: null,
		tooltip: null,

		init: function()
		{
			if (!this.options.previewUrl)
			{
				console.error('No preview URL');
				return;
			}

			this.tooltip = new XF.TooltipElement(XF.proxy(this, 'getContent'), {
				extraClass: 'tooltip--preview',
				html: true,
				loadRequired: true
			});
			this.trigger = new XF.TooltipTrigger(this.$target, this.tooltip, {
				maintain: true,
				delayInLoading: this.options.delay,
				delayIn: this.options.delay
			});

			this.trigger.init();
		},

		getContent: function(onContent)
		{
			var self = this,
				options = {
					skipDefault: true,
					skipError: true,
					global: false
				};

			XF.ajax(
				'get', this.options.previewUrl, {},
				function(data) { self.loaded(data, onContent); },
				options
			);
		},

		loaded: function(data, onContent)
		{
			if (!data.html)
			{
				return;
			}

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				onContent($html);
			});
		}
	});

	// ################################## MEMBER TOOLTIP ###########################################

	XF.MemberTooltipCache = {};

	XF.MemberTooltip = XF.Element.newHandler({
		options: {
			delay: 600
		},

		trigger: null,
		tooltip: null,
		userId: null,

		init: function()
		{
			this.userId = this.$target.data('user-id');

			this.tooltip = new XF.TooltipElement(XF.proxy(this, 'getContent'), {
				extraClass: 'tooltip--member',
				html: true,
				loadRequired: true
			});

			this.trigger = new XF.TooltipTrigger(this.$target, this.tooltip, {
				maintain: true,
				delayInLoading: this.options.delay,
				delayIn: this.options.delay,
				trigger: 'hover focus click',
				onShow: XF.proxy(this, 'onShow'),
				onHide: XF.proxy(this, 'onHide')
			});

			this.trigger.init();
		},

		getContent: function(onContent)
		{
			var userId = this.userId,
				existing = XF.MemberTooltipCache[userId];
			if (existing)
			{
				var $content = $($.parseHTML(existing));
				onContent($content);
				return;
			}

			var self = this,
				options = {
					skipDefault: true,
					skipError: true,
					global: false
				};

			if (this.trigger.wasClickTriggered())
			{
				options.global = true;
			}

			XF.ajax(
				'get', this.$target.attr('href'), { tooltip: true },
				function(data) { self.loaded(data, onContent); },
				options
			);
		},

		loaded: function(data, onContent)
		{
			if (!data.html)
			{
				return;
			}

			var userId = this.userId;

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				XF.MemberTooltipCache[userId] = data.html.content;

				onContent($html);
			});
		},

		onShow: function()
		{
			var activeTooltip = XF.MemberTooltip.activeTooltip;
			if (activeTooltip && activeTooltip !== this)
			{
				activeTooltip.hide();
			}

			XF.MemberTooltip.activeTooltip = this;
		},

		onHide: function()
		{
			// it's possible for another show event to trigger so don't empty this if it isn't us
			if (XF.MemberTooltip.activeTooltip === this)
			{
				XF.MemberTooltip.activeTooltip = null;
			}
		},

		show: function()
		{
			this.trigger.show();
		},

		hide: function()
		{
			this.trigger.hide();
		}
	});
	XF.MemberTooltip.activeTooltip = null;

	// ################################## SHARE TOOLTIP ###########################################

	XF.ShareTooltip = XF.Element.newHandler({
		options: {
			delay: 300
		},

		trigger: null,
		tooltip: null,
		url: null,

		init: function()
		{
			this.url = this.$target.attr('href');

			this.tooltip = new XF.TooltipElement(XF.proxy(this, 'getContent'), {
				extraClass: 'tooltip--share',
				html: true,
				loadRequired: true
			});

			this.trigger = new XF.TooltipTrigger(this.$target, this.tooltip, {
				maintain: true,
				delayInLoading: this.options.delay,
				delayIn: this.options.delay,
				trigger: 'hover focus click',
				onShow: XF.proxy(this, 'onShow'),
				onHide: XF.proxy(this, 'onHide')
			});

			this.trigger.init();
		},

		getContent: function(onContent)
		{
			var self = this,
				options = {
					skipDefault: true,
					skipError: true,
					global: false
				};

			if (this.trigger.wasClickTriggered())
			{
				options.global = true;
			}

			XF.ajax(
				'get', this.$target.data('href'), {},
				function(data) { self.loaded(data, onContent); },
				options
			);
		},

		loaded: function(data, onContent)
		{
			if (!data.html)
			{
				return;
			}

			var url = this.url;

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				onContent($html);
			});
		},

		onShow: function()
		{
			var activeTooltip = XF.ShareTooltip.activeTooltip;
			if (activeTooltip && activeTooltip !== this)
			{
				activeTooltip.hide();
			}

			XF.ShareTooltip.activeTooltip = this;
		},

		onHide: function()
		{
			// it's possible for another show event to trigger so don't empty this if it isn't us
			if (XF.ShareTooltip.activeTooltip === this)
			{
				XF.ShareTooltip.activeTooltip = null;
			}
		},

		show: function()
		{
			this.trigger.show();
		},

		hide: function()
		{
			this.trigger.hide();
		}
	});
	XF.ShareTooltip.activeTooltip = null;

	XF.Element.register('element-tooltip', 'XF.ElementTooltip');
	XF.Element.register('member-tooltip', 'XF.MemberTooltip');
	XF.Element.register('preview-tooltip', 'XF.PreviewTooltip');
	XF.Element.register('share-tooltip', 'XF.ShareTooltip');
	XF.Element.register('tooltip', 'XF.Tooltip');

}
(jQuery, window, document);