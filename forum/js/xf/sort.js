!function ($, window, document, _undefined) {
	"use strict";

	// ################################## NESTABLE HANDLER ###########################################

	XF.ListSorter = XF.Element.newHandler({

		options: {
			dragParent: null,
			dragHandle: null,
			undraggable: '.is-undraggable'
		},

		dragula: null,

		init: function ()
		{
			this.dragula = dragula(this.options.dragParent ? this.$target.find(this.options.dragParent).get() : [this.$target[0]],
			{
				moves: XF.proxy(this, 'isMoveable'),
				accepts: XF.proxy(this, 'isValidTarget')
			});
		},

		isMoveable: function (el, source, handle, sibling) {
			var handleIs = this.options.dragHandle,
				undraggableIs = this.options.undraggable;

			if (handleIs)
			{
				if (!$(handle).closest(handleIs).length)
				{
					return false;
				}
			}
			if (undraggableIs)
			{
				if ($(el).closest(undraggableIs).length)
				{
					return false;
				}
			}

			return true;
		},

		isValidTarget: function (el, target, source, sibling) {
			var $sibling;

			if (!sibling)
			{
				$sibling = this.$target.children().last();

			}
			else
			{
				$sibling = $(sibling).prev();
			}

			while ($sibling.length)
			{
				if ($sibling.is('.js-blockDragafter'))
				{
					return false;
				}

				$sibling = $sibling.prev();
			}

			return true;
		}
	});

	XF.Element.register('list-sorter', 'XF.ListSorter');
}
(jQuery, window, document);