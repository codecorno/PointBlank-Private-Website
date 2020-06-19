<?php
// FROM HASH: e1d61b1a183a7105311d35ec7a06d58d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.searchResult
{
	display: table;
	table-layout: fixed;
	width: 100%;
}

.searchResult-icon
{
	display: table-cell;
	vertical-align: top;
	width: 54px;
	white-space: nowrap;
	word-wrap: normal;

	img,
	.avatar
	{
		max-width: 100%;
	}
}

.searchResult-main
{
	display: table-cell;
	vertical-align: middle;
	padding-left: @xf-paddingLarge;

	&:before
	{
		content: \'\';
		display: block;
		margin-top: -.25em;
	}
}

.searchResult-title
{
	font-size: @xf-fontSizeLarge;
}';
	return $__finalCompiled;
});