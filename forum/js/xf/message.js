!function($, window, document, _undefined)
{
	"use strict";

	XF.Message = XF.Message || {};

	XF.Message.insertMessages = function(dataHtml, $container, ascending, onInsert)
	{
		XF.setupHtmlInsert(dataHtml, function($html, container, onComplete)
		{
			var $noMessages = $container.find('.js-replyNoMessages');

			if ($noMessages.length)
			{
				$noMessages.xfFadeUp();
			}

			$html.each(function()
			{
				if (!this.tagName)
				{
					return;
				}

				XF.Message.insertMessage($(this), $container, ascending);
			});

			if (onInsert)
			{
				onInsert($html);
			}
		});
	};

	XF.Message.insertMessage = function($message, $container, ascending)
	{
		var $firstChild = $container.children().first();

		$message.hide();

		if ($firstChild.is('form') && !ascending)
		{
			$message.insertAfter($firstChild);
		}
		else if (!ascending)
		{
			$container.prepend($message);
		}
		else
		{
			$container.append($message);
		}

		$message.xfFadeDown();

		XF.activate($message);
	};

	// ################################## MESSAGE LOADER HANDLER ###########################################

	XF.MessageLoaderClick = XF.Event.newHandler({
		eventNameSpace: 'XFMessageLoaderClick',
		options: {
			href: null,
			messagesContainer: '< .js-replyNewMessageContainer',
			selfContainer: '.message',
			ascending: true
		},

		loading: false,

		init: function()
		{
			if (!this.options.href)
			{
				this.options.href = this.$target.attr('href');
				if (!this.options.href)
				{
					console.error('Must be initialized with a data-href or href attribute.');
				}
			}
		},

		click: function(e)
		{
			e.preventDefault();

			if (this.loading)
			{
				return;
			}

			var self = this;

			XF.ajax('GET', this.options.href, {}, XF.proxy(this, 'loaded'))
				.always(function() { self.loading = false; });
		},

		loaded: function(data)
		{
			if (!data.html)
			{
				return;
			}

			var $container = XF.findRelativeIf(this.options.messagesContainer, this.$target);
			XF.Message.insertMessages(data.html, $container, this.options.ascending);

			var $selfMessage = this.$target.closest(this.options.selfContainer);
			$selfMessage.xfFadeUp(null, function()
			{
				$selfMessage.remove();
			});

			if (data.lastDate)
			{
				$('.js-quickReply input[name="last_date"]').val(data.lastDate);
			}
		}
	});

	// ################################## QUICK EDIT HANDLER ###########################################

	XF.QuickEditClick = XF.Event.newHandler({
		eventNameSpace: 'XFQuickEdit',

		options: {
			editorTarget: null,
			editContainer: '.js-editContainer',
			href: null,
			noInlineMod: 0
		},

		$editorTarget: null,
		$editForm: null,

		href: null,
		loading: false,

		init: function()
		{
			var edTarget = this.options.editorTarget;

			if (!edTarget)
			{
				console.error('No quick edit editorTarget specified');
				return;
			}

			this.$editorTarget = XF.findRelativeIf(edTarget, this.$target);
			if (!this.$editorTarget.length)
			{
				console.error('No quick edit target found');
				return;
			}

			this.href = this.options.href || this.$target.attr('href');
			if (!this.href)
			{
				console.error('No edit URL specified.');
			}
		},

		click: function(e)
		{
			if (!this.$editorTarget || !this.href)
			{
				return;
			}

			e.preventDefault();

			if (this.loading)
			{
				return;
			}

			this.loading = true;

			var data = {};
			if (this.options.noInlineMod)
			{
				data['_xfNoInlineMod'] = true;
			}

			XF.ajax('GET', this.href, data, XF.proxy(this, 'handleAjax'), { skipDefaultSuccessError: true });
		},

		handleAjax: function(data)
		{
			var $editorTarget = this.$editorTarget,
				self = this;

			if (data.errors)
			{
				this.loading = false;
				XF.alert(data.errors);
				return;
			}

			XF.setupHtmlInsert(data.html, function($html, container)
			{
				$html.hide().insertAfter($editorTarget);
				XF.activate($html);
				self.$editForm = $html;

				$html.on('ajax-submit:response', XF.proxy(self, 'editSubmit'));
				$html.find('.js-cancelButton').on('click', XF.proxy(self, 'cancelClick'));

				var $hidden = $html.find('input[type=hidden]').first();
				$hidden.after('<input type="hidden" name="_xfInlineEdit" value="1" />');

				$editorTarget.xfFadeUp(null, function()
				{
					$editorTarget.parent().addClass('is-editing');

					$html.xfFadeDown(XF.config.speed.normal, function()
					{
						$html.trigger('quick-edit:shown');

						var $editContainer = $html.find(self.options.editContainer);
						if ($editContainer.length && !XF.isElementVisible($editContainer))
						{
							$editContainer.get(0).scrollIntoView(true);
						}

						self.loading = false;
					});
				});

				$html.trigger('quick-edit:show');
			});
		},

		editSubmit: function(e, data)
		{
			if (data.errors || data.exception)
			{
				return;
			}

			e.preventDefault();

			if (data.message)
			{
				XF.flashMessage(data.message, 3000);
			}

			var $editorTarget = this.$editorTarget,
				self = this;

			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				var target = self.options.editorTarget;
				target = target.replace(/<|\|/g, '').replace(/#[a-zA-Z0-9_-]+\s*/, '');

				var $message = $html.find(target);

				$message.hide();
				$editorTarget.replaceWith($message);
				self.$editorTarget = $message;
				XF.activate($message);

				self.stopEditing(false, function()
				{
					$message.xfFadeDown();

					self.$editForm.trigger('quickedit:editcomplete', data);
				});
			});
		},

		cancelClick: function(e)
		{
			this.stopEditing(true);
		},

		stopEditing: function(showMessage, onComplete)
		{
			var $editorTarget = this.$editorTarget,
				$editForm = this.$editForm,
				self = this;

			var finish = function()
			{
				$editorTarget.parent().removeClass('is-editing');

				if (showMessage)
				{
					$editorTarget.xfFadeDown();
				}

				if (onComplete)
				{
					onComplete();
				}

				$editForm.remove();
				self.$editForm = null;
			};

			if ($editForm)
			{
				$editForm.xfFadeUp(null, finish);
			}
			else
			{
				finish();
			}
		}
	});

	// ################################## QUOTE HANDLER ###########################################

	XF.QuoteClick = XF.Event.newHandler({
		eventNameSpace: 'XFQuoteClick',
		options: {
			quoteHref: null,
			editor: '.js-quickReply .js-editor'
		},

		init: function()
		{
			if (!this.options.quoteHref)
			{
				console.error('Must be initialized with a data-quote-href attribute.');
			}
		},

		click: function(e)
		{
			e.preventDefault();

			var href = this.options.quoteHref,
				$selectToQuote = $(e.target).parents('.tooltip--selectToQuote'),
				quoteHtml = XF.unparseBbCode($selectToQuote.data('quote-html'));

			XF.ajax('POST', href, { quoteHtml: quoteHtml }, XF.proxy(this, 'handleAjax'), {skipDefaultSuccess: true});

			$(e.target).trigger('s2q:click');

			var $editor = XF.findRelativeIf(this.options.editor, this.$target);
			$editor.closest('.js-quickReply').get(0).scrollIntoView(true);
			XF.focusEditor($editor);
		},

		handleAjax: function(data)
		{
			var $editor = XF.findRelativeIf(this.options.editor, this.$target);

			XF.insertIntoEditor($editor, data.quoteHtml, data.quote);
		}
	});

	// ################################## MULTI QUOTE HANDLER ###########################################

	XF.MultiQuote = XF.Element.newHandler({
		options: {
			href: '',
			messageSelector: '',
			addMessage: '',
			removeMessage: '',
			storageKey: ''
		},

		mqStorage: null,
		mqOverlay: null,

		removing: false,
		quoting: false,

		init: function()
		{
			this.initButton();
			this.initControls();

			var self = this;

			XF.CrossTab.on('mqChange', function(data)
			{
				if (data.storageKey !== self.options.storageKey)
				{
					return;
				}

				var messageId = data.messageId;

				switch (data.action)
				{
					case 'added':
						self.selectMqControl(messageId);
						break;

					case 'removed':
						self.deselectMqControl(messageId);
						break;

					case 'refresh':
						// the code below will handle this
						break;
				}

				self.refreshMqData();
				self.updateButtonState();
			});
		},

		initButton: function()
		{
			this.mqStorage = XF.LocalStorage.getJson(this.options.storageKey);
			if (this.hasQuotesStored())
			{
				this.$target.show();
			}

			this.$target.on('click', XF.proxy(this, 'buttonClick'));
		},

		buttonClick: function(e)
		{
			e.preventDefault();
			if (!this.options.href)
			{
				console.error('Multi-quote button must have a data-href attribute set to display selected quotes');
				return false;
			}

			XF.ajax('post', this.options.href, {
				quotes: XF.LocalStorage.get(this.options.storageKey)
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
					$overlay.find('.js-removeMessage').on('click', XF.proxy(self, 'removeMessage'));
					$overlay.find('.js-quoteMessages').on('click', XF.proxy(self, 'quoteMessages'));
					self.mqOverlay = XF.showOverlay($overlay);
				});
			}
		},

		removeMessage: function(e)
		{
			e.preventDefault();

			if (this.removing)
			{
				return;
			}

			this.removing = true;

			var $item = $(e.target).closest('.nestable-item'),
				messageId = $item.data('id'),
				overlay = this.mqOverlay;

			this.removeFromMultiQuote(messageId);

			$item.xfFadeUp(XF.config.speed.fast, function()
			{
				$item.remove();
			});

			if (!this.hasQuotesStored())
			{
				overlay.hide();
			}

			this.removing = false;
		},

		quoteMessages: function(e)
		{
			e.preventDefault();

			if (this.quoting)
			{
				return;
			}

			this.quoting = true;

			var overlay = this.mqOverlay,
				$overlay = overlay.getOverlay(),
				toInsert = $.parseJSON($overlay.find('input[name="message_ids"]').val()),
				multiQuotes = this.mqStorage,
				t = this;

			for (var i in toInsert)
			{
				if (!toInsert.hasOwnProperty(i) || !toInsert[i].hasOwnProperty('id'))
				{
					continue;
				}

				var id = toInsert[i]['id'],
					parts = id.split('-'),
					messageId = parts[0],
					key = parts[1];

				if (!this.isValidQuote(multiQuotes[messageId], key))
				{
					continue;
				}

				var value = multiQuotes[messageId][key];
				if (value !== true)
				{
					value = XF.unparseBbCode(value);
				}
				toInsert[i]['value'] = value;
			}

			overlay.hide();

			XF.ajax('post', this.options.href, {
				insert: toInsert,
				quotes: XF.LocalStorage.get(this.options.storageKey)
			}, XF.proxy(this, 'insertMessages')).always(function()
			{
				t.quoting = false;
			});
		},

		isValidQuote: function(quote, key)
		{
			if (quote != undefined)
			{
				if (quote.hasOwnProperty(key))
				{
					if (quote[key] === true || typeof quote[key] == 'string')
					{
						return true;
					}
				}
			}

			return false;
		},

		insertMessages: function(data)
		{
			var $editor = XF.findRelativeIf('form .js-editor', this.$target);
			if (!$editor.length)
			{
				$editor = $('.js-editor').parent();
			}

			$.each(data, function(i, quoteObj)
			{
				if (!quoteObj.hasOwnProperty('quote') || !quoteObj.hasOwnProperty('quoteHtml'))
				{
					return true;
				}

				XF.insertIntoEditor($editor, quoteObj.quoteHtml, quoteObj.quote);
			});

			for (var messageId in this.mqStorage)
			{
				this.removeFromMultiQuote(messageId);
			}
		},

		initControls: function()
		{
			var messages = '.tooltip--selectToQuote, ' + this.options.messageSelector,
				$controls = $(messages).find('.js-multiQuote');

			$(document).on('click', messages, XF.proxy(this, 'controlClick'));

			var self = this;
			$controls.each(function()
			{
				var $control = $(this),
					messageId = $control.data('messageId');

				if (self.mqStorage.hasOwnProperty(messageId))
				{
					$control.addClass('is-selected');
					$control.data('mqAction', 'remove');
				}
			});
		},

		controlClick: function(e)
		{
			if (!$(e.target).is('.js-multiQuote'))
			{
				return;
			}

			e.preventDefault();

			var $target = $(e.target),
				action = $target.data('mqAction'),
				messageId = $target.data('messageId');

			switch (action)
			{
				case 'add':
					this.addToMultiQuote(messageId);
					XF.flashMessage(this.options.addMessage, 3000);
					break;

				case 'remove':
					this.removeFromMultiQuote(messageId);
					XF.flashMessage(this.options.removeMessage, 3000);
					break;
			}

			$(e.target).trigger('s2q:click');
		},

		addToMultiQuote: function(messageId)
		{
			var $mqControl = $('.js-multiQuote[data-message-id="' + messageId + '"]'),
				$selectToQuote = $mqControl.parents('.tooltip--selectToQuote'),
				quoteHtml = XF.unparseBbCode($selectToQuote.data('quote-html'));

			this.refreshMqData();

			if (!this.hasQuotesStored())
			{
				this.mqStorage = {};
				this.mqStorage[messageId] = [];
			}
			else
			{
				if (!this.mqStorage[messageId])
				{
					this.mqStorage[messageId] = [];
				}
			}

			if ($selectToQuote.length)
			{
				this.mqStorage[messageId].push(quoteHtml);
			}
			else
			{
				this.mqStorage[messageId].push(true); // true == quoting the full message
			}
			this.updateMultiQuote();

			this.selectMqControl(messageId);
			this.triggerCrossTabEvent('added', messageId);
		},

		removeFromMultiQuote: function(messageId)
		{
			var quoteInfo = String(messageId).match(/^(\d+)-(\d+)$/);

			this.refreshMqData();

			if (quoteInfo)
			{
				messageId = quoteInfo[1];

				delete this.mqStorage[messageId][quoteInfo[2]];

				if (!this.getQuoteStoreCount(this.mqStorage[messageId]))
				{
					delete this.mqStorage[messageId];
				}
			}
			else
			{
				delete this.mqStorage[messageId];
			}

			this.updateMultiQuote();

			if (!this.mqStorage[messageId])
			{
				this.deselectMqControl(messageId);
				this.triggerCrossTabEvent('removed', messageId);
			}
		},

		selectMqControl: function(messageId)
		{
			var $mqControl = $('.js-multiQuote[data-message-id="' + messageId + '"]');

			if ($mqControl.length)
			{
				$mqControl.addClass('is-selected');
				$mqControl.data('mqAction', 'remove');
			}
		},

		deselectMqControl: function(messageId)
		{
			var $mqControl = $('.js-multiQuote[data-message-id="' + messageId + '"]');

			if ($mqControl.length)
			{
				$mqControl.removeClass('is-selected');
				$mqControl.data('mqAction', 'add');
			}
		},

		getQuoteStoreCount: function(quoteStore)
		{
			var length = 0;

			for (var i in quoteStore)
			{
				if (quoteStore.hasOwnProperty(i))
				{
					if (quoteStore[i] == true || typeof quoteStore[i] == 'string')
					{
						length ++;
					}
				}
			}

			return length;
		},

		updateMultiQuote: function()
		{
			XF.LocalStorage.setJson(this.options.storageKey, this.mqStorage, true);
			this.updateButtonState();
		},

		updateButtonState: function()
		{
			if (!this.hasQuotesStored())
			{
				this.$target.hide();
			}
			else
			{
				this.$target.show();
			}
		},

		refreshMqData: function()
		{
			this.mqStorage = XF.LocalStorage.getJson(this.options.storageKey);
		},

		hasQuotesStored: function()
		{
			return this.mqStorage && !$.isEmptyObject(this.mqStorage);
		},

		triggerCrossTabEvent: function(action, messageId, data)
		{
			data = data || {};
			data.storageKey = this.options.storageKey;
			data.action = action;
			data.messageId = messageId;

			XF.CrossTab.trigger('mqChange', data);
		}
	});

	// ################################## SELECT TO QUOTE HANDLER ###########################################

	XF.SelectToQuote = XF.Element.newHandler({
		options: {
			messageSelector: ''
		},

		$quickReply: null,

		timeout: null,
		processing: false,
		isMouseDown: false,
		tooltip: null,
		tooltipId: null,

		init: function()
		{
			if (!window.getSelection)
			{
				return;
			}

			if (!this.options.messageSelector)
			{
				console.error('No messageSelector');
				return;
			}

			this.$quickReply = $('.js-quickReply .js-editor').parent();
			if (!this.$quickReply.length)
			{
				return;
			}

			this.$target.on('mousedown', XF.proxy(this, 'mouseDown'));
			this.$target.on('mouseup', XF.proxy(this, 'mouseUp'));
			$(document).on('selectionchange', XF.proxy(this, 'selectionChange'));
		},

		mouseDown: function()
		{
			this.isMouseDown = true;
		},

		mouseUp: function()
		{
			this.isMouseDown = false;
			this.trigger();
		},

		selectionChange: function()
		{
			if (!this.isMouseDown)
			{
				this.trigger();
			}
		},

		trigger: function()
		{
			if (!this.timeout && !this.processing)
			{
				this.timeout = setTimeout(XF.proxy(this, 'handleSelection'), 100);
			}
		},

		handleSelection: function()
		{
			this.processing = true;
			this.timeout = null;

			var selection = window.getSelection(),
				$selectionContainer = this.getValidSelectionContainer(selection);

			if ($selectionContainer)
			{
				this.showQuoteButton($selectionContainer, selection);
			}
			else
			{
				this.hideQuoteButton();
			}

			var self = this;
			setTimeout(function()
			{
				self.processing = false;
			}, 0);
		},

		getValidSelectionContainer: function(selection)
		{
			if (selection.isCollapsed || !selection.rangeCount)
			{
				return null;
			}

			var range = selection.getRangeAt(0);
			this.adjustRange(range);

			if (!$.trim(range.toString()).length)
			{
				if (!range.cloneContents().querySelectorAll('img').length)
				{
					return null;
				}
			}

			var $container = $(range.commonAncestorContainer).closest('.js-selectToQuote');
			if (!$container.length)
			{
				return null;
			}

			var $message = $container.closest(this.options.messageSelector);
			if (!$message.find('.actionBar-action[data-xf-click="quote"]').length)
			{
				return null;
			}

			if ($(range.startContainer).closest('.bbCodeBlock--quote, .js-noSelectToQuote').length
				|| $(range.endContainer).closest('.bbCodeBlock--quote, .js-noSelectToQuote').length)
			{
				return null;
			}

			return $container;
		},

		adjustRange: function(range)
		{
			var changed = false,
				isQuote = false,
				end = range.endContainer,
				$end = $(end);

			if (range.endOffset == 0)
			{
				if (end.nodeType == 3 && !end.previousSibling)
				{
					// text node with nothing before it, move up
					$end = $end.parent();
				}
				isQuote = ($end.closest('.bbCodeBlock--quote').length > 0);
			}

			if (isQuote)
			{
				var $quote = $end.closest('.bbCodeBlock--quote');
				if ($quote.length)
				{
					range.setEndBefore($quote[0]);
					changed = true;
				}
			}

			if (changed)
			{
				var sel = window.getSelection();
				sel.removeAllRanges();
				sel.addRange(range);
			}
		},

		showQuoteButton: function($selectionContainer, selection)
		{
			var id = $selectionContainer.xfUniqueId();
			if (!this.tooltip || this.tooltipId !== id)
			{
				this.hideQuoteButton();
				this.createButton($selectionContainer, id);
			}

			var $tooltip = this.tooltip.getTooltip();
			$tooltip.data('quote-html', this.getSelectionHtml(selection));

			var offset = this.getButtonPositionMarker(selection);
			if (XF.browser.android)
			{
				offset.top += 10;
			}
			this.tooltip.setPositioner([offset.left, offset.top]);

			if (this.tooltip.isShown())
			{
				this.tooltip.reposition();
			}
			else
			{
				this.tooltip.show();
			}

			$tooltip.addClass('tooltip--selectToQuote');
		},

		getButtonPositionMarker: function(selection)
		{
			// get absolute position of end of selection - or maybe focusNode
			// and position the quote button immediately next to the highlight
			var $el, range, offset, height, bounds;

			$el = $('<span />').text('\u200B');

			range = selection.getRangeAt(0).cloneRange();
			bounds = range.getBoundingClientRect ? range.getBoundingClientRect() : null;
			range.collapse(false);
			range.insertNode($el[0]);

			var changed,
				moves = 0;

			do
			{
				changed = false;
				moves++;

				if ($el[0].parentNode && $el[0].parentNode.className == 'js-selectToQuoteEnd')
				{
					// highlight after the marker to ensure that triple click works
					$el.insertBefore($el[0].parentNode);

					changed = true;
				}
				if ($el[0].previousSibling && $el[0].previousSibling.nodeType == 3 && $.trim($el[0].previousSibling.textContent).length == 0)
				{
					// highlight after an empty text block
					$el.insertBefore($el[0].previousSibling);

					changed = true;
				}
				if ($el[0].parentNode && $el[0].parentNode.tagName == 'LI' && !$el[0].previousSibling)
				{
					// highlight at the beginning of a list item, move to previous item if possible
					var li = $el[0].parentNode;
					if ($(li).prev('li').length)
					{
						// move to inside the last li
						$el.appendTo($(li).prev('li'));

						changed = true;
					}
					else if (li.parentNode)
					{
						// first list item, move before the list
						$el.insertBefore(li.parentNode);

						changed = true;
					}
				}
				if ($el[0].parentNode && !$el[0].previousSibling && $.inArray($el[0].parentNode.tagName, ['DIV', 'BLOCKQUOTE', 'PRE']) != -1)
				{
					$el.insertBefore($el[0].parentNode);

					changed = true;
				}
				if ($el[0].previousSibling && $.inArray($el[0].previousSibling.tagName, ['OL', 'UL']) != -1)
				{
					// immediately after a list, position at end of last LI
					$($el[0].previousSibling).find('li:last').append($el);

					changed = true;
				}
				if ($el[0].previousSibling && $.inArray($el[0].previousSibling.tagName, ['DIV', 'BLOCKQUOTE', 'PRE']) != -1)
				{
					// highlight immediately after a block causes weird positioning
					$el.appendTo($el[0].previousSibling);

					changed = true;
				}
				if ($el[0].previousSibling && $el[0].previousSibling.tagName == 'BR')
				{
					// highlight immediately after a line break causes weird positioning
					$el.insertBefore($el[0].previousSibling);

					changed = true;
				}
			}
			while (changed && moves < 5);

			offset = $el.offset();
			height = $el.height();

			// if we're in a scrollable element, find the right edge of that element and don't position beyond it
			$el.parentsUntil('body').each(function()
			{
				var $parent = $(this), left, right;

				switch ($parent.css('overflow-x'))
				{
					case 'hidden':
					case 'scroll':
					case 'auto':
						left = $parent.offset().left;
						right = left + $parent.outerWidth();
						if (offset.left < left)
						{
							offset.left = left;
						}
						if (right < offset.left)
						{
							offset.left = right;
						}
				}
			});

			var $parent = $el.parent();
			$el.remove();

			if (!XF.browser.msie) // IE loses the selection from this
			{
				$parent[0].normalize(); // recombine text nodes for accurate text rendering
			}

			if (bounds && !XF.isRtl())
			{
				if (offset.left - bounds.left > 32)
				{
					offset.left -= 16;
				}
			}

			offset.top += height;

			return offset;
		},

		createButton: function($selectionContainer, id)
		{
			var $message = $selectionContainer.closest(this.options.messageSelector),
				$tooltip = $('<span />');

			var $mqButton = $message.find('.actionBar-action.js-multiQuote').clone();
			if ($mqButton.length)
			{
				$mqButton
					.attr('title', '')
					.removeClass('is-selected')
					.data('mqAction', 'add')
					.css({
						marginLeft: 0,
						background: 'transparent'
					})
					.on('s2q:click', XF.proxy(this, 'buttonClicked'));

				$tooltip.append($mqButton);
				$tooltip.append(document.createTextNode(' | '));
			}

			var $quoteButton = $message.find('.actionBar-action[data-xf-click="quote"]')
					.attr('title', '')
					.clone()
					.css({
						marginLeft: 0
					})
					.on('s2q:click', XF.proxy(this, 'buttonClicked'));

			$tooltip.append($quoteButton);

			this.tooltip = new XF.TooltipElement($tooltip, {
				html: true,
				placement: 'bottom'
			});
			this.tooltipId = id;
		},

		buttonClicked: function()
		{
			var s = window.getSelection();
			if (!s.isCollapsed)
			{
				s.collapse(s.getRangeAt(0).commonAncestorContainer, 0);
				this.hideQuoteButton();
			}
		},

		hideQuoteButton: function()
		{
			var tooltip = this.tooltip;

			if (tooltip)
			{
				tooltip.destroy();
				this.tooltip = null;
			}
		},

		getSelectionHtml: function(selection)
		{
			var el = document.createElement('div'),
				i, len;

			for (i = 0, len = selection.rangeCount; i < len; i++)
			{
				el.appendChild(selection.getRangeAt(i).cloneContents());
			}

			return this.prepareSelectionHtml(el.innerHTML);
		},

		prepareSelectionHtml: function(html)
		{
			return XF.adjustHtmlForRte(html);
		}
	});

	// ################################## QUICK REPLY HANDLER ###########################################

	XF.QuickReply = XF.Element.newHandler({

		options: {
			messageContainer: '',
			ascending: true,

			filesContainer: '.js-attachmentFiles',
			fileRow: '.js-attachmentFile',
			insertAllRow: '.js-attachmentInsertAllRow',

			submitHide: null
		},

		init: function()
		{
			this.$target.on('ajax-submit:before', XF.proxy(this, 'beforeSubmit'));
			this.$target.on('ajax-submit:response', XF.proxy(this, 'afterSubmit'));
			this.$target.on('draft:complete', XF.proxy(this, 'onDraft'));
		},

		beforeSubmit: function(e, config)
		{
			var $button = config.submitButton;

			if ($button && $button.attr('name') == 'more_options')
			{
				e.preventDefault();
			}
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
				return;
			}

			this.$target.find('input[name="last_date"]').val(data.lastDate);
			this.getMessagesContainer().find('.js-newMessagesIndicator').remove();

			this.insertMessages(data.html);

			XF.clearEditorContent(this.$target);

			var editor = XF.getEditorInContainer(this.$target);
			if (editor && XF.Editor && editor instanceof XF.Editor)
			{
				editor.blur();
			}

			var $target = this.$target,
				options = this.options,
				$filesContainer = $target.find(options.filesContainer);

			if ($filesContainer.length)
			{
				$filesContainer.removeClass('is-active');

				var $insertAllRow = $filesContainer.find(options.insertAllRow),
					$files = $filesContainer.find(options.fileRow);

				if ($insertAllRow.length)
				{
					$insertAllRow.removeClass('is-active');
				}
				if ($files.length)
				{
					$files.remove();
				}
			}

			this.$target.trigger('preview:hide', [this]); // XF.PreviewClick listens for this

			if (this.options.submitHide)
			{
				var $submitHide = XF.findRelativeIf(this.options.submitHide, this.$target);
				$submitHide.hide();
			}
		},

		insertMessages: function(dataHtml)
		{
			XF.Message.insertMessages(
				dataHtml,
				this.getMessagesContainer(),
				this.options.ascending,
				function($messages)
				{
					var $message = $messages.first();
					if ($message && $message.length)
					{
						var dims = $message.dimensions(),
							windowTop = $(window).scrollTop(),
							windowBottom = windowTop + $(window).height();

						if (dims.top < windowTop + 50 || dims.top > windowBottom)
						{
							$('html, body').animate(
								{ scrollTop: Math.max(0, dims.top - 60) },
								200
							);
						}
					}
				}
			);
		},

		getMessagesContainer: function()
		{
			var containerOption = this.options.messageContainer;
			if (containerOption)
			{
				return XF.findRelativeIf(containerOption, this.$target).first();
			}
			else
			{
				return $('.js-replyNewMessageContainer').first();
			}
		},

		onDraft: function(e, data)
		{
			if (data.hasNew && data.html)
			{
				if (this.getMessagesContainer().find('.js-newMessagesIndicator').length)
				{
					return;
				}

				// structured like a message
				this.insertMessages(data.html);
			}
		}
	});

	// ################################## GUEST CAPTCHA HANDLER ###########################################

	XF.GuestCaptcha = XF.Element.newHandler({

		options: {
			url: 'index.php?misc/captcha&with_row=1',
			target: '.js-captchaContainer',
			skip: '[name=more_options]'
		},

		$captchaContainer: null,

		initialized: false,

		init: function()
		{
			var $form = this.$target;
			this.$captchaContainer = $form.find(this.options.target);
			if (!this.$captchaContainer.length)
			{
				return;
			}

			$form.on('focusin', XF.proxy(this, 'initializeCaptcha'));
			$form.on('submit ajax-submit:before', XF.proxy(this, 'submit'));
		},

		initializeCaptcha: function(e)
		{
			var $activeElement = $(document.activeElement);

			if (this.initialized || $activeElement.is(this.options.skip))
			{
				return;
			}

			var rowType = this.$captchaContainer.data('row-type') || '';

			XF.ajax('get',
				XF.canonicalizeUrl(this.options.url),
				{ row_type: rowType },
				XF.proxy(this, 'showCaptcha')
			);

			this.initialized = true;
		},

		showCaptcha: function(data)
		{
			var self = this;
			XF.setupHtmlInsert(data.html, function ($html, container, onComplete)
			{
				$html.replaceAll(self.$captchaContainer);

				onComplete();
			});
		},

		submit: function(e)
		{
			if (!this.initialized)
			{
				var $activeElement = $(document.activeElement);

				if (!$activeElement.is(this.options.skip))
				{
					e.preventDefault();
					return false;
				}
			}
		}
	});

	// ################################## POST EDIT HANDLER ######################

	XF.PostEdit = XF.Element.newHandler({

		init: function()
		{
			this.$target.on('quickedit:editcomplete', XF.proxy(this, 'editComplete'));
		},

		editComplete: function(e, data)
		{
			var self = this;
			XF.setupHtmlInsert(data.html, function($html, container, onComplete)
			{
				var threadChanges = data.threadChanges || {};

				if (threadChanges.title)
				{
					$('h1.p-title-value').html(container.h1);
					$('title').html(container.title);

					// This effectively runs twice, but we do need the title to be correct if updating this way.
					if (XF.config.visitorCounts['title_count'] && data.visitor)
					{
						XF.pageTitleCache = container.title;
						XF.pageTitleCounterUpdate(data.visitor.total_unread);
					}
				}

				if (threadChanges.customFields)
				{
					var $newThreadStatusField = $html.closest('.js-threadStatusField'),
						$threadStatusField = XF.findRelativeIf('< .block--messages | .js-threadStatusField', self.$target);

					if ($newThreadStatusField.length && $threadStatusField.length)
					{
						$threadStatusField.xfFadeUp(XF.config.speed.fast, function()
						{
							$threadStatusField.replaceWith($newThreadStatusField).xfFadeDown(XF.config.speed.fast);
						});
					}
				}
				else
				{
					$html.find('.js-threadStatusField').remove();
				}
			});
		}
	});

	XF.Event.register('click', 'message-loader', 'XF.MessageLoaderClick');
	XF.Event.register('click', 'quick-edit', 'XF.QuickEditClick');
	XF.Event.register('click', 'quote', 'XF.QuoteClick');

	XF.Element.register('multi-quote', 'XF.MultiQuote');
	XF.Element.register('select-to-quote', 'XF.SelectToQuote');
	XF.Element.register('quick-reply', 'XF.QuickReply');
	XF.Element.register('guest-captcha', 'XF.GuestCaptcha');
	XF.Element.register('post-edit', 'XF.PostEdit');
}
(jQuery, window, document);