<?php
// FROM HASH: 2dd8dbba18f92b5acf2896541f6783ee
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.templateModContents
{
	border: 1px solid #ccc;
	border-radius: 5px;
	background: #f2f2f2;
	max-width: 100%;
	max-height: 250px;
	min-height: 15px;
	overflow: auto;
	white-space: pre;
	word-wrap: normal;
	font-family: @xf-fontFamilyCode;
	direction: ltr;
	margin: 0;
	padding: 4px;
}

.templateModApply
{
	&.is-active
	{
		font-weight: @xf-fontWeightHeavy;
		font-size: 120%;
	}

	&.templateModApply--ok { color: green; }
	&.templateModApply--notFound { color: grey;}
	&.templateModApply--error { color: red; }
}';
	return $__finalCompiled;
});