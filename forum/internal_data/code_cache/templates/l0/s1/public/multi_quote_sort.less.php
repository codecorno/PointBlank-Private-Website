<?php
// FROM HASH: 9921c73d7cd022e0f1aa63ac2fac8b44
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.nestable-list
{
	.m-listPlain();
}

.nestable-placeholder,
.nestable-empty
{
	opacity: .6;
	background: @xf-contentAltBg;
	border: @xf-borderSize solid @xf-borderColor;
	border-bottom: none;
	margin-left: 0;
}

.nestable-item,
.nestable-empty,
.nestable-placeholder
{
	position: relative;
	vertical-align: middle;
}

.nestable-dragel
{
	position: absolute;
	pointer-events: none;
	z-index: 9999;
	background: @xf-contentBg;
	opacity: 0.9;
}

.nestable-handle
{
	position: absolute;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	z-index: 10000;
	cursor: move;

	display: flex;
	align-items: center;
	justify-content: center;
}';
	return $__finalCompiled;
});