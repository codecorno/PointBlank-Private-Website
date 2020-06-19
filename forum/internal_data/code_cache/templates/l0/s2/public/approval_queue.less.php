<?php
// FROM HASH: 02d17d9ca68414712a4823dc78cc98d8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.approvalQueue
{
	.block-container.approvalQueue-item--approve
	{
		.message,
		.message-cell--user,
		.message-cell--extra
		{
			background: @xf-inlineModHighlightColor;
		}

		.message .message-userArrow:after
		{
			border-right-color: @xf-inlineModHighlightColor;
		}
	}

	.block-container.approvalQueue-item--delete
	{
		.message-cell--user,
		.message-cell--main
		{
			opacity: 0.25;
		}
	}

	.block-container.approvalQueue-item--spam
	{
		border-color: red;

		.message-cell--user,
		.message-cell--main
		{
			opacity: 0.25;
		}
	}
}';
	return $__finalCompiled;
});