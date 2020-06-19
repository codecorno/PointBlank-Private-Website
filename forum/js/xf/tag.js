!function($, window, document, _undefined)
{
	"use strict";

	// ################################## TOKEN INPUT HANDLER ###########################################

	XF.Tagger = XF.Element.newHandler({

		options: {
			tagList: null
		},

		$tagContainer: null,
		$inlineEdit: null,

		init: function()
		{
			if (!this.options.tagList)
			{
				return;
			}

			this.$tagContainer = $('dl.tagList.' + this.options.tagList).find('.js-tagList');

			if (this.$tagContainer === null)
			{
				console.warn("No tag container was found for %s", this.options.tagList);
				return;
			}

			this.$target.on(
			{
				'ajax-submit:before': XF.proxy(this, 'beforeSubmit'),
				'ajax-submit:response': XF.proxy(this, 'afterSubmit')
			});

			this.$inlineEdit = $('<input type="hidden" name="_xfInlineEdit" value="1" />');
		},

		beforeSubmit: function()
		{
			this.$target.append(this.$inlineEdit);
		},

		afterSubmit: function(e, data, submitter)
		{
			if (data.errors || data.exception)
			{
				return;
			}

			if (data.message)
			{
				XF.flashMessage(data.message, 3000);
			}

			if (data.hasOwnProperty('html'))
			{
				this.updateTagList($(data.html.content).find('.js-tagList'));
			}

			XF.hideParentOverlay(this.$target);
		},

		updateTagList: function(newContent)
		{
			this.$tagContainer.html($(newContent).html());
		}
	});

	XF.Element.register('tagger', 'XF.Tagger');
}
(jQuery, window, document);