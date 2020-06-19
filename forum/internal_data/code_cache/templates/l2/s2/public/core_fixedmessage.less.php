<?php
// FROM HASH: 403ad376a92a172d61fa8ffe0e850775
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ############################ FIXED MESSAGE BAR ################

.fixedMessageBar
{
	.xf-fixedMessage();
	.m-transitionFadeDown();
	.m-clearFix();
}

.fixedMessageBar-inner
{
	display: flex;
	align-items: center;
	justify-content: space-between;
}

.fixedMessageBar-message
{
	order: 1;
}

.fixedMessageBar-close
{
	float: right;
	margin-left: 1em;
	order: 2;
	color: inherit;

	&:before
	{
		.m-faBase();
		.m-faContent(@fa-var-times, .69em);
	}

	&:hover
	{
		text-decoration: none;
		color: xf-intensify(@xf-fixedMessage--color, 10%);
	}
}';
	return $__finalCompiled;
});