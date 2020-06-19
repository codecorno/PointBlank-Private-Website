<?php
// FROM HASH: a3ca311ef58969b9866ceacaafc9c7e4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// #################################################### BADGES ###########################

.badge,
.badgeContainer:after
{
	display: inline-block;
	padding: 2px 4px;
	margin: -2px 0;
	font-size: 80%;
	line-height: 1;
	font-weight: @xf-fontWeightNormal;
	.xf-badge();
}

.badgeContainer
{
	&:after
	{
		content: attr(data-badge);
		display: none;
	}

	&.badgeContainer--visible:after
	{
		display: inline-block;
	}
}

.badge.badge--highlighted,
.badgeContainer.badgeContainer--highlighted:after
{
	display: inline-block;
	.xf-badgeHighlighted();
}';
	return $__finalCompiled;
});