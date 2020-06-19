<?php
// FROM HASH: 70becb1328c2bf334fa9654ea98ce22f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// ################## IGNORED USERS / CONTENT ##########################

.is-ignored
{
	display: none !important;
}

.showIgnoredLink
{
	&.is-hidden
	{
		display: none !important;
	}
}

.block-outer .showIgnoredLink,
.showIgnoredLink.showIgnoredLink--subtle
{
	font-size: @xf-fontSizeSmall;
	color: @xf-textColorMuted;

	&:hover
	{
		color: @xf-textColorDimmed;
	}
}';
	return $__finalCompiled;
});