!function($, window, document, _undefined)
{
	"use strict";

	XF.PermissionForm = XF.Element.newHandler({
		options: {
			form: null,
			filterInput: '.js-permissionFilterInput',
			rows: '.js-permission',
			rowLabel: '.formRow-label',
			groups: '.block-body',
			groupHeader: '.block-formSectionHeader',
			headerCollapser: '.collapseTrigger',
			quickSet: '.js-permissionQuickSet',
			permissionType: null
		},

		$form: null,
		$groups: null,
		groups: {},

		$filter: null,
		filterTimer: null,

		init: function()
		{
			var $target = this.$target,
				options = this.options,
				self = this;

			if (options.form)
			{
				$target = XF.findRelativeIf(options.form, $target);
			}
			this.$form = $target;

			if (!options.permissionType)
			{
				console.error('No permission type specified. Must be global or content.');
			}

			var headerSel = options.groupHeader,
				rowSel = options.rows,
				groups = {};

			this.$groups = $target.find(options.groups);
			this.$groups.each(function()
			{
				var $group = $(this),
					groupId = $group.xfUniqueId(),
					isModerator = parseInt($group.data('moderator-permissions'), 10) ? true : false,
					$header = $group.prev(headerSel),
					$rows = $group.find(rowSel);

				groups[groupId] = {
					$group: $group,
					isModerator: isModerator,
					$header: $header,
					$rows: $rows
				};
			});

			this.groups = groups;

			this.$filter = XF.findRelativeIf(options.filterInput, $target);
			this.$filter.on({
				keyup: XF.proxy(this, 'onKeyUp'),
				keypress: XF.proxy(this, 'onKeyPress'),
				paste: XF.proxy(this, 'onPaste')
			});

			// note that this can't use delegation as these are in menus which will get moved out when opened
			$target.find(options.quickSet).click(function()
			{
				self.triggerQuickSet($(this));
			});

			setTimeout(XF.proxy(this, 'applyInitialState'), 0);
		},

		applyInitialState: function()
		{
			var groups = this.groups,
				self = this,
				group,
				$header,
				$group,
				hasValue;

			for (var id in groups)
			{
				if (!groups.hasOwnProperty(id))
				{
					continue;
				}

				group = groups[id];

				$header = group.$header;
				$group = group.$group;
				hasValue = false;

				if (!$header.length || !group.isModerator)
				{
					continue;
				}

				group.$rows.each(function()
				{
					if (self.isRowValueSet($(this)))
					{
						hasValue = true;
						return false;
					}
				});

				this.setGroupExpandedState($group, $header, hasValue);
			}
		},

		setGroupExpandedState: function($group, $header, isExpanded)
		{
			$header.find(this.options.headerCollapser).toggleClass('is-active', isExpanded);
			$group.toggleClass('is-active', isExpanded);

			XF.layoutChange();
		},

		getRowValue: function($row)
		{
			var values = $row.find('input, select').serializeArray(),
				value = values[values.length - 1].value;

			if (value.match(/^[0-9]+$/))
			{
				value = parseInt(value, 10);
			}

			return value;
		},

		isValueSet: function(value)
		{
			if (typeof value == 'number')
			{
				return (value != 0);
			}
			else
			{
				switch (value)
				{
					case 'allow':
					case 'content_allow':
					case 'reset':
					case 'deny':
						return true;

					default:
						return false;
				}
			}
		},

		isRowValueSet: function($row)
		{
			return this.isValueSet(this.getRowValue($row));
		},

		// TODO: this code is lifted almost verbatim from filter.js. Look into reconciling the two.

		onKeyUp: function(e)
		{
			if (e.ctrlKey || e.metaKey)
			{
				return;
			}

			switch (e.key)
			{
				case 'Tab':
				case 'Enter':
				case 'Shift':
				case 'Control':
				case 'Alt':
					break;

				default:
					this.planFilter();
			}

			if (e.key != 'Enter')
			{
				this.planFilter();
			}
		},

		onKeyPress: function(e)
		{
			if (e.key == 'Enter')
			{
				e.preventDefault(); // stop enter from submitting
				this.filter(); // instant submit
			}
		},

		onPaste: function(e)
		{
			this.planFilter();
		},

		planFilter: function()
		{
			if (this.filterTimer)
			{
				clearTimeout(this.filterTimer);
			}
			this.filterTimer = setTimeout(XF.proxy(this, 'filter'), 250);
		},

		filter: function()
		{
			if (this.filterTimer)
			{
				clearTimeout(this.filterTimer);
			}

			var text = this.$filter.val(),
				regex,
				regexHtml,
				groups = this.groups,
				rowLabel = this.options.rowLabel,
				self = this;

			if (text.length)
			{
				regex = new RegExp('(' + XF.regexQuote(text) + ')', 'i');
				regexHtml = new RegExp('(' + XF.regexQuote(XF.htmlspecialchars(text)) + ')', 'i');
			}
			else
			{
				regex = false;
				regexHtml = false;
			}

			var hasAnySkipped = false;

			for (var id in groups)
			{
				if (!groups.hasOwnProperty(id))
				{
					continue;
				}

				var hasGroupMatches = false,
					hasGroupSkipped = false,
					group = groups[id];

				group.$rows.find('.textHighlight').each(function()
				{
					var parent = this.parentNode;
					$(this).replaceWith(this.childNodes);
					parent.normalize();
				});

				group.$rows.each(function()
				{
					var $row = $(this),
						matched = false;

					if (regex)
					{
						var $label = $row.find(rowLabel),
							label = $label.text();
						if (regex.test(label))
						{
							matched = true;

							var newValue = XF.htmlspecialchars(label).replace(
								regexHtml, '<span class="textHighlight textHighlight--attention">$1</span>'
							);
							$label.html(newValue);
						}
					}
					else
					{
						matched = true;
					}

					$row.css('display', matched ? '' : 'none');

					if (matched)
					{
						hasGroupMatches = true;
					}
					else
					{
						hasGroupSkipped = true;
						hasAnySkipped = true;
					}
				});

				if (regex && !hasGroupMatches)
				{
					group.$group.css('display', 'none');
					group.$header.css('display', 'none');
				}
				else
				{
					group.$group.css('display', '');
					group.$header.css('display', '');
					group.$group.find('.formRow--permissionQuickSet').css('display', hasGroupSkipped ? 'none' : '');

					if (regex)
					{
						this.setGroupExpandedState(group.$group, group.$header, true);
					}
				}
			}

			this.$form.find('.js-globalPermissionQuickSet').css('display', hasAnySkipped ? 'none' : '');

			XF.layoutChange();
		},

		triggerQuickSet: function($trigger)
		{
			var value = $trigger.data('value'),
				target = $trigger.data('target'),
				$target = null,
				self = this;

			if (target && target.length)
			{
				$target = $(target);
				if (!$target.length)
				{
					$target = null;
				}
			}

			if (!$target)
			{
				$target = this.$form;
			}

			$target.find(this.options.rows).each(function()
			{
				self.setRowValue($(this), value);
			});
		},

		setRowValue: function($row, value)
		{
			if ($row.data('permission-type') == 'flag')
			{
				$row.find('input[type=radio][value=' + value + ']')
					.prop('checked', true)
					.trigger('click', {triggered: true});
			}
			else
			{
				var intValue = (value == 'allow' || value == 'content_allow') ? -1 : 0;

				$row.find('input[type=radio]').each(function()
				{
					var $radio = $(this);
					if (parseInt($radio.val(), 10) == intValue)
					{
						$radio.prop('checked', true).trigger('click', {triggered: true});
						if ($radio.data('xf-init'))
						{
							$row.find('input[type=text], input[type=number]').val(intValue);
						}
					}
				});
			}
		}
	});

	XF.PermissionChoice = XF.Element.newHandler({
		options: {
			inputSelector: 'input[type="radio"]',
			inputContainerSelector: 'li'
		},

		init: function()
		{
			var self = this;

			this.$target.on('click', this.options.inputSelector, function()
			{
				setTimeout(function() { self.update(); }, 0);
			});

			this.update();
		},

		update: function()
		{
			var inputContainerSelector = this.options.inputContainerSelector;

			this.$target.find(this.options.inputSelector).each(function()
			{
				var $input = $(this),
					$container = $input.closest(inputContainerSelector);
				$container.toggleClass('is-selected', $input.prop('checked'));
			});

		}
	});

	XF.Element.register('permission-form', 'XF.PermissionForm');
	XF.Element.register('permission-choice', 'XF.PermissionChoice');
}
(jQuery, window, document);