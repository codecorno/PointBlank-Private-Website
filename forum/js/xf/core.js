var XF = window.XF || {};

if (window.jQuery === undefined) jQuery = $ = {};

!function($, window, document)
{
	"use strict";

	// already loaded, don't load this twice
	if (XF.activate)
	{
		console.error('XF core has been double loaded');
		return;
	}

	if (!XF.browser)
	{
		XF.browser = {
			browser: '',
			version: 0,
			os: '',
			osVersion: null
		};
	}

	// ################################# JQUERY EXTENSIONS #######################################

	(function()
	{
		// approach inspired by Bootstrap / http://blog.alexmaccaw.com/css-transitions
		var support = (function()
		{
			var el = document.createElement('fake'),
				transitions = {
					WebkitTransition: 'webkitTransitionEnd',
					MozTransition: 'transitionend',
					OTransition: 'oTransitionEnd otransitionend',
					transition: 'transitionend'
				};

			for (var name in transitions)
			{
				if (el.style[name] !== undefined)
				{
					return {end: transitions[name] };
				}
			}

			return false;
		})();

		$.support.transition = support;
		if ($.support.transition)
		{
			$.event.special.xfTransitionEnd = {
				bindType: support.end,
				delegateType: support.end,
				handle: function(e)
				{
					if ($(e.target).is(this))
					{
						return e.handleObj.handler.apply(this, arguments);
					}
				}
			};
		}

		var dir = $('html').attr('dir'),
			isRtl = (dir && dir.toUpperCase() == 'RTL'),
			scrollLeftType = 'normal';

		if (isRtl)
		{
			var $tester = $('<div style="width: 80px; height: 40px; font-size: 30px; overflow: scroll; white-space: nowrap; word-wrap: normal; position: absolute; top: -1000px; visibility: hidden; pointer-events: none">MMMMMMMMMM</div>'),
				tester = $tester[0];

			$tester.appendTo('body');

			if (tester.scrollLeft > 0)
			{
				// max value at start, scrolls towards 0
				scrollLeftType = 'inverted';
			}
			else
			{
				tester.scrollLeft = -1;
				if (tester.scrollLeft == -1)
				{
					// 0 at start, scrolls towards negative values
					scrollLeftType = 'negative';
				}
				// else normal: 0 at start, scrolls towards positive values
			}

			$tester.remove();
		}

		$.support.scrollLeftType = scrollLeftType;

		$.addEventCapture = (function()
		{
			var special = $.event.special;

			return function(names)
			{
				if (!document.addEventListener)
				{
					return;
				}

				if (typeof names == 'string')
				{
					names = [names];
				}

				$.each(names, function (i, name)
				{
					var handler = function (e)
					{
						e = $.event.fix(e);

						return $.event.dispatch.call(this, e);
					};

					special[name] = special[name] || {};

					if (special[name].setup || special[name].teardown)
					{
						return;
					}

					$.extend(special[name], {
						setup: function()
						{
							this.addEventListener(name, handler, true);
						},
						teardown: function()
						{
							this.removeEventListener(name, handler, true);
						}
					});
				});
			};
		})();
	})();

	$.fn.extend(
	{
		/**
		 * Allows an element to respond to an event fired by a parent element, such as the containing tab being un-hidden
		 *
		 * @param eventType
		 * @param callback
		 * @param once  bool    True if you want this to execute once only
		 * @returns {onWithin}
		 */
		onWithin: function(eventType, callback, once)
		{
			var $this = this;

			$(document).on(eventType, function(e)
			{
				if ($(e.target).has($this).length)
				{
					if (once)
					{
						$(document).off(e);
					}
					callback(e);
				}
			});

			return this;
		},

		oneWithin: function(eventType, callback)
		{
			return this.onWithin(eventType, callback, true);
		},

		onPassive: function(eventType, callback)
		{
			if (typeof eventType == 'object')
			{
				for (var type in eventType)
				{
					this.onPassive(type, eventType[type]);
				}
				return this;
			}

			if (typeof eventType != 'string' || typeof callback != 'function')
			{
				console.warn('$.onPassive failure for %s.on%s, check parameters', this.get(0), eventType);
				return this;
			}

			if (eventType.indexOf('.') !== -1)
			{
				console.warn('$.onPassive does not support namespaced events %s.on%s', this.get(0), eventType);
				return false;
			}

			if (XF.Feature.has('passiveeventlisteners'))
			{
				this.get(0).addEventListener(eventType, callback, { passive: true });
				return this;
			}
			else
			{
				this.get(0).addEventListener(eventType, callback);
				return this;
			}
		},

		offPassive: function(eventType, callback)
		{
			this.get(0).removeEventListener(eventType, callback);
			return this;
		},

		onPointer: function(events, callback)
		{
			if ($.isPlainObject(events))
			{
				for (var k in events)
				{
					if (events.hasOwnProperty(k))
					{
						this.onPointer(k, events[k]);
					}
				}

				return this;
			}

			if (typeof events === 'string')
			{
				events = events.split(/\s+/);
			}

			var t = this,
				dataKey = 'xf-pointer-type',
				cb = function(e)
				{
					var $this = $(this),
						type = $this.data(dataKey);

					e.xfPointerType = e.pointerType || type || '';

					callback(e);
				};

			events.forEach(function(event)
			{
				t.on(event, cb);
			});

			var watchEventName = 'pointerdown.pointer-watcher';

			this.off(watchEventName).on(watchEventName, function(e)
			{
				$(this).data(dataKey, e.pointerType);
			});

			return this;
		},

		xfFadeDown: function(speed, callback)
		{
			this.filter(':hidden').hide().css('opacity', 0);

			speed = speed || XF.config.speed.normal;

			this.find('.is-sticky').addClass('was-sticky').removeClass('is-sticky');

			this.animate(
				{
					opacity: 1,
					height: 'show',
					marginTop: 'show',
					marginBottom: 'show',
					paddingTop: 'show',
					paddingBottom: 'show'
				},
				{
					duration: speed,
					easing: 'swing',
					complete: function()
					{
						$(this).find('.was-sticky').addClass('is-sticky').removeClass('was-sticky');
						if (callback)
						{
							callback();
						}
						XF.layoutChange();
					}
				}
			);

			return this;
		},

		xfFadeUp: function(speed, callback)
		{
			speed = speed || XF.config.speed.normal;

			this.find('.is-sticky').addClass('was-sticky').removeClass('is-sticky');

			this.animate(
				{
					opacity: 0,
					height: 'hide',
					marginTop: 'hide',
					marginBottom: 'hide',
					paddingTop: 'hide',
					paddingBottom: 'hide'
				},
				{
					duration: speed,
					easing: 'swing',
					complete: function()
					{
						$(this).find('.was-sticky').addClass('is-sticky').removeClass('was-sticky');
						if (callback)
						{
							callback();
						}
						XF.layoutChange();
					}
				}
			);

			return this;
		},

		xfUniqueId: function()
		{
			var id = this.attr('id');
			if (!id)
			{
				this.attr('id', 'js-XFUniqueId' + XF.getUniqueCounter());
			}

			return id;
		},

		findExtended: function(selector)
		{
			var match;
			if (typeof selector === 'string' && (match = selector.match(/^<([^|]+)(\|([\s\S]+))?$/)))
			{
				var lookUp = $.trim(match[1]),
					innerMatch,
					i,
					relativeLookup = {
						up: 'parent',
						next: 'next',
						prev: 'prev'
					},
					move,
					$newBase = this;

				do
				{
					if (innerMatch = lookUp.match(/^:(up|next|prev)(\((\d+)\))?/))
					{
						if (!innerMatch[2])
						{
							innerMatch[3] = 1;
						}

						move = relativeLookup[innerMatch[1]];

						for (i = 0; i < innerMatch[3]; i++)
						{
							$newBase = $newBase[move]();
							if (!$newBase)
							{
								$newBase = $();
							}
						}

						lookUp = $.trim(lookUp.substr(innerMatch[0].length));
					}
				}
				while (innerMatch);

				if (lookUp.length)
				{
					$newBase = $newBase.closest(lookUp);
				}

				if (!$newBase.length)
				{
					$newBase = $();
				}

				selector = match[2] ? $.trim(match[3]) : '';

				if (selector.length)
				{
					return $newBase.find(selector);
				}
				else
				{
					return $newBase;
				}
			}

			return this._find(selector);
		},

		dimensions: function(outer, outerWithMargin)
		{
			var dims = this.offset(),
				dimensions = {
					top: dims.top,
					left: dims.left
					// offset may return a ClientRect object which is read only
				};

			outerWithMargin = outerWithMargin ? true : false;

			dimensions.width = outer ? this.outerWidth(outerWithMargin) : this.width();
			dimensions.height = outer ? this.outerHeight(outerWithMargin) : this.height();
			dimensions.right = dimensions.left + dimensions.width;
			dimensions.bottom = dimensions.top + dimensions.height;

			return dimensions;
		},

		viewport: function(outer, outerWithMargin)
		{
			var vp = {
				width: outer ? this.outerWidth(outerWithMargin) : this.width(),
				height: outer ? this.outerHeight(outerWithMargin) : this.height(),
				left: this.scrollLeft(),
				top: this.scrollTop(),
				right: 0,
				bottom: 0,
				docWidth: $(document).width(),
				docHeight: $(document).height()
			};

			vp.bottom = vp.top + vp.height;
			vp.right = vp.left + vp.width;

			return vp;
		},

		hasFixableParent: function()
		{
			var fixableParent = false;

			this.parents().each(function()
			{
				var $el = $(this);

				switch ($el.css('position'))
				{
					case 'fixed':
					case 'sticky':
					case '-webkit-sticky':
						fixableParent = $el;
						return false;
				}

				if ($el.data('sticky_kit'))
				{
					fixableParent = $el;
					return false;
				}
			});

			return fixableParent;
		},

		hasFixedParent: function()
		{
			var fixedParent = false;

			this.parents().each(function()
			{
				var $el = $(this);

				switch ($el.css('position'))
				{
					case 'fixed':
					{
						fixedParent = $el;
						return false;
					}

					case 'sticky':
					case '-webkit-sticky':
					{
						var elDimensions = $el.dimensions(true),
							viewport = $(window).viewport(),
							stickyTop = $el.css('top'),
							stickyBottom = $el.css('bottom'),
							edgeDiff;

						// If an element is sticky, it is 'stuck' when its "interesting" edge is exactly at its offset position.
						// This code currently supports only sticky/top and sticky/bottom

						if (stickyTop !== 'auto')
						{
							// iOS seems to have half a pixel inconsistency in reporting, so give a little leeway
							edgeDiff = (elDimensions.top - viewport.top) - parseInt(stickyTop, 10);
							if (edgeDiff <= 0.5 && edgeDiff >= -0.5)
							{
								fixedParent = $el;
								return false;
							}
						}

						if (stickyBottom !== 'auto')
						{
							// iOS seems to have half a pixel inconsistency in reporting, so give a little leeway
							edgeDiff = (elDimensions.bottom - viewport.bottom) - parseInt(stickyBottom, 10);
							if (edgeDiff <= 0.5 && edgeDiff >= -0.5)
							{
								fixedParent = $el;
								return false;
							}
						}
					}
				}
			});

			return fixedParent;
		},

		onTransitionEnd: function(duration, callback)
		{
			var called = false,
				$el = this,
				f = function()
				{
					if (called)
					{
						return;
					}

					called = true;
					return callback.apply(this, arguments);
				};

			this.one('xfTransitionEnd', f);
			setTimeout(function()
			{
				if (!called)
				{
					$el.trigger('xfTransitionEnd');
				}
			}, duration + 10);

			return this;
		},

		autofocus: function()
		{
			var $input = $(this);

			if (XF.isIOS())
			{
				if (!$input.is(':focus'))
				{
					$input.addClass('is-focused');
					$input.on('blur', function()
					{
						$input.removeClass('is-focused');
					});
				}
			}
			else
			{
				$input.focus();
			}

			return this;
		},

		normalizedScrollLeft: function(newLeft)
		{
			var type = $.support.scrollLeftType;

			if (typeof newLeft !== 'undefined')
			{
				for (var i = 0; i < this.length; i++)
				{
					(function(el, newValue)
					{
						switch (type)
						{
							case 'negative':
								newValue = newValue > 0 ? -newValue : 0;
								break;

							case 'inverted':
								newValue = el.scrollWidth - el.offsetWidth - newValue;

							// otherwise don't need to change
						}

						el.scrollLeft = newValue;
					})(this[i], newLeft);
				}

				return this;
			}

			var el = this[0];
			if (!el)
			{
				return 0;
			}

			var scrollLeft = el.scrollLeft;

			switch (type)
			{
				case 'negative':
					return scrollLeft < 0 ? -scrollLeft : 0;

				case 'inverted':
					var calc = el.scrollWidth - scrollLeft - el.offsetWidth;
					return (calc < 0.5 ? 0 : calc); // avoid rounding issues

				case 'normal':
				default:
					return scrollLeft;
			}
		},

		/**
		 * Attempts to focus the next focusable element
		 *
		 * @returns jQuery The next focusable element
		 */
		focusNext: function()
		{
			var $focusable = $('input:not([type="hidden"]), select, textarea, a, button').filter(':visible'),

				$next = $focusable.eq($focusable.index(this) + 1).focus();

			return $next;
		}
	});

	(function()
	{
		function getCssTransitionDuration($el)
		{
			if (!$.support.transition)
			{
				return 0;
			}

			var el = $el[0];
			if (!el || !(el instanceof window.Element))
			{
				return 0;
			}

			var durationCss = $el.css('transition-duration'), duration = 0;
			if (durationCss && durationCss.match(/^(\+|-|)([0-9]*\.[0-9]+|[0-9]+)(ms|s)/i))
			{
				duration = (RegExp.$1 == '-' ? -1 : 1) * parseFloat(RegExp.$2) * (RegExp.$3.toLowerCase() == 'ms' ? 1 : 1000);
			}

			return duration;
		}

		function getClassDiff($el, checkClassList, getMissing)
		{
			var diff = [];

			if ($.isFunction(checkClassList))
			{
				checkClassList = checkClassList.call($el[0], 0, $el[0].className);
			}

			var checkClasses = $.trim(checkClassList).split(/\s+/),
				classes = " " + $el[0].className + " ",
				present;
			for (var i = 0; i < checkClasses.length; i++)
			{
				present = (classes.indexOf(" " + checkClasses[i] + " ") >= 0);
				if ((present && !getMissing) || (!present && getMissing))
				{
					diff.push(checkClasses[i]);
				}
			}

			return diff.join(" ");
		}

		var mappedAttrs = {
			height: ['height', 'padding-top', 'padding-bottom', 'margin-top', 'margin-bottom', 'border-top-width', 'border-bottom-width'],
			width: ['width', 'padding-left', 'padding-right', 'margin-left', 'margin-right', 'border-right-width', 'border-left-width']
		};

		function adjustClasses($el, isAdding, className, onTransitionEnd, instant)
		{
			var duration = instant ? 0 : getCssTransitionDuration($el),
				mainFunc = isAdding ? 'addClass' : 'removeClass',
				inverseFunc = isAdding ? 'removeClass' : 'addClass',
				getMissing = isAdding ? true : false,
				adjustClasses = getClassDiff($el, className, getMissing),
				el = $el[0],
				transitioningClass = 'is-transitioning',
				transitionEndFakeCall = function()
				{
					if (onTransitionEnd)
					{
						setTimeout(function()
						{
							onTransitionEnd.call(el, $.Event('xfTransitionEnd'));
						}, 0);
					}
				};

			if (!adjustClasses.length)
			{
				transitionEndFakeCall();
				return;
			}

			if (duration <= 0)
			{
				$el[mainFunc](adjustClasses);
				transitionEndFakeCall();
				return;
			}

			if ($el.hasClass(transitioningClass))
			{
				$el.trigger('xfTransitionEnd');
			}

			$el.addClass(transitioningClass);

			if ($el.css('transition-property').match(/(^|\s|,)-xf-(width|height)($|\s|,)/))
			{
				var attr = RegExp.$2,
					relatedAttrs = mappedAttrs[attr],
					curCssValues = $el.css(relatedAttrs),
					curCssValue = curCssValues[attr],
					storeCurStyle = "transition." + attr,
					curStyleValues = $el.data(storeCurStyle),
					style = el.style,
					previousTransition = style['transition']
						|| style['-webkit-transition']
						|| style['-moz-transition']
						|| style['-o-transition']
						|| '',
					i;

				if (curStyleValues === undefined)
				{
					curStyleValues = {};
					for (i = 0; i < relatedAttrs.length; i++)
					{
						curStyleValues[relatedAttrs[i]] = style[relatedAttrs[i]] || '';
					}
				}

				if ($el[attr]() == 0)
				{
					curCssValue = '0';

					for (i in curCssValues)
					{
						if (curCssValues.hasOwnProperty(i))
						{
							curCssValues[i] = '0';
						}
					}
				}

				$el.data(storeCurStyle, curStyleValues)
					.css('transition', 'none')
					[mainFunc](adjustClasses);

				var newCssValues = $el.css(relatedAttrs),
					newCssValue = newCssValues[attr];

				if ($el[attr]() == 0)
				{
					newCssValue = '0';
					for (i in newCssValues)
					{
						if (newCssValues.hasOwnProperty(i))
						{
							newCssValues[i] = '0';
						}
					}
				}

				$el[inverseFunc](adjustClasses);

				if (curCssValue != newCssValue)
				{
					var originalCallback = onTransitionEnd;

					$el.css(curCssValues);
					el.offsetWidth; // this is needed to force a redraw; must be before the transition restore line
					$el.css('transition', previousTransition)
						.css(newCssValues);

					onTransitionEnd = function()
					{
						$el.css($el.data(storeCurStyle))
							.removeData(storeCurStyle);

						if (originalCallback)
						{
							originalCallback.apply(this, arguments);
						}
					}
				}
				else
				{
					$el.css('transition', previousTransition);
				}
			}

			$el.onTransitionEnd(duration, function()
			{
				$el.removeClass(transitioningClass);

				if (onTransitionEnd)
				{
					onTransitionEnd.apply(this, arguments);
				}
			});
			$el[mainFunc](className);
		}

		$.fn.addClassTransitioned = function(className, onTransitionEnd, instant)
		{
			var len = this.length;
			for (var i = 0; i < len; i++)
			{
				adjustClasses($(this[i]), true, className, onTransitionEnd, instant)
			}

			return this;
		};

		$.fn.removeClassTransitioned = function(className, onTransitionEnd, instant)
		{
			var len = this.length;
			for (var i = 0; i < len; i++)
			{
				adjustClasses($(this[i]), false, className, onTransitionEnd, instant)
			}

			return this;
		};

		$.fn.toggleClassTransitioned = function(className, state, onTransitionEnd, instant)
		{
			if (typeof state !== 'boolean' && typeof onTransitionEnd === 'undefined')
			{
				onTransitionEnd = state;
				state = null;
			}

			var useState = (typeof state === 'boolean'),
				len = this.length;

			for (var i = 0; i < len; i++)
			{
				var $el = $(this[i]),
					add;

				if (useState)
				{
					add = state;
				}
				else
				{
					add = $el.hasClass(className) ? false : true;
				}

				adjustClasses($el, add, className, onTransitionEnd, instant);
			}

			return this;
		};
	})();

	// ################################# BASE HELPERS ############################################

	$.extend(XF, {
		config: {
			userId: null,
			enablePush: false,
			skipServiceWorkerRegistration: false,
			skipPushNotificationCta: false,
			pushAppServerKey: null,
			csrf: $('html').data('csrf'),
			time: {
				now: 0,
				today: 0,
				todayDow: 0
			},
			cookie: {
				path: '/',
				domain: '',
				prefix: 'xf_'
			},
			url: {
				fullBase: '/',
				basePath: '/',
				css: '',
				keepAlive: ''
			},
			css: {},
			js: {},
			jsState: {},
			speed: {
				xxfast: 50,
				xfast: 100,
				fast: 200,
				normal: 400,
				slow: 600
			},
			job: {
				manualUrl: ''
			},
			borderSizeFeature: '3px',
			fontAwesomeWeight: 'r',
			enableRtnProtect: true,
			enableFormSubmitSticky: true,
			visitorCounts: {
				conversations_unread: 0,
				alerts_unread: 0,
				title_count: false,
				icon_indicator: false
			},
			uploadMaxFilesize: null,
			allowedVideoExtensions: [],
			shortcodeToEmoji: true,
			publicMetadataLogoUrl: '',
			publicPushBadgeUrl: ''
		},

		debug: {
			disableAjaxSubmit: false
		},

		counter: 1,

		pageDisplayTime: null,

		phrases: {},

		getApp: function()
		{
			return $('html').data('app') || null;
		},

		getKeyboardInputs: function()
		{
			return 'input:not([type=radio], [type=checkbox], [type=submit], [type=reset]), textarea';
		},

		onPageLoad: function()
		{
			$(document).trigger('xf:page-load-start');

			XF.NavDeviceWatcher.initialize();
			XF.ActionIndicator.initialize();
			XF.DynamicDate.initialize();
			XF.KeepAlive.initialize();
			XF.LinkWatcher.initLinkProxy();
			XF.LinkWatcher.initExternalWatcher();
			XF.NoticeWatcher.initialize();
			XF.BbBlockExpand.watch();
			XF.ScrollButtons.initialize();
			XF.KeyboardShortcuts.initialize();
			XF.FormInputValidation.initialize();
			XF.Push.initialize();
			XF.IgnoreWatcher.initializeHash();

			XF.config.jsState = XF.applyJsState({}, XF.config.jsState);

			XF.activate(document);

			$(document).on('ajax:complete', function(e, xhr, status)
			{
				var data = xhr.responseJSON;
				if (!data)
				{
					return;
				}

				if (data.visitor)
				{
					XF.updateVisitorCounts(data.visitor, true);
				}
			});

			$(document).on('ajax:before-success', function(e, data, status, xhr)
			{
				var data = xhr.responseJSON;
				if (!data)
				{
					return;
				}

				if (data && data.job)
				{
					var job = data.job;
					if (job.manual)
					{
						XF.JobRunner.runManual(job.manual);
					}

					if (job.autoBlocking)
					{
						XF.JobRunner.runAutoBlocking(job.autoBlocking, job.autoBlockingMessage);
					}
					else if (job.auto)
					{
						setTimeout(XF.JobRunner.runAuto, 0);
					}
				}
			});

			$(document).on('keyup', 'a:not([href])', function(e)
			{
				if (e.key == 'Enter')
				{
					$(e.currentTarget).click();
				}
			});

			if ($('html[data-run-jobs]').length)
			{
				setTimeout(XF.JobRunner.runAuto, 100);
			}

			XF.updateVisitorCountsOnLoad(XF.config.visitorCounts);

			XF.CrossTab.on('visitorCounts', function(counts)
			{
				XF.updateVisitorCounts(counts, false);
			});

			XF.pageLoadScrollFix();

			setTimeout(function()
			{
				$('[data-load-auto-click]').first().click();
			}, 100);

			$(document).trigger('xf:page-load-complete');
		},

		addExtraPhrases: function(el)
		{
			$(el).find('script.js-extraPhrases').each(function()
			{
				var $script = $(this),
					phrases;

				try
				{
					phrases = $.parseJSON($script.html()) || {};
					$.extend(XF.phrases, phrases);
				}
				catch (e)
				{
					console.error(e);
				}

				$script.remove();
			});
		},

		phrase: function(name, vars, fallback)
		{
			var phrase = XF.phrases[name];
			if (vars)
			{
				phrase = XF.stringTranslate(phrase, vars);
			}
			return phrase || fallback || name;
		},

		_isRtl: null,

		isRtl: function()
		{
			if (XF._isRtl === null)
			{
				var dir = $('html').attr('dir');
				XF._isRtl = (dir && dir.toUpperCase() == 'RTL');
			}
			return XF._isRtl;
		},

		rtlFlipKeyword: function(keyword)
		{
			if (!XF.isRtl())
			{
				return keyword;
			}

			var lower  = keyword.toLowerCase();
			switch (lower)
			{
				case 'left': return 'right';
				case 'right': return 'left';
				default: return keyword;
			}
		},

		isMac: function()
		{
			return navigator.userAgent.indexOf('Mac OS') != -1;
		},

		isIOS: function()
		{
			return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
		},

		isIE: function()
		{
			var ua = navigator.userAgent;
			return (ua.indexOf('MSIE ') > 0 || ua.indexOf('Trident/') > 0);
		},

		log: function()
		{
			if (!console.log || !console.log.apply)
			{
				return;
			}

			console.log.apply(console, arguments);
		},

		findRelativeIf: function(selector, $base)
		{
			if (!selector)
			{
				throw new Error('No selector provided');
			}

			var match;
			if (match = selector.match(/^(<|>|\|)/))
			{
				if (match[1] == '<')
				{
					return $base.findExtended(selector);
				}

				if (match[1] == '|')
				{
					selector = selector.substr(1);
				}

				return $base.find(selector);
			}
			else
			{
				return $(selector);
			}
		},

		isElementVisible: function($el)
		{
			var el = $el[0],
				rect = el.getBoundingClientRect();

			return (
				rect.top >= 0
				&& rect.left >= 0
				&& rect.bottom <= $(window).height()
				&& rect.right <= $(window).width()
			);
		},

		/**
		 * Simple function to be run whenever we change the page layout with JS,
		 * to trigger recalculation of JS-positioned elements
		 * such as sticky_kit items
		 */
		layoutChange: function()
		{
			if (!XF._layoutChangeTriggered)
			{
				XF._layoutChangeTriggered = true;
				setTimeout(function()
				{
					XF._layoutChangeTriggered = false;

					$(document.body)
						.trigger('sticky_kit:recalc')
						.trigger('xf:layout');
				}, 0);
			}
		},

		_layoutChangeTriggered: false,

		updateAvatars: function(userId, newAvatars, includeEditor)
		{
			$('.avatar').each(function()
			{
				var $avatarContainer = $(this),
					$avatar = $avatarContainer.find('img, span').first(),
					classPrefix = 'avatar-u' + userId + '-',
					$update = $avatarContainer.hasClass('avatar--updateLink')
						? $avatarContainer.find('.avatar-update')
						: null,
					$newAvatar;

				if (!includeEditor && $avatar.hasClass('.js-croppedAvatar'))
				{
					return;
				}

				if ($avatar.is('[class^="' + classPrefix + '"]'))
				{
					if ($avatar.hasClass(classPrefix + 's'))
					{
						$newAvatar = $(newAvatars['s']);
					}
					else if ($avatar.hasClass(classPrefix + 'm'))
					{
						$newAvatar = $(newAvatars['m']);
					}
					else if ($avatar.hasClass(classPrefix + 'l'))
					{
						$newAvatar = $(newAvatars['l']);
					}
					else if ($avatar.hasClass(classPrefix + 'o'))
					{
						$newAvatar = $(newAvatars['o']);
					}
					else
					{
						return;
					}

					$avatarContainer.html($newAvatar.html());

					if ($newAvatar.hasClass('avatar--default'))
					{
						$avatarContainer.addClass('avatar--default');
						if ($newAvatar.hasClass('avatar--default--dynamic'))
						{
							$avatarContainer.addClass('avatar--default--dynamic');
						}
						else if ($newAvatar.hasClass('avatar--default--text'))
						{
							$avatarContainer.addClass('avatar--default--text');
						}
						else if ($newAvatar.hasClass('avatar--default--image'))
						{
							$avatarContainer.addClass('avatar--default--image');
						}
					}
					else
					{
						$avatarContainer.removeClass('avatar--default avatar--default--dynamic avatar--default--text avatar--default--image');
					}
					$avatarContainer.attr('style', $newAvatar.attr('style'));

					if ($update)
					{
						$avatarContainer.append($update);
					}
				}
			});
		},

		updateVisitorCounts: function(visitor, isForegroundUpdate, sourceTime)
		{
			if (!visitor || XF.getApp() != 'public')
			{
				return;
			}

			XF.badgeCounterUpdate($('.js-badge--conversations'), visitor.conversations_unread);
			XF.badgeCounterUpdate($('.js-badge--alerts'), visitor.alerts_unread);

			if (XF.config.visitorCounts['title_count'])
			{
				XF.pageTitleCounterUpdate(visitor.total_unread);
			}

			if (XF.config.visitorCounts['icon_indicator'])
			{
				XF.faviconUpdate(visitor.total_unread);
			}

			if (isForegroundUpdate)
			{
				XF.CrossTab.trigger('visitorCounts', visitor);

				XF.LocalStorage.setJson('visitorCounts', {
					time: sourceTime || (Math.floor(new Date().getTime() / 1000) - 1),
					conversations_unread: visitor.conversations_unread,
					alerts_unread: visitor.alerts_unread,
					total_unread: visitor.total_unread
				});
			}

			// TODO: Stack alerts?
		},

		updateVisitorCountsOnLoad: function(visitor)
		{
			var localLoadTime = XF.getLocalLoadTime(),
				cachedData = XF.LocalStorage.getJson('visitorCounts');

			if (cachedData && cachedData.time && cachedData.time > localLoadTime)
			{
				visitor.conversations_unread = cachedData.conversations_unread;
				visitor.alerts_unread = cachedData.alerts_unread;
				visitor.total_unread = cachedData.total_unread;
			}

			XF.updateVisitorCounts(visitor, true, localLoadTime);
		},

		badgeCounterUpdate: function($badge, newCount)
		{
			if (!$badge.length)
			{
				return;
			}

			$badge.attr('data-badge', newCount);

			if (String(newCount) != '0')
			{
				$badge.addClass('badgeContainer--highlighted');
			}
			else
			{
				$badge.removeClass('badgeContainer--highlighted');
			}
		},

		pageTitleCache: '',

		pageTitleCounterUpdate: function(newCount)
		{
			var pageTitle = document.title,
				newTitle;

			if (XF.pageTitleCache === '')
			{
				XF.pageTitleCache = pageTitle;
			}

			if (pageTitle !== XF.pageTitleCache && pageTitle.charAt(0) === '(')
			{
				pageTitle = XF.pageTitleCache;
			}

			newTitle = (newCount > 0 ? '(' + newCount + ') ' : '') + pageTitle;

			if (newTitle != document.title)
			{
				document.title = newTitle;
			}
		},

		favIconAlertShown: false,

		faviconUpdate: function(newCount)
		{
			var shouldBeShown = (newCount > 0);
			if (shouldBeShown == XF.favIconAlertShown)
			{
				return;
			}

			var $favicons = $('link[rel~="icon"]');

			if (!$favicons.length)
			{
				// no favicons support
				return;
			}

			XF.favIconAlertShown = shouldBeShown;

			$favicons.each(function(i, favicon)
			{
				var $favicon = $(favicon),
					href = $favicon.attr('href'),
					originalHrefKey = 'original-href',
					originalHref = $favicon.data(originalHrefKey);

				if (newCount > 0)
				{
					if (!originalHref)
					{
						$favicon.data(originalHrefKey, href);
					}

					$('<img />')
						.on('load', function()
						{
							var updatedFaviconUrl = XF.faviconDraw(this);

							if (updatedFaviconUrl)
							{
								$favicon.attr('href', updatedFaviconUrl);
							}
						})
						.attr('src', href);
				}
				else
				{
					if (originalHref)
					{
						$favicon.attr('href', originalHref)
							.removeData(originalHrefKey);
					}
				}
			});
		},

		faviconDraw: function(image)
		{
			var w = image.naturalWidth,
				h = image.naturalHeight,
				$canvas = $('<canvas />').attr({ width: w, height: h }),
				context = $canvas[0].getContext('2d');

			var ratio = 32 / 6,
				radius = w / ratio,
				x = radius,
				y = radius,
				startAngle = 0,
				endAngle = Math.PI * 2,
				antiClockwise = false;

			context.drawImage(image, 0, 0);
			context.beginPath();
			context.arc(x, y, radius, startAngle, endAngle, antiClockwise);
			context.fillStyle = "#E03030";
			context.fill();
			context.lineWidth = w / 16;
			context.strokeStyle = "#EAEAEA";
			context.stroke();
			context.closePath();

			try
			{
				return $canvas[0].toDataURL('image/png');
			}
			catch (e)
			{
				return null;
			}
		},

		/**
		 * Attempts to convert various HTML-BB codes back into BB code
		 *
		 * @param html
		 *
		 * @returns string
		 */
		unparseBbCode: function(html)
		{
			var $div = $(document.createElement('div'));

			$div.html(html);

			// get rid of anything with this class
			$div.find('.js-noSelectToQuote').each(function()
			{
				$(this).remove();
			});

			// handle b, i, u, s
			$.each(['B', 'I', 'U', 'S'], function(i, tagName)
			{
				$div.find(tagName).each(function()
				{
					$(this).replaceWith('[' + tagName + ']' + $(this).html() + '[/' + tagName + ']');
				});
			});

			// handle quote tags as best we can
			$div.find('.bbCodeBlock--quote').each(function()
			{
				var $this = $(this),
					$quote = $this.find('.bbCodeBlock-expandContent');
				if ($quote.length)
				{
					$this.replaceWith('<div>[QUOTE]' + $quote.html() + '[/QUOTE]</div>');
				}
				else
				{
					$quote.find('.bbCodeBlock-expand').remove();
				}
			});

			// now for PHP, CODE and HTML
			$div.find('.bbCodeBlock--code').each(function()
			{
				var $this = $(this);

				if (!$this.find('.bbCodeCode'))
				{
					return true;
				}

				var	$code = $this.find('.bbCodeCode code');

				if (!$code.length)
				{
					return true;
				}

				var language = $code.attr('class').match(/language-(\S+)/)[1];

				$code.removeAttr('class');

				$this.replaceWith($code.first().attr('data-language', language || 'none'));
			});

			// handle [URL unfurl=true] tags
			$div.find('.bbCodeBlock--unfurl').each(function()
			{
				var url = $(this).data('url');
				$(this).replaceWith('[URL unfurl=true]' + url + '[/URL]');
			});

			// now alignment tags
			$div.find('div[style*="text-align"]').each(function()
			{
				var align = $(this).css('text-align').toUpperCase();

				$(this).replaceWith('[' + align + ']' + $(this).html() + '[/' + align + ']');
			});

			// and finally, spoilers...

			$div.find('.bbCodeSpoiler').each(function()
			{
				var $button, target, $spoilerTitle, spoilerTitle = '', spoilerText;

				// find the button and the target
				$button = $(this).find('.bbCodeSpoiler-button');
				if ($button.length)
				{
					spoilerText = $(this).find('.bbCodeSpoiler-content').html();
					$spoilerTitle = $button.find('.bbCodeSpoiler-button-title');

					if ($spoilerTitle.length)
					{
						spoilerTitle = '="' + $spoilerTitle.text() + '"';
					}

					$(this).replaceWith('[SPOILER' + spoilerTitle + ']' + spoilerText + '[/SPOILER]');
				}
			});

			$div.find('.bbCodeInlineSpoiler').each(function()
			{
				var spoilerText = $(this).html();
				$(this).replaceWith('[ISPOILER]' + spoilerText + '[/ISPOILER]');
			});

			return $div.html();
		},

		hideOverlays: function()
		{
			$.each(XF.Overlay.cache, function(id, overlay)
			{
				overlay.hide();
			});
		},

		hideTooltips: function()
		{
			$.each(XF.TooltipTrigger.cache, function(id, trigger)
			{
				trigger.hide();
			});
		},

		hideParentOverlay: function($child)
		{
			var $overlayContainer = $child.closest('.overlay-container');
			if ($overlayContainer.length && $overlayContainer.data('overlay'))
			{
				$overlayContainer.data('overlay').hide();
			}
		},

		loadedScripts: {},

		/**
		 * Given a URL, load it (if not already loaded)
		 * before running a callback function on success
		 *
		 * @param url
		 * @param successCallback
		 */
		loadScript: function(url, successCallback)
		{
			if (XF.loadedScripts.hasOwnProperty(url))
			{
				return false;
			}

			XF.loadedScripts[url] = true;

			return $.ajax({
				url: url,
				dataType: "script",
				cache: true,
				global: false,
				success: successCallback
			});
		},

		/**
		 * Given an array of URLs, load them all (if not already loaded)
		 * before running a callback function on complete (success or error).
		 *
		 * In the absolute majority of browsers, this will execute the loaded scripts in the order provided.
		 *
		 * @param urls
		 * @param completeCallback
		 */
		loadScripts: function(urls, completeCallback)
		{
			var firstScript = document.scripts[0],
				useAsync = 'async' in firstScript,
				useReadyState = firstScript.readyState,
				head = document.head,
				toLoad = 0,
				url,
				pendingScripts = [];

			function loaded()
			{
				toLoad--;
				if (toLoad === 0 && completeCallback)
				{
					completeCallback();
				}
			}

			function stateChange()
			{
				var pendingScript;
				while (pendingScripts[0] && pendingScripts[0].readyState == 'loaded')
				{
					pendingScript = pendingScripts.shift();
					pendingScript.onreadystatechange = null;
					pendingScript.onerror = null;
					head.appendChild(pendingScript);

					loaded();
				}
			}

			for (var i in urls)
			{
				if (!urls.hasOwnProperty(i))
				{
					continue;
				}

				url = urls[i];

				if (XF.loadedScripts[url])
				{
					continue;
				}

				XF.loadedScripts[url] = true;
				toLoad++;

				if (useAsync)
				{
					// pretty much any modern browser
					(function(url)
					{
						var $script = $('<script>').prop({
							src: url,
							async: false
						});
						$script.on('load error', function(e)
						{
							$script.off('load error');
							loaded();
						});

						head.appendChild($script[0]);
					})(url);
				}
				else if (useReadyState)
				{
					// IE 9
					(function(url)
					{
						var script = document.createElement('script');
						pendingScripts.push(script);

						script.onreadystatechange = stateChange;
						script.onerror = function()
						{
							script.onreadystatechange = null;
							script.onerror = null;
							loaded();
						};

						script.src = url;
					})(url);
				}
				else
				{
					// should very rarely be used
					$.ajax({
						url: url,
						dataType: "script",
						cache: true,
						global: false
					}).always(loaded);
				}
			}

			if (!toLoad && completeCallback)
			{
				completeCallback();
			}
		},

		ajax: function(method, url, data, successCallback, options)
		{
			if (typeof data == 'function' && successCallback === undefined)
			{
				successCallback = data;
				data = {};
			}

			data = data || {};

			var useDefaultSuccess = true,
				useDefaultSuccessError = true,
				useError = true;
			if (options)
			{
				if (options.skipDefault)
				{
					useDefaultSuccess = false;
					useDefaultSuccessError = false;
					delete options.skipDefault;
				}
				if (options.skipDefaultSuccessError)
				{
					useDefaultSuccessError = false;
					delete options.skipDefaultSuccessError;
				}
				if (options.skipDefaultSuccess)
				{
					useDefaultSuccess = false;
					delete options.skipDefaultSuccess;
				}
				if (options.skipError)
				{
					useError = false;
					delete options.skipError;
				}
			}

			var onBeforeSend = function(xhr, settings)
			{
				$(document).trigger('ajax:send', [xhr, settings]);
			};

			var onSuccess = function(data, status, xhr)
			{
				$(document).trigger('ajax:before-success', [data, status, xhr]);

				if (useDefaultSuccessError && XF.defaultAjaxSuccessError(data, status, xhr))
				{
					// this processed successfully, don't continue
					return;
				}
				if (useDefaultSuccess && XF.defaultAjaxSuccess(data, status, xhr))
				{
					// this processed successfully, don't continue
					return;
				}

				if (successCallback)
				{
					successCallback(data, status, xhr);
				}
			};

			var onError = function(xhr, status, exception)
			{
				if (!xhr.readyState)
				{
					return;
				}

				try
				{
					var json = $.parseJSON(xhr.responseText);
					onSuccess(json, '', xhr);
				}
				catch (e)
				{
					XF.defaultAjaxError(xhr, status, exception);
				}
			};

			var onComplete = function(xhr, status)
			{
				$(document).trigger('ajax:complete', [xhr, status]);
			};

			data = XF.dataPush(data, '_xfRequestUri', window.location.pathname + window.location.search);
			data = XF.dataPush(data, '_xfWithData', 1);
			if (XF.config.csrf)
			{
				data = XF.dataPush(data, '_xfToken', XF.config.csrf);
			}

			var isFormDataObject = (window.FormData && data instanceof FormData),
				ajax = $.extend(true, {
					cache: true,
					data: data || {},
					dataType: 'json',
					beforeSend: onBeforeSend,
					error: useError ? onError : null,
					success: onSuccess,
					complete: onComplete,
					timeout: method === 'get' ? 30000 : 60000,
					type: method,
					url: url,
					processData: isFormDataObject ? false : true
				}, options);

			if (isFormDataObject)
			{
				ajax['contentType'] = false;
			}

			switch (ajax.dataType)
			{
				case 'html':
				case 'json':
				case 'xml':
					ajax.data = XF.dataPush(ajax.data, '_xfResponseType', ajax.dataType);
			}

			if (ajax.dataType != 'json')
			{
				useDefaultSuccess = false;
			}

			return $.ajax(ajax);
		},

		dataPush: function(data, key, value)
		{
			if (!data || typeof data == 'string')
			{
				// data is empty, or a url string - &name=value
				data = String(data);
				data += '&' + encodeURIComponent(key) + '=' + encodeURIComponent(value);
			}
			else if (data[0] !== undefined)
			{
				// data is a numerically-keyed array of name/value pairs
				data.push({ name: key, value: value });
			}
			else if (window.FormData && data instanceof FormData)
			{
				// data is a FormData object
				data.append(key, value);
			}
			else
			{
				// data is an object with a single set of name & value properties
				data[key] = value;
			}

			return data;
		},

		defaultAjaxSuccessError: function(data, status, xhr)
		{
			if (typeof data != 'object')
			{
				XF.alert('Response was not JSON.');
				return true;
			}

			if (data.html && data.html.templateErrors)
			{
				var templateErrorStr = 'Errors were triggered when rendering this template:';
				if (data.html.templateErrorDetails)
				{
					templateErrorStr += '\n* ' + data.html.templateErrorDetails.join('\n* ');
				}
				console.error(templateErrorStr)
			}

			if (data.errorHtml)
			{
				XF.setupHtmlInsert(data.errorHtml, function($html, container)
				{
					var title = container.h1 || container.title || XF.phrase('oops_we_ran_into_some_problems');
					XF.overlayMessage(title, $html);
				});
				return true;
			}

			if (data.errors)
			{
				XF.alert(data.errors);
				return true;
			}

			if (data.exception)
			{
				XF.alert(data.exception);
				return true;
			}

			return false;
		},

		defaultAjaxSuccess: function(data, status, xhr)
		{
			if (data && data.status == 'ok' && data.message)
			{
				XF.flashMessage(data.message, 3000);
				// let the real callback still run
			}

			return false;
		},

		defaultAjaxError: function(xhr, error, exception)
		{
			switch (error)
			{
				case 'abort':
					return;

				case 'timeout':
					XF.alert(
						XF.phrase('server_did_not_respond_in_time_try_again')
					);
					return;

				case 'notmodified':
				case 'error':
					if (!xhr || !xhr.responseText)
					{
						// this is likely a user cancellation, so just return
						return;
					}
					break;
			}

			console.error('PHP: ' + xhr.responseText);
			XF.alert(XF.phrase('oops_we_ran_into_some_problems_more_details_console'));
		},

		activate: function(el)
		{
			XF.addExtraPhrases(el);
			XF.IgnoreWatcher.refresh(el);
			XF.Element.initialize(el);
			XF.DynamicDate.refresh(el);
			XF.BbBlockExpand.checkSizing(el);
			XF.UnfurlLoader.activateContainer(el);
			XF.KeyboardShortcuts.initializeElements(el);
			XF.FormInputValidation.initializeElements(el);

			var domEl = (el instanceof $ ? el.get(0) : el);

			if (window.FB)
			{
				setTimeout(function()
				{
					FB.XFBML.parse(domEl);
				}, 0);
			}

			$(document).trigger('xf:reinit', [el]);
		},

		getDefaultFormData: function($form, $submitButton, jsonName, jsonOptIn)
		{
			var formData,
				submitName;

			if ($submitButton && $submitButton.length && $submitButton.attr('name'))
			{
				submitName = $submitButton.attr('name');
			}

			if (jsonName && $form.attr('enctype') === 'multipart/form-data')
			{
				console.error('JSON serialized forms do not support the file upload-style enctype.');
			}

			// JSON serialization doesn't support uploads, so form data isn't needed
			if (window.FormData && !jsonName)
			{
				formData = new FormData($form[0]);
				if (submitName)
				{
					formData.append(submitName, $submitButton.attr('value'));
				}

				// note: this is to workaround a Safari/iOS bug which falls over on empty file inputs
				$form.find('input[type="file"]').each(function()
				{
					var $input = $(this),
						files = $input.prop('files');

					if (typeof files !== undefined && files.length === 0)
					{
						try
						{
							formData.delete($input.attr('name'));
						}
						catch (e) {}
					}
				});
			}
			else
			{
				if (jsonName)
				{
					var $els = $form.is('form') ? $($form[0].elements) : $form,
						jsonOptInRegex,
						jsonEls = [],
						regularEls = [],
						inputs;

					if (jsonOptIn)
					{
						if (typeof jsonOptIn === 'string')
						{
							jsonOptIn = jsonOptIn.split(',');
						}

						var jsonOptInRegexFields = [];
						$.each(jsonOptIn, function(i, v)
						{
							if (typeof i === 'number')
							{
								jsonOptInRegexFields.push(XF.regexQuote($.trim(v)));
							}
							else
							{
								jsonOptInRegexFields.push(XF.regexQuote($.trim(i)));
							}
						});
						if (jsonOptInRegexFields.length)
						{
							jsonOptInRegex = new RegExp('^(' + jsonOptInRegexFields.join('|') + ')(\\[|$)');
						}
					}

					$els.each(function(i, el)
					{
						var name = el.name;

						if (!name || name.substring(0, 3) === '_xf')
						{
							regularEls.push(el);
							return;
						}

						if (!jsonOptInRegex || jsonOptInRegex.test(name))
						{
							jsonEls.push(el);
						}
						else
						{
							regularEls.push(el);
						}
					});

					formData = $(regularEls).serializeArray();

					inputs = $(jsonEls).serializeJSON();
					formData.unshift({
						name: jsonName,
						value: JSON.stringify(inputs)
					});
				}
				else
				{
					formData = $form.serializeArray();
				}

				if (submitName)
				{
					formData.push({
						name: submitName,
						value: $submitButton.attr('value')
					});
				}
			}

			return formData;
		},

		scriptMatchRegex: /<script([^>]*)>([\s\S]*?)<\/script>/ig,

		setupHtmlInsert: function(container, onReady, retainScripts)
		{
			if (typeof container === 'string' || container instanceof $)
			{
				container = { content: container };
			}

			if (typeof container != 'object' || !container.content)
			{
				console.error('Was not provided an object or HTML content');
				return;
			}

			var args = arguments;

			XF.Loader.load(container.js, container.css, function()
			{
				var scriptRegexMatch,
					embeddedScripts = container.jsInline || [],
					html = container.content,
					isString = typeof html == 'string',
					retainScripts = args[2] ? true : false;

				if (container.cssInline)
				{
					for (var i = 0; i < container.cssInline.length; i++)
					{
						$('<style>' + container.cssInline[i] + '</style>').appendTo('head');
					}
				}

				if (isString)
				{
					var isJs, typeMatch;

					html = $.trim(html);

					if (!retainScripts)
					{
						while (scriptRegexMatch = XF.scriptMatchRegex.exec(html))
						{
							isJs = false;
							if (typeMatch = scriptRegexMatch[1].match(/(^|\s)type=("|'|)([^"' ;]+)/))
							{
								switch (typeMatch[3].toLowerCase())
								{
									case 'text/javascript':
									case 'text/ecmascript':
									case 'application/javascript':
									case 'application/ecmascript':
										isJs = true;
										break;
								}
							}
							else
							{
								isJs = true;
							}

							if (isJs)
							{
								embeddedScripts.push(scriptRegexMatch[2]);
								html = html.replace(scriptRegexMatch[0], '');
							}
						}
					}

					// IE11 doesn't appear to find the noscript tags in the DOM
					html = html.replace(/<noscript>([\s\S]*?)<\/noscript>/ig, '');
				}

				var $html = $(isString ? $.parseHTML(html, null, retainScripts) : html);

				// fix retina images on loaded content in iOS
				if (window.devicePixelRatio >= 2 && XF.isIOS())
				{
					$html.find('img[srcset]').each(function()
					{
						$(this).prop('src', this.getAttribute('srcset').replace(/\s+2x$/i, ''));
					});
				}

				// Remove <noscript> tags to ensure they never get parsed when not needed.
				$html.find('noscript').empty().remove();

				if (onReady instanceof $)
				{
					var $target = onReady;
					onReady = function ($h)
					{
						$target.html($h);
					};
				}
				if (typeof onReady !== 'function')
				{
					console.error('onReady was not a function');
					return;
				}

				var onCompleteRun = false,
					onComplete = function(skipActivate)
					{
						if (onCompleteRun)
						{
							return;
						}
						onCompleteRun = true;

						for (var j = 0; j < embeddedScripts.length; j++)
						{
							$.globalEval(embeddedScripts[j]);
						}

						if (container.jsState)
						{
							XF.config.jsState = XF.applyJsState(XF.config.jsState, container.jsState);
						}

						if (!skipActivate)
						{
							XF.activate($html);
						}
					};

				var result = onReady($html, container, onComplete);
				if (result !== false)
				{
					onComplete();
				}
			});
		},

		alert: function(message, messageType, title, onClose)
		{
			var messageHtml = message;
			if (typeof message == 'object')
			{
				messageHtml = '<ul>';
				$.each(message, function(k, v)
				{
					messageHtml += '<li>' + v + '</li>';
				});
				messageHtml += '</ul>';
				messageHtml = '<div class="blockMessage">' + messageHtml + '</div>';
			}

			if (!messageType)
			{
				messageType = 'error';
			}

			if (!title)
			{
				switch (messageType)
				{
					case 'error':
						title = XF.phrase('oops_we_ran_into_some_problems');
						break;

					default:
						title = '';
				}
			}

			return XF.overlayMessage(title, messageHtml);
		},

		getOverlayHtml: function(content)
		{
			var $html,
				options = {
					dismissible: true,
					title: null
				};

			if ($.isPlainObject(content))
			{
				options = $.extend({}, options, content);
				if (content.html)
				{
					content = content.html;
				}
			}

			if (typeof content == 'string')
			{
				$html = $($.parseHTML(content));
			}
			else if (content instanceof $)
			{
				$html = content;
			}
			else
			{
				throw new Error('Can only create an overlay with html provided as a string or jQuery object');
			}

			if (!$html.is('.overlay'))
			{
				var title = options.title;
				if (!title)
				{
					var $header = $html.find('.overlay-title');
					if ($header.length)
					{
						title = $header.contents();
						$header.remove();
					}
				}
				if (!title)
				{
					title = XF.htmlspecialchars($('title').text());
				}

				var $bodyInsert = $html.find('.overlay-content');

				if ($bodyInsert.length)
				{
					$html = $bodyInsert;
				}

				var $overlay = $(
					'<div class="overlay" tabindex="-1">'
					+ '<div class="overlay-title"></div>'
					+ '<div class="overlay-content"></div>'
					+ '</div>'
				);
				var $title = $overlay.find('.overlay-title');

				$title.html(title);
				if (options.dismissible)
				{
					$title.prepend('<a class="overlay-titleCloser js-overlayClose" role="button" tabindex="0" aria-label="' + XF.phrase('close') + '"></a>');
				}
				$overlay.find('.overlay-content').html($html);

				$html = $overlay;
			}

			$html.appendTo('body');

			return $html;
		},

		createMultiBar: function(url, callee, onSubmit, onCancel)
		{

		},

		getMultiBarHtml: function(content)
		{
			var $html,
				options = {
					dismissible: true,
					title: null
				};

			if ($.isPlainObject(content))
			{
				options = $.extend({}, options, content);
				if (content.html)
				{
					content = content.html;
				}
			}

			if (typeof content == 'string')
			{
				$html = $($.parseHTML(content));
			}
			else if (content instanceof $)
			{
				$html = content;
			}
			else
			{
				throw new Error('Can only create an action bar with html provided as a string or jQuery object');
			}

			var $multiBar = $(
				'<div class="multiBar" tabindex="-1">'
				+ '<div class="multiBar-inner"><span>Hello there.</span></div>'
				+ '</div>'
			);

			$multiBar.find('.multiBar-inner').html($html);

			$multiBar.appendTo('body');

			return $multiBar;
		},

		overlayMessage: function(title, contentHtml)
		{
			var $html,
				formattedSelector = '.block, .blockMessage';

			if (typeof contentHtml == 'string')
			{
				$html = $($.parseHTML(contentHtml));
			}
			else if (contentHtml instanceof $)
			{
				$html = contentHtml;
			}
			else
			{
				throw new Error('Can only create an overlay with html provided as a string or jQuery object');
			}

			if (!$html.is(formattedSelector) && !$html.find(formattedSelector).length)
			{
				$html = $('<div class="blockMessage" />').html($html);
			}

			$html = XF.getOverlayHtml({
				title: title,
				html: $html
			});

			return XF.showOverlay($html, {role: 'alertdialog'});
		},

		flashMessage: function(message, timeout, onClose)
		{
			var $message = $('<div class="flashMessage"><div class="flashMessage-content"></div></div>');
			$message.find('.flashMessage-content').html(message);

			$message.appendTo('body').addClassTransitioned('is-active');
			setTimeout(function()
			{
				$message.removeClassTransitioned('is-active', function()
				{
					$message.remove();
					if (onClose)
					{
						onClose();
					}
				});
			}, Math.max(500, timeout));
		},

		htmlspecialchars: function(string)
		{
			return String(string)
				.replace(/&/g, '&amp;')
				.replace(/"/g, '&quot;')
				.replace(/</g, '&lt;')
				.replace(/>/g, '&gt;');
		},

		regexQuote: function(string)
		{
			return (string + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!<>\|\:])/g, "\\$1");
		},

		stringTranslate: function(string, pairs)
		{
			string = string.toString();
			for (var key in pairs)
			{
				if (pairs.hasOwnProperty(key))
				{
					var regex = new RegExp(XF.regexQuote(key, 'g'));
					string = string.replace(regex, pairs[key]);
				}
			}
			return string;
		},

		stringHashCode: function(str)
		{
			// adapted from http://stackoverflow.com/a/7616484/1480610
			var hash = 0, i, chr, len;

			if (str.length === 0) return hash;

			for (i = 0, len = str.length; i < len; i++)
			{
				chr   = str.charCodeAt(i);
				hash  = ((hash << 5) - hash) + chr;
				hash |= 0;
			}

			return hash;
		},

		getUniqueCounter: function()
		{
			var counter = XF.counter;
			XF.counter++;

			return counter;
		},

		canonicalizeUrl: function(url)
		{
			if (url.match(/^[a-z]+:/i))
			{
				return url;
			}

			if (url.indexOf('/') == 0)
			{
				var fullPath = XF.config.url.fullBase,
					match;
				if (match = fullPath.match(/^([a-z]+:(\/\/)?[^\/]+)\//i))
				{
					return match[1] + url;
				}

				return url;
			}

			return XF.config.url.fullBase + url;
		},

		isRedirecting: false,

		redirect: function(url)
		{
			XF.isRedirecting = true;

			if (XF.JobRunner.isBlockingJobRunning())
			{
				$(document).one('job:blocking-complete', function()
				{
					XF.redirect(url);
				});
				return false;
			}

			url = XF.canonicalizeUrl(url);

			var location = window.location;

			if (url == location.href)
			{
				location.reload(true);
			}
			else
			{
				window.location = url;

				var destParts = url.split('#'),
					srcParts = location.href.split('#');

				// on the same page except we changed the hash, because we're asking for a redirect,
				// we should explicitly reload
				if (destParts[1] && destParts[0] == srcParts[0])
				{
					location.reload(true);
				}
			}

			return true;
		},

		getAutoCompleteUrl: function()
		{
			if (XF.getApp() == 'admin')
			{
				return XF.canonicalizeUrl('admin.php?users/find')
			}
			else
			{
				return XF.canonicalizeUrl('index.php?members/find')
			}
		},

		applyDataOptions: function(options, data, finalTrusted)
		{
			var output = {}, v, vType, setValue;

			for (var i in options)
			{
				if (!options.hasOwnProperty(i))
				{
					continue;
				}

				output[i] = options[i];

				if (data.hasOwnProperty(i))
				{
					v = data[i];
					vType = typeof v;
					setValue = true;

					switch (typeof output[i])
					{
						case 'string':
							if (vType != 'string')
							{
								v = String(v);
							}
							break;

						case 'number':
							if (vType != 'number')
							{
								v = Number(v);
								if (isNaN(v))
								{
									setValue = false;
								}
							}
							break;

						case 'boolean':
							if (vType != 'boolean')
							{
								switch (v)
								{
									case 'true':
									case 'yes':
									case 'on':
									case '1':
										v = true;
										break;

									default:
										v = false;
								}
							}
					}

					if (setValue)
					{
						output[i] = v;
					}
				}
			}

			if ($.isPlainObject(finalTrusted))
			{
				output = $.extend(output, finalTrusted);
			}

			return output;
		},

		watchInputChangeDelayed: function(input, onChange, delay)
		{
			var $input = $(input),
				value = $input.val(),
				timeOut;

			delay = delay || 200;

			function onKeyup()
			{
				clearTimeout(timeOut);
				timeOut = setTimeout(function()
				{
					var newValue = $input.val();
					if (newValue != value)
					{
						value = newValue;
						onChange();
					}
				}, delay);
			}

			function onPaste()
			{
				setTimeout(function() { $input.trigger('keyup'); }, 0);
			}

			$input.onPassive(
			{
				keyup: onKeyup,
				paste: onPaste
			});
		},

		insertIntoEditor: function($container, html, text, notConstraints)
		{
			var htmlCallback = function(editor)
			{
				editor.insertContent(html);
			};

			var textCallback = function($textarea)
			{
				XF.insertIntoTextBox($textarea, text);
			};

			return XF.modifyEditorContent($container, htmlCallback, textCallback, notConstraints);
		},

		replaceEditorContent: function($container, html, text, notConstraints)
		{
			var htmlCallback = function(editor)
			{
				editor.replaceContent(html);
			};

			var textCallback = function($textarea)
			{
				XF.replaceIntoTextBox($textarea, text);
			};

			return XF.modifyEditorContent($container, htmlCallback, textCallback, notConstraints);
		},

		clearEditorContent: function($container, notConstraints)
		{
			var ret = XF.replaceEditorContent($container, '', '', notConstraints);

			$container.trigger('draft:sync');

			return ret;
		},

		modifyEditorContent: function($container, htmlCallback, textCallback, notConstraints)
		{
			var editor = XF.getEditorInContainer($container, notConstraints);
			if (!editor)
			{
				return false;
			}

			if (XF.Editor && editor instanceof XF.Editor)
			{
				if (editor.isBbCodeView())
				{
					var textarea = editor.ed.bbCode.getTextArea();
					textCallback(textarea);
					textarea.trigger('autosize');
				}
				else
				{
					htmlCallback(editor);
				}
				return true;
			}

			if (editor instanceof $ && editor.is('textarea'))
			{
				textCallback(editor);
				editor.trigger('autosize');
				return true;
			}

			return false;
		},

		getEditorInContainer: function($container, notConstraints)
		{
			var $editor;

			if ($container.is('.js-editor'))
			{
				if (notConstraints && $container.is(notConstraints))
				{
					return null;
				}

				$editor = $container;
			}
			else
			{
				var $editors = $container.find('.js-editor');
				if (notConstraints)
				{
					$editors = $editors.not(notConstraints);
				}

				if (!$editors.length)
				{
					return null;
				}

				$editor = $editors.first();
			}

			var editor = XF.Element.getHandler($editor, 'editor');
			if (editor)
			{
				return editor;
			}

			if ($editor.is('textarea'))
			{
				return $editor;
			}

			return null;
		},

		focusEditor: function($container, notConstraints)
		{
			var editor = XF.getEditorInContainer($container, notConstraints);
			if (!editor)
			{
				return false;
			}

			if (XF.Editor && editor instanceof XF.Editor)
			{
				editor.scrollToCursor();
				return true;
			}

			if (editor instanceof $ && editor.is('textarea'))
			{
				editor.autofocus();
				return true;
			}

			return false;
		},

		insertIntoTextBox: function($textBox, insert)
		{
			var textBox = $textBox[0],
				scrollPos = textBox.scrollTop,
				startPos = textBox.selectionStart,
				endPos = textBox.selectionEnd,
				value = $textBox.val(),
				before = value.substring(0, startPos),
				after = value.substring(endPos, value.length);

			$textBox.val(before + insert + after).trigger('autosize');
			textBox.selectionStart = textBox.selectionEnd = startPos + insert.length;
			textBox.scrollTop = scrollPos;
			$textBox.autofocus();
		},

		replaceIntoTextBox: function($textBox, insert)
		{
			$textBox.val(insert).trigger('autosize');
		},

		logRecentEmojiUsage: function(shortname)
		{
			shortname = $.trim(shortname);

			var limit = XF.Feature.has('hiddenscroll') ? 12 : 11, // bit arbitrary but basically a single row on full width displays
				value = XF.Cookie.get('emoji_usage'),
				recent = value ? value.split(',') : [],
				exist = recent.indexOf(shortname);

			if (exist !== -1)
			{
				recent.splice(exist, 1);
			}

			recent.push(shortname);

			if (recent.length > limit)
			{
				recent = recent.reverse().slice(0, limit).reverse();
			}

			XF.Cookie.set(
				'emoji_usage',
				recent.join(','),
				new Date(new Date().setFullYear(new Date().getFullYear() + 1))
			);

			$(document).trigger('recent-emoji:logged');

			return recent;
		},

		getRecentEmojiUsage: function()
		{
			var value = XF.Cookie.get('emoji_usage'),
				recent = value ? value.split(',') : [];

			return recent.reverse();
		},

		getFixedOffsetParent: function($el)
		{
			do
			{
				if ($el.css('position') == 'fixed')
				{
					return $el;
				}

				$el = $el.parent();
			}
			while ($el[0] && $el[0].nodeType === 1);

			return $(document.documentElement);
		},

		getFixedOffset: function($el)
		{
			var offset = $el.offset(),
				$offsetParent = XF.getFixedOffsetParent($el);

			if ($el.is('html'))
			{
				return offset;
			}

			var parentOffset = $offsetParent.offset();
			return {
				top: offset.top - parentOffset.top,
				left: offset.left - parentOffset.left
			};
		},

		autoFocusWithin: function($container, autoFocusSelector, $fallback)
		{
			var $focusEl = $container.find(autoFocusSelector || '[autofocus]');

			if (!$focusEl.length)
			{
				if (!$focusEl.length && XF.NavDeviceWatcher.isKeyboardNav())
				{
					$focusEl = $container.find('a, button, :input, [tabindex]').filter(':visible').not(':disabled, [data-no-auto-focus]');
				}
				if (!$focusEl.length)
				{
					var $form = $container.is('form:not([data-no-auto-focus])') ? $container : $container.find('form:not([data-no-auto-focus])').first();
					if ($form.length)
					{
						$focusEl = $form.find(':input, button').filter(':visible').not(':disabled');
					}
				}
				if (!$focusEl.length && $fallback && $fallback.length)
				{
					$focusEl = $fallback;
				}
				if (!$focusEl.length)
				{
					$container.attr('tabindex', '-1');
					$focusEl = $container;
				}
			}

			// focusing will trigger a scroll, so we want to prevent that. We need to maintain all scroll
			// values and restore them after focusing.
			var scrolls = [],
				parent = $focusEl[0].parentNode;

			do
			{
				scrolls.push({
					el: parent,
					left: parent.scrollLeft,
					top: parent.scrollTop
				});
			}
			while (parent = parent.parentNode);

			// fairly ugly workaround for bug #149004
			// scroll page jump menu into view after keyboard is displayed
			// if it is not already visible.
			$focusEl.on('focus', function()
			{
				$(window).on('resize', function()
				{
					setTimeout(function()
					{
						if (!XF.isElementVisible($focusEl))
						{
							$focusEl.get(0).scrollIntoView({
								behavior: 'smooth',
								block: 'end',
								inline: 'nearest'
							});
							$(window).off('resize');
						}
					}, 50);
				});
			});

			$focusEl.first().autofocus();

			var el;
			for (var i = 0; i < scrolls.length; i++)
			{
				el = scrolls[i].el;

				if (el.scrollLeft != scrolls[i].left)
				{
					el.scrollLeft = scrolls[i].left;
				}
				if (el.scrollTop != scrolls[i].top)
				{
					el.scrollTop = scrolls[i].top;
				}
			}
		},

		bottomFix: function(el)
		{
			var $fixer = $('.js-bottomFixTarget').first();
			if ($fixer)
			{
				$fixer.append(el);
			}
			else
			{
				$(el).css({
					position: 'fixed',
					bottom: 0
				}).appendTo('body');
			}
		},

		addFixedMessage: function(el, extraAttrs)
		{
			var $message;

			$message = $($.parseHTML(
				'<div class="fixedMessageBar">'
					+ '<div class="fixedMessageBar-inner">'
						+ '<div class="fixedMessageBar-message"></div>'
						+ '<a class="fixedMessageBar-close" data-close="true" role="button" tabindex="0" aria-label="' + XF.phrase('close') + '"></a>'
					+ '</div>'
				+ '</div>'
			));

			$message.find('.fixedMessageBar-message').html(el);

			if (extraAttrs)
			{
				if (extraAttrs.class)
				{
					$message.addClass(extraAttrs.class);
					delete extraAttrs.class;
				}
				$message.attr(extraAttrs);
			}

			$message.on('click', '[data-close]', function()
			{
				$message.removeClassTransitioned('is-active', function()
				{
					$message.remove();
				});
			});

			XF.bottomFix($message);
			$message.addClassTransitioned('is-active');
		},

		_measureScrollBar: null,

		measureScrollBar: function($container, type)
		{
			if (type == 'height' || type == 'h')
			{
				type = 'h';
			}
			else
			{
				type = 'w';
			}

			if ($container || XF._measureScrollBar === null)
			{
				var $measure = $('<div class="scrollMeasure" />');
				$measure.appendTo($container || 'body');
				var el = $measure[0],
					width = el.offsetWidth - el.clientWidth,
					height = el.offsetHeight - el.clientHeight,
					value = {w: width, h: height};
				$measure.remove();

				if (!$container)
				{
					XF._measureScrollBar = value;
				}

				return value[type];
			}
			else
			{
				return XF._measureScrollBar[type];
			}
		},

		windowHeight: function()
		{
			if (XF.browser.ios || XF.browser.android)
			{
				// return the effective height, without any browser UI
				return window.innerHeight;
			}
			else
			{
				return $(window).height();
			}
		},

		pageLoadScrollFix: function()
		{
			if (!window.location.hash)
			{
				return;
			}

			var isScrolled = false;
			var onLoad = function()
			{
				if (isScrolled)
				{
					return;
				}

				var hash = window.location.hash.replace(/[^a-zA-Z0-9_-]/g, ''),
					$match = hash ? $('#' + hash) : $();

				if ($match.length)
				{
					$match.get(0).scrollIntoView(true);
				}
			};

			if (document.readyState == 'complete')
			{
				// load has already fired
				setTimeout(onLoad, 0);
			}
			else
			{
				setTimeout(function()
				{
					$(window).one('scroll', function(e) {
						isScrolled = true;
					});
				}, 100);

				$(window).one('load', onLoad);
			}
		},

		applyJsState: function(currentState, additionalState)
		{
			currentState = currentState || {};

			if (!additionalState)
			{
				return currentState;
			}

			var state, applyJsStateFn;

			for (state in additionalState)
			{
				if (additionalState.hasOwnProperty(state) && !currentState[state])
				{
					if (XF.jsStates.hasOwnProperty(state))
					{
						if (XF.jsStates[state]())
						{
							currentState[state] = true;
						}
					}

				}
			}

			return currentState;
		},

		jsStates:
		{
			fbSdk: function()
			{
				$(document.body).append($('<div id="fb-root" />'));

				window.fbAsyncInit = function()
				{
					FB.init({
						version: 'v2.7',
						xfbml: true
					});
				};

				XF.loadScript('https://connect.facebook.net/' + XF.getLocale() + '/sdk.js');

				return true;
			},

			twitter: function()
			{
				// https://dev.twitter.com/web/javascript
				window.twttr = (function()
				{
					var t = window.twttr || {};

					if (XF.loadScript("https://platform.twitter.com/widgets.js"))
					{
						t._e = [];
						t.ready = function(f)
						{
							t._e.push(f);
						};
					}
					return t;
				}());

				return true;
			},

			flickr: function()
			{
				XF.loadScript('https://embedr.flickr.com/assets/client-code.js');

				return true;
			},

			instagram: function()
			{
				XF.loadScript('https://platform.instagram.com/' + XF.getLocale() + '/embeds.js', function()
				{
					$(document).on('xf:reinit', function(e, el)
					{
						if (window.instgrm)
						{
							instgrm.Embeds.process(el instanceof $ ? el.get(0) : el);
						}
					});
				});
			},

			reddit: function()
			{
				XF.loadScript('https://embed.redditmedia.com/widgets/platform.js');

				return true;
			},

			reddit_comment: function()
			{
				XF.loadScript('https://www.redditstatic.com/comment-embed.js', function()
				{
					$(document).on('xf:reinit', function(e, el)
					{
						if (window.rembeddit)
						{
							rembeddit.init();
						}
					});
				});

				return true;
			},

			imgur: function()
			{
				var selector = 'blockquote.imgur-embed-pub';

				if (!window.imgurEmbed)
				{
					window.imgurEmbed = { tasks: $(selector).length };
				}

				XF.loadScript('//s.imgur.com/min/embed-controller.js', function()
				{
					$(document).on('xf:reinit', function(e, el)
					{
						imgurEmbed.tasks += $(selector).length;

						for (var i = 0; i < imgurEmbed.tasks; i++)
						{
							imgurEmbed.createIframe();
							imgurEmbed.tasks --;

						}
					});
				});

				return true;
			},

			pinterest: function()
			{
				XF.loadScript('//assets.pinterest.com/js/pinit.js', function()
				{
					$(document).on('xf:reinit', function(e, el)
					{
						PinUtils.build(el instanceof $ ? el.get(0) : el);
					});
				});

				return true;
			}
		},

		getLocale: function()
		{
			var locale = $('html').attr('lang').replace('-', '_');
			if (!locale)
			{
				locale = 'en_US';
			}

			return locale;
		},

		supportsPointerEvents: function()
		{
			return ('PointerEvent' in window);
		},

		isEventTouchTriggered: function(e)
		{
			if (e)
			{
				if (e.xfPointerType)
				{
					// this isn't normally exposed to click events, so we have a system to expose this without having
					// to manually implement full click emulation
					return (e.xfPointerType === 'touch');
				}

				var oe = e.originalEvent;

				if (oe)
				{
					if (XF.supportsPointerEvents() && oe instanceof PointerEvent)
					{
						return oe.pointerType === 'touch';
					}

					if (oe.sourceCapabilities)
					{
						return oe.sourceCapabilities.firesTouchEvents;
					}
				}
			}

			return XF.Feature.has('touchevents');
		},

		getElEffectiveZIndex: function($reference)
		{
			var maxZIndex = parseInt($reference.css('z-index'), 10) || 0;

			$reference.parents().each(function(i, el)
			{
				var zIndex = parseInt($(el).css('z-index'), 10);
				if (zIndex > maxZIndex)
				{
					maxZIndex = zIndex;
				}
			});

			return maxZIndex;
		},

		setRelativeZIndex: function($targets, $reference, offsetAmount, minZIndex)
		{
			if (!minZIndex)
			{
				minZIndex = 6; // make sure we go over the default editor stuff
			}

			var maxZIndex = XF.getElEffectiveZIndex($reference);
			if (minZIndex && minZIndex > maxZIndex)
			{
				maxZIndex = minZIndex;
			}

			if (offsetAmount === null || typeof offsetAmount === 'undefined')
			{
				offsetAmount = 0;
			}

			if (maxZIndex || offsetAmount)
			{
				$targets.each(function()
				{
					var $this = $(this),
						dataKey = 'base-z-index';
					if (typeof $this.data(dataKey) == 'undefined')
					{
						$this.data(dataKey, parseInt($this.css('z-index'), 10) || 0);
					}
					$this.css('z-index', $this.data(dataKey) + offsetAmount + maxZIndex);
				});
			}
			else
			{
				$targets.css('z-index', '');
			}
		},

		adjustHtmlForRte: function(content)
		{
			content = content.replace(/<img[^>]+>/ig, function(match)
			{
				if (match.match(/class="([^"]* )?smilie( |")/))
				{
					var altMatch;
					if (altMatch = match.match(/alt="([^"]+)"/))
					{
						return altMatch[1];
					}
				}

				return match;
			});

			content = content.replace(/([\w\W]|^)<a\s[^>]*data-user-id="\d+"\s+data-username="([^"]+)"[^>]*>([\w\W]+?)<\/a>/gi,
				function(match, prefix, user, username) {
					return prefix + (prefix == '@' ? '' : '@') + username.replace(/^@/, '');
				}
			);

			content = content.replace(/(<img\s[^>]*)src="[^"]*"(\s[^>]*)data-url="([^"]+)"/gi,
				function(match, prefix, suffix, source) {
					return prefix + 'src="' + source + '"' + suffix;
				}
			);

			return content;
		},

		requestAnimationTimeout: function(fn, delay)
		{
			if (!delay)
			{
				delay = 0;
			}

			var raf = window.requestAnimationFrame || function(cb) { return window.setTimeout(cb, 1000 / 60); },
				start = Date.now(),
				data = {};

			function loop()
			{
				if (Date.now() - start >= delay)
				{
					fn();
				}
				else
				{
					data.id = raf(loop);
				}
			}

			data.id = raf(loop);
			data.cancel = function()
			{
				var caf = window.cancelAnimationFrame || window.clearTimeout;
				caf(this.id);
			};

			return data;
		},


		/**
		 * Returns a function replacing the default this object with the supplied context.
		 *
		 * jQuery equivalent function has been deprecated in 3.3. If only two arguments passed in
		 * then we can use fn.bind instead. If we support the spread operator one day we can
		 * probably just use fn.bind by default.
		 *
		 * @param fn
		 * @param context
		 * @returns {undefined|Function}
		 */
		proxy: function(fn, context)
		{
			var tmp, args;

			if (typeof context === "string")
			{
				tmp = fn[context];
				context = fn;
				fn = tmp;
			}

			if (typeof fn !== 'function')
			{
				return undefined;
			}

			args = [].slice.call(arguments, 2);

			if (args)
			{
				return function()
				{
					return fn.apply(context, args.concat([].slice.call(arguments)));
				};
			}
			else
			{
				return fn.bind(context, args);
			}
		},

		_localLoadTime: null,

		getLocalLoadTime: function()
		{
			if (XF._localLoadTime)
			{
				return XF._localLoadTime;
			}

			var localLoadTime,
				time = XF.config.time,
				$loadCache = $('#_xfClientLoadTime'),
				loadVal = $loadCache.val();

			if (loadVal && loadVal.length)
			{
				var parts = loadVal.split(',');
				if (parts.length == 2 && parseInt(parts[1], 10) == time.now)
				{
					localLoadTime = parseInt(parts[0], 10);
					$loadCache.val(loadVal); // IE needs this to maintain across multiple views
				}
			}

			if (!localLoadTime)
			{
				if (window.performance && window.performance.timing && window.performance.timing.requestStart !== 0)
				{
					var timing = window.performance.timing;

					// average between request and response start is likely to be somewhere around when the server started
					localLoadTime = Math.floor(
						(timing.requestStart + timing.responseStart) / (2 * 1000)
					);
				}
				else
				{
					localLoadTime = Math.floor(new Date().getTime() / 1000) - 1;
				}
				$loadCache.val(localLoadTime + ',' + time.now);
			}

			XF._localLoadTime = localLoadTime;

			return localLoadTime;
		}
	});

	if (typeof Object.create != 'function')
	{
		Object.create = (function()
		{
			var o = function() {};
			return function (prototype)
			{
				o.prototype = prototype;
				var result = new o();
				o.prototype = null;
				return result;
			};
		})();
	}

	XF.create = function(props)
	{
		var fn = function()
		{
			this.__construct.apply(this, arguments);
		};

		fn.prototype = Object.create(props);

		if (!fn.prototype.__construct)
		{
			fn.prototype.__construct = function() {};
		}
		fn.prototype.constructor = fn;

		return fn;
	};

	XF.extend = function(parent, extension)
	{
		var fn = function()
		{
			this.__construct.apply(this, arguments);
		};
		var i;

		fn.prototype = Object.create(parent.prototype);

		if (!fn.prototype.__construct)
		{
			fn.prototype.__construct = function() {};
		}
		fn.prototype.constructor = fn;

		if (typeof extension == 'object')
		{
			if (typeof extension.__backup == 'object')
			{
				var backup = extension.__backup;
				for (i in backup)
				{
					if (backup.hasOwnProperty(i))
					{
						if (fn.prototype[backup[i]])
						{
							throw new Error('Method ' + backup[i] + ' already exists on object. Aliases must be unique.');
						}
						fn.prototype[backup[i]] = fn.prototype[i];
					}
				}

				delete extension.__backup;
			}

			for (i in extension)
			{
				if (extension.hasOwnProperty(i))
				{
					fn.prototype[i] = extension[i];
				}
			}
		}

		return fn;
	};

	XF.classToConstructor = function(className)
	{
		var obj = window,
			parts = className.split('.'),
			i = 0;

		for (i = 0; i < parts.length; i++)
		{
			obj = obj[parts[i]];
		}

		if (typeof obj != 'function')
		{
			console.error('%s is not a function.', className);
			return false;
		}

		return obj;
	};

	XF.Cookie = {
		get: function(name)
		{
			var expr, cookie;

			expr = new RegExp('(^| )' + XF.config.cookie.prefix + name + '=([^;]+)(;|$)');
			cookie = expr.exec(document.cookie);

			if (cookie)
			{
				return decodeURIComponent(cookie[2]);
			}
			else
			{
				return null;
			}
		},

		set: function(name, value, expires)
		{
			var c = XF.config.cookie;

			document.cookie = c.prefix + name + '=' + encodeURIComponent(value)
				+ (expires === undefined ? '' : ';expires=' + expires.toUTCString())
				+ (c.path  ? ';path=' + c.path : '')
				+ (c.domain ? ';domain=' + c.domain : '')
				+ (c.secure ? ';secure' : '');
		},

		getJson: function(name)
		{
			var data = this.get(name);
			if (!data)
			{
				return {};
			}

			try
			{
				return $.parseJSON(data) || {};
			}
			catch (e)
			{
				return {};
			}
		},

		setJson: function(name, value, expires)
		{
			this.set(name, JSON.stringify(value), expires);
		},

		remove: function(name)
		{
			var c = XF.config.cookie;

			document.cookie = c.prefix + name + '='
				+ (c.path  ? '; path=' + c.path : '')
				+ (c.domain ? '; domain=' + c.domain : '')
				+ (c.secure ? '; secure' : '')
				+ '; expires=Thu, 01-Jan-70 00:00:01 GMT';
		}
	};

	XF.LocalStorage = {
		getKeyName: function(name)
		{
			return XF.config.cookie.prefix + name;
		},

		get: function(name)
		{
			var value  = null;

			try
			{
				value = window.localStorage.getItem(this.getKeyName(name));
			}
			catch (e) {}

			if (value === null)
			{
				var localStorage = this.getFallbackValue();
				if (localStorage && localStorage.hasOwnProperty(name))
				{
					value = localStorage[name];
				}
			}

			return value;
		},

		getJson: function(name)
		{
			var data = this.get(name);
			if (!data)
			{
				return {};
			}

			try
			{
				return $.parseJSON(data) || {};
			}
			catch (e)
			{
				return {};
			}
		},

		set: function(name, value, allowFallback)
		{
			try
			{
				window.localStorage.setItem(this.getKeyName(name), value);
			}
			catch (e)
			{
				if (allowFallback)
				{
					var localStorage = this.getFallbackValue();
					localStorage[name] = value;
					this.updateFallbackValue(localStorage);
				}
			}
		},

		setJson: function(name, value, allowFallback)
		{
			this.set(name, JSON.stringify(value), allowFallback);
		},

		remove: function(name)
		{
			try
			{
				window.localStorage.removeItem(this.getKeyName(name));
			}
			catch (e) {}

			var localStorage = this.getFallbackValue();
			if (localStorage && localStorage.hasOwnProperty(name))
			{
				delete localStorage[name];
				this.updateFallbackValue(localStorage);
			}
		},

		getFallbackValue: function()
		{
			var value = XF.Cookie.get('ls');
			if (value)
			{
				try
				{
					value = $.parseJSON(value);
				}
				catch (e)
				{
					value = {};
				}
			}

			return value || {};
		},

		updateFallbackValue: function(newValue)
		{
			if ($.isEmptyObject(newValue))
			{
				XF.Cookie.remove('ls');
			}
			else
			{
				XF.Cookie.set('ls', JSON.stringify(newValue));
			}
		}
	};

	XF.CrossTab = (function()
	{
		var listeners = {},
			listening = false,
			communicationKey = '__crossTab',
			activeEvent;

		function handleEvent(e)
		{
			var expectedKey = XF.LocalStorage.getKeyName(communicationKey);
			if (e.key !== expectedKey)
			{
				return;
			}

			var json;

			try
			{
				json = $.parseJSON(e.newValue);
			}
			catch (e)
			{
				return;
			}

			if (!json || !json.event)
			{
				return;
			}

			var event = json.event,
				data = json.data || null,
				activeListeners = listeners[event];
			if (!activeListeners)
			{
				return;
			}

			activeEvent = event;

			for (var i = 0; i < activeListeners.length; i++)
			{
				activeListeners[i](data);
			}

			activeEvent = null;
		}

		function on(event, callback)
		{
			if (!listeners[event])
			{
				listeners[event] = [];
			}

			listeners[event].push(callback);

			if (!listening)
			{
				listening = true;
				window.addEventListener('storage', handleEvent);
			}
		}

		function trigger(event, data, forceCall)
		{
			if (!forceCall && activeEvent && activeEvent == event)
			{
				// this is to help prevent infinite loops where the code that reacts to an event
				// is the same code that gets called by the event
				return;
			}

			XF.LocalStorage.setJson(communicationKey, {
				event: event,
				data: data,
				'_': new Date() + Math.random() // forces the event to fire
			});
		}

		return {
			on: on,
			trigger: trigger
		}
	})();

	XF.Breakpoint = (function()
	{
		var val = null,
			sizes = ['narrow', 'medium', 'wide', 'full'];

		function current()
		{
			return val;
		}

		function isNarrowerThan(test)
		{
			for (var i = 0; i < sizes.length; i++)
			{
				if (test == sizes[i])
				{
					return false;
				}

				if (val == sizes[i])
				{
					return true;
				}
			}

			return false;
		}

		function isAtOrNarrowerThan(test)
		{
			return (val == test || isNarrowerThan(test));
		}

		function isWiderThan(test)
		{
			var afterTest = false;

			for (var i = 0; i < sizes.length; i++)
			{
				if (test == sizes[i])
				{
					afterTest = true;
					continue;
				}

				if (val == sizes[i])
				{
					return afterTest;
				}
			}

			return false;
		}

		function isAtOrWiderThan(test)
		{
			return (val == test || isWiderThan(test));
		}

		function refresh()
		{
			var newVal = window.getComputedStyle($('html')[0], ':after').getPropertyValue('content').replace(/\"/g, '');

			if (val)
			{
				if (newVal != val)
				{
					var oldVal = val;
					val = newVal;

					$(document).trigger('breakpoint:change', [oldVal, newVal]);
				}
			}
			else
			{
				// initial load, don't trigger anything
				val = newVal;
			}

			return val;
		}

		refresh();
		$(window).onPassive('resize', refresh);

		return {
			current: current,
			refresh: refresh,
			isNarrowerThan: isNarrowerThan,
			isAtOrNarrowerThan: isAtOrNarrowerThan,
			isWiderThan: isWiderThan,
			isAtOrWiderThan: isAtOrWiderThan
		};
	})();

	XF.JobRunner = (function()
	{
		var manualRunning = false,
			manualOnlyIds = [],
			manualXhr,
			manualOverlay = null,
			autoBlockingRunning = 0,
			autoBlockingXhr,
			autoBlockingOverlay = null;

		var runAuto = function()
		{
			$.ajax({
				url: XF.canonicalizeUrl('job.php'),
				type: 'post',
				cache: false,
				dataType: 'json',
				global: false
			}).always(function(data)
			{
				if (data && data.more)
				{
					setTimeout(runAuto, 100);
				}
			});
		};

		// ####### AUTO BLOCKING ###########

		var runAutoBlocking = function(onlyIds, message)
		{
			if (typeof onlyIds === 'number')
			{
				onlyIds = [onlyIds];
			}
			else if (!Array.isArray(onlyIds))
			{
				return;
			}

			if (!onlyIds.length)
			{
				return;
			}

			autoBlockingRunning++;
			getAutoBlockingOverlay().show();

			if (!message)
			{
				message = XF.phrase('processing...');
			}
			$('#xfAutoBlockingJobStatus').text(message);

			runAutoBlockingRequest(onlyIds);
		};

		var runAutoBlockingRequest = function(onlyIds)
		{
			autoBlockingXhr = XF.ajax(
				'post',
				XF.canonicalizeUrl('job.php'),
				{ only_ids: onlyIds },
				function(data)
				{
					if (data.more && data.ids && data.ids.length)
					{
						if (data.status)
						{
							$('#xfAutoBlockingJobStatus').text(data.status);
						}

						setTimeout(function()
						{
							runAutoBlockingRequest(data.ids);
						}, 0);
					}
					else
					{
						stopAutoBlocking();
						if (data.moreAuto)
						{
							setTimeout(runAuto, 100);
						}
					}
				},
				{ skipDefault: true }
			).fail(stopAutoBlocking);
		};

		var stopAutoBlocking = function()
		{
			if (autoBlockingOverlay)
			{
				autoBlockingOverlay.hide();
			}

			autoBlockingRunning--;
			if (autoBlockingRunning < 0)
			{
				autoBlockingRunning = 0;
			}

			if (autoBlockingRunning == 0)
			{
				$(document).trigger('job:auto-blocking-complete');
				triggerBlockingComplete();
			}

			if (autoBlockingXhr)
			{
				autoBlockingXhr.abort();
			}
			autoBlockingXhr = null;
		};

		var getAutoBlockingOverlay = function()
		{
			if (!autoBlockingOverlay)
			{
				autoBlockingOverlay = getModalJobOverlay('xfAutoBlockingJobStatus');
			}
			return autoBlockingOverlay;
		};

		// ################# MANUAL ###############

		var runManual = function(onlyId)
		{
			var url = XF.config.job.manualUrl;
			if (!url)
			{
				return;
			}

			if (onlyId === null)
			{
				manualOnlyIds = null;
			}
			else
			{
				var manualOnlyIds = manualOnlyIds || [];
				if (typeof onlyId === 'number')
				{
					manualOnlyIds.push(onlyId);
				}
				else if (Array.isArray(onlyId))
				{
					manualOnlyIds.push.apply(manualOnlyIds, onlyId);
				}
			}

			if (manualRunning)
			{
				return;
			}
			manualRunning = true;

			getManualOverlay().show();

			var runJob = function(runOnlyId) {
				manualXhr = XF.ajax('post', url, runOnlyId ? {only_id: runOnlyId} : null, function(data)
				{
					if (data.jobRunner)
					{
						$('#xfManualJobStatus').text(data.jobRunner.status || XF.phrase('processing...'));

						setTimeout(function ()
						{
							runJob(runOnlyId);
						}, 0);
					}
					else
					{
						runNext();
					}
				}, {skipDefault: true}).fail(stopManual);
			};
			var runNext = function()
			{
				var ids = manualOnlyIds;
				if (Array.isArray(manualOnlyIds) && manualOnlyIds.length == 0)
				{
					stopManual();
				}
				else
				{
					runJob(manualOnlyIds ? manualOnlyIds.shift() : null);
				}
			};
			runNext();
		};

		var stopManual = function()
		{
			if (manualOverlay)
			{
				manualOverlay.hide();
			}

			manualOnlyIds = [];
			manualRunning = false;
			$(document).trigger('job:manual-complete');
			triggerBlockingComplete();

			if (manualXhr)
			{
				manualXhr.abort();
			}
			manualXhr = null;
		};

		var getManualOverlay = function()
		{
			if (!manualOverlay)
			{
				manualOverlay = getModalJobOverlay('xfManualJobStatus');
			}
			return manualOverlay;
		};

		// ################# HELPERS ###########

		var getModalJobOverlay = function(statusId)
		{
			var $overlay = XF.getOverlayHtml({
				title: XF.phrase('processing...'),
				dismissible: false,
				html: '<div class="blockMessage"><span id="' + statusId + '">'
					+ XF.phrase('processing...') + '</span></div>'
			});
			return new XF.Overlay($overlay, {
				backdropClose: false,
				keyboard: false
			});
		};

		var triggerBlockingComplete = function()
		{
			if (!isBlockingJobRunning())
			{
				$(document).trigger('job:blocking-complete');
			}
		};

		var isBlockingJobRunning = function()
		{
			return (manualRunning || autoBlockingRunning > 0);
		};

		return {
			isBlockingJobRunning: isBlockingJobRunning,
			runAuto: runAuto,
			runAutoBlocking: runAutoBlocking,
			runManual: runManual,
			stopManual: stopManual,
			getManualOverlay: getManualOverlay
		}
	})();

	XF.Loader = (function()
	{
		var loadedCss = XF.config.css,
			loadedJs = XF.config.js;

		var load = function(js, css, onComplete)
		{
			js = js || [];
			css = css || [];

			var loadJs = [], loadCss = [], i;

			for (i = 0; i < js.length; i++)
			{
				if (!loadedJs.hasOwnProperty(js[i]))
				{
					loadJs.push(js[i]);
				}
			}
			for (i = 0; i < css.length; i++)
			{
				if (!loadedCss.hasOwnProperty(css[i]))
				{
					loadCss.push(css[i]);
				}
			}

			var totalRemaining = (loadJs.length ? 1 : 0) + (loadCss.length ? 1 : 0),
				markFinished = function()
				{
					totalRemaining--;
					if (totalRemaining == 0 && onComplete)
					{
						onComplete();
					}
				};

			if (!totalRemaining)
			{
				if (onComplete)
				{
					onComplete();
				}
				return;
			}

			if (loadJs.length)
			{
				XF.loadScripts(loadJs, function()
				{
					$.each(loadJs, function (i, jsFile)
					{
						loadedJs[jsFile] = true;
					});
					markFinished();
				});
			}

			if (loadCss.length)
			{
				var cssUrl = XF.config.url.css;
				if (cssUrl)
				{
					cssUrl = cssUrl.replace('__SENTINEL__', loadCss.join(','));

					$.ajax({
						type: 'GET',
						url: cssUrl,
						cache: true,
						global: false,
						dataType: 'text',
						success: function (cssText)
						{
							var baseHref = XF.config.url.basePath;
							if (baseHref)
							{
								cssText = cssText.replace(
									/(url\(("|')?)([^"')]+)(("|')?\))/gi,
									function (all, front, null1, url, back, null2)
									{
										if (!url.match(/^([a-z]+:|\/)/i))
										{
											url = baseHref + url;
										}
										return front + url + back;
									}
								);
							}

							$('<style>' + cssText + '</style>').appendTo('head');
						}
					}).always(function ()
					{
						$.each(loadCss, function (i, stylesheet)
						{
							loadedCss[stylesheet] = true;
						});
						markFinished();
					});
				}
				else
				{
					console.error('No CSS URL so cannot dynamically load CSS');
					markFinished();
				}
			}
		};

		return {
			load: load,
			loadCss: function(css, onComplete) { load([], css, onComplete); },
			loadJs: function(js, onComplete) { load(js, [], onComplete); }
		};
	})();

	XF.ClassMapper = XF.create({
		_map: {},
		_toExtend: {},

		add: function(identifier, className)
		{
			this._map[identifier] = className;
		},

		extend: function(identifier, extension)
		{
			var obj = this.getObjectFromIdentifier(identifier);
			if (obj)
			{
				obj = XF.extend(obj, extension);
				this._map[identifier] = obj;
			}
			else
			{
				if (!this._toExtend[identifier])
				{
					this._toExtend[identifier] = [];
				}
				this._toExtend[identifier].push(extension);
			}
		},

		getObjectFromIdentifier: function(identifier)
		{
			var record = this._map[identifier],
				extensions = this._toExtend[identifier];

			if (!record)
			{
				return null;
			}

			if (typeof record == 'string')
			{
				record = XF.classToConstructor(record);
				if (extensions)
				{
					for (var i = 0; i < extensions.length; i++)
					{
						record = XF.extend(record, extensions[i]);
					}

					delete this._toExtend[identifier];
				}

				this._map[identifier] = record;
			}

			return record;
		}
	});

	XF.ActionIndicator = (function()
	{
		var activeCounter = 0, $indicator;

		var initialize = function()
		{
			$(document).on({
				ajaxStart: show,
				'xf:action-start': show,
				ajaxStop: hide,
				'xf:action-stop': hide
			});
		};

		var show = function()
		{
			activeCounter++;
			if (activeCounter != 1)
			{
				return;
			}

			if (!$indicator)
			{
				$indicator = $(
					'<span class="globalAction">'
					+ '<span class="globalAction-bar" />'
					+ '<span class="globalAction-block"><i></i><i></i><i></i></span>'
					+ '</span>'
				).appendTo('body');
			}

			$indicator.addClassTransitioned('is-active');
		};

		var hide = function()
		{
			activeCounter--;
			if (activeCounter > 0)
			{
				return;
			}

			activeCounter = 0;
			$indicator.removeClassTransitioned('is-active');
		};

		return {
			initialize: initialize,
			show: show,
			hide: hide
		}
	})();

	XF.DynamicDate = (function()
	{
		var localLoadTime,
			serverLoadTime,
			todayStart,
			todayDow,
			yesterdayStart,
			tomorrowStart,
			weekStart,
			initialized = false,
			interval,
			futureInterval;

		var startInterval = function()
		{
			interval = setInterval(function()
			{
				refresh(document);
			}, 20 * 1000);
		};

		var initialize = function()
		{
			if (initialized)
			{
				return;
			}
			initialized = true;

			var time = XF.config.time;

			localLoadTime = XF.getLocalLoadTime();
			serverLoadTime = time.now;
			todayStart = time.today;
			todayDow = time.todayDow;
			yesterdayStart = todayStart - 86400;
			tomorrowStart = todayStart + 86400;
			weekStart = todayStart - 6 * 86400;

			if (document.hidden !== undefined)
			{
				if (!document.hidden)
				{
					startInterval();
				}

				$(document).on('visibilitychange', function()
				{
					if (document.hidden)
					{
						clearInterval(interval);
					}
					else
					{
						startInterval();
						refresh(document);
					}
				});
			}
			else
			{
				startInterval();
			}
		};

		var refresh = function(root)
		{
			if (!initialized)
			{
				this.initialize();
			}

			var $els = $(root).find('time[data-time]'),
				length = $els.length,
				now = Math.floor(new Date().getTime() / 1000),
				openLength = now - localLoadTime;

			var el, $el, interval, futureInterval, dynType, thisTime;

			if (serverLoadTime + openLength > todayStart + 86400)
			{
				// day has changed, need to adjust
				var dayOffset = Math.floor((serverLoadTime + openLength - todayStart) / 86400);

				todayStart += dayOffset * 86400;
				todayDow = (todayDow + dayOffset) % 7;
				yesterdayStart = todayStart - 86400;
				tomorrowStart = todayStart + 86400;
				weekStart = todayStart - 6 * 86400;
			}

			for (var i = 0; i < length; i++)
			{
				el = $els[i];
				$el = $(el);
				thisTime = parseInt(el.getAttribute('data-time'), 10);
				interval = (serverLoadTime - thisTime) + openLength;
				dynType = el.xfDynType;

				if (interval < -2)
				{
					// date in the future, note that -2 is a bit of fudging as times might be very close and our local
					// load time may not jive 100% with the server

					futureInterval = thisTime -  (serverLoadTime + openLength);

					if (futureInterval < 60)
					{
						if (dynType != 'futureMoment')
						{
							$el.text(XF.phrase('in_a_moment'));
							el.xfDynType = 'futureMoment';
						}
					}
					else if (futureInterval < 120)
					{
						if (dynType != 'futureMinute')
						{
							$el.text(XF.phrase('in_a_minute'));
							el.xfDynType = 'futureMinute';
						}
					}
					else if (futureInterval < 3600)
					{
						var minutes = Math.floor(futureInterval / 60);
						if (dynType !== 'futureMinutes' + minutes)
						{
							$el.text(XF.phrase('in_x_minutes', {
								'{minutes}': minutes
							}));
							el.xfDynType = 'futureMinutes' + minutes;
						}
					}
					else if (thisTime < tomorrowStart)
					{
						if (dynType != 'latertoday')
						{
							$el.text(XF.phrase('later_today_at_x', {
								'{time}': $el.attr('data-time-string')
							}));
							el.xfDynType = 'latertoday';
						}
					}
					else if (thisTime < tomorrowStart + 86400)
					{
						if (dynType != 'tomorrow')
						{
							$el.text(XF.phrase('tomorrow_at_x', {
								'{time}': $el.attr('data-time-string')
							}));
							el.xfDynType = 'tomorrow';
						}
					}
					else if (futureInterval < (7 * 86400))
					{
						// no need to change anything
						el.xfDynType = 'future';
					}
					else
					{
						// after the next week
						if ($el.attr('data-full-date'))
						{
							$el.text(XF.phrase('date_x_at_time_y', {
								'{date}': $el.attr('data-date-string'), // must use attr for string value
								'{time}': $el.attr('data-time-string') // must use attr for string value
							}));
						}
						else
						{
							$el.text($el.attr('data-date-string')); // must use attr for string value
						}

						el.xfDynType = 'future';
					}
				}
				else if (interval <= 60)
				{
					if (dynType !== 'moment')
					{
						$el.text(XF.phrase('a_moment_ago'));
						el.xfDynType = 'moment';
					}
				}
				else if (interval <= 120)
				{
					if (dynType !== 'minute')
					{
						$el.text(XF.phrase('one_minute_ago'));
						el.xfDynType = 'minute';
					}
				}
				else if (interval < 3600)
				{
					var minutes = Math.floor(interval / 60);
					if (dynType !== 'minutes' + minutes)
					{
						$el.text(XF.phrase('x_minutes_ago', {
							'{minutes}': minutes
						}));
						el.xfDynType = 'minutes' + minutes;
					}
				}
				else if (thisTime >= todayStart)
				{
					if (dynType !== 'today')
					{
						$el.text(XF.phrase('today_at_x', {
							'{time}': $el.attr('data-time-string') // must use attr for string value
						}));
						el.xfDynType = 'today';
					}
				}
				else if (thisTime >= yesterdayStart)
				{
					if (dynType !== 'yesterday')
					{
						$el.text(XF.phrase('yesterday_at_x', {
							'{time}': $el.attr('data-time-string') // must use attr for string value
						}));
						el.xfDynType = 'yesterday';
					}
				}
				else if (thisTime >= weekStart)
				{
					if (dynType !== 'week')
					{
						var calcDow = todayDow - Math.ceil((todayStart - thisTime) / 86400);
						if (calcDow < 0)
						{
							calcDow += 7;
						}

						$el.text(XF.phrase('day_x_at_time_y', {
							'{day}': XF.phrase('day' + calcDow),
							'{time}': $el.attr('data-time-string') // must use attr for string value
						}));
						el.xfDynType = 'week';
					}
				}
				else
				{
					if (dynType !== 'old')
					{
						if ($el.attr('data-full-date'))
						{
							$el.text(XF.phrase('date_x_at_time_y', {
								'{date}': $el.attr('data-date-string'), // must use attr for string value
								'{time}': $el.attr('data-time-string') // must use attr for string value
							}));
						}
						else
						{
							$el.text($el.attr('data-date-string')); // must use attr for string value
						}

						el.xfDynType = 'old';
					}
				}
			}
		};

		return {
			initialize: initialize,
			refresh: refresh
		};
	})();

	XF.KeepAlive = (function()
	{
		var url,
			crossTabEvent,
			initialized = false,
			baseTimerDelay = 50 * 60, // in seconds, 50 minutes
			jitterRange = 120,
			interval;

		var initialize = function()
		{
			if (initialized)
			{
				return;
			}

			if (!XF.config.url.keepAlive || !XF.config.url.keepAlive.length)
			{
				return;
			}
			initialized = true;

			url = XF.config.url.keepAlive;
			crossTabEvent = 'keepAlive' + XF.stringHashCode(url);

			resetTimer();

			XF.CrossTab.on(crossTabEvent, applyChanges);

			if (window.performance && window.performance.navigation)
			{
				var navType = window.performance.navigation.type;
				if (navType == 0 || navType == 1)
				{
					// navigate or reload, we have the most recent data from the server so pass that on
					XF.CrossTab.trigger(crossTabEvent, {
						csrf: XF.config.csrf,
						time: XF.config.time.now,
						user_id: XF.config.userId
					});
				}
			}

			if (!XF.Cookie.get('csrf'))
			{
				refresh();
			}
		};

		var resetTimer = function()
		{
			var rand = function(min, max)
			{
				return Math.floor(Math.random() * (max - min + 1)) + min;
			};

			var delay = baseTimerDelay + rand(-jitterRange, jitterRange); // +/- jitter to prevent opened tabs sticking together
			if (delay < jitterRange)
			{
				delay = jitterRange;
			}

			if (interval)
			{
				clearInterval(interval);
			}
			interval = setInterval(refresh, delay * 1000);

			// note that while this should be reset each time it's triggered, using an interval ensures
			// that it runs again even if there's an error
		};

		var offlineCount = 0,
			offlineDelayTimer;

		var refresh = function()
		{
			if (!initialized)
			{
				return;
			}

			// if we're offline, delay testing by 30 seconds a few times. This tries to maintain the keep alive
			// when there are temporary network drops or if waking up from sleep and the network isn't ready yet.
			if (window.navigator.onLine === false)
			{
				offlineCount++;

				if (offlineCount <= 5)
				{
					offlineDelayTimer = setTimeout(refresh, 30);
				}
			}

			offlineCount = 0;
			clearTimeout(offlineDelayTimer);

			$.ajax({
				url: XF.canonicalizeUrl(url),
				data: {
					_xfResponseType: 'json',
					_xfToken: XF.config.csrf
				},
				type: 'post',
				cache: false,
				dataType: 'json',
				global: false
			}).done(function(data)
			{
				if (data.status != 'ok')
				{
					return;
				}

				applyChanges(data);
				XF.CrossTab.trigger(crossTabEvent, data);
			});
		};

		var applyChanges = function(data)
		{
			if (data.csrf)
			{
				XF.config.csrf = data.csrf;
				$('input[name=_xfToken]').val(data.csrf);
			}

			if (typeof data.user_id !== 'undefined')
			{
				var $activeChangeMessage = $('.js-activeUserChangeMessage');

				if (data.user_id != XF.config.userId && !$activeChangeMessage.length)
				{
					XF.addFixedMessage(XF.phrase('active_user_changed_reload_page'), {
						'class': 'js-activeUserChangeMessage'
					});
				}
				if (data.user_id == XF.config.userId && $activeChangeMessage.length)
				{
					$activeChangeMessage.remove();
				}
			}

			resetTimer();
		};

		return {
			initialize: initialize,
			refresh: refresh
		};
	})();

	// ################################## LINK PROXY WATCHER ###########################################

	XF.LinkWatcher = (function()
	{
		var proxyInternals = false;

		var proxyLinkClick = function(e)
		{
			var $this = $(this),
				proxyHref = $this.data('proxy-href'),
				lastEvent = $this.data('proxy-handler-last');

			if (!proxyHref)
			{
				return;
			}

			// we may have a direct click event and a bubbled event. Ensure they don't both fire.
			if (lastEvent && lastEvent == e.timeStamp)
			{
				return;
			}
			$this.data('proxy-handler-last', e.timeStamp);

			$.ajax({
				url: XF.canonicalizeUrl(proxyHref),
				data: { _xfResponseType: 'json', referrer: window.location.href.replace(/#.*$/, '') },
				type: 'post',
				cache: false,
				dataType: 'json',
				global: false
			});
		};

		var initLinkProxy = function()
		{
			var selector = 'a[data-proxy-href]',
				dataAttrAttacher = 'proxy-handler';

			if (!proxyInternals)
			{
				selector += ':not(.link--internal)';
			}

			$(document)
				.on('click', selector, proxyLinkClick)
				.on('focusin', selector, function(e)
				{
					// This approach is taken because middle click events do not bubble. This is a way of
					// getting the equivalent of event bubbling on middle clicks in Chrome.
					var $this = $(this);
					if ($this.data(dataAttrAttacher))
					{
						return;
					}

					$this.data(dataAttrAttacher, true)
						.click(proxyLinkClick);
				});
		};

		var externalLinkClick = function(e)
		{
			if (!XF.config.enableRtnProtect)
			{
				return;
			}

			if (e.isDefaultPrevented())
			{
				return;
			}

			var $this = $(this),
				href = $this.attr('href'),
				lastEvent = $this.data('blank-handler-last');
			if (!href)
			{
				return;
			}

			if (href.match(/^[a-z]:/i) && !href.match(/^https?:/i))
			{
				// ignore canonical but non http(s) links
				return;
			}

			// if noopener is supported and in use, then use that instead
			if ($this.is('[rel~=noopener]'))
			{
				var browser = XF.browser;
				if (
					(browser.chrome && browser.version >= 49)
					|| (browser.mozilla && browser.version >= 52)
					|| (browser.safari && browser.version >= 11) // may be supported in some 10.x releases
					// Edge and IE don't support it yet
				)
				{
					return;
				}
			}

			if ($this.closest('[contenteditable=true]').length)
			{
				return;
			}

			href = XF.canonicalizeUrl(href);

			var regex = new RegExp('^[a-z]+://' + location.host + '(/|$|:)', 'i');
			if (regex.test(href))
			{
				// if the link is local, then don't do the special processing
				return;
			}

			// we may have a direct click event and a bubbled event. Ensure they don't both fire.
			if (lastEvent && lastEvent == e.timeStamp)
			{
				return;
			}

			$this.data('blank-handler-last', e.timeStamp);

			var ua = navigator.userAgent,
				isOldIE = ua.indexOf('MSIE') !== -1,
				isSafari = ua.indexOf('Safari') !== -1 && ua.indexOf('Chrome') == -1,
				isGecko = ua.indexOf('Gecko/') !== -1;

			if (e.shiftKey && isGecko)
			{
				// Firefox doesn't trigger when holding shift. If the code below runs, it will force
				// opening in a new tab instead of a new window, so stop. Note that Chrome still triggers here,
				// but it does open in a new window anyway so we run the normal code.
				return;
			}
			if (isSafari && (e.shiftKey || e.altKey))
			{
				// this adds to reading list or downloads instead of opening a new tab
				return;
			}
			if (isOldIE)
			{
				// IE has mitigations for this and this blocks referrers
				return;
			}

			// now run the opener clearing

			if (isSafari)
			{
				// Safari doesn't work with the other approach
				// Concept from: https://github.com/danielstjules/blankshield
				var $iframe, iframeDoc, $script;

				$iframe = $('<iframe style="display: none" />').appendTo(document.body);
				iframeDoc = $iframe[0].contentDocument || $iframe[0].contentWindow.document;

				iframeDoc.__href = href; // set this so we don't need to do an eval-type thing

				$script = $('<script />', iframeDoc);
				$script[0].text = 'window.opener=null;' +
					'window.parent=null;window.top=null;window.frameElement=null;' +
					'window.open(document.__href).opener = null;';

				iframeDoc.body.appendChild($script[0]);
				$iframe.remove();
			}
			else
			{
				// use this approach for the rest to maintain referrers when possible
				var w = window.open(href);

				try
				{
					// this can potentially fail, don't want to break
					w.opener = null;
				}
				catch (e) {}
			}

			e.preventDefault();
		};

		var initExternalWatcher = function()
		{
			var selector = 'a[target=_blank]',
				dataAttrAttacher = 'blank-handler';

			$(document)
				.on('click', selector, externalLinkClick)
				.on('focusin', selector, function(e)
				{
					// This approach is taken because middle click events do not bubble. This is a way of
					// getting the equivalent of event bubbling on middle clicks in Chrome.
					var $this = $(this);
					if ($this.data(dataAttrAttacher))
					{
						return;
					}

					$this.data(dataAttrAttacher, true)
						.click(externalLinkClick);
				});
		};

		return {
			initLinkProxy: initLinkProxy,
			initExternalWatcher: initExternalWatcher
		};
	})();

	// ################################## IGNORED CONTENT WATCHER ###########################################

	XF._IgnoredWatcher = XF.create({
		options: {
			container: 'body',
			ignored: '.is-ignored',
			link: '.js-showIgnored'
		},

		$container: null,
		authors: [],
		shown: false,

		__construct: function(options)
		{
			this.options = $.extend(true, {}, this.options, options || {});

			var $container = $(this.options.container);
			this.$container = $container;

			this.updateState();

			$container.on('click', this.options.link, XF.proxy(this, 'show'));
		},

		refresh: function($el)
		{
			if (!this.$container.find($el).length)
			{
				// el is not in our search area
				return;
			}

			if (this.shown)
			{
				// already showing, so apply that here as well
				this.show();
			}
			else
			{
				this.updateState();
			}
		},

		updateState: function()
		{
			if (this.shown)
			{
				// already showing
				return;
			}

			var $ignored = this.getIgnored(),
				authors = [];

			if (!$ignored.length)
			{
				// nothing to do - assume hidden by default
				return;
			}

			$ignored.each(function()
			{
				var author = $(this).data('author');
				if (author && $.inArray(author, authors) === -1)
				{
					authors.push(author);
				}
			});

			if (authors.length)
			{
				var textReplace = { names: authors.join(', ') };

				this.getLinks().each(function()
				{
					var $link = $(this),
						title = $link.attr('title');
					if (title)
					{
						$link.attr('title', Mustache.render(title, textReplace))
							.removeClass('is-hidden');
					}
				});
			}
			else
			{
				this.getLinks().each(function()
				{
					$(this).removeAttr('title').removeClass('is-hidden');
				});
			}
		},

		getIgnored: function()
		{
			return this.$container.find(this.options.ignored);
		},

		getLinks: function()
		{
			return this.$container.find(this.options.link);
		},

		show: function()
		{
			this.shown = true;
			this.getIgnored().removeClass('is-ignored');
			this.getLinks().addClass('is-hidden');
		},

		initializeHash: function()
		{
			if (window.location.hash)
			{
				var cleanedHash = window.location.hash.replace(/[^\w_#-]/g, '');
				if (cleanedHash === '#')
				{
					return;
				}

				var $jump = $(cleanedHash),
					ignoredSel = this.options.ignored,
					$ignored;

				if ($jump.is(ignoredSel))
				{
					$ignored = $jump;
				}
				else
				{
					$ignored = $jump.closest(ignoredSel);
				}

				if ($ignored && $ignored.length)
				{
					$ignored.removeClass('is-ignored');
					$jump.get(0).scrollIntoView(true);
				}
			}
		}
	});
	XF.IgnoreWatcher = new XF._IgnoredWatcher();

	// ################################ ACTION BAR HANDLER ##########################################

	XF.MultiBar = XF.create({
		options: {
			role: null,
			focusShow: false,
			className: '',
			fastReplace: false
		},

		$container: null,
		$multiBar: null,
		shown: false,

		__construct: function(content, options)
		{
			this.options = $.extend(true, {}, this.options, options || {});

			this.$multiBar = content instanceof $ ? content : $($.parseHTML(content));
			this.$multiBar
				.attr('role', this.options.role || 'dialog')
				.attr('aria-hidden', 'true')
				.on('multibar:hide', XF.proxy(this, 'hide'))
				.on('multibar:show', XF.proxy(this, 'show'));

			this.$container = $('<div class="multiBar-container" />');
			this.$container
				.html(this.$multiBar)
				.data('multibar', this)
				.addClass(this.options.className)
			this.$container.xfUniqueId();

			this.$container.appendTo('body');
			//XF.bottomFix(this.$container);
			XF.activate(this.$container);

			XF.MultiBar.cache[this.$container.attr('id')] = this;
		},

		show: function()
		{
			if (this.shown)
			{
				return;
			}

			this.shown = true;
			this.$multiBar.attr('aria-hidden', 'false');

			$('.p-pageWrapper').addClass('has-multiBar');

			if (this.options.fastReplace)
			{
				this.$multiBar.css('transition-duration', '0s');
			}

			var self = this;
			this.$container.appendTo('body');
			this.$multiBar.addClassTransitioned('is-active', function()
			{
				if (self.options.focusShow)
				{
					var $autoFocusFallback = self.$multiBar.find('.js-multiBarClose');
					XF.autoFocusWithin(self.$multiBar.find('.multiBar-content'), null, $autoFocusFallback);
				}

				self.$container.trigger('multibar:shown');
				XF.layoutChange();
			});

			if (this.options.fastReplace)
			{
				this.$multiBar.css('transition-duration', '');
			}

			this.$container.trigger('multibar:showing');

			XF.layoutChange();
		},

		hide: function()
		{
			if (!this.shown)
			{
				return;
			}

			this.shown = false;
			this.$multiBar.attr('aria-hidden', 'true');

			var self = this;
			this.$multiBar.removeClassTransitioned('is-active', function()
			{
				$('.p-pageWrapper').removeClass('has-multiBar');

				self.$container.trigger('multibar:hidden');
				XF.layoutChange();
			});

			this.$container.trigger('multibar:hiding');

			XF.layoutChange();
		},

		toggle: function(forceState)
		{
			var newState = (forceState === null ? !this.shown : forceState);

			newState ? this.show() : this.hide();
		},

		destroy: function()
		{
			var id = this.$container.attr('id'),
				cache = XF.MultiBar.cache;

			this.$container.remove();
			if (cache.hasOwnProperty(id))
			{
				delete cache[id];
			}
		},

		on: function()
		{
			this.$container.on.apply(this.$container, arguments);
		},

		getContainer: function()
		{
			return this.$container;
		},

		getMultiBar: function()
		{
			return this.$multiBar;
		}
	});
	XF.MultiBar.cache = {};

	XF.showMultiBar = function($html, options)
	{
		var MultiBar = new XF.MultiBar($html, options);
		MultiBar.show();
		return MultiBar;
	};

	XF.loadMultiBar = function(url, data, options, multiBarOptions)
	{
		if ($.isFunction(options))
		{
			options = {init: options};
		}

		options = $.extend({
			cache: false,
			beforeShow: null,
			afterShow: null,
			onRedirect: null,
			init: null,
			show: true
		}, options || {});

		var show = function(MultiBar)
		{
			if (options.beforeShow)
			{
				var e = $.Event();
				options.beforeShow(MultiBar, e);
				if (e.isDefaultPrevented())
				{
					return;
				}
			}

			if (options.show)
			{
				MultiBar.show();
			}

			if (options.afterShow)
			{
				var e = $.Event();
				options.afterShow(MultiBar, e);
				if (e.isDefaultPrevented())
				{
					return;
				}
			}
		};

		if (options.cache && XF.loadMultiBar.cache[url])
		{
			show(XF.loadMultiBar.cache[url]);
			return;
		}

		var multiBarAjaxHandler = function(data)
		{
			if (data.redirect)
			{
				if (options.onRedirect)
				{
					options.onRedirect(data, multiBarAjaxHandler);
				}
				else
				{
					XF.ajax('get', data.redirect, function(data)
					{
						multiBarAjaxHandler(data);
					});
				}
			}

			if (!data.html)
			{
				return;
			}

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				var MultiBar = new XF.MultiBar(XF.getMultiBarHtml({
					html: $html,
					title: container.title || container.h1
				}),  multiBarOptions);

				if (options.init)
				{
					options.init(MultiBar);
				}

				if (!options.cache)
				{
					MultiBar.on('multibar:hidden', function()
					{
						MultiBar.destroy();
					});
				}

				onComplete();

				if (options.cache)
				{
					XF.loadMultiBar.cache[url] = MultiBar;
				}

				show(MultiBar);
			});
		};

		return XF.ajax('post', url, data, function(data)
		{
			multiBarAjaxHandler(data);
		});
	};
	XF.loadMultiBar.cache = {};

	// ################################## OVERLAY HANDLER ###########################################

	XF.Overlay = XF.create({
		options: {
			backdropClose: true,
			escapeClose: true,
			focusShow: true,
			className: ''
		},

		$container: null,
		$overlay: null,
		shown: false,

		__construct: function(content, options)
		{
			this.options = $.extend(true, {}, this.options, options || {});

			this.$overlay = content instanceof $ ? content : $($.parseHTML(content));
			this.$overlay.attr('role', this.options.role || 'dialog')
				.attr('aria-hidden', 'true');

			this.$container = $('<div class="overlay-container" />').html(this.$overlay);
			this.$container
				.data('overlay', this)
				.xfUniqueId();

			var self = this;

			if (this.options.escapeClose)
			{
				this.$container.on('keydown.overlay', function(e)
				{
					if (e.which === 27)
					{
						self.hide();
					}
				});
			}

			if (this.options.backdropClose)
			{
				this.$container.on('mousedown', function(e)
				{
					self.$container.data('block-close', false);

					if (!$(e.target).is(self.$container))
					{
						// click didn't target container so block closing.
						self.$container.data('block-close', true);
					}
				});

				this.$container.on('click', function(e)
				{
					if ($(e.target).is(self.$container))
					{
						if (!self.$container.data('block-close'))
						{
							self.hide();
						}
					}

					self.$container.data('block-close', false);
				});
			}

			if (this.options.className)
			{
				this.$container.addClass(this.options.className);
			}

			this.$container.on('click', '.js-overlayClose', XF.proxy(this, 'hide'));

			this.$container.appendTo('body');
			XF.activate(this.$container);

			XF.Overlay.cache[this.$container.attr('id')] = this;

			this.$overlay.on('overlay:hide', XF.proxy(this, 'hide'));
			this.$overlay.on('overlay:show', XF.proxy(this, 'show'));
		},

		show: function()
		{
			if (this.shown)
			{
				return;
			}

			this.shown = true;

			this.$overlay.attr('aria-hidden', 'false');

			// reappending to the body ensures this is the last one, which should allow stacking
			var self = this;
			this.$container.appendTo('body').addClassTransitioned('is-active', function()
			{
				if (self.options.focusShow)
				{
					var $autoFocusFallback = self.$overlay.find('.js-overlayClose');
					XF.autoFocusWithin(self.$overlay.find('.overlay-content'), null, $autoFocusFallback);
				}

				self.$container.trigger('overlay:shown');
				XF.layoutChange();
			});

			this.$container.trigger('overlay:showing');

			XF.ModalOverlay.open();
			XF.layoutChange();
		},

		hide: function()
		{
			if (!this.shown)
			{
				return;
			}

			this.shown = false;

			this.$overlay.attr('aria-hidden', 'true');

			var self = this;
			this.$container.removeClassTransitioned('is-active', function()
			{
				self.$container.trigger('overlay:hidden');
				XF.ModalOverlay.close();
				XF.layoutChange();
			});

			this.$container.trigger('overlay:hiding');

			XF.layoutChange();
		},

		recalculate: function()
		{
			if (this.shown)
			{
				XF.Modal.updateScrollbarPadding();
			}
		},

		toggle: function()
		{
			this.shown ? this.hide() : this.show();
		},

		destroy: function()
		{
			var id = this.$container.attr('id'),
				cache = XF.Overlay.cache;

			this.$container.remove();
			if (cache.hasOwnProperty(id))
			{
				delete cache[id];
			}
		},

		on: function()
		{
			this.$container.on.apply(this.$container, arguments);
		},

		getContainer: function() { return this.$container; },
		getOverlay: function() { return this.$overlay; }
	});
	XF.Overlay.cache = {};

	XF.ModalOverlay = (function()
	{
		var count = 0,
			$applyEl = $('body').first();

		function open()
		{
			XF.Modal.open();

			count++;
			if (count == 1)
			{
				$applyEl.addClass('is-modalOverlayOpen');
			}
		}

		function close()
		{
			XF.Modal.close();

			if (count > 0)
			{
				count--;
				if (count == 0)
				{
					$applyEl.removeClass('is-modalOverlayOpen');
				}
			}
		}

		return {
			getOpenCount: function() { return count; },
			open: open,
			close: close
		}
	})();

	XF.Modal = (function()
	{
		var count = 0,
			$applyEl = $('body').first(),
			$html = $('html');

		var open = function()
		{
			count++;
			if (count == 1)
			{
				$applyEl.addClass('is-modalOpen');
				updateScrollbarPadding();
			}
		};
		var close = function()
		{
			if (count > 0)
			{
				count--;
				if (count == 0)
				{
					$applyEl.removeClass('is-modalOpen');
					updateScrollbarPadding();
				}
			}
		};

		var updateScrollbarPadding = function()
		{
			var side = 'right',
				value = $applyEl.hasClass('is-modalOpen') ? XF.measureScrollBar() + 'px' : '';

			if (XF.isRtl())
			{
				// Chrome and Firefox keep the body scrollbar on the right but IE/Edge flips it
				if (!XF.browser.chrome && !XF.browser.mozilla)
				{
					side = 'left';
				}
			}

			$html.css('margin-' + side, value);
		};

		return {
			getOpenCount: function() { return count; },
			open: open,
			close: close,
			updateScrollbarPadding: updateScrollbarPadding
		};
	})();

	XF.showOverlay = function($html, options)
	{
		var overlay = new XF.Overlay($html, options);
		overlay.show();
		return overlay;
	};
	XF.loadOverlay = function(url, options, overlayOptions)
	{
		if ($.isFunction(options))
		{
			options = {init: options};
		}

		options = $.extend({
			cache: false,
			beforeShow: null,
			afterShow: null,
			onRedirect: null,
			init: null,
			show: true
		}, options || {});

		var show = function(overlay)
		{
			if (options.beforeShow)
			{
				var e = $.Event();
				options.beforeShow(overlay, e);
				if (e.isDefaultPrevented())
				{
					return;
				}
			}

			if (options.show)
			{
				overlay.show();
			}

			if (options.afterShow)
			{
				var e = $.Event();
				options.afterShow(overlay, e);
				if (e.isDefaultPrevented())
				{
					return;
				}
			}
		};

		if (options.cache && XF.loadOverlay.cache[url])
		{
			show(XF.loadOverlay.cache[url]);
			return;
		}

		var overlayAjaxHandler = function(data)
		{
			if (data.redirect)
			{
				if (options.onRedirect)
				{
					options.onRedirect(data, overlayAjaxHandler);
				}
				else
				{
					XF.ajax('get', data.redirect, function(data)
					{
						overlayAjaxHandler(data);
					});
				}
			}

			if (!data.html)
			{
				return;
			}

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				var overlay = new XF.Overlay(XF.getOverlayHtml({
					html: $html,
					title: container.title || container.h1
				}), overlayOptions);
				if (options.init)
				{
					options.init(overlay);
				}
				if (!options.cache)
				{
					overlay.on('overlay:hidden', function()
					{
						overlay.destroy();
					});
				}

				onComplete();

				if (options.cache)
				{
					XF.loadOverlay.cache[url] = overlay;
				}

				show(overlay);
			});
		};

		return XF.ajax('get', url, function(data)
		{
			overlayAjaxHandler(data);
		});
	};
	XF.loadOverlay.cache = {};

	// ################################## NAVIGATION DEVICE WATCHER ###########################################

	/**
	 * Allows querying of the current input device (mouse or keyboard) -- .isKeyboardNav()
	 * And sets a CSS class (has-pointer-nav) on <html> to allow styling based on current input
	 *
	 * @type {{initialize, toggle, isKeyboardNav}}
	 */
	XF.NavDeviceWatcher = (function()
	{
		var isKeyboard = true;

		function initialize()
		{
			$(document).onPassive(
			{
				mousedown: function()
				{
					toggle(false);
				},
				keydown: function(e)
				{
					switch (e.key)
					{
						case 'Tab':
						case 'Enter':
							toggle(true);
					}
				}
			});
		}

		function toggle(toKeyboard)
		{
			if (toKeyboard != isKeyboard)
			{
				$('html').toggleClass('has-pointer-nav', !toKeyboard);

				isKeyboard = toKeyboard;
			}
		}

		function isKeyboardNav()
		{
			return isKeyboard;
		}

		return {
			initialize: initialize,
			toggle: toggle,
			isKeyboardNav: isKeyboardNav
		};
	})();

	XF.ScrollButtons = (function()
	{
		var hideTimer = null,
			pauseScrollWatch = false,
			upOnly = false,
			isShown = false,
			scrollTop = window.pageYOffset || document.documentElement.scrollTop,
			scrollDir = null,
			scrollTopDirChange = null,
			scrollTrigger,
			$buttons = null;

		function initialize()
		{
			if ($buttons && $buttons.length)
			{
				// already initialized
				return false;
			}

			$buttons = $('.js-scrollButtons');
			if (!$buttons.length)
			{
				return false;
			}

			if ($buttons.data('trigger-type') === 'up')
			{
				upOnly = true;
			}

			$buttons.on({
				'mouseenter focus': enter,
				'mouseleave blur': leave,
				'click': click
			});

			$(window).onPassive('scroll', onScroll);

			return true;
		}

		function onScroll(e)
		{
			if (pauseScrollWatch)
			{
				return;
			}

			var newScrollTop = window.pageYOffset || document.documentElement.scrollTop,
				oldScrollTop = scrollTop;

			scrollTop = newScrollTop;

			if (newScrollTop > oldScrollTop)
			{
				if (scrollDir != 'down')
				{
					scrollDir = 'down';
					scrollTopDirChange = oldScrollTop;
				}
			}
			else if (newScrollTop < oldScrollTop)
			{
				if (scrollDir != 'up')
				{
					scrollDir = 'up';
					scrollTopDirChange = oldScrollTop;
				}
			}
			else
			{
				// didn't scroll?
				return;
			}

			if (upOnly)
			{
				// downward scroll or we're near the top anyway
				if (scrollDir !== 'up' || scrollTop < 100)
				{
					if (scrollTrigger)
					{
						scrollTrigger.cancel();
						scrollTrigger = null;
					}
					return;
				}

				// only trigger after scrolling up 30px to reduce false positives
				if (scrollTopDirChange - newScrollTop < 30)
				{
					return;
				}
			}

			if (scrollTrigger)
			{
				// already about to be triggered
				return;
			}

			// note that Chrome on Android can heavily throttle setTimeout, so use a requestAnimationFrame
			// alternative if possible to ensure this triggers when expected
			scrollTrigger = XF.requestAnimationTimeout(function()
			{
				scrollTrigger = null;

				show();
				startHideTimer();
			}, 200);
		}

		function show()
		{
			if (!isShown)
			{
				$buttons.addClassTransitioned('is-active');
				isShown = true;
			}
		}

		function hide()
		{
			if (isShown)
			{
				$buttons.removeClassTransitioned('is-active');
				isShown = false;
			}
		}

		function startHideTimer()
		{
			clearHideTimer();

			hideTimer = setTimeout(function()
			{
				hide();
			}, 3000);
		}

		function clearHideTimer()
		{
			clearTimeout(hideTimer);
		}

		function enter()
		{
			clearHideTimer();

			show();
		}

		function leave()
		{
			clearHideTimer();
		}

		function click()
		{
			pauseScrollWatch = true;

			setTimeout(function()
			{
				pauseScrollWatch = false;
			}, 500);

			hide();
		}

		return {
			initialize: initialize,
			show: show,
			hide: hide,
			startHideTimer: startHideTimer,
			clearHideTimer: clearHideTimer
		}
	})();

	// ################################## KEYBOARD SHORTCUT HANDLER ###########################################

	/**
	 * Activates keyboard shortcuts for elements based on data-xf-key attributes
	 *
	 * @type {{initialize, initializeElements}}
	 */
	XF.KeyboardShortcuts = (function()
	{
		var shortcuts = {},

			Ctrl = 1,
			Alt  = 2,
			Meta = 4,

			debug = false;

		function initialize()
		{
			$(document.body).onPassive('keyup', keyEvent);
		}

		function initializeElements(root)
		{
			var $root = $(root);

			if ($root.length > 1)
			{
				$root.each(function()
				{
					initializeElements(this);
				});
				return;
			}

			if ($root.is('[data-xf-key]'))
			{
				initializeElement($root[0]);
			}
			$root.find('[data-xf-key]').each(function()
			{
				initializeElement(this);
			});

			if (debug) console.info('Registered keyboard shortcuts: %o', shortcuts);
		}

		function initializeElement(el)
		{
			// accepts a shortcut key either as 'a', 'B', etc., or a charcode with a # prefix - '#97', '#56'
			var shortcut = String($(el).data('xf-key')),
				key = shortcut.substr(shortcut.lastIndexOf('+') + 1),
				charCode = key[0] === '#' ? key.substr(1): key.toUpperCase().charCodeAt(0),
				codeInfo = shortcut.toUpperCase().split('+'),
				modifierCode = getModifierCode(
					codeInfo.indexOf('CTRL') !== -1,
					codeInfo.indexOf('ALT') !== -1,
					codeInfo.indexOf('META') !== -1
				);

			if (modifierCode)
			{
				if (XF.Keyboard.isStandardKey(charCode))
				{
					shortcuts[charCode] = shortcuts[charCode] || {};
					shortcuts[charCode][modifierCode] = el;

					if (debug) console.info('Shortcut %c%s%c registered as %s + %s for %s', 'color:red;font-weight:bold;font-size:larger', shortcut, 'color:inherit;font-weight:inherit;font-size:inherit', charCode, modifierCode, el);
				}
				else
				{
					console.warn('It is not possible to specify a keyboard shortcut using this key combination (%s)', shortcut);
				}
			}
			else
			{
				shortcuts[key] = el;

				if (debug) console.info('Shortcut %c%s%c registered as %s for %s', 'color:red;font-weight:bold;font-size:larger', shortcut, 'color:inherit;font-weight:inherit;font-size:inherit', key, el);
			}
		}

		function keyEvent(e)
		{
			switch (e.key)
			{
				case 'Escape':
					XF.MenuWatcher.closeAll(); // close all menus
					XF.hideTooltips();
					return;

				case 'Shift':
				case 'Control':
				case 'Alt':
				case 'Meta':
					return;
			}

			if (!XF.Keyboard.isShortcutAllowed(document.activeElement))
			{
				return;
			}

			if (debug) console.log('KEYUP: key:%s, which:%s (charCode from key: %s), Decoded from e.which: %s%s%s%s',
				e.key, e.which, e.key.charCodeAt(0),
				(e.ctrlKey?"CTRL+":""),
				(e.altKey?"ALT+":""),
				(e.metaKey?"META+":""),
				String.fromCharCode(e.which)
			);

			if (shortcuts.hasOwnProperty(e.key) && getModifierCodeFromEvent(e) == 0) // try simple mapping first
			{
				if (fireShortcut(shortcuts[e.key]))
				{
					return;
				}
			}

			if (shortcuts.hasOwnProperty(e.which)) // try complex mapping next
			{
				var modifierCode = getModifierCodeFromEvent(e);

				if (shortcuts[e.which].hasOwnProperty(modifierCode))
				{
					if (fireShortcut(shortcuts[e.which][modifierCode]))
					{
						return;
					}
				}
			}
		}

		function fireShortcut(target)
		{
			// only act on elements that are :visible
			var $target = $(target).filter(':visible');

			if ($target.length)
			{
				XF.NavDeviceWatcher.toggle(true);

				if ($target.is(XF.getKeyboardInputs()))
				{
					$target.autofocus();
				}
				else if ($target.is('a[href]'))
				{
					$target.get(0).click();
				}
				else
				{
					$target.click();
				}

				return true;
			}

			return false;
		}

		function getModifierCode(CtrlKey, AltKey, MetaKey)
		{
			return 0
			+ CtrlKey  ? Ctrl  : 0
			+ AltKey   ? Alt   : 0
			+ MetaKey  ? Meta  : 0;
		}

		function getModifierCodeFromEvent(event)
		{
			return getModifierCode(event.ctrlKey, event.altKey, event.metaKey);
		}

		return {
			initialize: initialize,
			initializeElements: initializeElements
		}
	})();

	/**
	 * Collection of methods for working with the keyboard
	 */
	XF.Keyboard =
	{
		/**
		 * Determines whether a keyboard shortcut can be fired with the current activeElement
		 *
		 * @param object activeElement (usually document.activeElement)
		 *
		 * @returns {boolean}
		 */
		isShortcutAllowed: function(activeElement)
		{
			switch (activeElement.tagName)
			{
				case 'TEXTAREA':
				case 'SELECT':
					return false;

				case 'INPUT':
					switch (activeElement.type)
					{
						case 'checkbox':
						case 'radio':
						case 'submit':
						case 'reset':
							return true;
						default:
							return false;
					}

				case 'BODY':
					return true;

				default:
					// active element can be different in IE bail out if the active element is a child of the editor
					if (XF.browser.msie)
					{
						var $el = $(activeElement);
						if ($el.parents('.fr-element').length)
						{
							return false;
						}
					}
					return activeElement.contentEditable === 'true' ? false : true;
			}
		},

		isStandardKey: function(charcode)
		{
			return (charcode >= 48 && charcode <= 90);
		}
	};

	// ################################## FORM VALIDATION HANDLER ###########################################

	/**
	 * Sets up some custom behaviour on forms so that when invalid inputs are scrolled
	 * to they are not covered by fixed headers.
	 *
	 * @type {{initialize, initializeElements}}
	 */
	XF.FormInputValidation = (function()
	{
		var $forms = {};

		function initialize()
		{
			$forms = $('form').not('[novalidate]');

			prepareForms();
		}

		function initializeElements(root)
		{
			var $root = $(root);

			if ($root.length > 1)
			{
				$root.each(function() { initializeElements(this); });
				return;
			}

			if ($root.is('form'))
			{
				prepareForm($root);
			}
		}

		function prepareForms()
		{
			if (!$forms.length)
			{
				return;
			}

			$forms.each(function()
			{
				prepareForm($(this));
			});
		}

		function prepareForm($form)
		{
			$form.find(':input').on('invalid', { form: $form }, onInvalidInput);
		}

		function onInvalidInput(event)
		{
			var $input = $(this),
				$form = event.data.form,
				$first = $form.find(':invalid').first();

			if ($input[0] === $first[0])
			{
				if (XF.isElementVisible($input))
				{
					// element is already visible so skip
					return;
				}

				var offset = 100;
				var $overlayContainer = $form.closest('.overlay-container.is-active');

				if ($overlayContainer.length)
				{
					$overlayContainer.scrollTop(
						$input.offset().top - $overlayContainer.offset().top + $overlayContainer.scrollTop() - offset
					);
				}
				else
				{
					// put the input 100px from the top of the screen
					$input[0].scrollIntoView();
					window.scrollBy(0, -offset);
				}
			}
		}

		return {
			initialize: initialize,
			initializeElements: initializeElements
		}
	})();

	// ################################## NOTICE WATCHER ###########################################

	XF.NoticeWatcher = (function()
	{
		function initialize()
		{
			$(document).on('xf:notice-change xf:layout', XF.proxy(this, 'checkNotices'));
			this.checkNotices();
		}

		function checkNotices()
		{
			var noticeHeight = 0;
			var $bottomFixers = $(document).find('.js-bottomFixTarget .notices--bottom_fixer .js-notice');

			$bottomFixers.each(function()
			{
				var $notice = $(this);

				if ($notice.is(':visible'))
				{
					noticeHeight += $notice.height();
				}
			});

			$(document).find('footer.p-footer').css('margin-bottom', noticeHeight);
		}

		return {
			initialize: initialize,
			checkNotices: checkNotices
		};
	})();

	// ################################## PUSH NOTIFICATION HANDLER ###########################################

	XF.Push = (function()
	{
		function initialize()
		{
			if (!XF.Push.isSupported())
			{
				return;
			}

			if (!XF.config.skipServiceWorkerRegistration)
			{
				registerWorker();
			}
		}

		function registerWorker(onRegisterSuccess, onRegisterError)
		{
			navigator.serviceWorker.register(XF.canonicalizeUrl('js/xf/service_worker.js'))
				.then(function(swReg)
				{
					XF.Push.serviceWorkerReg = swReg;

					getSubscription();

					if (onRegisterSuccess)
					{
						onRegisterSuccess();
					}
				})
				.catch(function(error)
				{
					console.error('Service worker error', error);

					if (onRegisterError)
					{
						onRegisterError();
					}
				});
		}

		function getSubscription()
		{
			XF.Push.serviceWorkerReg.pushManager.getSubscription()
				.then(function(subscription)
				{
					XF.Push.isSubscribed = !(subscription === null);

					if (XF.Push.isSubscribed)
					{
						$(document).trigger('push:init-subscribed');

						// If the browser is subscribed, but there is no userId then
						// we should unsubscribe to avoid leaking notifications to
						// unauthenticated users on a shared device.
						// If the server key doesn't match, then we should unsubscribe as we'd
						// need to resubscribe with the new key.
						if (XF.config.userId && isExpectedServerKey(subscription))
						{
							XF.Push.updateUserSubscription(subscription, 'update');
						}
						else
						{
							subscription.unsubscribe();
							XF.Push.updateUserSubscription(subscription, 'unsubscribe');
						}
					}
					else
					{
						$(document).trigger('push:init-unsubscribed');
					}
				});
		}

		function getPushHistoryUserIds()
		{
			return XF.LocalStorage.getJson('push_history_user_ids') || {};
		}

		function setPushHistoryUserIds(userIds)
		{
			XF.LocalStorage.setJson('push_history_user_ids', userIds || {});
		}

		function hasUserPreviouslySubscribed(userId)
		{
			var userIdHistory = XF.Push.getPushHistoryUserIds();
			return userIdHistory.hasOwnProperty(userId || XF.config.userId);
		}

		function addUserToPushHistory(userId)
		{
			var userIdHistory = XF.Push.getPushHistoryUserIds();
			userIdHistory[userId || XF.config.userId] = true;
			XF.Push.setPushHistoryUserIds(userIdHistory);
		}

		function removeUserFromPushHistory(userId)
		{
			// also remove history entry as this is an explicit unsubscribe
			var userIdHistory = XF.Push.getPushHistoryUserIds();
			delete userIdHistory[userId || XF.config.userId];
			XF.Push.setPushHistoryUserIds(userIdHistory);
		}

		var cancellingSub = null;

		function handleUnsubscribeAction(onUnsubscribe, onUnsubscribeError)
		{
			if (!XF.Push.isSubscribed)
			{
				return;
			}

			var t = this;

			XF.Push.serviceWorkerReg.pushManager
				.getSubscription()
				.then(function(subscription)
				{
					if (subscription)
					{
						cancellingSub = subscription;
						return subscription.unsubscribe();
					}
				})
				.catch(function(error)
				{
					console.error('Error unsubscribing', error);

					if (onUnsubscribeError)
					{
						onUnsubscribeError();
					}
				})
				.then(function()
				{
					if (cancellingSub)
					{
						XF.Push.updateUserSubscription(cancellingSub, 'unsubscribe');
					}

					XF.Push.isSubscribed = false;

					if (onUnsubscribe)
					{
						onUnsubscribe();
					}
				});
		}

		function handleSubscribeAction(suppressNotification, onSubscribe, onSubscribeError)
		{
			if (XF.Push.isSubscribed)
			{
				return;
			}

			Notification.requestPermission().then(function(result)
			{
				if (result !== 'granted')
				{
					console.error('Permission was not granted');
					return;
				}

				var applicationServerKey = XF.Push.base64ToUint8(XF.config.pushAppServerKey);

				XF.Push.serviceWorkerReg.pushManager
					.subscribe({
						userVisibleOnly: true,
						applicationServerKey: applicationServerKey
					})
					.then(function(subscription)
					{
						XF.Push.updateUserSubscription(subscription, 'insert');
						XF.Push.isSubscribed = true;

						var options = {
							body: XF.phrase('push_enable_notification_body'),
							dir: XF.isRtl() ? 'rtl' : 'ltr'
						};
						if (XF.config.publicMetadataLogoUrl)
						{
							options['icon'] = XF.config.publicMetadataLogoUrl;
						}
						if (XF.config.publicPushBadgeUrl)
						{
							options['badge'] = XF.config.publicPushBadgeUrl;
						}

						if (!suppressNotification)
						{
							XF.Push.serviceWorkerReg.showNotification(
								XF.phrase('push_enable_notification_title'), options
							);
						}

						if (XF.config.userId)
						{
							XF.Push.addUserToPushHistory();
						}

						if (onSubscribe)
						{
							onSubscribe();
						}
					})
					.catch(function(error)
					{
						console.error('Failed to subscribe the user: ', error);

						if (onSubscribeError)
						{
							onSubscribeError();
						}
					});
			});
		}

		function handleToggleAction(onUnsubscribe, onUnsubscribeError, onSubscribe, onSubscribeError)
		{
			if (XF.Push.isSubscribed)
			{
				XF.Push.handleUnsubscribeAction(onUnsubscribe, onUnsubscribeError);
			}
			else
			{
				XF.Push.handleSubscribeAction(false, onSubscribe, onSubscribeError);
			}
		}

		function updateUserSubscription(subscription, type)
		{
			if (type === 'update' && XF.Cookie.get('push_subscription_updated'))
			{
				return;
			}

			var key = subscription.getKey('p256dh'),
				token = subscription.getKey('auth'),
				encoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

			$.ajax({
				url: XF.canonicalizeUrl('index.php?misc/update-push-subscription'),
				type: 'post',
				data: {
					endpoint: subscription.endpoint,
					key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
					token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
					encoding: encoding,
					unsubscribed: (type === 'unsubscribe') ? 1 : 0,
					_xfResponseType: 'json',
					_xfToken: XF.config.csrf
				},
				cache: false,
				dataType: 'json',
				global: false
			}).always(function()
			{
				if (type === 'update')
				{
					XF.Cookie.set('push_subscription_updated', '1');
				}
			});
		}

		function isSupported()
		{
			return (
				XF.config.enablePush
				&& XF.config.pushAppServerKey
				&& XF.getApp() === 'public'
				&& 'serviceWorker' in navigator
				&& 'PushManager' in window
				&& 'Notification' in window
			);
		}

		function base64ToUint8(base64String)
		{
			var padding = "=".repeat((4 - base64String.length % 4) % 4);
			var base64 = (base64String + padding).replace(/\-/g, "+").replace(/_/g, "/");

			var rawData = window.atob(base64);
			var outputArray = new Uint8Array(rawData.length);

			for (var i = 0; i < rawData.length; ++i)
			{
				outputArray[i] = rawData.charCodeAt(i);
			}

			return outputArray;
		}

		function isExpectedServerKey(input)
		{
			if (input instanceof PushSubscription)
			{
				input = input.options.applicationServerKey;
			}

			if (typeof input === 'string')
			{
				return (XF.config.pushAppServerKey === input);
			}

			if (input.buffer && input.BYTES_PER_ELEMENT)
			{
				// typed array -- not exposed directly to JS
				input = input.buffer;
			}
			if (!(input instanceof ArrayBuffer))
			{
				throw new Error("input must be an array buffer or convertable to it");
			}

			var serverKey = base64ToUint8(XF.config.pushAppServerKey).buffer,
				length = serverKey.byteLength;

			if (length !== input.byteLength)
			{
				return false;
			}

			var serverKeyView = new DataView(serverKey),
				inputView = new DataView(input);

			for (var i = 0; i < length; i++)
			{
				if (serverKeyView.getUint8(i) !== inputView.getUint8(i))
				{
					return false;
				}
			}

			return true;
		}

		return {
			serviceWorkerReg: null,
			isSubscribed: null,
			initialize: initialize,
			registerWorker: registerWorker,
			getPushHistoryUserIds: getPushHistoryUserIds,
			setPushHistoryUserIds: setPushHistoryUserIds,
			hasUserPreviouslySubscribed: hasUserPreviouslySubscribed,
			addUserToPushHistory: addUserToPushHistory,
			removeUserFromPushHistory: removeUserFromPushHistory,
			handleUnsubscribeAction: handleUnsubscribeAction,
			handleSubscribeAction: handleSubscribeAction,
			handleToggleAction: handleToggleAction,
			updateUserSubscription: updateUserSubscription,
			isSupported: isSupported,
			base64ToUint8: base64ToUint8,
			isExpectedServerKey: isExpectedServerKey
		}
	})();

	// ################################## BB CODE EXPAND WATCHER ###########################################

	XF.BbBlockExpand = (function()
	{
		var containerSel = '.bbCodeBlock--expandable';

		function watch()
		{
			$(document).on('click', '.bbCodeBlock-expandLink', function(e)
			{
				var $target = $(e.target);
				$target.closest(containerSel).addClassTransitioned('is-expanded', XF.layoutChange);
			});

			$(window).onPassive('resize', function()
			{
				checkSizing(document);
			});

			$(document).on('embed:loaded', function()
			{
				checkSizing(document);
			});
		}

		function checkSizing(el)
		{
			$(el).find(containerSel + ':not(.is-expanded)').each(function()
			{
				var $this = $(this),
					content = $this.find('.bbCodeBlock-expandContent')[0];

				if (!content)
				{
					return;
				}

				var timer,
					delay = 0,
					check = function()
					{
						var scroll = content.scrollHeight,
							offset = content.offsetHeight;

						if (scroll == 0 || offset == 0)
						{
							if (delay > 2000)
							{
								return;
							}
							if (timer)
							{
								clearTimeout(timer);
								delay += 200;
							}
							timer = setTimeout(check, delay);
							return;
						}

						if (scroll > offset + 1) // +1 resolves a Chrome rounding issue
						{
							$this.addClass('is-expandable');
						}
						else
						{
							$this.removeClass('is-expandable');
						}
					};

				check();

				if (!$this.data('expand-check-triggered'))
				{
					$this.data('expand-check-triggered', true);

					$this.find('img').one('load', check);

					if (window.MutationObserver)
					{
						var observer,
							mutationTimeout,
							allowMutationTrigger = true,
							mutationTrigger = function()
							{
								allowMutationTrigger = false;
								check();

								// prevent triggers for a little bit after this so we limit thrashing
								setTimeout(function()
								{
									allowMutationTrigger = true;
								}, 100);
							};

						observer = new MutationObserver(function(mutations)
						{
							if ($this.hasClass('is-expanded'))
							{
								observer.disconnect();
								return;
							}

							if (!allowMutationTrigger)
							{
								return;
							}

							if (mutationTimeout)
							{
								clearTimeout(mutationTimeout);
							}
							mutationTimeout = setTimeout(mutationTrigger, 200);
						});
						observer.observe(this, {
							attributes: true,
							childList: true,
							subtree: true
						});
					}
				}
			});
		}

		return {
			watch: watch,
			checkSizing: checkSizing
		}
	})();

	// ################################## UNFURL LOADER WATCHER ###########################################

	XF.UnfurlLoader = (function()
	{
		var unfurlIds = [],
			pending = false,
			pendingIds = [];

		function activateContainer(container)
		{
			var $unfurls = $(container).find('.js-unfurl');

			if (!$unfurls.length)
			{
				return;
			}

			$unfurls.each(function()
			{
				var $el = $(this);

				if ($el.data('pending') === false || $el.data('pending-seen'))
				{
					return true;
				}

				$el.data('pending-seen', true);

				var id = $el.data('result-id');
				if (pending)
				{
					pendingIds.push(id);
				}
				else
				{
					unfurlIds.push(id);
				}
			});

			unfurl();
		}

		function unfurl()
		{
			if (!unfurlIds.length || pending)
			{
				return;
			}

			var lastResponseLength = null;

			function onResponseContent(response)
			{
				var currentResponse, currentEnd;

				if (lastResponseLength === null)
				{
					currentResponse = response;
				}
				else
				{
					currentResponse = response.substring(lastResponseLength);
				}

				currentEnd = currentResponse.indexOf("\n");
				if (currentEnd === -1)
				{
					// partial response (no line-break) so wait for more progress
					return;
				}

				// gets the response up to the first line-break delimiter
				currentResponse = currentResponse.substring(0, currentEnd);

				if (lastResponseLength === null)
				{
					lastResponseLength = currentResponse.length;
				}
				else
				{
					lastResponseLength += currentResponse.length;
				}

				// ignore trailing line break
				lastResponseLength++;

				XF.UnfurlLoader.handleResponse(JSON.parse(currentResponse));

				if (lastResponseLength < response.length)
				{
					onResponseContent(response);
				}
			}

			pending = true;

			XF.ajax(
				'post',
				XF.canonicalizeUrl('unfurl.php'),
				{ result_ids: unfurlIds },
				function(data)
				{
					onResponseContent(data);
				},
				{
					skipDefault: true,
					dataType: 'text',
					xhrFields: {
						onprogress: function(e)
						{
							var response = e.currentTarget.response;

							if (!response.length)
							{
								return;
							}

							onResponseContent(response);
						}
					}
				}
			).always(function()
			{
				unfurlIds = [];
				pending = false;
				lastResponseLength = null;

				if (pendingIds)
				{
					unfurlIds = pendingIds;
					pendingIds = [];
					pending = false;

					setTimeout(unfurl, 0);
				}
			});

		}

		function handleResponse(data)
		{
			var $unfurl = $('.js-unfurl[data-result-id="' + data.result_id + '"]');

			if (!$unfurl.length)
			{
				return;
			}

			if (data.success)
			{
				XF.setupHtmlInsert(data.html, function($html, container, onComplete)
				{
					$unfurl.replaceWith($html);
				});
			}
			else
			{
				var $link = $unfurl.find('.js-unfurl-title a');
				$link.text($unfurl.data('url'))
					.addClass('bbCodePlainUnfurl')
					.removeClass('fauxBlockLink-blockLink');
				$unfurl.replaceWith($link);
			}
		}

		return {
			activateContainer: activateContainer,
			unfurl: unfurl,
			handleResponse: handleResponse
		}
	})();

	// ############################### ELEMENT EVENT HANDLER SYSTEM ########################################

	/**
	 * This system allows elements to have a trigger event attached (eg: click, focus etc.),
	 * such that the code for handling the event is only attached at the time that the event
	 * is actually triggered, making for fast page initialization time.
	 */
	XF.Event = (function()
	{
		var mapper = new XF.ClassMapper();

		var eventsWatched = {},
			pointerDataKey = 'xf-pointer-type';

		var watch = function(eventType)
		{
			eventType = String(eventType).toLowerCase();

			function isValidTarget(e, target)
			{
				if (!target)
				{
					target = e.currentTarget;
				}

				if (!target || !target.getAttribute)
				{
					// not an element so can't have a handler
					return false;
				}

				var $target = $(target);

				if ($target.is('a') && !$target.data('click-allow-modifier'))
				{
					// abort if the event has a modifier key
					if (e.ctrlKey || e.shiftKey || e.altKey || e.metaKey)
					{
						return false;
					}

					// abort if the event is a middle or right-button click
					if (e.which > 1)
					{
						return false;
					}
				}

				if ($target.closest('[contenteditable=true]').length)
				{
					return false;
				}

				return true;
			}

			if (!eventsWatched.hasOwnProperty(eventType))
			{
				eventsWatched[eventType] = true;

				$(document).on(eventType, '[data-xf-' + eventType + ']', function(e)
				{
					var target = e.currentTarget;

					if (isValidTarget(e, target))
					{
						var $target = $(target),
							type = $target.data(pointerDataKey);

						e.xfPointerType = e.pointerType || type || '';

						initElement(target, eventType, e);
					}
				});

				$(document).on('pointerdown', '[data-xf-' + eventType + ']', function(e)
				{
					var target = e.currentTarget;

					if (isValidTarget(e, target))
					{
						$(target).data(pointerDataKey, e.pointerType);
					}
				});
			}
		};

		var initElement = function(target, eventType, e)
		{
			var $target = $(target),
				handlerList = $target.data('xf-' + eventType).split(' ') || [],
				handlerObjects = $target.data('xf-' + eventType + '-handlers') || {},
				identifier, obj, i, options;

			for (i = 0; i < handlerList.length; i++)
			{
				identifier = handlerList[i];
				if (!identifier.length)
				{
					continue;
				}

				if (!handlerObjects[identifier])
				{
					obj = mapper.getObjectFromIdentifier(identifier);
					if (!obj)
					{
						console.error('Could not find %s handler for %s', eventType, identifier);
						continue;
					}

					options = $target.data('xf-' + identifier) || {};
					handlerObjects[identifier] = new obj($target, options);
				}

				if (e && handlerObjects[identifier]._onEvent(e) === false)
				{
					break;
				}
			}

			$target.data('xf-' + eventType + '-handlers', handlerObjects);

			return handlerObjects;
		};

		var getElementHandler = function($el, handlerName, eventType)
		{
			var handlers = $el.data('xf-' + eventType + '-handlers');

			if (!handlers)
			{
				handlers = XF.Event.initElement($el[0], eventType);
			}

			if (handlers && handlers[handlerName])
			{
				return handlers[handlerName];
			}
			else
			{
				return null;
			}
		};

		var AbstractHandler = XF.create(
			{
				initialized: false,
				eventType: 'click',
				eventNameSpace: null,
				$target: null,
				options: {},

				__construct: function($target, options)
				{
					this.$target = $target;
					this.options = XF.applyDataOptions(this.options, $target.data(), options);
					this.eventType = this.eventType.toLowerCase();

					if (!this.eventNameSpace)
					{
						throw new Error('Please provide an eventNameSpace for your extended ' + this.eventType + ' handler class');
					}

					this._init();
				},

				/**
				 * 'protected' wrapper function for init(),
				 *  containing Before/AfterInit events
				 */
				_init: function()
				{
					var beforeInitEvent = new $.Event('xf-' + this.eventType + ':before-init.' + this.eventNameSpace),
						returnValue = false;

					this.$target.trigger(beforeInitEvent, [this]);

					if (!beforeInitEvent.isDefaultPrevented())
					{
						returnValue = this.init();

						this.$target.trigger('xf-' + this.eventType + ':after-init.' + this.eventNameSpace, [this, returnValue]);
					}

					this.initialized = true;

					return returnValue;
				},

				_onEvent: function(e)
				{
					var beforeEvent = new $.Event('xf-' + this.eventType + ':before-' + this.eventType + '.' + this.eventNameSpace),
						returnValue = null;

					this.$target.trigger(beforeEvent, [this]);

					if (!beforeEvent.isDefaultPrevented())
					{
						if (typeof this[this.eventType] == 'function')
						{
							returnValue = this[this.eventType](e);
						}
						else if (typeof this.onEvent == 'function')
						{
							returnValue = this.onEvent(e);
						}
						else
						{
							console.error('You must provide a %1$s(e) method for your %1$s event handler', this.eventType, this.eventNameSpace);
							e.preventDefault();
							return false;
						}

						this.$target.trigger('xf-' + this.eventType + ':after-' + this.eventType + '.' + this.eventNameSpace, [this, returnValue, e]);
					}

					return null;
				},

				// methods to be overridden by inheriting classes
				init: function()
				{
					console.error('This is the abstract init method for XF.%s, which must be overridden.', this.eventType, this.eventNameSpace);
				}
			});

		return {
			watch: watch,
			initElement: initElement,
			getElementHandler: getElementHandler,
			register: function(eventType, identifier, className)
			{
				XF.Event.watch(eventType);
				mapper.add(identifier, className);
			},
			extend: function(identifier, extension)
			{
				mapper.extend(identifier, extension);
			},
			newHandler: function(extend)
			{
				return XF.extend(AbstractHandler, extend);
			},
			AbstractHandler: AbstractHandler
		};
	})();

	// ################################## CLICK HANDLER SYSTEM ###########################################

	/**
	 * @deprecated This will be retired in a future version, use XF.Event instead.
	 */
	XF.Click = (function()
	{
		return {
			watch: function () {
				return XF.Event.watch('click');
			},
			initElement: function(target, clickE)
			{
				return XF.Event.initElement(target, 'click', clickE);
			},
			getElementHandler: function ($el, handlerName) {
				return XF.Event.getElementHandler($el, handlerName, 'click');
			},
			register: function(identifier, className)
			{
				XF.Event.watch('click');
				return XF.Event.register('click', identifier, className);
			},
			extend: function(identifier, extension)
			{
				return XF.Event.extend(identifier, extension);
			},
			newHandler: function(extend)
			{
				return XF.Event.newHandler(extend);
			}
		};
	})();

	// ################################## ELEMENT HANDLER SYSTEM ##########################################

	/**
	 * This system allows elements with data-xf-init to be initialized at page load time
	 */
	XF.Element = (function()
	{
		var elements = [];

		var mapper = new XF.ClassMapper();

		var applyHandler = function($el, handlerId, options)
		{
			var handlers = $el.data('xf-element-handlers') || {};
			if (handlers[handlerId])
			{
				return handlers[handlerId];
			}

			var ctor = mapper.getObjectFromIdentifier(handlerId);
			if (!ctor)
			{
				return null;
			}

			var obj = new ctor($el, options || {});

			handlers[handlerId] = obj;
			$el.data('xf-element-handlers', handlers);

			var hp = _getHandlerPrefixed(handlerId);
			elements[hp] = elements[hp] || [];
			elements[hp].push(obj);

			obj.init();

			return obj;
		};

		var getHandlers = function(handlerId)
		{
			var hp = _getHandlerPrefixed(handlerId);

			if (typeof elements[hp] != 'undefined')
			{
				return elements[hp];
			}

			return false;
		};

		var _getHandlerPrefixed = function(handlerId)
		{
			return 'XF.Element.Handler.' + handlerId;
		};

		var getHandler = function($el, handlerId)
		{
			var handlers = $el.data('xf-element-handlers');
			if (handlers === undefined)
			{
				initializeElement($el);
				handlers = $el.data('xf-element-handlers');
			}

			if (handlers && handlers[handlerId])
			{
				return handlers[handlerId];
			}
			else
			{
				return null;
			}
		};

		var initializeElement = function(el)
		{
			if (el instanceof $)
			{
				el = el[0];
			}

			if (!el || !el.getAttribute)
			{
				// not an element -- probably a text node
				return;
			}

			var init = el.getAttribute('data-xf-init');
			if (!init)
			{
				return;
			}

			var parts = init.split(' '),
				len = parts.length,
				$el = $(el),
				handlerId;
			for (var i = 0; i < len; i++)
			{
				handlerId = parts[i];
				if (!handlerId)
				{
					continue;
				}

				applyHandler($el, handlerId, $el.data('xf-' + handlerId));
			}
		};

		var initialize = function(root)
		{
			var $root = $(root);

			if ($root.length > 1)
			{
				$root.each(function() { initialize(this); });
				return;
			}

			var initCallback = function()
			{
				if (!this || !this.getAttribute)
				{
					// not an element -- probably a text node
					return;
				}

				var $this = $(this),
					init = this.getAttribute('data-xf-init');
				if (!init)
				{
					return;
				}

				var parts = init.split(' '),
					len = parts.length,
					handlerId;
				for (var i = 0; i < len; i++)
				{
					handlerId = parts[i];
					if (!handlerId)
					{
						continue;
					}

					applyHandler($this, handlerId, $this.data('xf-' + handlerId));
				}
			};

			if ($root.is('[data-xf-init]'))
			{
				initializeElement($root[0]);
			}
			$root.find('[data-xf-init]').each(function() { initializeElement(this); });
		};

		var AbstractHandler = XF.create(
		{
			$target: null,
			options: {},

			__construct: function ($target, options)
			{
				this.$target = $target;
				this.options = XF.applyDataOptions(this.options, $target.data(), options);
			},

			init: function()
			{
				console.error('This is the abstract init method for XF.Element, '
					+ 'which should be overridden.');
			},

			getOption: function(option)
			{
				return this.options[option];
			}
		});

		return {
			register: function(identifier, className) { mapper.add(identifier, className); },
			extend: function(identifier, extension) { mapper.extend(identifier, extension); },
			initialize: initialize,
			initializeElement: initializeElement,
			applyHandler: applyHandler,
			getHandler: getHandler,
			getHandlers: getHandlers,
			newHandler: function(extend)
			{
				return XF.extend(AbstractHandler, extend);
			},

			AbstractHandler: AbstractHandler
		};
	})();

	XF.AutoCompleteResults = XF.create({
		selectedResult: 0,
		$results: false,
		$scrollWatchers: null,
		resultsVisible: false,
		resizeBound: false,
		options: {},

		__construct: function(options)
		{
			this.options = $.extend({
				onInsert: null,
				clickAttacher: null,
				beforeInsert: null,
				insertMode: 'text',
				displayTemplate: '{{{icon}}}{{{text}}}'
			}, options);
		},

		isVisible: function()
		{
			return this.resultsVisible;
		},

		hideResults: function()
		{
			this.resultsVisible = false;

			if (this.$results)
			{
				this.$results.hide();
			}
			this.stopScrollWatching();
		},

		stopScrollWatching: function()
		{
			if (this.$scrollWatchers)
			{
				this.$scrollWatchers.off('scroll.autocomplete');
				this.$scrollWatchers = null;
			}
		},

		showResults: function(val, results, $targetOver, cssPosition)
		{
			var maxZIndex = 0,
				i,
				filterRegex,
				result,
				$li;

			if (!results)
			{
				this.hideResults();
				return;
			}

			this.resultsVisible = false;

			if (!this.$results)
			{
				this.$results = $('<ul />')
					.css({position: 'absolute', display: 'none'})
					.addClass('autoCompleteList')
					.attr('role', 'listbox')
					.appendTo(document.body);

				XF.setRelativeZIndex(this.$results, $targetOver, 1);
			}
			else
			{
				this.$results.hide().empty();
			}

			filterRegex = new RegExp('(' + XF.regexQuote(val) + ')', 'i');

			for (i in results)
			{
				if (!results.hasOwnProperty(i))
				{
					continue;
				}

				result = results[i];

				$li = $('<li />')
					.css('cursor', 'pointer')
					.attr('unselectable', 'on')
					.attr('role', 'option')
					.mouseenter(XF.proxy(this, 'resultMouseEnter'));

				if (this.options.clickAttacher)
				{
					this.options.clickAttacher($li, XF.proxy(this, 'resultClick'));
				}
				else
				{
					$li.click(XF.proxy(this, 'resultClick'));
				}

				var textValue,
					params = {
						icon: '',
						text: '',
						desc: ''
					};

				if (typeof result == 'string')
				{
					textValue = result;
					params.text = XF.htmlspecialchars(result);
				}
				else
				{
					textValue = result.text;
					params.text = XF.htmlspecialchars(result.text);

					if (typeof result.desc !== 'undefined')
					{
						params.desc = XF.htmlspecialchars(result.desc);
					}

					if (typeof result.icon !== 'undefined')
					{
						params.icon = $('<img class="autoCompleteList-icon" />').attr('src', XF.htmlspecialchars(result.icon));
					}
					else if (typeof result.iconHtml !== 'undefined')
					{
						params.icon = $('<span class="autoCompleteList-icon" />').html(result.iconHtml);
					}

					if (params.icon)
					{
						params.icon = params.icon[0].outerHTML;
					}
				}

				$li.data('insert-text', textValue);
				$li.data('insert-html', result.html || '');

				params.text = params.text.replace(filterRegex, '<strong>$1</strong>');
				params.desc = params.desc.replace(filterRegex, '<strong>$1</strong>');

				$li.html(Mustache.render(this.options.displayTemplate, params)).appendTo(this.$results);
			}

			if (!this.$results.children().length)
			{
				return;
			}

			this.selectResult(0, true);

			if (!this.resizeBound)
			{
				$(window).onPassive('resize', XF.proxy(this, 'hideResults'));
			}

			this.$results.css({
				top: '',
				left: '',
				right: '',
				bottom: ''
			});

			var $results = this.$results,
				getPositioning = function(cssPosition)
				{
					if ($.isFunction(cssPosition))
					{
						cssPosition = cssPosition($results, $targetOver);
					}

					if (!cssPosition)
					{
						var offset = $targetOver.offset();

						cssPosition = {
							top: offset.top + $targetOver.outerHeight(),
							left: offset.left
						};

						if (XF.isRtl())
						{
							cssPosition.right = $('html').width() - offset.left - $targetOver.outerWidth();
							cssPosition.left = 'auto';
						}
					}

					return cssPosition;
				};

			// if this is in a scrollable area, watch anything scrollable
			this.stopScrollWatching();
			var $scrollWatchers = $targetOver.parents().filter(function()
			{
				switch ($(this).css('overflow-x'))
				{
					case 'scroll':
					case 'auto':
						return true;

					default:
						return false;
				}
			});
			if ($scrollWatchers && $scrollWatchers.length)
			{
				$scrollWatchers.on('scroll.autocomplete', function()
				{
					$results.css(getPositioning(cssPosition));
				});

				this.$scrollWatchers = $scrollWatchers;
			}

			this.$results.css(getPositioning(cssPosition)).show();
			this.resultsVisible = true;
		},

		resultClick: function(e)
		{
			e.stopPropagation();

			this.insertResult(
				this.getResultText(e.currentTarget)
			);
			this.hideResults();
		},

		resultMouseEnter: function (e)
		{
			this.selectResult($(e.currentTarget).index(), true);
		},

		selectResult: function(shift, absolute)
		{
			var sel, children;

			if (!this.$results)
			{
				return;
			}

			if (absolute)
			{
				this.selectedResult = shift;
			}
			else
			{
				this.selectedResult += shift;
			}

			sel = this.selectedResult;
			children = this.$results.children();
			children.each(function(i)
			{
				if (i == sel)
				{
					$(this).addClass('is-selected');
				}
				else
				{
					$(this).removeClass('is-selected');
				}
			});

			if (sel < 0 || sel >= children.length)
			{
				this.selectedResult = -1;
			}
		},

		insertSelectedResult: function()
		{
			var res, ret = false;

			if (!this.resultsVisible)
			{
				return false;
			}

			if (this.selectedResult >= 0)
			{
				res = this.$results.children().get(this.selectedResult);
				if (res)
				{
					var resultText = this.getResultText(res);

					if (this.options.beforeInsert)
					{
						resultText = this.options.beforeInsert(resultText, res)
					}
					this.insertResult(resultText);
					ret = true;
				}
			}

			this.hideResults();

			return ret;
		},

		insertResult: function(value)
		{
			if (this.options.onInsert)
			{
				this.options.onInsert(value);
			}
		},

		getResultText: function(el)
		{
			var text;

			switch (this.options.insertMode)
			{
				case 'text':
					text = $(el).data('insert-text');
					break;

				case 'html':
					text = $(el).data('insert-html');
					break;
			}

			return text;
		}
	});

	XF.AutoCompleter = XF.create({
		options: {
			url: null,
			method: 'GET',
			idleWait: 200,
			minLength: 2,
			at: '@',
			keepAt: true,
			insertMode: 'text',
			displayTemplate: '{{{icon}}}{{{text}}}',
			beforeInsert: null
		},

		$input: null,
		ed: null,

		results: null,
		visible: false,
		idleTimer: null,
		pendingQuery: '',

		__construct: function($input, options, editor)
		{
			this.options = $.extend(true, {}, this.options, options);
			this.$input = $input;
			this.ed = editor;

			if (!this.options.url)
			{
				console.error('No URL option passed in to XF.AutoCompleter.');
				return;
			}

			if (typeof this.options.at != 'string' || this.options.at.length > 1)
			{
				console.error('The \'at\' option should be a single character string.');
			}

			this.init();
		},

		init: function()
		{
			var t = this,
				resultOpts = {
					onInsert: function(result)
					{
						t.insertResult(result);
					},
					beforeInsert: this.options.beforeInsert,
					insertMode: this.options.insertMode,
					displayTemplate: this.options.displayTemplate
				};

			if (this.ed)
			{
				resultOpts['clickAttacher'] = function($li, f)
				{
					t.ed.events.bindClick($li, $li, f);
				};
			}

			this.results = new XF.AutoCompleteResults(resultOpts);

			if (this.ed)
			{
				this.ed.events.on('keydown', XF.proxy(this, 'keydown'), true);
				this.ed.events.on('keyup', XF.proxy(this, 'keyup'), true);
				this.ed.events.on('click blur', XF.proxy(this, 'blur'));
				this.ed.$wp.onPassive('scroll', XF.proxy(this, 'blur'));
			}
			else
			{
				this.$input.on('keydown', XF.proxy(this, 'keydown'));
				this.$input.on('keyup', XF.proxy(this, 'keyup'));
				this.$input.on('click blur', XF.proxy(this, 'blur'));
				$(document).onPassive('scroll', XF.proxy(this, 'blur'));
			}
		},

		keydown: function(e)
		{
			if (!this.visible)
			{
				return;
			}

			switch (e.which)
			{
				case 40: // down
					this.results.selectResult(1);
					e.preventDefault();
					return false;

				case 38: // up
					this.results.selectResult(-1);
					e.preventDefault();
					return false;

				case 27: // esc
					this.hide();
					e.preventDefault();
					return false;

				case 13: // enter
					if (this.visible)
					{
						this.results.insertSelectedResult();
						e.preventDefault();
						return false;
					}
					break;
			}
		},

		keyup: function(e)
		{
			if (this.visible)
			{
				switch (e.which)
				{
					case 40: // down
					case 38: // up
					case 13: // enter
						return;
				}
			}

			this.hide();

			if (this.idleTimer)
			{
				clearTimeout(this.idleTimer);
			}
			this.idleTimer = setTimeout(XF.proxy(this, 'lookForMatch'), this.options.idleWait);
		},

		blur: function()
		{
			if (!this.visible)
			{
				return;
			}

			// timeout ensures that clicks still register
			setTimeout(XF.proxy(this, 'hide'), 250);
		},

		lookForMatch: function()
		{
			var match = this.getCurrentMatchInfo();
			if (match)
			{
				this.foundMatch(match.query);
			}
			else
			{
				this.hide();
			}
		},

		getCurrentMatchInfo: function()
		{
			var selection, textNode, text;

			if (this.ed)
			{
				selection = this.ed.selection.ranges(0);
				if (!selection || !selection.collapsed)
				{
					return null;
				}

				var focus = selection.endContainer;
				if (!focus || focus.nodeType !== 3)
				{
					// expected to be a text node
					return null;
				}

				textNode = focus;
				text = focus.nodeValue.substring(0, selection.endOffset);
			}
			else
			{
				var $input = this.$input;
				$input.autofocus();

				selection = $input.getSelection();

				if (!selection || selection.end <= 1)
				{
					return false;
				}

				text = $input.val().substring(0, selection.end);
			}

			var lastAt = text.lastIndexOf(this.options.at);

			if (lastAt === -1) // no 'at'
			{
				return null;
			}

			if (lastAt === 0 || text.substr(lastAt - 1, 1).match(/(\s|[\](,]|--)/))
			{
				var afterAt = text.substr(lastAt + 1);
				if (!afterAt.match(/\s/) || afterAt.length <= 15)
				{
					return {
						text: text,
						textNode: textNode,
						start: lastAt,
						query: afterAt.replace(new RegExp(String.fromCharCode(160), 'g'), ' '),
						range: selection
					};
				}
			}

			return null;
		},

		foundMatch: function(query)
		{
			if (this.pendingQuery === query)
			{
				return;
			}

			this.pendingQuery = query;

			if (query.length >= this.options.minLength && query.substr(0, 1) !== '[')
			{
				this.getPendingQueryOptions();
			}
		},

		getPendingQueryOptions: function()
		{
			XF.ajax(
				this.options.method, this.options.url, { q: this.pendingQuery },
				XF.proxy(this, 'handlePendingQueryOptions'),
				{ global: false, error: false }
			);
		},

		handlePendingQueryOptions: function(data)
		{
			var current = this.getCurrentMatchInfo();

			if (!data.q || !current || data.q !== current.query)
			{
				return;
			}

			if (data.results && data.results.length)
			{
				this.show(data.q, data.results);
			}
			else
			{
				this.hide();
			}
		},

		insertResult: function(result)
		{
			this.hide();

			var matchInfo = this.getCurrentMatchInfo();
			if (!matchInfo)
			{
				return;
			}

			var afterAtPos = matchInfo.start + 1,
				range = matchInfo.range;

			if (this.ed)
			{
				this.ed.selection.save();

				XF.EditorHelpers.focus(this.ed);

				var node = matchInfo.textNode,
					text = node.nodeValue,
					suffix = '\u00a0',
					insert;

				var insertRef = node.splitText(this.options.keepAt ? afterAtPos : afterAtPos - 1);
				insertRef.textContent = text.substr(afterAtPos + matchInfo.query.length);

				if (this.options.insertMode === 'html')
				{
					insert = $.parseHTML(result + suffix);
				}
				else
				{
					insert = document.createTextNode(result + suffix);
				}

				$(insertRef).before(insert);

				node.parentNode.normalize();

				this.ed.selection.restore();
			}
			else
			{
				var $input = this.$input;
				$input.autofocus();

				if (afterAtPos !== -1)
				{
					$input.setSelection(matchInfo.start, range.end);
					$input.replaceSelectedText((this.options.keepAt ? this.options.at : '') + result + ' ', 'collapseToEnd');
				}
			}
		},

		show: function(val, results)
		{
			var matchInfo = this.getCurrentMatchInfo(),
				$input = this.$input,
				inputDimensions = $input.dimensions(),
				t = this;

			if (!matchInfo)
			{
				return;
			}

			this.visible = true;

			if (this.ed)
			{
				var range = matchInfo.range;

				this.results.showResults(val, results, $input, function($results)
				{
					if (!range || !range.getBoundingClientRect)
					{
						var start = range.startContainer,
							$start = start.nodeType === 3 ?  $(start.parentNode) : $(start),
							startDims = $start.dimensions();

						return {
							top: startDims.bottom + 3,
							left: inputDimensions.left + 5
						};
					}

					var startRange = range.cloneRange();

					// Set the range to start before the @ and cover it. This works around a problem where the @ is the
					// first character on the line and when the cursor is before it, it's on the previous line.
					startRange.setStart(matchInfo.textNode, matchInfo.start);
					startRange.setEnd(matchInfo.textNode, matchInfo.start + 1);

					var rect = startRange.getBoundingClientRect();

					return t.getResultPositionForSelection(
						rect.left,
						rect.bottom,
						range.getBoundingClientRect().left,
						$results,
						inputDimensions
					);
				});
			}
			else
			{
				this.results.showResults(val, results, $input, function($results)
				{
					var $div = $('<div />'),
						computedCss = window.getComputedStyle($input[0]),
						name,
						applyCss = '';

					for (var i = 0; i < computedCss.length; i++)
					{
						name = computedCss[i];
						applyCss += name + ': ' + computedCss.getPropertyValue(name) + '; ';
					}

					$div[0].style.cssText = applyCss;
					$div.css({
						position: 'absolute',
						height: '',
						width: $input.outerWidth(),
						opacity: 0,
						top: 0,
						left: '-9999px'
					});
					$div[0].textContent = $input.val();
					$div.appendTo(document.body);

					var testRange = document.createRange();

					testRange.setStart($div[0].firstChild, matchInfo.start);
					testRange.setEnd($div[0].firstChild, matchInfo.start + 1);

					var rect = testRange.getBoundingClientRect(),
						divDimensions = $div.dimensions(),
						startLeft, startBottom, endLeft;

					startLeft = inputDimensions.left + (rect.left - divDimensions.left);
					startBottom = inputDimensions.top + (rect.bottom - divDimensions.top);

					testRange.setStart($div[0].firstChild, matchInfo.start + 1 + matchInfo.query.length);
					testRange.setEnd($div[0].firstChild, matchInfo.start + 1 + matchInfo.query.length);
					rect = testRange.getBoundingClientRect();

					endLeft = inputDimensions.left + (rect.left - divDimensions.left);

					$div.remove();

					return t.getResultPositionForSelection(startLeft, startBottom, endLeft, $results, inputDimensions);
				});
			}
		},

		getResultPositionForSelection: function(startX, startY, endX, $results, inputDimensions)
		{
			var resultsWidth = $results.width(),
				targetTop = startY + $(window).scrollTop() + 3,
				targetLeft = startX;

			if (targetLeft + resultsWidth > inputDimensions.right)
			{
				targetLeft = endX - resultsWidth;
			}

			if (targetLeft < inputDimensions.left)
			{
				targetLeft = inputDimensions.left;
			}

			return {
				top: targetTop,
				left: targetLeft
			};
		},

		hide: function()
		{
			if (this.visible)
			{
				this.visible = false;
				this.results.hideResults();
			}
		}
	});

	XF.pageDisplayTime = Date.now();
	$(XF.onPageLoad);

	$(window).on('pageshow', function()
	{
		if (!XF.pageDisplayTime || Date.now() > XF.pageDisplayTime)
		{
			XF.pageDisplayTime = Date.now();
		}
	});
}
(window.jQuery, window, document);