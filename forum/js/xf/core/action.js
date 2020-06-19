/** @param {jQuery} $ jQuery Object */
!function($, window, document, _undefined)
{
	"use strict";

	// ################################## ATTRIBUTION HANDLER ###########################################

	XF.AttributionClick = XF.Event.newHandler({
		eventType: 'click',
		eventNameSpace: 'XFAttributionClick',
		options: {
			contentSelector: null
		},

		init: function()
		{
		},

		click: function(e)
		{
			var hash = this.options.contentSelector,
				$content = $(hash);

			if ($content.length)
			{
				try
				{
					var top = $content.offset().top;

					if ("pushState" in window.history)
					{
						window.history.pushState({}, '', window.location.toString().replace(/#.*$/, '') + hash);
					}

					$('html, body').animate({ scrollTop: top }, XF.config.speed.normal, function()
					{
						if (!window.history.pushState)
						{
							window.location.hash = hash;
						}
					});
				}
				catch (e)
				{
					window.location.hash = hash;
				}

				e.preventDefault();
			}
		}
	});

	// ################################## LIKE HANDLER ###########################################

	XF.LikeClick = XF.Event.newHandler({
		eventType: 'click',
		eventNameSpace: 'XFLikeClick',
		options: {
			likeList: null,
			container: null
		},

		processing: false,
		container: null,

		init: function()
		{
			if (this.options.container)
			{
				this.$container = XF.findRelativeIf(this.options.container, this.$target);
			}
		},

		click: function(e)
		{
			e.preventDefault();

			if (this.processing)
			{
				return;
			}
			this.processing = true;

			var href = this.$target.attr('href'),
				self = this;

			XF.ajax('POST', href, {}, XF.proxy(this, 'handleAjax'), {skipDefaultSuccess: true})
				.always(function()
				{
					setTimeout(function()
					{
						self.processing = false;
					}, 250);
				});
		},

		handleAjax: function(data)
		{
			var $target = this.$target;

			if (data.addClass)
			{
				$target.addClass(data.addClass);
			}
			if (data.removeClass)
			{
				$target.removeClass(data.removeClass);
			}
			if (data.text)
			{
				var $label = $target.find('.label');
				if (!$label.length)
				{
					$label = $target;
				}
				$label.text(data.text);
			}

			if (data.hasOwnProperty('isLiked'))
			{
				$target.toggleClass('is-liked', data.isLiked);
				if (this.$container)
				{
					this.$container.toggleClass('is-liked', data.isLiked);
				}
			}

			var $likeList = this.options.likeList ? XF.findRelativeIf(this.options.likeList, $target) : $([]);

			if (typeof data.html !== 'undefined' && $likeList.length)
			{
				if (data.html.content)
				{
					XF.setupHtmlInsert(data.html, function($html, container)
					{
						$likeList.html($html).addClassTransitioned('is-active');
					});
				}
				else
				{
					$likeList.removeClassTransitioned('is-active', function()
					{
						$likeList.empty();
					});
				}
			}
		}
	});

	// ################################## PREVIEW CLICK ###########################################

	XF.PreviewClick = XF.Event.newHandler({
		eventType: 'click',
		eventNameSpace: 'XFPreviewClick',
		options: {
			previewUrl: ''
		},

		$form: null,
		href: null,
		loading: false,

		init: function()
		{
			this.$form = this.$target.closest('form');

			var href = this.$form.data('preview-url') || this.options.previewUrl || this.$target.attr('href');

			if (!href)
			{
				console.error('Preview button must have a href');
				return;
			}

			this.href = href;

			if (!this.getCurrentContainer())
			{
				console.error('Preview form must have a .js-previewContainer element');
				return;
			}

			this.$form.on('preview:hide', XF.proxy(this, 'onPreviewHide'));
		},

		click: function(e)
		{
			if (!this.href)
			{
				return;
			}

			e.preventDefault();

			if (this.loading)
			{
				return;
			}

			this.loading = true;

			var draftHandler = XF.Element.getHandler(this.$form, 'draft');
			if (draftHandler)
			{
				draftHandler.triggerSave();
			}

			var self = this,
				formData = XF.getDefaultFormData(this.$form);

			XF.ajax('post', this.href, formData, XF.proxy(this, 'onLoad'))
				.always(function() { self.loading = false; });
		},

		onLoad: function(data)
		{
			if (!data.html)
			{
				return;
			}

			var $container = this.getCurrentContainer();

			if (data.html.content)
			{
				XF.setupHtmlInsert(data.html, function($html, container, onComplete)
				{
					$container.removeClassTransitioned('is-active', function()
					{
						$container.replaceWith($html);
						onComplete();
						$html.addClassTransitioned('is-active');
					});

					return false;
				});
			}
			else
			{
				$container.xfFadeUp(XF.config.speed.fast);
			}
		},

		onPreviewHide: function(e)
		{
			this.getCurrentContainer().removeClassTransitioned('is-active');
		},

		getCurrentContainer: function()
		{
			return this.$form.find('.js-previewContainer').first();
		}
	});

	// ################################## SWITCH HANDLER ###########################################

	XF.handleSwitchResponse = function($target, data, allowRedirect)
	{
		if (data.switchKey)
		{
			var switchActions = $target.data('sk-' + data.switchKey);

			if (switchActions)
			{
				var match, value;
				while (match = switchActions.match(/(\s*,)?\s*(addClass|removeClass):([^,]+)(,|$)/))
				{
					switchActions = switchActions.substring(match[0].length);

					value = $.trim(match[3]);
					if (value.length)
					{
						switch (match[2])
						{
							case 'addClass': $target.addClass(value); break;
							case 'removeClass': $target.removeClass(value); break;
						}
					}
				}

				switchActions = $.trim(switchActions);

				if (switchActions.length && !data.text)
				{
					data.text = switchActions;
				}
			}
		}

		if (data.addClass)
		{
			$target.addClass(data.addClass);
		}
		if (data.removeClass)
		{
			$target.removeClass(data.removeClass);
		}

		if (data.text)
		{
			var $label = $target.find($target.data('label'));
			if (!$label.length)
			{
				$label = $target;
			}
			$label.text(data.text);
		}

		if (data.message)
		{
			var doRedirect = (allowRedirect && data.redirect),
				flashLength = doRedirect ? 1000 : 3000;

			XF.flashMessage(data.message, flashLength, function()
			{
				if (doRedirect)
				{
					XF.redirect(data.redirect);
				}
			});
		}
	};

	XF.ScrollToClick = XF.Event.newHandler({
		eventType: 'click',
		eventNameSpace: 'XFScrollToClick',
		options: {
			target: null, // specify a target to which to scroll, when href is not available
			silent: false, // if true and no scroll
			hash: null, // override history hash - off by default, use true to use target's ID or string for arbitrary hash value
			speed: 300 // scroll animation speed
		},

		$scroll: null,

		init: function()
		{
			var $scroll,
				hash = this.options.hash,
				targetHref = this.$target.attr('href');

			if (this.options.target)
			{
				$scroll = XF.findRelativeIf(this.options.target, this.$target);
			}
			if (!$scroll || !$scroll.length)
			{
				if (targetHref && targetHref.length && targetHref.charAt(0) == '#')
				{
					$scroll = $(targetHref);
				}
				else if (this.options.silent)
				{
					// don't let an error happen here, just silently ignore
					return;
				}
			}

			if (!$scroll || !$scroll.length)
			{
				console.error('No scroll target could be found');
				return;
			}

			this.$scroll = $scroll;

			if (hash === true || hash === 'true')
			{
				var id = $scroll.attr('id');
				this.options.hash = (id && id.length) ? id : null;
			}
			else if (hash === false || hash === 'false')
			{
				this.options.hash = null;
			}
		},

		click: function(e)
		{
			if (!this.$scroll)
			{
				return;
			}

			e.preventDefault();

			var hash = this.options.hash;

			$('html, body').animate(
				{ scrollTop: this.$scroll.offset().top },
				this.options.speed,
				null,
				function()
				{
					if (hash)
					{
						window.location.hash = hash;
					}
				}
			);
		}
	});

	XF.SwitchClick = XF.Event.newHandler({
		eventType: 'click',
		eventNameSpace: 'XFSwitchClick',
		options: {
			redirect: false,
			label: '.js-label'
		},

		processing: false,

		init: function()
		{
			this.$target.data('label', this.options.label);
		},

		click: function(e)
		{
			e.preventDefault();

			if (this.processing)
			{
				return;
			}
			this.processing = true;

			var href = this.$target.attr('href'),
				self = this;

			XF.ajax('POST', href, {}, XF.proxy(this, 'handleAjax'), {skipDefaultSuccess: true})
				.always(function()
				{
					setTimeout(function()
					{
						self.processing = false;
					}, 250);
				});
		},

		handleAjax: function(data)
		{
			var $target = this.$target,
				event = $.Event('switchclick:complete');

			$target.trigger(event, data, this);
			if (event.isDefaultPrevented())
			{
				return;
			}

			XF.handleSwitchResponse($target, data, this.options.redirect);
		}
	});

	XF.SwitchOverlayClick = XF.Event.newHandler({
		eventType: 'click',
		eventNameSpace: 'XFSwitchOverlayClick',
		options: {
			redirect: false
		},

		overlay: null,

		init: function()
		{
		},

		click: function(e)
		{
			e.preventDefault();

			if (this.overlay)
			{
				this.overlay.show();
				return;
			}

			var href = this.$target.attr('href');

			XF.loadOverlay(href, {
				cache: false,
				init: XF.proxy(this, 'setupOverlay')
			});
		},

		setupOverlay: function(overlay)
		{
			this.overlay = overlay;

			var $form = overlay.getOverlay().find('form');

			$form.on('ajax-submit:response', XF.proxy(this, 'handleOverlaySubmit'));

			var t = this;
			overlay.on('overlay:hidden', function() { t.overlay = null; });

			return overlay;
		},

		handleOverlaySubmit: function(e, data)
		{
			if (data.status == 'ok')
			{
				e.preventDefault();

				var overlay = this.overlay;
				if (overlay)
				{
					overlay.hide();
				}

				XF.handleSwitchResponse(this.$target, data, this.options.redirect);
			}
		}
	});

	// ################################## DRAFT HANDLER ###########################################

	XF.Draft = XF.Element.newHandler({
		options: {
			draftAutosave: 60,
			draftName: 'message',
			draftUrl: null,

			saveButton: '.js-saveDraft',
			deleteButton: '.js-deleteDraft',
			actionIndicator: '.draftStatus'
		},

		lastActionContent: null,
		autoSaveRunning: false,

		init: function()
		{
			if (!this.options.draftUrl)
			{
				console.error('No draft URL specified.');
				return;
			}

			var self = this;
			this.$target.on(this.options.saveButton, 'click', function(e)
			{
				e.preventDefault();
				self.triggerSave();
			});
			this.$target.on(this.options.deleteButton, 'click', function(e)
			{
				e.preventDefault();
				self.triggerDelete();
			});

			var proxySync = XF.proxy(this, 'syncState');

			// set the default value and check it after other JS loads
			this.syncState();
			setTimeout(proxySync, 500);

			this.$target.on('draft:sync', proxySync);

			setInterval(XF.proxy(this, 'triggerSave'), this.options.draftAutosave * 1000);
		},

		triggerSave: function()
		{
			if (XF.isRedirecting)
			{
				// we're unloading the page, don't try to save any longer
				return;
			}

			var event = $.Event('draft:beforesave');

			this.$target.trigger(event);
			if (event.isDefaultPrevented())
			{
				return;
			}

			this._executeDraftAction(this.getSaveData());
		},

		triggerDelete: function()
		{
			// prevent re-saving the content until it's changed
			this.lastActionContent = this.getSaveData();

			this._sendDraftAction('delete=1');
		},

		_executeDraftAction: function(data)
		{
			if (data == this.lastActionContent)
			{
				return;
			}
			if (this.autoSaveRunning)
			{
				return false;
			}

			this.lastActionContent = data;
			this._sendDraftAction(data);
		},

		_sendDraftAction: function(data)
		{
			this.autoSaveRunning = true;

			var self = this;

			return XF.ajax(
				'post',
				this.options.draftUrl,
				data,
				XF.proxy(this, 'completeAction'),
				{ skipDefault: true, skipError: true, global: false }
			).always(
				function() { self.autoSaveRunning = false; }
			);
		},

		completeAction: function(data)
		{
			var event = $.Event('draft:complete');
			this.$target.trigger(event, data);
			if (event.isDefaultPrevented())
			{
				return;
			}

			var $complete = this.$target.find(this.options.actionIndicator);

			$complete.removeClass('is-active').text(data.complete).addClass('is-active');
			setTimeout(function()
			{
				$complete.removeClass('is-active');
			}, 2000);
		},

		syncState: function()
		{
			this.lastActionContent = this.getSaveData();
		},

		getSaveData: function()
		{
			var $target = this.$target;

			$target.trigger('draft:beforesync');
			return $target.serialize()
				.replace(/(^|&)_xfToken=[^&]+(?=&|$)/g, '')
				.replace(/^&+/, '');
		}
	});

	// ################################## FOCUS TRIGGER HANDLER ###########################################

	XF.FocusTrigger = XF.Element.newHandler({
		options: {
			display: null,
			activeClass: 'is-active'
		},

		init: function()
		{
			if (this.$target.attr('autofocus'))
			{
				this.trigger();
			}
			else
			{
				this.$target.one('focusin', XF.proxy(this, 'trigger'));
			}
		},

		trigger: function()
		{
			var display = this.options.display;
			if (display)
			{
				var $display = XF.findRelativeIf(display, this.$target);
				if ($display.length)
				{
					$display.addClassTransitioned(this.options.activeClass);
				}
			}
		}
	});

	// ################################## POLL BLOCK HANDLER ###########################################

	XF.PollBlock = XF.Element.newHandler({
		options: {},

		init: function()
		{
			this.$target.on('ajax-submit:response', XF.proxy(this, 'afterSubmit'));
		},

		afterSubmit: function(e, data)
		{
			if (data.errors || data.exception)
			{
				return;
			}

			e.preventDefault();

			if (data.redirect)
			{
				XF.redirect(data.redirect);
			}

			var self = this;
			XF.setupHtmlInsert(data.html, function($html, container)
			{
				$html.hide();
				$html.insertAfter(self.$target);

				self.$target.xfFadeUp(null, function()
				{
					self.$target.remove();

					$html.xfFadeDown();
				});
			});
		}
	});

	// ################################## PREVIEW HANDLER ###########################################

	XF.Preview = XF.Element.newHandler({
		options: {
			previewUrl: null,
			previewButton: 'button.js-previewButton'
		},

		previewing: null,

		init: function()
		{
			var $form = this.$target,
				$button = XF.findRelativeIf(this.options.previewButton, $form);

			if (!this.options.previewUrl)
			{
				console.warn('Preview form has no data-preview-url: %o', $form);
				return;
			}

			if (!$button.length)
			{
				console.warn('Preview form has no preview button: %o', $form);
				return;
			}

			$button.on({
				click: XF.proxy(this, 'preview')
			});
		},

		preview: function(e)
		{
			e.preventDefault();

			if (this.previewing)
			{
				return false;
			}
			this.previewing = true;

			var draftHandler = XF.Element.getHandler(this.$target, 'draft');
			if (draftHandler)
			{
				draftHandler.triggerSave();
			}

			var t = this;
			XF.ajax('post', this.options.previewUrl, this.$target.serializeArray(), function(data)
			{
				if (data.html)
				{
					XF.setupHtmlInsert(data.html, function ($html, container, onComplete)
					{
						XF.overlayMessage(container.title, $html);
					});
				}
			}).always(function()
			{
				t.previewing = false;
			});
		}
	});

	// ################################## SHARE BUTTONS HANDLER ###########################################

	XF.ShareButtons = XF.Element.newHandler({
		options: {
			buttons: '.shareButtons-button',
			iconic: '.shareButtons--iconic',
			pageUrl: null,
			pageTitle: null,
			pageDesc: null
		},

		pageTitle: null,
		pageDesc: null,
		pageUrl: null,

		clipboard: null,

		init: function()
		{
			var buttonSel = this.options.buttons,
				iconic = this.options.iconic;

			this.$target
				.on('focus mouseenter', buttonSel, XF.proxy(this, 'focus'))
				.on('click', buttonSel, XF.proxy(this, 'click'));

			if (typeof iconic == 'string')
			{
				iconic = this.$target.is(iconic);
			}
			this.$target.find(buttonSel).each(function()
			{
				var $el = $(this);
				if (iconic)
				{
					XF.Element.applyHandler($el, 'element-tooltip', {
						element: '> span'
					});
				}
				if ($el.data('clipboard'))
				{
					if (Clipboard.isSupported())
					{
						$el.removeClass('is-hidden');
					}
				}
			});
		},

		setupPageData: function()
		{
			if (this.options.pageTitle && this.options.pageTitle.length)
			{
				this.pageTitle = this.options.pageTitle;
			}
			else
			{
				this.pageTitle = $('meta[property="og:title"]').attr('content');
				if (!this.pageTitle)
				{
					this.pageTitle = $('title').text();
				}
			}

			if (this.options.pageUrl && this.options.pageUrl.length)
			{
				this.pageUrl = this.options.pageUrl;
			}
			else
			{
				this.pageUrl = $('meta[property="og:url"]').attr('content');
				if (!this.pageUrl)
				{
					this.pageUrl = window.location.href;
				}
			}

			if (this.options.pageDesc && this.options.pageDesc.length)
			{
				this.pageDesc = this.options.pageDesc;
			}
			else
			{
				this.pageDesc = $('meta[property="og:description"]').attr('content');
				if (this.pageDesc)
				{
					this.pageDesc = $('meta[name=description]').attr('content') || '';
				}
			}
		},

		focus: function(e)
		{
			var $el = $(e.currentTarget);

			if ($el.attr('href'))
			{
				return;
			}

			if (!this.pageUrl)
			{
				this.setupPageData();
			}

			var href = $el.data('href');
			if (!href)
			{
				if (!$el.data('clipboard'))
				{
					console.error('No data-href on share button %o', e.currentTarget);
				}
				else
				{
					// this sets a new click handler

					if (!this.clipboard)
					{
						var self = this;

						this.clipboard = new Clipboard($el[0], {
							text: function(trigger)
							{
								return $(trigger).data('clipboard')
									.replace('{url}', self.pageUrl)
									.replace('{title}', self.pageTitle);
							}
						});

						this.clipboard.on('success', function()
						{
							XF.flashMessage(XF.phrase('link_copied_to_clipboard'), 3000);
						});
					}
				}
				return;
			}

			href = href.replace('{url}', encodeURIComponent(this.pageUrl))
				.replace('{title}', encodeURIComponent(this.pageTitle));

			$el.attr('href', href);
		},

		click: function(e)
		{
			var $el = $(e.currentTarget),
				href = $el.attr('href');

			if (!href)
			{
				return;
			}
			if (e.altKey || e.ctrlKey || e.metaKey || e.shiftKey)
			{
				return;
			}

			if (href.match(/^https?:/i))
			{
				e.preventDefault();

				var popupWidth = 600,
					popupHeight = 400,
					popupLeft = (screen.width - popupWidth) / 2,
					popupTop = (screen.height - popupHeight) / 2;

				window.open(href, 'share',
					'toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes'
					+ ',width=' + popupWidth + ',height=' + popupHeight
					+ ',left=' + popupLeft + ',top=' + popupTop
				);
			}
		}
	});

	// ################################## SHARE INPUT HANDLER ###########################################

	XF.ShareInput = XF.Element.newHandler({
		options: {
			button: '.js-shareButton',
			input: '.js-shareInput',
			successText: '',
		},

		init: function()
		{
			var $button = this.$target.find(this.options.button),
				$input = this.$target.find(this.options.input);

			if (Clipboard.isSupported())
			{
				$button.removeClass('is-hidden');
			}

			var clipboard = new Clipboard($button[0], {
				target: function(trigger)
				{
					return $input[0];
				}
			});
			clipboard.on('success', XF.proxy(this, 'success'));

			$input.on('click', XF.proxy(this, 'click'));
		},

		success: function()
		{
			XF.flashMessage(this.options.successText ? this.options.successText : XF.phrase('text_copied_to_clipboard'), 3000);
		},

		click: function(e)
		{
			$(e.target).select();
		}
	});

	// ################################## COPY TO CLIPBOARD HANDLER ###########################################

	XF.CopyToClipboard = XF.Element.newHandler({
		options: {
			copyText: '',
			copyTarget: '',
			success: ''
		},

		copyText: null,

		init: function()
		{
			if (this.options.copyText)
			{
				this.copyText = this.options.copyText;
			}
			else if (this.options.copyTarget)
			{
				var $target = $(this.options.copyTarget);

				if ($target.is('input[type="text"], textarea')) // TODO: expand to other types?
				{
					this.copyText = $target.val();
				}
				else
				{
					this.copyText = $target.text();
				}
			}

			if (!this.copyText)
			{
				console.error('No text to copy to clipboard');
			}

			var t = this,
				clipboard = new Clipboard(this.$target[0], {
					text: function(trigger)
					{
						return t.copyText;
					}
				});

			clipboard.on('success', function()
			{
				if (t.options.success)
				{
					XF.flashMessage(t.options.success, 3000);
				}
				else
				{
					var flashText = XF.phrase('text_copied_to_clipboard');

					if (t.copyText.match(/^[a-z0-9-]+:\/\/[^\s"<>{}`]+$/i))
					{
						flashText =  XF.phrase('link_copied_to_clipboard');
					}
					XF.flashMessage(flashText, 3000);
				}
			});
		}
	});

	// ################################## PUSH NOTIFICATION TOGGLE HANDLER ###########################################

	XF.PushToggle = XF.Element.newHandler({
		options: {},

		isSubscribed: false,
		cancellingSub: null,

		init: function()
		{
			if (!XF.Push.isSupported())
			{
				this.updateButton(XF.phrase('push_not_supported_label'), false);
				console.error('XF.Push.isSupported() returned false');
				return;
			}

			if (Notification.permission === 'denied')
			{
				this.updateButton(XF.phrase('push_blocked_label'), false);
				console.error('Notification.permission === denied');
				return;
			}

			this.registerWorker();
		},

		registerWorker: function()
		{
			var t = this;

			var onRegisterSuccess = function()
			{
				t.$target.on('click', XF.proxy(t, 'buttonClick'));

				$(document).on('push:init-subscribed', function()
				{
					t.updateButton(XF.phrase('push_disable_label'), true);
				});

				$(document).on('push:init-unsubscribed', function()
				{
					t.updateButton(XF.phrase('push_enable_label'), true);
				});
			};
			var	onRegisterError = function()
			{
				t.updateButton(XF.phrase('push_not_supported_label'), false);
				console.error('navigator.serviceWorker.register threw an error.');
			};
			XF.Push.registerWorker(onRegisterSuccess, onRegisterError);
		},

		buttonClick: function(e)
		{
			var t = this;

			var onUnsubscribe = function()
			{
				t.updateButton(XF.phrase('push_enable_label'), true);

				// dismiss the push CTA for the current session
				// after push has just been explicitly disabled.
				XF.Cookie.set('push_notice_dismiss', '1');

				if (XF.config.userId)
				{
					// also remove history entry as this is an explicit unsubscribe
					XF.Push.removeUserFromPushHistory();
				}
			};
			var onSubscribe = function()
			{
				t.updateButton(XF.phrase('push_disable_label'), true);
			};
			var onSubscribeError = function()
			{
				t.updateButton(XF.phrase('push_not_supported_label'), false);
			};
			XF.Push.handleToggleAction(onUnsubscribe, false, onSubscribe, onSubscribeError);
		},

		updateButton: function(phrase, enable)
		{
			this.$target.find('.button-text').text(phrase);
			if (enable)
			{
				this.$target.removeClass('is-disabled');
			}
			else
			{
				this.$target.addClass('is-disabled');
			}
		}
	});

	XF.PushCta = XF.Element.newHandler({
		options: {},

		init: function()
		{
			if (XF.config.skipPushNotificationCta)
			{
				return;
			}

			if (!XF.Push.isSupported())
			{
				return;
			}

			if (Notification.permission === 'denied')
			{
				return;
			}

			this.registerWorker();
		},

		registerWorker: function()
		{
			var t = this;

			var onRegisterSuccess = function()
			{
				$(document).on('push:init-unsubscribed', function()
				{
					if (XF.Push.hasUserPreviouslySubscribed())
					{
						try
						{
							XF.Push.handleSubscribeAction(true);
						}
						catch (e)
						{
							XF.Push.removeUserFromPushHistory();
						}
					}
					else
					{
						if (t.getDismissCookie())
						{
							return;
						}

						t.$target
							.closest('.js-enablePushContainer')
							.xfFadeDown(XF.config.speed.fast, XF.proxy(t, 'initLinks'));
					}
				});
			};
			XF.Push.registerWorker(onRegisterSuccess);
		},

		initLinks: function()
		{
			var $target = this.$target;
			$target.find('.js-enablePushLink').on('click', XF.proxy(this, 'linkClick'));
			$target.siblings('.js-enablePushDismiss').on('click', XF.proxy(this, 'dismissClick'));
		},

		linkClick: function(e)
		{
			e.preventDefault();

			this.hidePushContainer();
			this.setDismissCookie(true, 12 * 3600 * 1000); // 12 hours - it's possible the browser may not allow the setup to complete

			XF.Push.handleSubscribeAction(false);
		},

		dismissClick: function(e)
		{
			e.preventDefault();

			$(e.currentTarget).hide();

			this.$target
				.closest('.js-enablePushContainer')
				.addClass('notice--accent')
				.removeClass('notice--primary');

			this.$target.find('.js-initialMessage')
				.hide();

			var $dismissMessage = this.$target.find('.js-dismissMessage');

			$dismissMessage.show();
			$dismissMessage.find('.js-dismissTemp').on('click', XF.proxy(this, 'dismissTemp'));
			$dismissMessage.find('.js-dismissPerm').on('click', XF.proxy(this, 'dismissPerm'));
		},

		dismissTemp: function(e)
		{
			e.preventDefault();

			this.hidePushContainer();

			this.setDismissCookie(false);
		},

		dismissPerm: function(e)
		{
			e.preventDefault();

			this.hidePushContainer();

			this.setDismissCookie(true);
		},

		setDismissCookie: function(perm, permLength)
		{
			if (perm) // 10 years should do it
			{
				if (!permLength)
				{
					permLength = (86400 * 1000) * 365 * 10; // ~10 years
				}

				XF.Cookie.set(
					'push_notice_dismiss',
					'1',
					new Date(Date.now() + permLength)
				);
			}
			else
			{
				XF.Cookie.set(
					'push_notice_dismiss',
					'1'
				);
			}
		},

		getDismissCookie: function()
		{
			return XF.Cookie.get('push_notice_dismiss');
		},

		hidePushContainer: function()
		{
			this.$target
				.closest('.js-enablePushContainer')
				.xfFadeUp(XF.config.speed.fast);
		}
	});

	XF.Reaction = XF.Element.newHandler({
		options: {
			delay: 200,
			reactionList: null
		},

		$tooltipHtml: null,
		trigger: null,
		tooltip: null,
		href: null,

		init: function()
		{
			if (!this.$target.is('a') || !this.$target.attr('href'))
			{
				// no href so can't do anything
				return;
			}

			this.href = this.$target.attr('href');

			// check if we have a tooltip template. if we do not then it
			// likely means that all reactions (except like) are disabled
			// so there's little point in displaying it.
			var $tooltipTemplate = $('#xfReactTooltipTemplate');
			if ($tooltipTemplate.length)
			{
				this.$tooltipHtml = $($.parseHTML($tooltipTemplate.html()));

				this.tooltip = new XF.TooltipElement(XF.proxy(this, 'getContent'), {
					extraClass: 'tooltip--reaction',
					html: true
				});
				this.trigger = new XF.TooltipTrigger(this.$target, this.tooltip, {
					maintain: true,
					delayIn: this.options.delay,
					trigger: 'hover focus touchhold',
					onShow: XF.proxy(this, 'onShow'),
					onHide: XF.proxy(this, 'onHide')
				});
				this.trigger.init();
			}

			this.$target.on('click', XF.proxy(this, 'actionClick'));
		},

		getContent: function()
		{
			var href = this.href;

			href = href.replace(/(\?|&)reaction_id=[^&]*(&|$)/, '$1reaction_id=');

			this.$tooltipHtml.find('.reaction').each(function()
			{
				var $this = $(this),
					reactionId = $this.data('reaction-id');

				$this.attr('href', reactionId ? href + parseInt(reactionId, 10) : false);
			});

			this.$tooltipHtml.find('[data-xf-init~="tooltip"]').attr('data-delay-in', 50).attr('data-delay-out', 50);

			this.$tooltipHtml.on('click', '.reaction', XF.proxy(this, 'actionClick'));

			return this.$tooltipHtml;
		},

		onShow: function()
		{
			var activeTooltip = XF.Reaction.activeTooltip;
			if (activeTooltip && activeTooltip !== this)
			{
				activeTooltip.hide();
			}

			XF.Reaction.activeTooltip = this;
		},

		onHide: function()
		{
			// it's possible for another show event to trigger so don't empty this if it isn't us
			if (XF.Reaction.activeTooltip === this)
			{
				XF.Reaction.activeTooltip = null;
			}

			this.$target.removeData('tooltip:taphold');
		},

		show: function()
		{
			if (this.trigger)
			{
				this.trigger.show();
			}
		},

		hide: function()
		{
			if (this.trigger)
			{
				this.trigger.hide();
			}
		},

		actionClick: function(e)
		{
			e.preventDefault();

			if (this.$target.data('tooltip:taphold') && this.$target.is(e.currentTarget))
			{
				// click originated from taphold event
				this.$target.removeData('tooltip:taphold');
				return;
			}

			XF.ajax('post', $(e.currentTarget).attr('href'), XF.proxy(this, 'actionComplete'));
		},

		actionComplete: function(data)
		{
			if (!data.html)
			{
				return;
			}

			var $target = this.$target,
				oldReactionId = $target.data('reaction-id'),
				newReactionId = data.reactionId,
				linkReactionId = data.linkReactionId,
				t = this;

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				t.hide();

				var $reaction = $html.find('.js-reaction'),
					$reactionText = $html.find('.js-reactionText'),
					$originalReaction = $target.find('.js-reaction'),
					$originalReactionText = $target.find('.js-reactionText'),
					originalHref = $target.attr('href'), newHref;

				if (linkReactionId)
				{
					newHref = originalHref.replace(/(\?|&)reaction_id=\d+(?=&|$)/, '$1reaction_id=' + linkReactionId);
					$target.attr('href', newHref);
				}

				if (newReactionId)
				{
					$target.addClass('has-reaction');
					$target.removeClass('reaction--imageHidden');
					if (oldReactionId)
					{
						$target.removeClass('reaction--' + oldReactionId);
					}
					$target.addClass('reaction--' + newReactionId);
					$target.data('reaction-id', newReactionId);
				}
				else
				{
					$target.removeClass('has-reaction');
					$target.addClass('reaction--imageHidden');
					if (oldReactionId)
					{
						$target.removeClass('reaction--' + oldReactionId);
						$target.addClass('reaction--' + $html.data('reaction-id'));
						$target.data('reaction-id', 0);
					}
				}

				$originalReaction.replaceWith($reaction);
				if ($originalReactionText && $reactionText)
				{
					$originalReactionText.replaceWith($reactionText);
				}
			});

			var $reactionList = this.options.reactionList ? XF.findRelativeIf(this.options.reactionList, $target) : $([]);

			if (typeof data.reactionList !== 'undefined' && $reactionList.length)
			{
				if (data.reactionList.content)
				{
					XF.setupHtmlInsert(data.reactionList, function($html, container)
					{
						$reactionList.html($html).addClassTransitioned('is-active');
					});
				}
				else
				{
					$reactionList.removeClassTransitioned('is-active', function()
					{
						$reactionList.empty();
					});
				}
			}
		}
	});
	XF.Reaction.activeTooltip = null;

	XF.BookmarkClick = XF.Event.newHandler({
		eventType: 'click',
		eventNameSpace: 'XFBookmarkClick',

		processing: false,

		href: null,
		tooltip: null,
		trigger: null,
		$tooltipHtml: null,
		clickE: null,

		init: function()
		{
			this.href = this.$target.attr('href');

			this.tooltip = new XF.TooltipElement(XF.proxy(this, 'getTooltipContent'), {
				extraClass: 'tooltip--bookmark',
				html: true,
				loadRequired: true
			});
			this.trigger = new XF.TooltipTrigger(this.$target, this.tooltip, {
				maintain: true,
				trigger: ''
			});
			this.trigger.init();
		},

		click: function(e)
		{
			if (e.button > 0 || e.ctrlKey || e.shiftKey || e.metaKey || e.altKey)
			{
				return;
			}

			e.preventDefault();

			this.clickE = e;

			if (this.$target.hasClass('is-bookmarked'))
			{
				this.trigger.clickShow(e);
			}
			else
			{
				if (this.processing)
				{
					return;
				}
				this.processing = true;

				var self = this;

				XF.ajax('POST', this.href, {tooltip: 1}, XF.proxy(this, 'handleSwitchClick'), {skipDefaultSuccess: true})
					.always(function()
					{
						setTimeout(function()
						{
							self.processing = false;
						}, 250);
					});
			}
		},

		handleSwitchClick: function(data)
		{
			var t = this,
				onReady = function()
				{
					var $target = t.$target;
					XF.handleSwitchResponse($target, data);
					//t.trigger.show();
					t.trigger.clickShow(t.clickE);
				};

			if (data.html)
			{
				XF.setupHtmlInsert(data.html, function($html, data, onComplete)
				{
					if (t.tooltip.requiresLoad())
					{
						t.$tooltipHtml = $html;
						t.tooltip.setLoadRequired(false);
					}
					onReady();

				});
			}
			else
			{
				onReady();
			}
		},

		getTooltipContent: function(onContent)
		{
			if (this.$tooltipHtml && !this.tooltip.requiresLoad())
			{
				this.initializeTooltip(this.$tooltipHtml);

				return this.$tooltipHtml;
			}

			var t = this,
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
				'get', this.href, { tooltip: 1 },
				function(data) { t.tooltipLoaded(data, onContent); },
				options
			);
		},

		tooltipLoaded: function(data, onContent)
		{
			var t = this;
			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				t.initializeTooltip($html);
				onContent($html);
			});
		},

		initializeTooltip: function($html)
		{
			var $form = $html.find('form');
			$form.on('ajax-submit:response', XF.proxy(this, 'handleOverlaySubmit'));
		},

		handleOverlaySubmit: function(e, data)
		{
			if (data.status == 'ok')
			{
				e.preventDefault();

				if (this.trigger)
				{
					this.trigger.hide();
				}

				XF.handleSwitchResponse(this.$target, data);

				if (data.switchKey == 'bookmarkremoved')
				{
					var $form = e.currentTarget;
					$form.reset();
				}
			}
		}
	});

	XF.BookmarkLabelFilter = XF.Element.newHandler({
		options: {
			target: null
		},

		loading: false,
		$filterTarget: null,

		init: function()
		{
			this.$filterTarget = XF.findRelativeIf(this.options.target, this.$target);
			if (!this.$filterTarget.length)
			{
				console.error('No filter target found.');
				return;
			}

			var t = this;

			this.$target.on('select2:select', XF.proxy(this, 'loadResults'));
			this.$target.on('select2:unselect', function(e)
			{
				t.loadResults();
			});
		},

		loadResults: function()
		{
			if (this.loading)
			{
				return;
			}

			this.loading = true;

			var label = this.$target.find('.js-labelFilter').val();

			var t = this;
			XF.ajax('get', XF.canonicalizeUrl('account/bookmarks-popup'), { label: label }, function(data)
			{
				if (data.html)
				{
					XF.setupHtmlInsert(data.html, function($html, container)
					{
						t.$target.find('.js-tokenSelect').select2('close');
						t.$filterTarget.empty();
						t.$filterTarget.append($html);
					});
				}
			}).always(function()
			{
				t.loading = false;
			});
		}
	});

	XF.Event.register('click', 'attribution', 'XF.AttributionClick');
	XF.Event.register('click', 'like', 'XF.LikeClick');
	XF.Event.register('click', 'preview-click', 'XF.PreviewClick');
	XF.Event.register('click', 'scroll-to', 'XF.ScrollToClick');
	XF.Event.register('click', 'switch', 'XF.SwitchClick');
	XF.Event.register('click', 'switch-overlay', 'XF.SwitchOverlayClick');

	XF.Element.register('draft', 'XF.Draft');
	XF.Element.register('focus-trigger', 'XF.FocusTrigger');
	XF.Element.register('poll-block', 'XF.PollBlock');
	XF.Element.register('preview', 'XF.Preview');
	XF.Element.register('share-buttons', 'XF.ShareButtons');
	XF.Element.register('share-input', 'XF.ShareInput');
	XF.Element.register('copy-to-clipboard', 'XF.CopyToClipboard');
	XF.Element.register('push-toggle', 'XF.PushToggle');
	XF.Element.register('push-cta', 'XF.PushCta');
	XF.Element.register('reaction', 'XF.Reaction');
	XF.Element.register('bookmark-click', 'XF.BookmarkClick');
	XF.Element.register('bookmark-label-filter', 'XF.BookmarkLabelFilter');
}
(jQuery, window, document);