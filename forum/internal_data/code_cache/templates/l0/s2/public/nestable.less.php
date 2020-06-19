<?php
// FROM HASH: cae6259d58d5e399e549a47c19692cbd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.nestable-container
{
	position: relative;

	&:first-child
	{
		padding: 0 @xf-paddingMedium;
	}

	.m-clearFix();
}

.nestable-list
{
	.m-listPlain();

	width: 100%;
	position: relative;

	.nestable-list
	{
		padding-left: 30px;
	}

	.nestable-collapsed
	{
		.nestable-list
		{
			display: none;
		}
	}
}

.nestable-item
{
	.nestable-handle,
	.nestable-content,
	.nestable-button
	{
		border: 1px solid @xf-borderColor;
		border-radius: @xf-borderRadiusSmall;
		padding: @xf-paddingMedium;

		background-color: @xf-contentAltBg;
	}

	.nestable-button
	{
		float: left;

		color: @xf-textColorMuted;
		margin-left: 30px;

		border-radius: 0;
		border-left: none;
		border-right: none;
		outline: none;
	}

	.nestable-handle
	{
		position: absolute;
		margin: 0;
		left: 0;
		top: 0;
		width: 30px;

		text-align: center;
		color: @xf-textColorMuted;
		cursor: move;

		border-right: @xf-borderSize solid @xf-borderColor;
		border-top-right-radius: 0;
		border-bottom-right-radius: 0;

		&.nestable-handle--full
		{
			bottom: 0;
		}
	}

	.nestable-content
	{
		text-overflow: ellipsis;
		overflow: hidden;
		white-space: nowrap;

		color: @xf-textColorMuted;

		margin: @xf-paddingMedium 0;
		margin-left: 30px;

		border-left: none;
		border-top-left-radius: 0;
		border-bottom-left-radius: 0;
	}
}

.nestable-item,
.nestable-empty,
.nestable-placeholder
{
	position: relative;
	vertical-align: middle;
}

.nestable-placeholder,
.nestable-empty
{
	opacity: .6;
	border: 1px solid @xf-borderColor;
	background: @xf-contentAltBg;
	border-radius: @xf-borderRadiusSmall;
	margin: @xf-paddingMedium 0;
}

.nestable-empty
{
	min-height: floor((@xf-fontSizeNormal) * 1.475 + 2 * (@xf-paddingMedium) + 2 * (@xf-borderSize));
}

.nestable-dragel
{
	position: absolute;
	pointer-events: none;
	z-index: 9999;
}';
	return $__finalCompiled;
});