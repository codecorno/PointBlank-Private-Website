!function($, window, document, _undefined)
{
	"use strict";

	XF.Filter = XF.Element.newHandler({
		options: {
			inputEl: '| .js-filterInput',
			prefixEl: '| .js-filterPrefix',
			clearEl: '| .js-filterClear',
			countEl: '.js-filterCount',
			totalsEl: '.js-displayTotals',
			searchTarget: null,
			searchRow: null,
			searchRowGroup: null,
			searchLimit: '',
			key: '',
			ajax: null,
			noResultsFormat: '<div class="js-filterNoResults">%s</div>'
		},

		storageContainer: 'filter',
		storageCutOff: 3600, // 1 hour
		storageKey: null,

		$input: null,
		$prefix: null,
		$clear: null,
		$count: null,
		$displayTotals: null,
		$search: null,
		$noResults: null,
		$ajaxRows: null,

		updateTimer: null,
		xhr: null,
		xhrFilter: null,

		init: function()
		{
			var $target = this.$target;

			if (this.options.searchTarget)
			{
				this.$search = XF.findRelativeIf(this.options.searchTarget, $target);
			}
			if (!this.$search || !this.$search.length)
			{
				this.$search = $target.next();
			}
			if (!this.$search || !this.$search.length)
			{
				this.$search = $target.findExtended('< .block | .dataList');
			}

			if (this.$search.is('.dataList') && !this.options.searchRow)
			{
				this.options.searchRow = '.dataList-row:not(.dataList-row--header):not(.dataList-row--subSection):not(.dataList-row--footer)';
				this.options.searchRowGroup = '.dataList-rowGroup';
				this.options.searchLimit = '.dataList-cell:not(.dataList-cell--action):not(.dataList-cell--noSearch)';
				this.options.noResultsFormat = '<tbody><tr class="js-filterNoResults dataList-row dataList-row--note dataList-row--noHover is-hidden">'
					+ '<td class="dataList-cell" colspan="50">%s</td></tr></tbody>';
			}

			this.$input = XF.findRelativeIf(this.options.inputEl, $target);
			this.$prefix = XF.findRelativeIf(this.options.prefixEl, $target);
			this.$clear = XF.findRelativeIf(this.options.clearEl, $target);
			this.$count = XF.findRelativeIf(this.options.countEl, $target.closest('form, .block'));
			this.$displayTotals = XF.findRelativeIf(this.options.totalsEl, $target.closest('form', '.block'));

			this.$input.on({
				keyup: XF.proxy(this, 'onKeyUp'),
				keypress: XF.proxy(this, 'onKeyPress'),
				paste: XF.proxy(this, 'onPaste')
			});
			this.$prefix.on('change', XF.proxy(this, 'onPrefixChange'));
			this.$clear.on('click', XF.proxy(this, 'onClearFilter'));

			this.storageKey = this.options.key;
			if (!this.storageKey.length)
			{
				var $form = $target.closest('form');
				if ($form.length)
				{
					this.storageKey = $form.attr('action');
				}
			}

			var existing = this._getStoredValue();
			if (existing)
			{
				this.$input.val(existing.filter);
				this.$prefix.prop('checked', existing.prefix);
			}

			if (this.$input.val().length)
			{
				// this will trigger an update of the stored key
				this.update();
			}

			this._cleanUpStorage();
		},

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
					this.planUpdate();
			}

			if (e.key != 'Enter')
			{
				this.planUpdate();
			}
		},

		onKeyPress: function(e)
		{
			if (e.key == 'Enter')
			{
				e.preventDefault(); // stop enter from submitting
				this.update(); // instant submit
			}
		},

		onPaste: function(e)
		{
			this.planUpdate();
		},

		onPrefixChange: function(e)
		{
			this.update();
		},

		onClearFilter: function(e)
		{
			if (!this.$clear.is('.is-disabled'))
			{
				this.$input.val('');
				this.$prefix.prop('checked', false);
				this.update();
			}
		},

		planUpdate: function()
		{
			if (this.updateTimer)
			{
				clearTimeout(this.updateTimer);
			}
			this.updateTimer = setTimeout(XF.proxy(this, 'update'), 250);
		},

		update: function()
		{
			if (this.updateTimer)
			{
				clearTimeout(this.updateTimer);
			}

			this.filter(this.$input.val(), this.$prefix.is(':checked') ? true : false);
		},

		_getSearchRows: function($container)
		{
			if (!$container)
			{
				$container = this.$search;
			}

			var $rows = $container.find(this.options.searchRow);

			if (this.$noResults)
			{
				$rows = $rows.not(this.$noResults);
			}

			return $rows;
		},

		filter: function(text, prefix)
		{
			this._updateStoredValue(text, prefix);
			this._toggleFilterHide(text.length > 0);

			if (this.options.ajax)
			{
				this._filterAjax(text, prefix);
			}
			else
			{
				var matched = this._applyFilter(this._getSearchRows(), text, prefix);
				this._toggleNoResults(matched == 0);
			}
		},

		_filterAjax: function(text, prefix)
		{
			if (this.xhr)
			{
				this.xhr.abort();
				this.xhr = null;
			}

			if (!text.length)
			{
				this._clearAjaxRows();
				var matched = this._applyFilter(this._getSearchRows(), text, prefix);
				this._toggleNoResults(matched == 0);
			}
			else
			{
				var data = {
					_xfFilter: {
						text: text,
						prefix: prefix ? 1 : 0
					}
				};

				this.xhrFilter = { text: text, prefix: prefix };
				this.xhr = XF.ajax('GET', this.options.ajax, data, XF.proxy(this, '_filterAjaxResponse'));
			}
		},

		_filterAjaxResponse: function(result)
		{
			this.xhr = null;
			this._clearAjaxRows();

			var $result = $($.parseHTML(result.html.content)),
				$rows = $result.find(this.options.searchRow),
				filter = this.xhrFilter,
				$existingRows = this._getSearchRows();

			$existingRows.addClass('is-hidden');
			this._applyRowGroupLimit();
			this._toggleNoResults($rows.length == 0);

			if ($rows.length)
			{
				this._appendRows($rows);
				XF.activate($rows);
				this.$ajaxRows = $rows;

				this._applyFilter($rows, filter.text, filter.prefix);
			}
			else
			{
				XF.layoutChange();
			}

			this.xhrFilter = null;
		},

		_applyFilter: function($rows, text, prefix)
		{
			var searchLimit = this.options.searchLimit,
				matched = 0,
				regex, regexHtml,
				self = this;

			if (text.length)
			{
				regex = new RegExp((prefix ? '^' : '') + '(' + XF.regexQuote(text) + ')', 'i');
				regexHtml = new RegExp((prefix ? '^' : '') + '(' + XF.regexQuote(XF.htmlspecialchars(text)) + ')', 'ig');
			}
			else
			{
				regex = false;
				regexHtml = false;
			}

			$rows.each(function()
			{
				var $this = $(this),
					thisMatched = false,
					$targets = searchLimit ? $this.find(searchLimit) : $this;

				$targets.find('.is-match').each(function()
				{
					var parent = this.parentNode;
					$(this).replaceWith(this.childNodes);
					parent.normalize();
				});

				if (!regex || $this.hasClass('js-filterForceShow'))
				{
					thisMatched = true;
				}
				else
				{
					$targets.each(function()
					{
						if (self._searchFilter(this, regex, regexHtml))
						{
							thisMatched = true;
							// don't short circuit for highlighting purposes
						}
					});
				}

				if (thisMatched)
				{
					$this.removeClass('is-hidden');
					if (!$this.hasClass('js-filterForceShow'))
					{
						matched++;
					}
				}
				else
				{
					$this.addClass('is-hidden');
				}
			});

			this._applyRowGroupLimit(text.length == 0);
			this.updateDisplayTotals(matched);

			XF.layoutChange();

			return matched;
		},

		_applyRowGroupLimit: function(forceShow)
		{
			var searchRowGroup = this.options.searchRowGroup,
				searchRow = this.options.searchRow;

			if (searchRowGroup)
			{
				this.$search.find(searchRowGroup).each(function()
				{
					var $this = $(this),
						$rows = $this.find(searchRow).filter(':not(.is-hidden)');

					if (forceShow || $rows.length)
					{
						$this.removeClass('is-hidden');
					}
					else
					{
						$this.addClass('is-hidden');
					}
				});
			}
		},

		_searchFilter: function(node, regex, regexHtml)
		{
			var matched = false;

			if (node.nodeType == 3)
			{
				if (regex.test(node.data))
				{
					matched = true;
					var newValue = XF.htmlspecialchars(node.data).replace(regexHtml, '<span class="is-match">$1</span>');
					$(node).replaceWith($.parseHTML(newValue));
				}
			}
			else
			{
				var children = node.childNodes;
				for (var i = children.length - 1; i >= 0; i--)
				{
					if (this._searchFilter(children[i], regex, regexHtml))
					{
						matched = true;
					}
				}
			}

			return matched;
		},

		_clearAjaxRows: function()
		{
			if (this.$ajaxRows)
			{
				this.$ajaxRows.remove();
				this.$ajaxRows = null;
			}
		},

		_toggleFilterHide: function(show)
		{
			this.$clear.toggleClass('is-disabled', !show);

			$('.js-filterHide').css('display', show ? 'none' : '');
		},

		_toggleNoResults: function(show)
		{
			if (show)
			{
				// show no results
				this.getNoResultsRow().removeClass('is-hidden');
				this.updateDisplayTotals(0);
			}
			else
			{
				// hide no results
				if (this.$noResults)
				{
					this.$noResults.addClass('is-hidden');
				}
			}
		},

		updateDisplayTotals: function(count)
		{
			this.$count.text(count);

			if (this.$displayTotals.length)
			{
				this.$displayTotals.data('count', count);

				var phrase = '',
					total = this.$displayTotals.data('total');

				if (count < 1)
				{
					// no results
					phrase = XF.phrases.no_items_to_display;
				}
				else if (count == total)
				{
					// all results
					phrase = XF.phrases.showing_all_items;
				}
				else
				{
					// showing of total
					phrase = XF.phrases.showing_x_of_y_items;
				}

				this.$displayTotals.text(XF.stringTranslate(phrase, {
					'{count}': count.toLocaleString(),
					'{total}': total.toLocaleString()
				}));
			}
		},

		getNoResultsRow: function()
		{
			if (this.$noResults)
			{
				return this.$noResults;
			}

			var noResultsHtml = this.options.noResultsFormat.replace('%s', XF.phrase('no_items_matched_your_filter')),
				$noResults = this.$noResults = $($.parseHTML(noResultsHtml)),
				searchRow = '.js-filterNoResults';

			this._appendRows($noResults);

			if ($noResults.is(searchRow))
			{
				this.$noResults = $noResults;
			}
			else
			{
				this.$noResults = $noResults.find(searchRow);
			}

			return this.$noResults;
		},

		_appendRows: function($rows)
		{
			var $lastRow = this.$search.find(this.options.searchRow).last(),
				$lastRowContainer = null,
				searchRowGroup = this.options.searchRowGroup,
				$lastRowGroup = $lastRow.closest(this.options.searchRowGroup + ', tbody'),
				$last;

			if (searchRowGroup)
			{
				$lastRowContainer = $lastRow.closest(searchRowGroup);
				if (!$lastRowContainer.length)
				{
					$lastRowContainer = null;
				}
			}
			if (!$lastRowContainer && $lastRow.is('tr'))
			{
				$lastRowContainer = $lastRow.closest('tbody');
				if (!$lastRowContainer.length)
				{
					$lastRowContainer = null;
				}
			}
			if (!$lastRowContainer)
			{
				$lastRowContainer = $lastRow;
			}

			if ($lastRowContainer.length)
			{
				$rows.insertAfter($lastRowContainer);
			}
			else
			{
				$rows.appendTo(this.$search);
			}
		},

		_getStoredValue: function()
		{
			if (!this.storageKey)
			{
				return null;
			}

			var data = this._readFromStorage();
			if (data[this.storageKey])
			{
				var record = data[this.storageKey],
					tsSaved = record.saved || 0,
					tsNow = Math.floor(new Date().getTime() / 1000);

				if (tsSaved + this.storageCutOff >= tsNow)
				{
					return {
						filter: record.filter || '',
						prefix: record.prefix || false
					};
				}
			}

			return null;
		},

		_updateStoredValue: function(val, prefix)
		{
			if (!this.storageKey)
			{
				return;
			}

			var data = this._readFromStorage();

			if (!val.length)
			{
				if (data[this.storageKey])
				{
					delete data[this.storageKey];
				}
			}
			else
			{
				data[this.storageKey] = {
					filter: val,
					prefix: prefix ? true : false,
					saved: Math.floor(new Date().getTime() / 1000)
				};
			}

			this._writeToStorage(data);
		},

		_cleanUpStorage: function()
		{
			if (!this.storageKey)
			{
				return;
			}

			var data = this._readFromStorage(),
				updated = false,
				tsCutoff = Math.floor(new Date().getTime() / 1000) - this.storageCutOff;

			for (var k in data)
			{
				if (data.hasOwnProperty(k))
				{
					if ((data[k].saved || 0) < tsCutoff)
					{
						delete data[k];
						updated = true;
					}
				}
			}

			if (updated)
			{
				this._writeToStorage(data);
			}
		},

		_readFromStorage: function()
		{
			return XF.LocalStorage.getJson(this.storageContainer);
		},

		_writeToStorage: function(data)
		{
			if ($.isEmptyObject(data))
			{
				XF.LocalStorage.remove(this.storageContainer);
			}
			else
			{
				XF.LocalStorage.setJson(this.storageContainer, data, true);
			}
		}
	});

	XF.PrefixGrabber = XF.Event.newHandler({
		eventNameSpace: 'XFPrefixGrabberClick',

		options: {
			filterElement: '[data-xf-init~=filter]'
		},

		filterHandler: null,

		init: function()
		{
			this.filterHandler = XF.Element.getHandler($(this.options.filterElement), 'filter');
			if (!this.filterHandler instanceof XF.Filter)
			{
				console.warn('PrefixGrabber did not find an element with an XF.Filter handler');
				return false;
			}
		},

		click: function(e)
		{
			if (this.filterHandler.$prefix.is(':checked'))
			{
				var prefix = this.filterHandler.$input.val(),
					href;

				if (prefix.length)
				{
					href = this.$target.attr('href');
					href = href + (href.indexOf('?') == -1 ? '?' : '&') + 'prefix=' + prefix;

					this.$target.attr('href', href);
				}
			}
		}
	});

	XF.Element.register('filter', 'XF.Filter');
	XF.Event.register('click', 'prefix-grabber', 'XF.PrefixGrabber');
}
(jQuery, window, document);