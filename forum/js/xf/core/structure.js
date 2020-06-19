/** @param {jQuery} $ jQuery Object */
!function($, window, document)
{
	"use strict";

	// ################################## INSERTER HANDLER ###########################################

	XF._baseInserterOptions = {
		after: null,
		append: null,
		before: null,
		prepend: null,
		replace: null,
		removeOldSelector: true,
		animateReplace: true,
		scrollTarget: null,
		href: null
	};

	XF.InserterClick = XF.Event.newHandler({
		eventNameSpace: 'XFInserterClick',
		options: $.extend(true, {}, XF._baseInserterOptions),

		inserter: null,

		init: function()
		{
			this.inserter = new XF.Inserter(this.$target, this.options);
		},

		click: function(e)
		{
			this.inserter.onEvent(e);
		}
	});

	XF.InserterFocus = XF.Element.newHandler({
		options: $.extend(true, {}, XF._baseInserterOptions),

		inserter: null,

		init: function()
		{
			this.inserter = new XF.Inserter(this.$target, this.options);
			this.$target.one('focus', XF.proxy(this.inserter, 'onEvent'));
		}
	});

	XF.Inserter = XF.create({
		options: $.extend(true, {}, XF._baseInserterOptions),

		$target: null,
		href: null,
		loading: false,

		__construct: function($target, options)
		{
			this.$target = $target;
			this.options = $.extend(true, {}, this.options, options);

			var href = this.options.href || this.$target.data('inserter-href') || this.$target.attr('href');
			if (!href)
			{
				console.error('Target must have href');
				return;
			}

			this.href = href;
		},

		onEvent: function(e)
		{
			e.preventDefault();

			if (this.loading)
			{
				return;
			}

			this.loading = true;

			var $replace = $(this.options.replace);
			if ($replace.length)
			{
				$replace.addClassTransitioned('is-active');
			}

			var self = this;

			XF.ajax('get', this.href, {}, XF.proxy(this, 'onLoad'))
				.always(function() { self.loading = false; });
		},

		onLoad: function(data)
		{
			if (!data.html)
			{
				return;
			}

			var self = this,
				options = this.options,
				scrollTarget = options.scrollTarget,
				$scrollTarget;

			if (scrollTarget)
			{
				$scrollTarget = XF.findRelativeIf(scrollTarget, this.$target);
			}

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				self._applyChange($html, options.after, XF.proxy(self, '_applyAfter'));
				self._applyChange($html, options.append, XF.proxy(self, '_applyAppend'));
				self._applyChange($html, options.before, XF.proxy(self, '_applyBefore'));
				self._applyChange($html, options.prepend, XF.proxy(self, '_applyPrepend'));
				self._applyChange($html, options.replace, XF.proxy(self, '_applyReplace'));

				onComplete(true);

				return false; // already run the necessary init on what was inserted
			});

			XF.layoutChange();

			if ($scrollTarget && $scrollTarget.length)
			{
				$scrollTarget[0].scrollIntoView(true);
			}
		},

		_applyChange: function($html, targets, applyFn)
		{
			if (!targets || !targets.length)
			{
				return;
			}

			var selectors = targets.split(','),
				selector,
				selectorOld, selectorNew,
				$old, $new;

			for (var i = 0; i < selectors.length; i++)
			{
				selector = selectors[i].split(' with ');
				selectorOld = $.trim(selector[0]);
				selectorNew = selector[1] ? $.trim(selector[1]) : selectorOld;

				if (selectorOld.length && selectorNew.length)
				{
					$old = $(selectorOld).first();

					if ($html.is(selectorNew))
					{
						$new = $html;
					}
					else
					{
						$new = $html.find(selectorNew).first();
					}

					applyFn(selectorOld, $old, $new);
				}
			}
		},

		_applyAfter: function(selectorOld, $old, $new)
		{
			if ($old.length && $new.length)
			{
				$new.insertAfter($old);
				XF.activate($new);

				this._removeOldSelector(selectorOld, $old);
			}
		},

		_applyAppend: function(selectorOld, $old, $new)
		{
			if ($old.length && $new.length)
			{
				var $children = $new.children();
				$children.appendTo($old);
				XF.activate($children);
			}
		},

		_applyBefore: function(selectorOld, $old, $new)
		{
			if ($old.length && $new.length)
			{
				$new.insertBefore($old);
				XF.activate($new);

				this._removeOldSelector(selectorOld, $old);
			}
		},

		_applyPrepend: function(selectorOld, $old, $new)
		{
			if ($old.length && $new.length)
			{
				var $children = $new.children();
				$children.prependTo($old);
				XF.activate($children);
			}
		},

		_applyReplace: function(selectorOld, $old, $new)
		{
			if ($old.length)
			{
				var animate = this.options.animateReplace;
				if (XF.isIOS())
				{
					// workaround for bug #137959 - disable the animation to avoid overlay becoming unscrollable
					animate = false;
				}

				if ($new.length)
				{
					if (animate)
					{
						$new.hide();
					}
					$new.insertAfter($old);
				}

				if (animate)
				{
					$old.xfFadeUp(null, function()
					{
						$old.remove();

						if ($new.length)
						{
							XF.activate($new);
						}

						$new.xfFadeDown(null, XF.layoutChange);
					})
				}
				else
				{
					$old.remove();

					if ($new.length)
					{
						XF.activate($new);
					}
				}
			}
		},

		_removeOldSelector: function(selector, $old)
		{
			if (!this.options.removeOldSelector)
			{
				return;
			}

			var match;
			if (match = selector.match(/^\.([a-z0-9_-]+)/i))
			{
				$old.removeClass(match[1]);
			}
		}
	});

	// ############################### MENU CLICK HANDLER ##############################################

	XF.MenuClick = XF.Event.newHandler({
		eventNameSpace: 'XFMenuClick',
		options: {
			menu: null,
			targetOpenClass: 'is-menuOpen',
			openClass: 'is-active',
			completeClass: 'is-complete',
			zIndexRef: null,
			menuPosRef: null, // menu will be positioned with relation to this
			arrowPosRef: null, // arrow will be positioned with relation to this
			directionThreshold: 0.6 // if menu trigger is more than this amount of the page to the right,
									// align with right edge instead of left
		},

		$menu: null,

		$menuPosRef: null,
		menuRef: null,
		$arrowPosRef: null,
		arrowRef: null,

		pauseRepositioning: false,
		scrollFunction: null,
		isPotentiallyFixed: false,
		menuIsUp: false,

		menuWidth: 0,
		menuHeight: 0,

		init: function()
		{
			if (this.options.menu)
			{
				this.$menu = XF.findRelativeIf(this.options.menu, this.$target);
			}
			if (!this.$menu || !this.$menu.length)
			{
				this.$menu = this.$target.nextAll('[data-menu]').first();
			}

			if (!this.$menu.length)
			{
				console.error('No menu found for %o', this.$target[0]);
				return;
			}

			this.$menuPosRef = this.$target;
			this.$arrowPosRef = this.$target;

			if (this.options.menuPosRef)
			{
				var $menuPosRef = XF.findRelativeIf(this.options.menuPosRef, this.$target);
				if ($menuPosRef.length)
				{
					this.$menuPosRef = $menuPosRef;

					if (this.options.arrowPosRef)
					{
						// only check for arrowPosRef if we have a menuPosRef,
						// and only allow it if it's a child of menuPosRef (or the same as menuPosRef)

						var $arrowPosRef = XF.findRelativeIf(this.options.arrowPosRef, this.$target);
						if ($arrowPosRef.closest($menuPosRef).length)
						{
							this.$arrowPosRef = $arrowPosRef;
						}
					}
				}
			}

			this.$target.attr('aria-controls', this.$menu.xfUniqueId());

			if (!this.$menu.find('.menu-arrow').length)
			{
				this.$menu.prepend('<span class="menu-arrow" />');
			}

			var self = this;

			this.$menu
				.data('menu-trigger', this)
				.on('click', '[data-menu-closer]', function() { self.close(); })
				.on({
					'menu:open': function () {
						self.open(XF.Feature.has('touchevents'));
					},
					'menu:close': function() { self.close(); },
					'menu:reposition': function() { if (self.isOpen()) { self.reposition(); } },
					'keydown': XF.proxy(this, 'keyboardEvent')
				});


			if (XF.isIOS() && this.$target.hasFixableParent())
			{
				// iOS has major issues with inputs within fixed elements
				var blurTimeout,
					focusTimer;

				this.$menu.on({
					touchstart: function(e)
					{
						clearTimeout(blurTimeout);
						clearTimeout(focusTimer);

						self.enableIOSInputFix($(e.currentTarget));

						// if this doesn't lead to a focus event, we need to disable it
						focusTimer = setTimeout(function()
						{
							self.resetIOSInputFix();
						}, 500);
					},
					focus: function(e)
					{
						clearTimeout(blurTimeout);
						clearTimeout(focusTimer);
						self.enableIOSInputFixFocus($(e.currentTarget));
					},
					blur: function(e)
					{
						clearTimeout(blurTimeout);
						blurTimeout = setTimeout(function()
						{
							if (self.isOpen())
							{
								self.resetIOSInputFix();
							}
						}, 200);
					}
				}, XF.getKeyboardInputs());
			}

			var $tooltip = this.$menu.closest('.tooltip');
			if ($tooltip.length)
			{
				$tooltip.on('tooltip:hidden', XF.proxy(this, 'close'));
			}

			var builder = this.$menu.data('menu-builder');
			if (builder)
			{
				if (XF.MenuBuilder[builder])
				{
					XF.MenuBuilder[builder](this.$menu, this.$target, this);
				}
				else
				{
					console.error('No menu builder ' + builder + ' found');
				}
			}
		},

		click: function(e)
		{
			if ((e.ctrlKey || e.shiftKey || e.altKey) && this.$target.attr('href'))
			{
				// don't open the menu as the target will be opened elsewhere
				return;
			}

			var touchTriggered = XF.isEventTouchTriggered(e),
				preventDefault = true;

			if (!touchTriggered && this.isOpen())
			{
				// allow second clicks to the menu trigger follow any link it has
				preventDefault = false;
			}

			if (preventDefault)
			{
				e.preventDefault();
			}

			this.toggle(touchTriggered, XF.NavDeviceWatcher.isKeyboardNav());
		},

		isOpen: function()
		{
			return this.$target.hasClass(this.options.targetOpenClass);
		},

		toggle: function(touchTriggered)
		{
			if (this.isOpen())
			{
				this.close();
			}
			else
			{
				this.open(touchTriggered);
			}
		},

		open: function(touchTriggered)
		{
			var $menu = this.$menu,
				$target = this.$target,
				$menuPosRef = this.$menuPosRef,
				minZIndex = 0;

			if (this.isOpen() || $menu.hasClass('is-disabled'))
			{
				return;
			}

			this.pauseRepositioning = false;

			// ensure this is always at the end, should help sort potential ordering issues
			$menu.appendTo('body');

			this.updateMenuDimensions();
			this.updatePositionReferences();

			var isPotentiallyFixed = this.$target.hasFixableParent(),
				scrolling = null,
				self = this;

			if (isPotentiallyFixed)
			{
				this.scrollFunction = function()
				{
					if ($target.is(':hidden'))
					{
						self.close();
					}
					else
					{
						self.repositionFixed(true);
					}
				};
				$menu.addClass('menu--potentialFixed');

				$(window).onPassive('scroll', this.scrollFunction);
			}

			if (this.options.zIndexRef)
			{
				var $ref = XF.findRelativeIf(this.options.zIndexRef, $target);
				if ($ref.length)
				{
					minZIndex = XF.getElEffectiveZIndex($ref);
				}
			}

			XF.setRelativeZIndex($menu, $target, 0, minZIndex);

			XF.MenuWatcher.onOpen($menu, touchTriggered);

			this.reposition();

			$target.attr('aria-expanded', 'true').addClassTransitioned(this.options.targetOpenClass);
			$menu.attr('aria-hidden', 'false').addClassTransitioned(this.options.openClass, XF.proxy(function() { $menu.addClassTransitioned(this.options.completeClass); }, this));
			$menuPosRef.addClassTransitioned(this.options.targetOpenClass);

			this.$target.trigger('menu:opened', [$menu]);
			$menu.trigger('menu:opened');

			// focus menu content
			if (!XF.isIOS() || !this.isPotentiallyFixed)
			{
				// we can't do autofocusing in iOS when we might be in fixed menu as this won't trigger our full workaround

				$menu.on('menu:complete', function ()
				{
					XF.autoFocusWithin($menu, '[autofocus], [data-menu-autofocus]');
				});
			}

			if ($menu.data('href'))
			{
				if ($menu.data('menu-loading'))
				{
					return;
				}
				$menu.data('menu-loading', true);

				var cacheResponse = $menu.data('nocache') ? false : true;

				XF.ajax('get', $menu.data('href'), {}, function(data)
				{
					if (cacheResponse)
					{
						$menu.data('href', false);
					}

					if (data.html)
					{
						XF.setupHtmlInsert(data.html, function($html, container, onComplete)
						{
							var loadTarget = $menu.data('load-target');
							if (loadTarget)
							{
								$menu.find(loadTarget).first().empty().html($html);
							}
							else
							{
								$menu.html($html);
							}

							self.$target.trigger('menu:loaded', [$html, $menu]);

							self.updateMenuDimensions();

							onComplete();
							setTimeout(XF.proxy(self, 'reposition'), 0);

							self.$target.trigger('menu:complete', [$menu]);
							$menu.trigger('menu:complete');
						});
					}
				}, { cache: cacheResponse }).always(function()
				{
					$menu.data('menu-loading', false);
				})
			}
			else
			{
				this.$target.trigger('menu:complete', [$menu]);
				$menu.trigger('menu:complete');
			}
		},

		reposition: function(force, forceAbsolute)
		{
			if (this.pauseRepositioning && !force)
			{
				return;
			}

			if (this.$menu.data('ios-scroll-timeout') && !force)
			{
				return;
			}

			this.updatePositionReferences();

			this.$menu.css({
				visibility: 'hidden',
				display: 'block',
				position: '',
				top: '',
				bottom: '',
				left: '',
				right: ''
			});

			var viewport = $(window).viewport(),

			menuCss = {};
			menuCss = this.getHorizontalPosition(viewport, menuCss);
			menuCss = this.getVerticalPosition(viewport, menuCss, forceAbsolute);
			menuCss.display = '';
			menuCss.visibility = '';

			this.$menu.css(menuCss);
		},

		repositionFixed: function(isScrolling)
		{
			if (this.pauseRepositioning)
			{
				return;
			}

			var $target = this.$target,
				$menu = this.$menu;

			if (isScrolling && XF.isIOS())
			{
				// iOS reports fixed/sticky offsets incorrectly while scrolling so we have no hope of repositioning this
				// correctly until those update. Hopefully the initial positioning will work; it won't when sticky
				// positioning switches between relative and fixed.
				// Relevant bug report: http://www.openradar.me/22872226
				var iOSScrollTimeout = $menu.data('ios-scroll-timeout'),
					self = this;

				clearTimeout(iOSScrollTimeout);
				iOSScrollTimeout = setTimeout(function()
				{
					$menu.removeData('ios-scroll-timeout');
					self.reposition();
				}, 300);
				$menu.data('ios-scroll-timeout', iOSScrollTimeout);

				return;
			}

			this.updatePositionReferences();

			var menuCurrent = this.$target.data('menu-h'),
				resetTimer = $menu.data('menu-reset-timer');

			if (!menuCurrent || this.menuRef.left != menuCurrent[0] || this.menuRef.width != menuCurrent[1])
			{
				this.reposition();
				return;
			}

			var viewport = $(window).viewport(),
				newMenuPosition = this.$target.hasFixedParent() ? 'fixed' : 'absolute',
				menuCss = {
					top: parseInt($menu.css('top'), 10)
				};

			this.menuIsUp = this.$menu.hasClass('menu--up');

			if (resetTimer)
			{
				clearTimeout(resetTimer);
			}

			if (newMenuPosition == 'fixed' && newMenuPosition != $menu.css('position'))
			{
				menuCss = { 'transition-property': 'none' };
				menuCss = this.getVerticalFixedPosition(viewport, menuCss);
			}
			else if (newMenuPosition == 'absolute')
			{
				menuCss = { 'transition-property': 'none' };
				menuCss = this.getVerticalAbsolutePosition(viewport, menuCss);
			}

			$menu.css(menuCss).toggleClass('menu--up', this.menuIsUp);

			resetTimer = setTimeout(function()
			{
				$menu.css('transition-property', '');
			}, 250);

			$menu.data('menu-reset-timer', resetTimer);
		},

		getHorizontalPosition: function(viewport, menuCss)
		{
			var menuIsRight = false,
				deltaLeft = 0;

			if (this.menuWidth > viewport.width)
			{
				// align menu to left viewport edge if menu is wider than viewport

				deltaLeft = this.menuRef.left - viewport.left;
			}
			else if (this.menuRef.left + this.menuRef.width / 2 > viewport.width * this.options.directionThreshold)
			{
				// align menu to right of this.menuRef if this.menuRef center is viewportwidth/directionThreshold of the page width

				deltaLeft = 0 - this.menuWidth + this.menuRef.width;
				menuIsRight = true;
			}
			else if (this.menuRef.width > this.menuWidth)
			{
				// align menu with middle of the ref
				deltaLeft = Math.floor((this.menuRef.width - this.menuWidth) / 2);
			}

			// corrections to constrain to viewport, as much as possible, with 5px to spare
			deltaLeft = Math.min(deltaLeft, viewport.right - this.menuWidth - this.menuRef.left - 5);
			deltaLeft = Math.max(deltaLeft, viewport.left - this.menuRef.left + 5);

			// final calculation for menu left position
			menuCss.left = this.menuRef.left + deltaLeft;

			this.$target.data('menu-h', [this.menuRef.left, this.menuRef.width, deltaLeft]);

			this.$menu
				.toggleClass('menu--left', !menuIsRight)
				.toggleClass('menu--right', menuIsRight);

			// don't allow the arrow to be moved outside of the menu
			var arrowOffset = Math.min(
				this.arrowRef.left - this.menuRef.left + this.arrowRef.width / 2 - deltaLeft,
				this.menuWidth - 20
			);

			this.$menu.find('.menu-arrow').css({
				top: '',
				left: arrowOffset
			});

			return menuCss;
		},

		getVerticalPosition: function(viewport, menuCss, forceAbsolute)
		{
			this.menuIsUp = false;

			if (!forceAbsolute && this.$target.hasFixedParent())
			{
				menuCss = this.getVerticalFixedPosition(viewport, menuCss);
			}
			else
			{
				menuCss = this.getVerticalAbsolutePosition(viewport, menuCss);
			}

			this.$menu.toggleClass('menu--up', this.menuIsUp);

			return menuCss;
		},

		getVerticalFixedPosition: function(viewport, menuCss)
		{
			menuCss.top = Math.max(0, Math.round(this.menuRef.bottom) - viewport.top) - this.getTopShift();
			menuCss.position = 'fixed';

			if (menuCss.top + this.menuHeight + viewport.top > viewport.bottom && this.menuRef.top - this.menuHeight > viewport.top) // fixed menu would overlap viewport bottom
			{
				menuCss.top = '';
				menuCss.bottom = viewport.bottom - this.menuRef.top + 5;

				this.menuIsUp = true;
			}
			else
			{
				this.menuIsUp = false;
			}

			return menuCss;
		},

		getVerticalAbsolutePosition: function(viewport, menuCss)
		{
			menuCss.top = this.menuRef.bottom - this.getTopShift();
			menuCss.position = ''; // this is in the CSS

			if (menuCss.top + this.menuHeight > viewport.bottom && this.menuRef.top - this.menuHeight > viewport.top) // absolute menu would overlap viewport bottom
			{
				menuCss.top = '';
				menuCss.bottom = viewport.height - this.menuRef.top + 5;

				this.menuIsUp = true;
			}
			else
			{
				this.menuIsUp = false;
			}

			return menuCss;
		},

		getTopShift: function()
		{
			return this.$menu.hasClass('menu--structural') ? parseInt(XF.config.borderSizeFeature, 10) : 0;
		},

		updateMenuDimensions: function()
		{
			this.menuWidth = this.$menu.outerWidth(true);
			this.menuHeight = this.$menu.outerHeight(true);

			return {
				menuWidth: this.menuWidth,
				menuHeight: this.menuHeight
			};
		},

		updatePositionReferences: function()
		{
			this.menuRef = this.$menuPosRef.dimensions(true);

			if (this.$arrowPosRef == this.$menuPosRef)
			{
				this.arrowRef = this.menuRef;
			}
			else
			{
				this.arrowRef = this.$arrowPosRef.dimensions(true);
			}

			return {
				menuRef: this.menuRef,
				arrowRef: this.arrowRef
			};
		},

		close: function()
		{
			if (!this.isOpen())
			{
				return;
			}

			var $menu = this.$menu;

			this.$target.attr('aria-expanded', 'false').removeClassTransitioned(this.options.targetOpenClass);
			$menu.attr('aria-hidden', 'true').removeClass(this.options.completeClass).removeClassTransitioned(this.options.openClass);
			this.$menuPosRef.removeClassTransitioned(this.options.targetOpenClass);

			$(window).offPassive('scroll', this.scrollFunction);

			XF.MenuWatcher.onClose($menu);

			this.$target.trigger('menu:closed', [$menu]);
			$menu.trigger('menu:closed');

			this.resetIOSInputFix();
		},

		enableIOSInputFix: function($input)
		{
			if (!XF.isIOS())
			{
				return;
			}

			this.reposition(false, true);
			this.pauseRepositioning = true;
		},

		enableIOSInputFixFocus: function($input)
		{
			if (!this.$target.hasFixedParent())
			{
				return;
			}

			var scrollPos = window.scrollY,
				$menu = this.$menu,
				$body = $('body');

			if ($body.css('position') == 'relative')
			{
				// already enabled, don't do anything again
				return;
			}

			// This will normally be true, but if we get here without the initial touch setup, this won't have been
			// applied. The menu positioning may still be fixed based as well, so ensure that's absolute.
			this.pauseRepositioning = true;

			$menu.css({
				position: 'absolute',
				top: scrollPos,
				'transition-property': 'none'
			});
			setTimeout(function()
			{
				$menu.css('transition-property', '');
			}, 200);

			$('html').css('overflow', 'hidden');
			$body.css({
				position: 'relative',
				top: -scrollPos,
				'margin-bottom': -scrollPos
			});

			// put the input 100px from the top of the screen
			$input[0].scrollIntoView();
			window.scrollBy(0, -100);
		},

		resetIOSInputFix: function()
		{
			if (!XF.isIOS())
			{
				return;
			}

			var $body = $('body');

			if ($body.css('position') == 'relative')
			{
				var scrollTo = (parseInt($body.css('top'), 10) * -1 || 0) + window.scrollY;

				$body.css({
					position: '',
					top: '',
					'margin-bottom': ''
				});
				$('html').css('overflow', '');

				window.scrollTo(0, scrollTo);
			}

			this.pauseRepositioning = false;
			if (this.isOpen())
			{
				this.reposition();
			}
		},

		/**
		 * Allow up and down arrow keys to navigate between links in the menu
		 * @param e
		 * @returns {boolean}
		 */
		keyboardEvent: function(e)
		{
			if (e.key == 'ArrowUp' || e.key == 'ArrowDown')
			{
				if (XF.Keyboard.isShortcutAllowed(document.activeElement))
				{
					if ($(document.activeElement).closest('.menu').get(0) == this.$menu.get(0))
					{
						var $activeElement = $(document.activeElement),
							$links = $activeElement.closest('.menu').find('a'),
							newIndex = $links.index($activeElement) + (e.key == 'ArrowUp' ? -1 : 1);

						if (newIndex < 0)
						{
							newIndex = $links.length - 1;
						}
						else if (newIndex >= $links.length)
						{
							newIndex = 0;
						}

						$($links.get(newIndex)).focus();
						e.preventDefault();
						return false;
					}
				}
			}
		}

	});

	XF.MenuWatcher = (function()
	{
		var $opened = $([]),
			$outsideClicker = null,
			closing = false,
			docClickPrevented = false;

		var preventDocClick = function()
		{
			docClickPrevented = true;
		};

		var allowDocClick = function()
		{
			docClickPrevented = false;
		};

		var docClick = function(e)
		{
			if (!docClickPrevented)
			{
				closeUnrelated(e.target);
			}
		};

		var windowResize = function(e)
		{
			$opened.trigger('menu:reposition');
		};

		var onOpen = function($menu, touchTriggered)
		{
			if (!$outsideClicker)
			{
				$outsideClicker = $('<div class="menuOutsideClicker" />')
					.on('click', docClick)
					.insertBefore($menu);
				// all menus should eventually appear after this one
			}

			if (!$opened.length)
			{
				$(document).on('click', docClick);
				$(window).onPassive('resize', windowResize);

				if (touchTriggered)
				{
					$outsideClicker.addClass('is-active');
				}
			}

			$opened = $opened.add($menu);
		};

		var onClose = function($menu)
		{
			$opened = $opened.not($menu);

			if (!$opened.length)
			{
				$(document).off('click', docClick);
				$(window).offPassive('resize', windowResize);

				if ($outsideClicker)
				{
					$outsideClicker.removeClass('is-active');
				}
			}

			closeUnrelated($menu);
		};

		var closeUnrelated = function(el)
		{
			if (closing)
			{
				return;
			}

			closing = true;

			var $el = $(el);

			$opened.each(function()
			{
				var trigger = $(this).data('menu-trigger'),
					$target = trigger ? trigger.$target : null;

				if (!$el.closest(this).length && (!$target || !$el.closest($target).length))
				{
					if (trigger)
					{
						trigger.close();
					}
				}
			});

			closing = false;
		};

		var closeAll = function()
		{
			closing = true;
			$opened.trigger('menu:close');
			closing = false;
		};

		return {
			onOpen: onOpen,
			onClose: onClose,
			closeAll: closeAll,
			closeUnrelated: closeUnrelated,
			preventDocClick: preventDocClick,
			allowDocClick: allowDocClick
		};
	})();

	XF.MenuBuilder = {
		actionBar: function($menu, $target, handler)
		{
			var $menuTarget = $menu.find('.js-menuBuilderTarget');
			$target.closest('.actionBar-set').find('.actionBar-action--menuItem').each(function()
			{
				var $item = $(this).clone();
				$item.removeClass().addClass('menu-linkRow');

				$menuTarget.append($item);
			});

			XF.activate($menuTarget);
		},

		dataList: function($menu, $target, handler)
		{
			var $menuTarget = $menu.find('.js-menuBuilderTarget');
			$target.closest('.dataList-row').find('.dataList-cell--responsiveMenuItem').each(function()
			{
				$(this).clone().children().each(function()
				{
					var $item = $(this);
					if ($item.is('a'))
					{
						$item.removeClass().addClass('menu-linkRow');
					}
					else
					{
						$item.wrap('<div class="menu-row"></div>');
					}
					$menuTarget.append($item);
				});
			});

			XF.activate($menuTarget);
		}
	};

	XF.MenuProxy = XF.Event.newHandler({
		eventNameSpace: 'XFMenuProxy',
		options: {
			trigger: null
		},

		$trigger: null,

		init: function()
		{
			this.$trigger = XF.findRelativeIf(this.options.trigger, this.$target);
			if (!this.$trigger || !this.$trigger.length)
			{
				throw new Error('Specified menu trigger not found');
			}
		},

		click: function(e)
		{
			setTimeout(XF.proxy(function()
			{
				this.$trigger.trigger('click', [e]);
			}, this), 0);
		}
	});

	// ############################### OFF CANVAS CLICK HANDLER ##############################################

	XF.OffCanvasClick = XF.Event.newHandler({
		eventNameSpace: 'XFOffCanvasClick',
		options: {
			menu: null,
			openClass: 'is-active'
		},

		$menu: null,

		init: function()
		{
			if (this.options.menu)
			{
				this.$menu = XF.findRelativeIf(this.options.menu, this.$target);
			}
			if (!this.$menu || !this.$menu.length)
			{
				this.$menu = this.$target.nextAll('[data-menu]').first();
			}

			if (!this.$menu.length)
			{
				console.error('No menu found for %o', this.$target[0]);
				return;
			}

			this.$menu.on('click', '[data-menu-close]', XF.proxy(this, 'closeTrigger'))
				.on('off-canvas:close', XF.proxy(this, 'closeTrigger'))
				.on('off-canvas:open', XF.proxy(this, 'openTrigger'));

			var builder = this.$menu.data('ocm-builder');
			if (builder)
			{
				if (XF.OffCanvasBuilder[builder])
				{
					XF.OffCanvasBuilder[builder](this.$menu, this);
				}
				else
				{
					console.error('No off canvas builder ' + builder + ' found');
				}
			}
		},

		click: function(e)
		{
			e.preventDefault();

			this.toggle();
		},

		isOpen: function()
		{
			return this.$menu.hasClass(this.options.openClass);
		},

		toggle: function()
		{
			if (this.isOpen())
			{
				this.close();
			}
			else
			{
				this.open();
			}
		},

		openTrigger: function(e)
		{
			e.preventDefault();

			this.open();
		},

		open: function()
		{
			if (this.isOpen())
			{
				return;
			}

			var $menu = this.$menu;

			this.addOcmClasses($menu);
			$menu.attr('aria-hidden', 'false')
				.trigger("off-canvas:opening");

			$menu.addClassTransitioned(this.options.openClass, function()
			{
				$menu.trigger("off-canvas:opened");
			});
			XF.Modal.open();
		},

		addOcmClasses: function($target)
		{
			var ocmClass = $target.attr('data-ocm-class');
			if (ocmClass)
			{
				$target.addClass(ocmClass);
			}

			$target.find('[data-ocm-class]').each(function()
			{
				var $this = $(this);
				$this.addClass($this.attr('data-ocm-class'));
			});
		},

		removeOcmClasses: function($target)
		{
			var ocmClass = $target.attr('data-ocm-class');
			if (ocmClass)
			{
				$target.removeClass(ocmClass);
			}

			$target.find('[data-ocm-class]').each(function()
			{
				var $this = $(this);
				$this.removeClass($this.attr('data-ocm-class'));
			});
		},

		closeTrigger: function(e, options)
		{
			e.preventDefault();

			var instant = (options && options.instant);
			this.close(instant);
		},

		close: function(instant)
		{
			if (!this.isOpen())
			{
				return;
			}

			var $menu = this.$menu,
				self = this;

			$menu.attr('aria-hidden', 'true')
				.trigger("off-canvas:closing");

			$menu.removeClassTransitioned(this.options.openClass, function()
			{
				$menu.trigger("off-canvas:closed");
				self.removeOcmClasses($menu);
			}, instant);
			XF.Modal.close();
		}
	});
	XF.OffCanvasBuilder = {
		navigation: function($menu, handler)
		{
			$menu.appendTo('body');

			var $entries = $('<ul class="offCanvasMenu-list" />');

			$('.js-offCanvasNavSource .p-navEl').each(function()
			{
				var $this = $(this),
					isSelected = $this.hasClass('is-selected'),
					$link = $this.find('.p-navEl-link'),
					$menu = $this.find('[data-menu]');

				if ($this.data('has-children') && !$menu.length)
				{
					// menu has been moved, find it
					var clickHandlers = $this.find('[data-xf-click~="menu"]').first().data('xf-click-handlers');
					if (clickHandlers && clickHandlers.menu)
					{
						$menu = clickHandlers.menu.$menu;
					}
				}

				if (!$link.length)
				{
					return;
				}

				var $linkContainer = $('<div class="offCanvasMenu-linkHolder" />'),
					$mainLink = $link.clone(),
					$entry = $('<li />');

				$mainLink.removeClass('p-navEl-link p-navEl-link--menuTrigger p-navEl-link--splitMenu')
					.addClass('offCanvasMenu-link');

				$linkContainer.html($mainLink);

				if (isSelected)
				{
					$entry.addClass('is-selected');
					$linkContainer.addClass('is-selected');
				}

				$entry.html($linkContainer);

				if ($menu.length)
				{
					var $splitToggle = $(
						'<a class="offCanvasMenu-link offCanvasMenu-link--splitToggle"'
						+ ' data-xf-click="toggle" data-target="< :up :next" role="button" tabindex="0" />'
					);
					if (isSelected)
					{
						$splitToggle.addClass('is-active');
					}
					$linkContainer.append($splitToggle);

					var $childLinks = $('<ul class="offCanvasMenu-subList" />');
					if (isSelected)
					{
						$childLinks.addClass('is-active');
					}
					$menu.find('.menu-linkRow').each(function()
					{
						var $childLink = $(this),
							$childEl = $('<li />');

						$childEl.html(
							$childLink.clone().removeClass('menu-linkRow').addClass('offCanvasMenu-link')
						);
						$childLinks.append($childEl);
					});

					$entry.append($childLinks);
				}

				$entries.append($entry);
			});

			var $addTarget = $menu.find('.js-offCanvasNavTarget').append($entries);
			XF.activate($addTarget);
		},

		sideNav: function($menu, handler)
		{
			var $content = $menu.find('.offCanvasMenu-content'),
				$target = handler.$target;

			if (!$content.find('[data-menu-close]').length)
			{
				var $header = $content.find('.block-header:first');
				if (!$header.length)
				{
					$header = $('<div class="offCanvasMenu-header offCanvasMenu-header--separated offCanvasMenu-shown" />');
					$header.html($target.html());
					$content.prepend($header);
				}

				$header.append('<a class="offCanvasMenu-closer" data-menu-close="true" role="button" tabindex="0" />');
			}

			$(window).onPassive('resize', function()
			{
				if (!$target.is(':visible'))
				{
					$menu.trigger('off-canvas:close');
				}
			})
		}
	};

	// ############################### OVERLAY CLICK HANDLER ##############################################

	XF.OverlayClick = XF.Event.newHandler({
		eventNameSpace: 'XFOverlayClick',

		// NOTE: these attributes must be reflected in XF\Template\Templater::overlayClickOptions
		options: {
			cache: true,
			overlayConfig: {},
			forceFlashMessage: false,
			followRedirects: false,
			closeMenus: true
		},

		overlay: null,
		loadUrl: null,

		loading: false,
		visible: false,

		init: function()
		{
			var $overlay = this.getOverlayHtml();
			if ($overlay)
			{
				this.setupOverlay(new XF.Overlay($overlay, this.options.overlayConfig));
			}
			else
			{
				var loadUrl = this.getLoadUrl();
				if (!loadUrl)
				{
					throw new Error("Could not find an overlay for target");
				}
				this.loadUrl = loadUrl;
			}

			if (this.options.closeMenus)
			{
				XF.MenuWatcher.closeAll();
			}
		},

		click: function(e)
		{
			e.preventDefault();

			this.toggle();
		},

		toggle: function()
		{
			if (this.overlay)
			{
				this.overlay.toggle();
			}
			else
			{
				this.show();
			}
		},

		show: function()
		{
			if (this.overlay)
			{
				this.overlay.show();
			}
			else
			{
				if (this.loading)
				{
					return;
				}

				this.loading = true;

				var t = this,
					options = {
						cache: this.options.cache,
						beforeShow: function(overlay)
						{
							t.overlay = overlay;
						},
						init: XF.proxy(this, 'setupOverlay')
					},
					ajax;

				if (this.options.followRedirects)
				{
					options['onRedirect'] = function(data, overlayAjaxHandler)
					{
						if (t.options.forceFlashMessage)
						{
							XF.flashMessage(data.message, 1000, function()
							{
								XF.redirect(data.redirect);
							});
						}
						else
						{
							XF.redirect(data.redirect);
						}
					};
				}

				ajax = XF.loadOverlay(this.loadUrl, options, this.options.overlayConfig);

				if (ajax)
				{
					ajax.always(function()
					{
						setTimeout(function()
						{
							t.loading = false;
						}, 300);
					});
				}
				else
				{
					this.loading = false;
				}
			}
		},

		hide: function()
		{
			if (this.overlay)
			{
				this.overlay.hide();
			}
		},

		getOverlayHtml: function()
		{
			var $el = this.$target,
				targetSelector = $el.data('target'),
				href,
				$overlay;

			if (targetSelector)
			{
				$overlay = $el.find(targetSelector).eq(0);
				if (!$overlay.length)
				{
					$overlay = $(targetSelector).eq(0);
				}
			}

			if (!$overlay || !$overlay.length)
			{
				href = $el.attr('href');
				if (href && href.substr(0, 1) == '#')
				{
					$overlay = $(href).eq(0);
				}
			}

			if ($overlay && $overlay.length && !$overlay.is('.overlay'))
			{
				$overlay = XF.getOverlayHtml($overlay);
			}

			return ($overlay && $overlay.length) ? $overlay : null;
		},

		getLoadUrl: function()
		{
			var $el = this.$target;

			return $el.data('href') || $el.attr('href') || null;
		},

		setupOverlay: function(overlay)
		{
			this.overlay = overlay;

			var self = this,
				$overlay = overlay.$overlay;

			overlay.on({
				'overlay:shown': function()
				{
					self.visible = true;
				},
				'overlay:hidden': function()
				{
					self.visible = false;
				}
			});

			if (!this.options.cache && this.loadUrl)
			{
				overlay.on('overlay:hidden', function()
				{
					self.overlay = null;
				});
			}

			return this.overlay;
		}
	});
	XF.OverlayClick.overlayCache = {};

	// ############################### TOGGLE CLICK HANDLER ##############################################

	XF.ToggleClick = XF.Event.newHandler({
		eventNameSpace: 'XFToggleClick',
		options: {
			target: null,
			container: null,
			hide: null,
			activeClass: 'is-active',
			activateParent: null,
			scrollTo: null
		},

		$toggleTarget: null,
		$toggleParent: null,
		toggleUrl: null,
		ajaxLoaded: false,
		loading: false,

		init: function()
		{
			this.$toggleTarget = XF.getToggleTarget(this.options.target, this.$target);
			if (!this.$toggleTarget)
			{
				return false;
			}

			if (this.options.activateParent)
			{
				this.$toggleParent = this.$target.parent();
			}

			this.toggleUrl = this.getToggleUrl();
		},

		click: function(e)
		{
			e.preventDefault();

			if (this.$toggleTarget)
			{
				this.toggle();
			}
		},

		isVisible: function()
		{
			return this.$toggleTarget.hasClass(this.options.activeClass);
		},

		isTransitioning: function()
		{
			return this.$toggleTarget.hasClass('is-transitioning');
		},

		toggle: function()
		{
			if (this.isVisible())
			{
				this.hide();
			}
			else
			{
				this.show();
			}

			this.$target.blur();
		},

		load: function()
		{
			var href = this.toggleUrl,
				t = this;

			if (!href || this.loading)
			{
				return;
			}

			this.loading = true;

			XF.ajax('get', href, function(data)
			{
				if (data.html)
				{
					XF.setupHtmlInsert(data.html, function ($html, container, onComplete)
					{
						var loadSelector = t.$toggleTarget.data('load-selector');
						if (loadSelector)
						{
							var $newHtml = $html.find(loadSelector).first();
							if ($newHtml.length)
							{
								$html = $newHtml;
							}
						}

						t.ajaxLoaded = true;
						t.$toggleTarget.append($html);
						XF.activate($html);

						onComplete(true);

						t.show();

						return false;
					});
				}
			}).always(function()
			{
				t.ajaxLoaded = true;
				t.loading = false;
			});
		},

		hide: function(instant)
		{
			if (!this.isVisible() || this.isTransitioning())
			{
				return;
			}

			var activeClass = this.options.activeClass;

			if (this.$toggleParent)
			{
				this.$toggleParent.removeClassTransitioned(activeClass, XF.proxy(this, 'inactiveTransitionComplete'), instant);
			}
			if (this.$toggleTarget)
			{
				this.$toggleTarget.removeClassTransitioned(activeClass, XF.proxy(this, 'inactiveTransitionComplete'), instant);
			}
			this.$target.removeClassTransitioned(activeClass, XF.proxy(this, 'inactiveTransitionComplete'), instant);
		},

		show: function(instant)
		{
			if (this.isVisible() || this.isTransitioning())
			{
				return;
			}

			if (this.getOtherToggles().filter('.is-transitioning').length)
			{
				return;
			}

			if (this.toggleUrl && !this.ajaxLoaded)
			{
				this.load();
				return;
			}

			this.closeOthers();

			var activeClass = this.options.activeClass;
			if (this.$toggleParent)
			{
				this.$toggleParent.addClassTransitioned(activeClass, this.activeTransitionComplete, instant);
			}
			if (this.$toggleTarget)
			{
				this.$toggleTarget.addClassTransitioned(activeClass, this.activeTransitionComplete, instant);
			}
			this.$target.addClassTransitioned(activeClass, this.activeTransitionComplete, instant);

			this.hideSpecified();
			this.scrollTo();

			XF.autoFocusWithin(this.$toggleTarget, '[autofocus], [data-toggle-autofocus]');
		},

		activeTransitionComplete: function(e)
		{
			$(this).trigger('toggle:shown');
			XF.layoutChange();
		},

		inactiveTransitionComplete: function(e)
		{
			$(this).trigger('toggle:hidden');
			XF.layoutChange();
		},

		closeOthers: function()
		{
			this.getOtherToggles().each(function()
			{
				var $toggle = $(this),
					handlers = $toggle.data('xf-click-handlers');

				if (!handlers)
				{
					handlers = XF.Event.initElement(this, 'click');
				}

				if (handlers && handlers.toggle)
				{
					handlers.toggle.hide(true);
				}
			});
		},

		hideSpecified: function()
		{
			var $hide = $(this.options.hide);
			if ($hide && $hide.length)
			{
				$hide.hide();
			}
		},

		scrollTo: function()
		{
			if (this.options.scrollTo)
			{
				var $toggleTarget = this.$toggleTarget,
					topOffset = $toggleTarget.offset().top,
					height = $toggleTarget.height(),
					windowHeight = $(window).height(),
					offset;

				if (height < windowHeight)
				{
					offset = topOffset - ((windowHeight / 2) - (height / 2));
				}
				else
				{
					offset = topOffset;
				}

				$('html, body').animate({scrollTop: offset}, XF.config.speed.fast);
			}
		},

		getToggleUrl: function()
		{
			var $toggleTarget = this.$toggleTarget,
				url;

			if ($toggleTarget && (url = $toggleTarget.data('href')))
			{
				return url == 'trigger-href' ? this.$target.attr('href') : url;
			}

			return null;
		},

		getContainer: function()
		{
			if (this.options.container)
			{
				var $container = this.$target.closest(this.options.container);
				if (!$container.length)
				{
					console.error('Container parent not found: ' + this.options.container);
					return null;
				}
				else
				{
					return $container;
				}
			}

			return null;
		},

		getOtherToggles: function()
		{
			var $container = this.getContainer();
			if ($container && $container.length)
			{
				return $container.find('[data-xf-click~=toggle]').not(this.$target[0]);
			}
			else
			{
				return $([]);
			}
		}
	});

	XF.getToggleTarget = function(optionTarget, $thisTarget)
	{
		var $target = optionTarget ? XF.findRelativeIf(optionTarget, $thisTarget) : $thisTarget.next();

		if (!$target.length)
		{
			throw new Error('No toggle target for %o', $thisTarget);
			return false;
		}

		return $target;
	};

	XF.ToggleStorage = XF.Element.newHandler({
		options: {
			storageType: 'local',
			storageContainer: 'toggle',
			storageKey: null,

			target: null,
			container: null,
			hide: null,
			activeClass: 'is-active',
			activateParent: null
		},

		targetId: null,
		storage: null,

		init: function()
		{
			var container = this.options.storageContainer;
			if (!container)
			{
				throw new Error("Storage container not specified for ToggleStorage handler");
			}

			var key = this.options.storageKey;
			if (!key)
			{
				throw new Error("Storage key not specified for ToggleStorage handler");
			}

			this.storage = XF.ToggleStorageData.getInstance(this.options.storageType);
			if (!this.storage)
			{
				throw new Error("Invalid storage type " + this.options.storageType);
			}

			var toggleValue = this.storage.get(container, key);
			if (toggleValue !== null)
			{
				var $toggleTarget = XF.getToggleTarget(this.options.target, this.$target);
				if ($toggleTarget.length)
				{
					var activeClass = this.options.activeClass;

					this.$target.toggleClass(activeClass, toggleValue);
					$toggleTarget.toggleClass(activeClass, toggleValue);
				}
			}

			this.storage.prune(container);

			this.$target.on('xf-click:after-click.XFToggleClick', XF.proxy(this, 'updateStorage'));
		},

		updateStorage: function()
		{
			var options = this.options;

			this.storage.set(
				options.storageContainer,
				options.storageKey,
				this.$target.hasClass(options.activeClass)
			);
		}
	});

	XF.ToggleStorageDataInstance = XF.create({
		storage: null,

		dataCache: {},
		syncTimers: {},
		pruneTimers: {},

		__construct: function(storageObject)
		{
			this.storage = storageObject;
		},

		getStorage: function()
		{
			return this.storage;
		},

		get: function(container, key, options)
		{
			if (!options)
			{
				options = {};
			}

			var allowExpired = options.allowExpired || true,
				touch = options.touch || true;

			if (!this.dataCache[container])
			{
				this.dataCache[container] = this.storage.getJson(container);
			}

			var data = this.dataCache[container];

			if (!data.hasOwnProperty(key))
			{
				return null;
			}

			var value = data[key],
				now = Math.floor(Date.now() / 1000);
			if (!allowExpired && (value[0] + value[1]) < now)
			{
				delete this.dataCache[container][key];
				this.scheduleSync(container);

				return null;
			}

			if (touch)
			{
				value[0] = now;
				this.dataCache[container][key] = value;
				this.scheduleSync(container);
			}

			return value[2];
		},

		set: function(container, key, value, expirySeconds)
		{
			if (!this.dataCache[container])
			{
				this.dataCache[container] = {};
			}

			if (!expirySeconds)
			{
				expirySeconds = 4 * 3600; // 4 hours
			}

			var now = Math.floor(Date.now() / 1000);
			this.dataCache[container][key] = [now, expirySeconds, value];
			this.scheduleSync(container);
		},

		remove: function(container, key)
		{
			if (!this.dataCache[container])
			{
				this.dataCache[container] = {};
			}

			delete this.dataCache[container][key];
			this.scheduleSync(container);
		},

		prune: function(container, immediate)
		{
			var timer = this.pruneTimers[container],
				t = this,
				triggerPrune = function()
				{
					clearTimeout(timer);
					t.pruneTimers[container] = null;
					t.pruneInternal(container);
				};

			if (immediate)
			{
				triggerPrune();
			}
			else if (!timer)
			{
				this.pruneTimers[container] = setTimeout(triggerPrune, 100);
			}
		},

		pruneInternal: function(container)
		{
			if (!this.dataCache[container])
			{
				this.dataCache[container] = this.storage.getJson(container);
			}

			var cache = this.dataCache[container],
				value,
				now = Math.floor(Date.now() / 1000),
				updated = false;

			for (var key in cache)
			{
				if (!cache.hasOwnProperty(key))
				{
					continue;
				}

				value = cache[key];
				if (value[0] + value[1] < now)
				{
					delete cache[key];
					updated = true;
				}
			}

			if (updated)
			{
				this.dataCache[container] = cache;
				this.scheduleSync(container);
			}
		},

		scheduleSync: function(container, immediate)
		{
			var timer = this.syncTimers[container],
				t = this,
				triggerSync = function()
				{
					clearTimeout(timer);
					t.syncTimers[container] = null;
					t.syncToStorage(container);
				};

			if (immediate)
			{
				triggerSync();
			}
			else if (!timer)
			{
				t.syncTimers[container] = setTimeout(triggerSync, 100);
			}
		},

		syncToStorage: function(container)
		{
			if (!this.dataCache[container])
			{
				return;
			}

			var writeValue = this.dataCache[container];
			if ($.isEmptyObject(writeValue))
			{
				this.storage.remove(container);
			}
			else
			{
				this.storage.setJson(container, writeValue);
			}
		}
	});

	XF.ToggleStorageData = (function()
	{
		var instances = {
			local: new XF.ToggleStorageDataInstance(XF.LocalStorage),
			cookie: new XF.ToggleStorageDataInstance(XF.Cookie)
		};
		var defaultInstance = instances.local;

		return {
			getInstance: function(type)
			{
				return instances[type];
			},
			get: function(container, key, options)
			{
				return defaultInstance.get(container, key, options);
			},
			set: function(container, key, value, expirySeconds)
			{
				return defaultInstance.set(container, key, value, expirySeconds);
			},
			remove: function(container, key)
			{
				return defaultInstance.remove(container, key);
			},
			prune: function(container, immediate)
			{
				return defaultInstance.prune(container, immediate)
			}
		};
	})();

	XF.ToggleClassClick = XF.Event.newHandler({
		eventNameSpace: 'XFToggleClassClick',
		options: {
			class: null
		},

		init: function()
		{
		},

		click: function(e)
		{
			if (!this.options.class)
			{
				return;
			}

			this.toggle();
		},

		toggle: function()
		{
			this.$target.toggleClass(this.options.class);
		}
	});

	// ################################## HORIZONTAL SCROLLER HANDLER ###########################################

	XF.HScroller = XF.Element.newHandler({
		options: {
			scrollerClass: 'hScroller-scroll',
			actionClass: 'hScroller-action',
			autoScroll: '.tabs-tab.is-active'
		},

		$scrollTarget: null,
		$goStart: null,
		$goEnd: null,

		init: function()
		{
			var $scrollTarget = this.$target.find('.' + this.options.scrollerClass).first();
			if (!$scrollTarget.length)
			{
				console.error('no scroll target');
				return;
			}

			this.$scrollTarget = $scrollTarget;

			var x, y,
				dragged,
				self = this,
				ns = 'horizontalScroller';

			$scrollTarget
				.on('mousedown.' + ns, function(e)
				{
					if (e.button)
					{
						// non-primary click
						return;
					}

					x = e.clientX;
					y = e.clientY;
					dragged = false;

					e.preventDefault();

					if (XF.isEventTouchTriggered(e))
					{
						// In touch browsers, we may have focus on an input which should have the keyboard showing.
						// When we trigger this and prevent the event, the focus is technically returned to the input,
						// which causes the soft keyboard to show again. In most cases, this isn't ideal, so blur
						// the input so we don't return focus.
						var $focus = $(document.activeElement);
						if ($focus.is(':input'))
						{
							$focus.blur();
						}
					}

					$(window)
						.on('mouseup.' + ns, function(e)
						{
							$(window).off('.' + ns);

							if (dragged)
							{
								e.preventDefault();
							}
						})
						.on('mousemove.' + ns, function(e)
						{
							var move = x - e.clientX;
							if (move != 0)
							{
								if (self.move(move))
								{
									dragged = true;
								}
								x = e.clientX;
							}
						});
				})
				.on('click.' + ns, function(e)
				{
					if (dragged)
					{
						e.preventDefault();
						e.stopImmediatePropagation();
						dragged = false;
					}
				})
				.on('scroll.' + ns, XF.proxy(this, 'updateScroll'))
				.on('tab:click.' + ns, function(e)
				{
					if (dragged)
					{
						e.preventDefault();
					}
				});

			var measure = XF.measureScrollBar(null, 'height');
			$scrollTarget.addClass('is-calculated');
			if (measure != 0)
			{
				$scrollTarget.css('margin-bottom', (parseInt($scrollTarget.css('margin-bottom'), 10) - measure) + 'px');
			}

			var actionClass = this.options.actionClass;
			this.$goStart = $('<i class="' + actionClass + ' ' + actionClass +'--start" aria-hidden="true" />')
				.click(function() { self.step(-1); })
				.insertAfter($scrollTarget);
			this.$goEnd = $('<i class="' + actionClass + ' ' + actionClass +'--end" aria-hidden="true" />')
				.click(function() { self.step(1); })
				.insertAfter($scrollTarget);

			this.updateScroll();

			$(document.body).on('xf:layout', XF.proxy(this, 'updateScroll'));

			var resizeTimer;
			$(window).on('resize', function()
			{
				if (resizeTimer)
				{
					clearTimeout(resizeTimer);
				}
				resizeTimer = setTimeout(XF.proxy(self, 'updateScroll'), 100);
			});

			var $autoScroll = $scrollTarget.find(this.options.autoScroll).first();
			if ($autoScroll.length)
			{
				var ttWidth = this.$target.width(),
					asPosition = $autoScroll.position(),
					asWidth = $autoScroll.outerWidth(),
					asLeft = asPosition.left,
					asRight = asLeft + asWidth;

				if (XF.isRtl())
				{
					if (asLeft < 80)
					{
						// This is a calculation to try to put the selected tab near the right edge.
						// -asRight gives a positive distance to scroll
						// + the full width displayed
						// - 50 to move it away from the right edge
						$scrollTarget.normalizedScrollLeft(-asRight + ttWidth - 50);
					}
				}
				else
				{
					if (asRight > ttWidth)
					{
						if (asRight + 80 > ttWidth)
						{
							$scrollTarget.normalizedScrollLeft(asLeft - 50);
						}
						else
						{
							$scrollTarget.normalizedScrollLeft(asLeft - 80);
						}
					}
				}
			}
		},

		move: function(amount)
		{
			var $target = this.$scrollTarget,
				left = $target.normalizedScrollLeft();

			if (XF.isRtl())
			{
				// Positive represents amount moved to right.
				// Since RTL scrolls the opposite way, need to account for that.
				amount *= -1;
			}

			$target.normalizedScrollLeft(left + amount);

			return ($target.normalizedScrollLeft() !== left);
		},

		step: function(dir)
		{
			var scrollAmount = Math.max(125, Math.floor(this.$scrollTarget.width() * .25)),
				op = '+=';

			switch ($.support.scrollLeftType)
			{
				case 'inverted':
				case 'negative':
					op = '-=';
			}

			this.$scrollTarget.animate({scrollLeft: op + (dir * scrollAmount)}, 150);
		},

		updateScroll: function()
		{
			var el = this.$scrollTarget[0],
				left = this.$scrollTarget.normalizedScrollLeft(),
				width = el.offsetWidth,
				scrollWidth = el.scrollWidth,
				startActive = (left > 0),
				endActive = (width + left + 1 < scrollWidth);

			this.$goStart[startActive ? 'addClass' : 'removeClass']('is-active');
			this.$goEnd[endActive ? 'addClass' : 'removeClass']('is-active');
		}
	});

	// ################################## RESPONSIVE DATE LIST ###########################################

	XF.ResponsiveDataList = XF.Element.newHandler({
		options: {
			headerRow: '.dataList-row--header',
			headerCells: 'th, td',
			rows: '.dataList-row:not(.dataList-row--subSection, .dataList-row--header)',
			rowCells: 'td',
			triggerWidth: 'narrow'
		},

		$headerRow: null,
		headerText: [],
		$rows: null,
		isResponsive: false,

		init: function()
		{
			var $headerRow = this.$target.find(this.options.headerRow).first(),
				headerText = [];

			$headerRow.find(this.options.headerCells).each(function()
			{
				var $cell = $(this),
					text = $cell.text(),
					colspan = parseInt($cell.attr('colspan'), 10);
				headerText.push($.trim(text));

				if (colspan > 1)
				{
					for (var i = 1; i < colspan; i++)
					{
						headerText.push('');
					}
				}
			});

			this.$headerRow = $headerRow;
			this.headerText = headerText;

			this.$rows = this.$target.find(this.options.rows);
			this.process();

			$(document).on('breakpoint:change', XF.proxy(this, 'process'));
		},

		process: function()
		{
			var triggerable = XF.Breakpoint.isAtOrNarrowerThan(this.options.triggerWidth);

			if ((triggerable && this.isResponsive) || (!triggerable && !this.isResponsive))
			{
				// no action needed
				return;
			}

			triggerable ? this.apply() : this.remove();
		},

		apply: function()
		{
			var self = this;

			this.$rows.each(function() { self.processRow($(this), true); });

			this.$target.addClass('dataList--responsive');
			this.$headerRow.addClass('dataList-row--headerResponsive');

			this.isResponsive = true;
		},

		remove: function()
		{
			var self = this;

			this.$rows.each(function() { self.processRow($(this), false); });

			this.$target.removeClass('dataList--responsive');
			this.$headerRow.removeClass('dataList-row--headerResponsive');

			this.isResponsive = false;
		},

		processRow: function($row, apply)
		{
			var i = 0,
				headerText = this.headerText;

			$row.find(this.options.rowCells).each(function()
			{
				var $cell = $(this);

				if (apply)
				{
					var	thisHeaderText = headerText[i];
					if (thisHeaderText && thisHeaderText.length && !$cell.data('hide-label'))
					{
						$cell.attr('data-cell-label', thisHeaderText);
					}
					else
					{
						$cell.removeAttr('data-cell-label');
					}
				}
				else
				{
					$cell.removeAttr('data-cell-label');
				}

				i++;
			});
		}
	});

	// ################################## STICKY ELEMENTS ###########################################

	XF.Sticky = XF.Element.newHandler({
		options: {
			parent: null,
			inner_scrolling: true,
			sticky_class: 'is-sticky',
			offset_top: null,
			spacer: null,
			bottoming: null,
			recalc_every: null
		},

		init: function()
		{
			if (this.options.spacer == "false")
			{
				this.options.spacer = false;
			}

			this.$target.stick_in_parent(this.options);
		}
	});

	// ################################## STICKY HEADER ###########################################

	XF.StickyHeader = XF.Element.newHandler({
		options: {
			stickyClass: 'is-sticky',
			stickyBrokenClass: 'is-sticky-broken',
			stickyDisabledClass: 'is-sticky-disabled',
			minWindowHeight: 251
		},

		active: null,
		supportsSticky: false,
		stickyBroken: false,
		windowTooSmall: false,

		init: function()
		{
			var $target = this.$target,
				position = $target.css('position'),
				supportsSticky = (position == 'sticky' || position == '-webkit-sticky'),
				stickyBroken = false;

			if (supportsSticky)
			{
				var ua = window.navigator.userAgent,
					match = ua.match(/Chrome\/(\d+)/);
				if (match && parseInt(match[1], 10) < 60)
				{
					// Chrome has sticky positioning bugs in desktop (canary) 57
					// and different bugs in Android (canary) 57, so keep it disabled for now.
					stickyBroken = true;
					supportsSticky = false;
				}

				match = ua.match(/ Edge\/(\d+)/);
				if (match && parseInt(match[1], 10) >= 17 && XF.isRtl())
				{
					// Edge 17 appears to have broken position sticky in RTL pages. It may be resolved in
					// 18, but as of this fix, the bug hasn't been resolved yet so we can't guarantee that
					stickyBroken = true;
					supportsSticky = false;
				}
			}

			this.supportsSticky = supportsSticky;
			this.stickyBroken = stickyBroken;

			this.update();

			$(window).on('resize.sticky-header', XF.proxy(this, 'update'));
		},

		update: function()
		{
			var winHeight = $(window).height();

			this.windowTooSmall = (winHeight < this.options.minWindowHeight);

			if (this.windowTooSmall)
			{
				// disable if we aren't explicitly disabled (true or null)
				if (this.active !== false)
				{
					this._disable();
				}
			}
			else
			{
				// enable if we aren't already enabled (false or null)
				if (!this.active)
				{
					this._enable();
				}
			}
		},

		_enable: function()
		{
			this.active = true;

			var $target = this.$target,
				stickyClass = this.options.stickyClass,
				stickyBrokenClass = this.options.stickyBrokenClass,
				stickyDisabledClass = this.options.stickyDisabledClass;

			$target.removeClass(stickyDisabledClass);

			if (this.supportsSticky)
			{
				var isSticky = false,
					stickyTop = parseInt($target.css('top'), 10),
					iOS = XF.isIOS(),
					iOSScrollTimeout;

				var checkIsSticky = function(isScrolling)
				{
					var targetTop = Math.floor($target[0].getBoundingClientRect().top),
						shouldBeSticky = false;

					if (targetTop < stickyTop || (targetTop == stickyTop && window.scrollY > 0))
					{
						if (!isSticky)
						{
							$target.addClass(stickyClass);
							isSticky = true;
						}
					}
					else
					{
						if (isSticky)
						{
							if (iOS && isScrolling)
							{
								// iOS doesn't report the correct top position when scrolling while sticky,
								// so we need to wait until scrolling appears to have stopped to recalculate.
								// http://www.openradar.me/22872226
								clearTimeout(iOSScrollTimeout);
								iOSScrollTimeout = setTimeout(function()
								{
									checkIsSticky(false);
								}, 200);
							}
							else
							{
								$target.removeClass(stickyClass);
								isSticky = false;
							}
						}
					}
				};

				$(window).on('scroll.sticky-header', function()
				{
					checkIsSticky(true);
				});

				checkIsSticky(false);
			}
			else
			{
				if (this.stickyBroken)
				{
					// run after sticky kit triggers their tick function
					setTimeout(function() { $target.addClass(stickyBrokenClass); }, 0);
				}

				$target.stick_in_parent({
					sticky_class: stickyClass
				});
			}
		},

		_disable: function()
		{
			this.active = false;

			var $target = this.$target,
				stickyClass = this.options.stickyClass,
				stickyBrokenClass = this.options.stickyBrokenClass,
				stickyDisabledClass = this.options.stickyDisabledClass;

			if (this.supportsSticky)
			{
				$(window).off('scroll.sticky-header');
			}
			else
			{
				$target.trigger('sticky_kit:detach').removeData('sticky_kit');
			}

			$target.removeClass(stickyClass).removeClass(stickyBrokenClass).addClass(stickyDisabledClass);
		}
	});

	// ################################## TABS HANDLER ###########################################

	XF.Tabs = XF.Element.newHandler({
		options: {
			tabs: '.tabs-tab',
			panes: null,
			activeClass: 'is-active',
			state: null,
			preventDefault: true // set to false to allow tab clicks to allow events to bubble
		},

		initial: 0,

		$tabs: null,
		$panes: null,

		$activeTab: null,
		$activePane: null,

		init: function()
		{
			var $container = this.$target,
				$tabs, $panes;

			$tabs = this.$tabs = $container.find(this.options.tabs);
			if (this.options.panes)
			{
				$panes = XF.findRelativeIf(this.options.panes, $container);
			}
			else
			{
				$panes = $container.next();
			}

			if ($panes.is('ol, ul'))
			{
				$panes = $panes.find('> li');
			}

			this.$panes = $panes;

			if ($tabs.length != $panes.length)
			{
				console.error('Tabs and panes contain different totals: %d tabs, %d panes', $tabs.length, $panes.length);
				console.error('Tabs: %o, Panes: %o', $tabs, $panes);
				return;
			}

			for (var i = 0; i < $tabs.length; i++)
			{
				if ($tabs.eq(i).hasClass(this.options.activeClass))
				{
					this.initial = i;
					break;
				}
			}

			$tabs.on('click', XF.proxy(this, 'tabClick'));
			$(window).on('hashchange', XF.proxy(this, 'onHashChange'));
			$(window).on('popstate', XF.proxy(this, 'onPopState'));

			this.reactToHash();
		},

		getSelectorFromHash: function()
		{
			var selector = '';
			if (window.location.hash.length > 1)
			{
				var hash = window.location.hash.replace(/[^a-zA-Z0-9_-]/g, '');
				if (hash && hash.length)
				{
					selector = '#' + hash;
				}
			}
			return selector;
		},

		reactToHash: function()
		{
			var selector = this.getSelectorFromHash();

			if (selector)
			{
				this.activateTarget(selector);
			}
			else
			{
				this.activateTab(this.initial);
			}
		},

		onHashChange: function(e)
		{
			this.reactToHash();
		},

		onPopState: function(e)
		{
			var popStateEvent = e.originalEvent,
				state = popStateEvent.state;

			if (state && state.id)
			{
				this.activateTarget('#' + state.id, false);
			}
			else if (state && state.offset)
			{
				this.activateTab(state.offset);
			}
			else
			{
				this.activateTab(this.initial);
			}
		},

		activateTarget: function(selector)
		{
			var $tabs = this.$tabs,
				selectorValid = false,
				found = false;

			if (selector)
			{
				try
				{
					// For a tab to be selected via a selector, the selector has to exist and the selector has to be valid.
					var $test = $(selector);
					selectorValid = ($test && $test.length > 0);
				}
				catch (e)
				{
					selectorValid = false;
				}

				if (selectorValid)
				{
					for (var i = 0; i < $tabs.length; i++)
					{
						if ($tabs.eq(i).is(selector))
						{
							this.activateTab(i);
							found = true;
						}
					}
				}
			}

			if (!found)
			{
				this.activateTab(this.initial);
			}
		},

		activateTab: function(offset)
		{
			var $tab = this.$tabs.eq(offset),
				$pane = this.$panes.eq(offset),
				activeClass = this.options.activeClass;

			if (!$tab.length || !$pane.length)
			{
				console.error('Selected invalid tab ' + offset);
				return;
			}

			// deactivate active other tab
			this.$tabs.filter('.' + activeClass)
				.removeClass(activeClass)
				.attr('aria-selected', 'false')
				.trigger('tab:hidden');
			this.$panes.filter('.' + activeClass)
				.removeClass(activeClass)
				.attr('aria-expanded', 'false')
				.trigger('tab:hidden');

			// activate tab
			$tab
				.addClass(activeClass)
				.attr('aria-selected', 'true')
				.trigger('tab:shown');
			$pane
				.addClass(activeClass)
				.attr('aria-expanded', 'true')
				.trigger('tab:shown');

			XF.layoutChange();

			if ($pane.data('href'))
			{
				if ($pane.data('tab-loading'))
				{
					return;
				}
				$pane.data('tab-loading', true);

				XF.ajax('get', $pane.data('href'), {}, function(data)
				{
					$pane.data('href', false);
					if (data.html)
					{
						var loadTarget = $pane.data('load-target');
						if (loadTarget)
						{
							XF.setupHtmlInsert(data.html, $pane.find(loadTarget));
						}
						else
						{
							XF.setupHtmlInsert(data.html, $pane);
						}
					}
				}).always(function(){
					$pane.data('tab-loading', false);
				})
			}
		},

		tabClick: function(e)
		{
			var click = e.currentTarget,
				offset = this.$tabs.index(click);
			if (offset == -1)
			{
				console.error('Did not find clicked element (%o) in tabs', click);
				return;
			}

			var $tab = this.$tabs.eq(offset),
				event = $.Event('tab:click');
			$tab.trigger(event, this);
			if (event.isDefaultPrevented())
			{
				return;
			}

			if (this.options.preventDefault)
			{
				e.preventDefault();
			}

			if (this.options.state)
			{
				var href = window.location.href.split("#")[0],
					state = {};

				if ($tab.attr('id'))
				{
					href = href + '#' + $tab.attr('id');
					state = {
						id: $tab.attr('id')
					}
				}
				else
				{
					state = {
						offset: offset
					}
				}

				switch (this.options.state)
				{
					case 'replace':
						window.history.replaceState(state, '', href);
						break;

					case 'push':
						window.history.pushState(state, '', href);
						break;
				}
			}

			this.activateTab(offset);
		}
	});

	// ################################## PAGE JUMP HANDLER ###########################################

	XF.PageJump = XF.Element.newHandler({
		options: {
			pageUrl: null,
			pageInput: '| .js-pageJumpPage',
			pageSubmit: '| .js-pageJumpGo',
			sentinel: '%page%'
		},

		$input: null,

		init: function()
		{
			var self = this;

			if (!this.options.pageUrl)
			{
				console.error('No page-url provided to page jump');
				return;
			}

			this.$input = XF.findRelativeIf(this.options.pageInput, this.$target);
			if (!this.$input.length)
			{
				console.error('No input provided to page jump');
				return;
			}

			this.$input.on('keyup', function(e)
			{
				if (e.key == 'Enter')
				{
					e.preventDefault();
					self.go();
				}
			});

			XF.findRelativeIf(this.options.pageSubmit, this.$target).on('click', function(e)
			{
				e.preventDefault();
				self.go();
			});

			this.$target.closest('.menu').on('menu:opened', function()
			{
				self.shown();
			});
		},

		shown: function()
		{
			this.$input.select();
		},

		go: function()
		{
			var page = parseInt(this.$input.val(), 10);
			if (page < 1)
			{
				page = 1;
			}

			var baseUrl = this.options.pageUrl,
				sentinel = this.options.sentinel,
				url = baseUrl.replace(sentinel, page);

			if (url == baseUrl)
			{
				url = baseUrl.replace(encodeURIComponent(sentinel), page);
			}

			XF.redirect(url);
		}
	});

	// ################################## QUICK SEARCH ###########################################

	XF.QuickSearch = XF.Element.newHandler({
		options: {
			select: '| .js-quickSearch-constraint'
		},

		$select: null,

		init: function()
		{
			this.$select = XF.findRelativeIf(this.options.select, this.$target);
			this.$select.on('change', XF.proxy(this, 'updateSelectWidth'));

			this.updateSelectWidth();
		},

		updateSelectWidth: function()
		{
			if (!this.$select.length)
			{
				return;
			}

			var $selectProxy = $('<span />').addClass(this.$select.attr('class')).addClass('input--select'),
				$selected = this.$select.find('option:selected');

			if (!$selected.length)
			{
				$selected = this.$select.find('option:first');
			}

			$selectProxy.text($selected.text());
			$selectProxy.css('display', 'inline');

			var $positioner = $('<div />');
			$positioner.css({
				position: 'absolute',
				top: -200,
				visibility: 'hidden'
			});
			$positioner.css(XF.isRtl() ? 'right' : 'left', -9999);

			$selectProxy.appendTo($positioner);
			$positioner.appendTo('body');

			// give a little extra space just in case; potential iOS quirk without it
			this.$select.css({
				width: $selectProxy.outerWidth() + 8,
				'flex-grow': 0,
				'flex-shrink': 0
			});
			$positioner.remove();
		}
	});

	// ################################## TOUCH PROXY ELEMENTS ###########################################

	XF.TouchProxy = XF.Element.newHandler({
		options: {
			allowed: ':input, :checkbox, a, label, [data-tp-clickable], [data-tp-primary]'
		},

		active: true,
		timer: null,

		$proxy: null,

		init: function()
		{
			var self = this;

			if ('InputDeviceCapabilities' in window || 'sourceCapabilities' in UIEvent.prototype)
			{
				this.$target.click(function(e)
				{
					var oe = e.originalEvent;
					if (!oe || !oe.sourceCapabilities || !oe.sourceCapabilities.firesTouchEvents)
					{
						return;
					}

					self.handleTapEvent(e);
				});
			}
			else if (XF.Feature.has('touchevents'))
			{
				var moved = false;
				this.$target
					.on('touchstart', function() { moved = false; })
					.on('touchmove', function() { moved = true; })
					.on('touchend', function(e)
					{
						if (!moved)
						{
							self.handleTapEvent(e);
						}
					});
			}
			// otherwise just act as a desktop browser
		},

		isClickable: function(clicked)
		{
			var $closest = $(clicked).closest(this.options.allowed);
			return !!($closest.length && this.$target.find($closest).length);
		},

		handleTapEvent: function(e)
		{
			if (!this.getProxy().length)
			{
				// can't find a proxy, act as if nothing is happening
				return;
			}

			if (this.active && !this.isClickable(e.target))
			{
				e.preventDefault();
				this.trigger();
			}
		},

		getProxy: function()
		{
			if (!this.$proxy)
			{
				var $proxy = this.$target.find('[data-tp-primary]').first();
				if (!$proxy.length)
				{
					$proxy = this.$target.find('a[href]').first();
				}

				this.$proxy = $proxy;
			}

			return this.$proxy;
		},

		trigger: function(e)
		{
			var $proxy = this.getProxy();
			if (!$proxy.length)
			{
				return;
			}

			if (this.timer)
			{
				clearTimeout(this.timer);
			}
			this.active = false;

			if ($proxy[0].click)
			{
				$proxy[0].click();
			}
			else
			{
				$proxy.click();
			}

			var self = this;
			this.timer = setTimeout(function() { self.active = true; }, 500);
		}
	});

	// ################################## ELEMENTS SWAPPER ###########################################

	XF.ShifterClick = XF.Event.newHandler({
		eventNameSpace: 'XFShifterClick',
		options: {
			selector: null,
			dir: 'up'
		},

		$element: null,

		init: function()
		{
			this.$element = this.$target.closest(this.options.selector);
		},

		click: function(e)
		{
			if (this.options.dir == 'down')
			{
				this.$element.insertAfter(this.$element.next());
			}
			else
			{
				this.$element.insertBefore(this.$element.prev());
			}
		}
	});

	// ################################## VIDEO ELEMENTS ###########################################

	XF.VideoInit = XF.Element.newHandler({
		options: {},

		video: null,
		loaded: false,

		/**
		 * This workaround loads the first frame of a video into a canvas element
		 * to workaround the fact that iOS doesn't do that until the video actually
		 * starts playing. This enables us to not worry about thumbnails / posters for videos.
		 */
		init: function()
		{
			if (!XF.isIOS())
			{
				return;
			}

			this.video = this.$target[0].cloneNode(true);
			this.video.load();

			this.video.addEventListener('loadeddata', XF.proxy(this, 'hasLoaded'));
			this.video.addEventListener('seeked', XF.proxy(this, 'hasSeeked'));
		},

		hasLoaded: function()
		{
			if (this.loaded)
			{
				return;
			}

			this.loaded = true;
			this.video.currentTime = 0;
		},

		hasSeeked: function()
		{
			var $canvas = $('<canvas />'),
				canvas = $canvas[0],
				width = this.$target.width(),
				height = this.$target.height(),
				context = canvas.getContext('2d');

			canvas.width = width;
			canvas.height = height;
			context.drawImage(this.video, 0, 0, width, height);

			if (!canvas)
			{
				return;
			}

			var t = this;
			canvas.toBlob(function(blob)
			{
				if (!blob)
				{
					return;
				}

				var url = URL.createObjectURL(blob);
				t.$target.attr('poster', url);
			});
		}
	});

	XF.Event.register('click', 'inserter', 'XF.InserterClick');
	XF.Event.register('click', 'menu', 'XF.MenuClick');
	XF.Event.register('click', 'menu-proxy', 'XF.MenuProxy');
	XF.Event.register('click', 'off-canvas', 'XF.OffCanvasClick');
	XF.Event.register('click', 'overlay', 'XF.OverlayClick');
	XF.Event.register('click', 'toggle', 'XF.ToggleClick');
	XF.Event.register('click', 'toggle-class', 'XF.ToggleClassClick');
	XF.Event.register('click', 'shifter', 'XF.ShifterClick');

	XF.Element.register('focus-inserter', 'XF.InserterFocus');
	XF.Element.register('h-scroller', 'XF.HScroller');
	XF.Element.register('page-jump', 'XF.PageJump');
	XF.Element.register('quick-search', 'XF.QuickSearch');
	XF.Element.register('responsive-data-list', 'XF.ResponsiveDataList');
	XF.Element.register('sticky', 'XF.Sticky');
	XF.Element.register('sticky-header', 'XF.StickyHeader');
	XF.Element.register('tabs', 'XF.Tabs');
	XF.Element.register('toggle-storage', 'XF.ToggleStorage');
	XF.Element.register('touch-proxy', 'XF.TouchProxy');
	XF.Element.register('video-init', 'XF.VideoInit');

	$(document).on('xf:page-load-complete', function()
	{
		var hash = window.location.hash.replace(/[^a-zA-Z0-9_-]/g, '');
		if (!hash)
		{
			return;
		}

		var $match = hash ? $('#' + hash) : $();
		if ($match.length)
		{
			var $toggleWrapper = $match.closest('[data-toggle-wrapper]');
			if ($toggleWrapper.length)
			{
				var $toggler = $toggleWrapper.find('[data-xf-click~="toggle"]').first();
				if ($toggler.length)
				{
					var toggleHandler = XF.Event.getElementHandler($toggler, 'toggle', 'click');
					if (toggleHandler)
					{
						toggleHandler.show(true);
					}
				}
			}
		}
	});
}
(jQuery, window, document);