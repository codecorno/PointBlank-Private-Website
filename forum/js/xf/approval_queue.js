/** @param {jQuery} $ jQuery Object */
!function($, window, document, _undefined)
{
	"use strict";

	XF.ApprovalControlClick = XF.Event.newHandler(
	{
		eventType: 'click',
		eventNameSpace: 'XFApprovalControlClick',
		options: {
			container: '.approvalQueue-item'
		},

		$item: null,
		value: null,

		init: function()
		{
			this.$item = this.$target.closest(this.options.container);
			this.value = this.$target.val();
		},

		click: function(e)
		{
			this.$item
				.toggleClass('approvalQueue-item--approve', this.value == 'approve')
				.toggleClass('approvalQueue-item--delete', this.value == 'delete' || this.value == 'reject')
				.toggleClass('approvalQueue-item--spam', this.value == 'spam_clean');
		}
	});

	XF.Event.register('click', 'approval-control', 'XF.ApprovalControlClick');
}
(jQuery, window, document);