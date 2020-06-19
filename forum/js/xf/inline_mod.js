!function($, window, document, _undefined)
{
	"use strict";

	XF.InlineMod = XF.Element.newHandler({
		options: {
			type: null,
			href: null,
			cookieBase: 'inlinemod',
			toggle: 'input[type=checkbox].js-inlineModToggle',
			toggleContainer: '.js-inlineModContainer',
			containerClass: 'is-mod-selected',
			actionTrigger: '.js-inlineModTrigger',
			counter: '.js-inlineModCounter',
			viewport: 'body'
		},

		cookie: null,
		$action: null,
		xhr: null,

		init: function()
		{
			if (!this.options.type)
			{
				console.error('No inline mod type specified');
				return;
			}

			if (!this.options.href)
			{
				console.error('No inline mod href specified');
			}

			this.cookie = this.options.cookieBase + '_' + this.options.type;

			this.$target.on('click', this.options.toggle, XF.proxy(this, 'onToggle'));
			this.$target.on('click', this.options.actionTrigger, XF.proxy(this, 'onActionTrigger'));

			var cookie = this.getCookieValue();
			this._initialLoad(cookie);
			this._updateCounter(cookie.length);

			var t = this;

			// timeout is so we don't listen immediately as this event is fired shortly after this is setup
			setTimeout(function()
			{
				$(document).on('xf:reinit', function(e, el)
				{
					// if the element we're activating is placed within the inline mod area and contains an
					// inline mod checkbox, we need to recalculate our status to account for new highlighting
					if (t.$target.has(el) && $(el).find(t.options.toggle).length)
					{
						t.recalculateFromCookie();
					}
				});
			}, 0);
		},

		_initialLoad: function(checked)
		{
			// Firefox seems to retain the checkbox state after reload. We need to clear any existing checkbox states.
			this.$target.find(this.options.toggle).each(function()
			{
				$(this).prop('checked', false);
			});

			var map = {};

			if (checked.length)
			{
				this.$target.find(this.options.toggle).each(function()
				{
					var $this = $(this);
					map[$this.val()] = $this;
				});

				var length = checked.length, id;
				for (var i = 0; i < length; i++)
				{
					id = checked[i];
					if (map[id])
					{
						map[id].prop('checked', true);
						this.toggleContainer(map[id], true);
					}
				}
			}
		},

		recalculateFromCookie: function()
		{
			var values = this.getCookieValue(),
				length = values.length,
				checked = {},
				self = this;

			for (var i = 0; i < length; i++)
			{
				checked[values[i]] = true;
			}

			this.$target.find(this.options.toggle).each(function()
			{
				var $this = $(this),
					thisState = $this.is(':checked'),
					expectedState = checked[$this.val()] ? true : false;

				if (thisState && !expectedState)
				{
					$this.prop('checked', false);
					self.toggleContainer($this, false);
				}
				else if (!thisState && expectedState)
				{
					$this.prop('checked', true);
					self.toggleContainer($this, true);
				}
			});
		},

		deselect: function()
		{
			this.setCookieValue([]);
			this.recalculateFromCookie();
			this.hideBar();
		},

		selectAll: function()
		{
			var cookie = this.getCookieValue();

			this.$target.find(this.options.toggle).each(function()
			{
				var id = parseInt($(this).val(), 10);
				if ($.inArray(id, cookie) === -1)
				{
					cookie.push(id);
				}
			});

			this.setCookieValue(cookie);
			this.recalculateFromCookie();

			return cookie;
		},

		deselectPage: function()
		{
			var existingCookie = this.getCookieValue(),
				newCookie = [],
				pageIds = [];

			this.$target.find(this.options.toggle).each(function()
			{
				pageIds.push(parseInt($(this).val(), 10));
			});

			for (var i = 0; i < existingCookie.length; i++)
			{
				if ($.inArray(existingCookie[i], pageIds) === -1)
				{
					newCookie.push(existingCookie[i]);
				}
			}

			this.setCookieValue(newCookie);
			this.recalculateFromCookie();

			if (newCookie.length)
			{
				this.loadBar();
			}
			else
			{
				this.hideBar();
			}

			return newCookie;
		},

		onToggle: function(e)
		{
			var $check = $(e.target),
				selected = $check.is(':checked'),
				cookie;

			cookie = this.toggleSelectedInCookie($check.val(), selected);
			this.toggleContainer($check, selected);

			if (cookie.length)
			{
				this.loadBar();
			}
			else
			{
				this.hideBar();
			}
		},

		onActionTrigger: function(e)
		{
			e.preventDefault();

			this.loadBar();
		},

		loadBar: function(onLoad)
		{
			var self = this;

			// put this in a timeout to handle JS setting multiple toggles
			// in a single action, and firing click actions for each one

			if (this.loadTimeout)
			{
				clearTimeout(this.loadTimeout);
			}

			this.loadTimeout = setTimeout(function()
			{
				if (self.xhr)
				{
					self.xhr.abort();
				}
				self.xhr = XF.ajax(
					'GET', self.options.href,
					{ type: self.options.type },
					function(result) { self._showBar(result, onLoad); }
				);
			}, 10);
		},

		_showBar: function(result, onLoad)
		{
			this.xhr = null;

			if (!result.html)
			{
				return;
			}

			var self = this;

			XF.setupHtmlInsert(result.html, function($html, container, onComplete)
			{
				var fastReplace = false;

				if (self.$bar)
				{
					fastReplace = true;
					self.$bar.remove();
					self.$bar = null;
				}

				self._setupBar($html);
				self.$bar = $html;
				XF.bottomFix($html);

				if (XF.browser.ios)
				{
					// iOS has a quirk with this bar being fixed. If you open the select and click "go" before
					// blurring the select, the blur will happen and the click will actually register on whatever
					// was under the go button (rather than the button itself). To workaround this, we add an invisible
					// cover over the screen (not the mod bar) whenever the select is focused.
					var $cover = $('<div class="inlineModBarCover" />'),
						$bar = self.$bar,
						$action = $bar.find('.js-inlineModAction');

					$cover.click(function() { $action.blur(); });

					$action.on({
						focus: function()
						{
							$bar.before($cover);
						},
						blur: function()
						{
							setTimeout(function() { $cover.remove(); }, 200);
						}
					});
				}

				if (fastReplace)
				{
					$html.css('transition-duration', '0s');
				}

				$html.addClassTransitioned('is-active');

				if (fastReplace)
				{
					setTimeout(function() {
						$html.css('transition-duration', '');
					}, 0);
				}

				if (onLoad)
				{
					onLoad($html);
				}
			});
		},

		_setupBar: function($bar)
		{
			$bar.on('click', ':submit', XF.proxy(this, 'submit'))
				.on('click', '.js-inlineModClose', XF.proxy(this, 'hideBar'))
				.on('click', '.js-inlineModSelectAll', XF.proxy(this, 'onSelectAllClick'));

			// check the 'select all' checkbox if all toggles are checked
			var $toggles = this.$target.find(this.options.toggle);

			if ($toggles.length == $toggles.filter(':checked').length)
			{
				$bar.find('input[type=checkbox].js-inlineModSelectAll').prop('checked', true);
			}
		},

		onSelectAllClick: function(e)
		{
			var $el = $(e.target);

			if ($el.is(':checked'))
			{
				var cookie = this.selectAll();
				if (cookie.length)
				{
					var check = function($bar)
					{
						$bar.find('input[type=checkbox].js-inlineModSelectAll').prop('checked', true);
					};
					this.loadBar(check);
				}
				else
				{
					this.deselect();
				}
			}
			else
			{
				this.deselectPage();
			}
		},

		submit: function()
		{
			if (!this.$bar)
			{
				return;
			}

			var $action = this.$bar.find('.js-inlineModAction');
			if (!$action.length)
			{
				console.error('No action selector found.');
				return;
			}

			var action = $action.val();
			if (!action)
			{
				// do nothing
				return;
			}
			else if (action == 'deselect')
			{
				this.deselect();
			}
			else
			{
				var self = this;

				XF.ajax(
					'POST', this.options.href,
					{ type: this.options.type, action: action },
					function(result) { self._handleSubmitResponse(result); },
					{ skipDefaultSuccess: true }
				);
			}
		},

		_handleSubmitResponse: function(data)
		{
			if (data.html)
			{
				XF.setupHtmlInsert(data.html, function($html, container)
				{
					var $overlay = XF.getOverlayHtml({
						html: $html,
						title: container.h1 || container.title
					});
					XF.showOverlay($overlay);
				});
			}
			else if (data.status == 'ok' && data.redirect)
			{
				if (data.message)
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
			}
			else
			{
				XF.alert('Unexpected response');
			}

			this.hideBar();
		},

		hideBar: function()
		{
			if (this.$bar)
			{
				var self = this;

				this.$bar.removeClassTransitioned('is-active', function()
				{
					if (self.$bar)
					{
						self.$bar.remove();
					}
					self.$bar = null;
				});
			}
		},

		_updateCounter: function(total)
		{
			var $actionTrigger = this.$target.find(this.options.actionTrigger),
				$toggleEl = $actionTrigger.find('.inlineModButton');

			if (!$toggleEl.length)
			{
				$toggleEl = $actionTrigger;
			}

			$toggleEl.toggleClass('is-mod-active', total > 0);

			this.$target.find(this.options.counter).text(total);
		},

		toggleContainer: function($toggle, selected)
		{
			var method = selected ? 'addClass' : 'removeClass';

			$toggle.closest(this.options.toggleContainer)[method](this.options.containerClass);
		},

		toggleSelectedInCookie: function(id, selected)
		{
			id = parseInt(id, 10);

			var value = this.getCookieValue(),
				index = $.inArray(id, value),
				changed = false;

			if (selected)
			{
				if (index < 0)
				{
					value.push(id);
					changed = true;
				}
			}
			else
			{
				if (index >= 0)
				{
					value.splice(index, 1);
					changed = true;
				}
			}

			if (changed)
			{
				return this.setCookieValue(value);
			}
			else
			{
				return value;
			}
		},

		getCookieValue: function()
		{
			var value = XF.Cookie.get(this.cookie);
			if (!value)
			{
				return [];
			}

			var parts = value.split(','),
				length = parts.length;

			for (var i = 0; i < length; i++)
			{
				parts[i] = parseInt(parts[i], 10);
			}

			return parts;
		},

		setCookieValue: function(ids)
		{
			if (!ids.length)
			{
				XF.Cookie.remove(this.cookie);
			}
			else
			{
				ids.sort(function(a, b) { return (a - b); });
				XF.Cookie.set(this.cookie, ids.join(','));
			}

			this._updateCounter(ids.length);

			return ids;
		}
	});

	XF.Element.register('inline-mod', 'XF.InlineMod');
}
(jQuery, window, document);