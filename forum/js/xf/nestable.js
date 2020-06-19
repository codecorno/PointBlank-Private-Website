!function($, window, document, _undefined)
{
	"use strict";

	// ################################## NESTABLE HANDLER ###########################################

	XF.Nestable = XF.Element.newHandler({

		options: {
			rootClass: 'nestable-container',
			listClass: 'nestable-list',
			itemClass: 'nestable-item',
			handleClass: 'nestable-handle',
			dragClass: 'nestable-dragel',
			collapsedClass: 'nestable-collapsed',
			placeClass: 'nestable-placeholder',
			noDragClass: 'nestable-nodrag',
			emptyClass: 'nestable-empty',
			classSuffix: '',

			maxDepth: 10000,
			groupId: null,
			parentId: null,
			valueInput: 'input[type="hidden"]',
			valueFunction: 'asNestedSet'
		},

		$valueInput: null,

		init: function()
		{
			if (this.options.classSuffix)
			{
				var t = this;
				$.each(this.options, function(index, value)
				{
					if (index.indexOf('Class') >= 0)
					{
						t.options[index] = value + t.options.classSuffix;
					}
				});
			}

			this.$valueInput = this.$target.find(this.options.valueInput);
			if (!this.$valueInput.length)
			{
				console.error('No value input found matching selector %s', this.options.valueInput);
				return false;
			}

			if (this.options.groupId === null)
			{
				this.options.groupId = 0;
			}
			if (this.options.parentId === null)
			{
				this.options.parentId = 0;
			}

			var config = {
				rootClass: this.options.rootClass,
				listClass: this.options.listClass,
				itemClass: this.options.itemClass,
				handleClass: this.options.handleClass,
				dragClass: this.options.dragClass,
				collapsedClass: this.options.collapsedClass,
				placeClass: this.options.placeClass,
				noDragClass: this.options.noDragClass,
				emptyClass: this.options.emptyClass,

				expandBtnHTML: '<button type="button" class="nestable-button" data-action="expand">	<i class="far fa-plus-square" aria-hidden="true"></i></button>',
				collapseBtnHTML: '<button type="button" class="nestable-button" data-action="collapse"><i class="far fa-minus-square" aria-hidden="true"></i></button>',

				maxDepth: this.options.maxDepth,
				group: this.options.groupId,
				parentID: this.options.parentId
			};
			this.$target.nestable(config);

			this.$target.on('change', XF.proxy(this, 'change'));
			this.change();
		},

		change: function(e)
		{
			this.$valueInput.val(JSON.stringify(this.$target.nestable(this.options.valueFunction)));
		}
	});

	XF.Element.register('nestable', 'XF.Nestable');
}
(jQuery, window, document);