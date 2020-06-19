!function($, window, document, _undefined)
{
	"use strict";

	XF.EditorManager = XF.Element.newHandler({
		options: {
			dragListClass: '.js-dragList',
			commandTrayClass: '.js-dragList-commandTray'
		},

		$lists: null,
		trayElements: [],
		listElements: [],
		isScrollable: true,
		dragula: null,

		init: function()
		{
			this.$lists = this.$target.find(this.options.dragListClass);
			this.$lists.each(XF.proxy(this, 'prepareList'));

			this.initDragula();
		},

		prepareList: function(i, list)
		{
			if ($(list).is(this.options.commandTrayClass))
			{
				this.trayElements.push(list);
			}
			else
			{
				this.listElements.push(list);
			}

			this.rebuildValueCache(list);
		},

		initDragula: function()
		{
			// the following is code to workaround an issue which makes the
			// page scroll while dragging elements.
			var t = this;
			document.addEventListener('touchmove', function(e)
			{
				if (!t.isScrollable)
				{
					e.preventDefault();
				}
			}, { passive:false });

			var lists = this.listElements;

			var i;
			for (i in this.trayElements)
			{
				lists.unshift(this.trayElements[i]);
			}

			this.dragula = dragula(lists, {
				direction: 'horizontal',
				removeOnSpill: true,
				copy: function (el, source)
				{
					return t.isTrayElement(source);
				},
				accepts: function (el, target)
				{
					return !t.isTrayElement(target);
				},
				moves: function (el, source, handle, sibling)
				{
					return !$(el).hasClass('toolbar-addDropdown') && !$(el).hasClass('fr-separator');
				}
			});

			this.dragula.on('drag', XF.proxy(this, 'drag'));
			this.dragula.on('dragend', XF.proxy(this, 'dragend'));
			this.dragula.on('drop', XF.proxy(this, 'drop'));
			this.dragula.on('cancel', XF.proxy(this, 'cancel'));
			this.dragula.on('remove', XF.proxy(this, 'remove'));
			this.dragula.on('over', XF.proxy(this, 'over'));
			this.dragula.on('out', XF.proxy(this, 'out'));
		},

		drag: function(el, source)
		{
			this.isScrollable = false;

			var $el = $(el),
				$source = $(source);

			if ($el.hasClass('toolbar-separator') && !$source.hasClass('js-dragList-commandTray'))
			{
				$el.next('.fr-separator').remove();
			}
		},

		dragend: function(el)
		{
			this.isScrollable = true;
			$('.js-dropTarget').remove();
		},

		drop: function(el, target, source, sibling)
		{
			var $el = $(el),
				$target = $(target),
				cmd = $el.data('cmd');

			// prevent adding duplicate buttons (unless it's a separator)
			if ($target.find('[data-cmd="' + cmd + '"]').length > 1
				&& !$el.hasClass('toolbar-separator')
			)
			{
				$el.remove();
				XF.flashMessage(XF.phrase('buttons_menus_may_not_be_duplicated'), 1500);
			}

			if ($el.hasClass('toolbar-separator'))
			{
				this.appendSeparator($el);
			}
			else
			{
				if ($el.next().is('.fr-separator'))
				{
					$el.insertAfter($el.next());
				}
			}

			// if dragged from our dropdown tray, remove the menu click attr
			if ($el.attr('data-xf-click') === 'menu')
			{
				$el.attr('data-xf-click', null);
			}

			if (!this.isTrayElement(source))
			{
				this.rebuildValueCache(source);
			}
			if (!this.isTrayElement(target))
			{
				this.rebuildValueCache(target);
			}
		},

		cancel: function(el, container, source)
		{
			var $el = $(el),
				$source = $(source);

			if ($el.hasClass('toolbar-separator') && !$source.hasClass('js-dragList-commandTray'))
			{
				this.appendSeparator($el);
			}
		},

		remove: function(el, container, source)
		{
			if (!this.isTrayElement(source))
			{
				XF.flashMessage(XF.phrase('button_removed'), 1500);
				this.rebuildValueCache(source);
			}
		},

		over: function(el, container, source)
		{
		},

		out: function(el, container, source)
		{
		},

		rebuildValueCache: function(list)
		{
			var $list = $(list),
				$cache = $list.find('.js-dragListValue'),
				value = [];

			if (!$cache.length)
			{
				return;
			}

			$list.children().each(function(i, cmd)
			{
				var $cmd = $(cmd);

				if (!$cmd.data('cmd'))
				{
					return;
				}

				value.push($cmd.data('cmd'));
			});

			$cache.val(JSON.stringify(value));
		},

		appendSeparator: function($el)
		{
			var $sep = $('<div />')
				.addClass('fr-separator')
				.addClass('fr' + $el.data('cmd'));

			$sep.insertAfter($el);
		},

		isTrayElement: function(el)
		{
			return (this.trayElements.indexOf(el) !== -1);
		}
	});

	XF.Element.register('editor-manager', 'XF.EditorManager');
}
(jQuery, window, document);