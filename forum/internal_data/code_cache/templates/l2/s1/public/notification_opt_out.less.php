<?php
// FROM HASH: 14b194cb4898b55cc9cb30811239aa3e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.notificationChoices
{
	.m-listPlain();
	.m-clearFix();

	> li
	{
		float: left;
		margin-left: 1em;
		padding: 0 @xf-paddingMedium;

		&:first-child
		{
			margin-left: 0;
		}
	}

	label
	{
		display: block;
		text-align: center;
	}
}';
	return $__finalCompiled;
});