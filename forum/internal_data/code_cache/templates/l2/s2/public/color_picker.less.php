<?php
// FROM HASH: be912c48b6722f4fae2ca7fc8f8a2a6a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.colorPickerBox
{
	display: inline-block;
	position: relative;
	//width: 1.8em;
	width: 56px;
	height: 1.3em;
	border: 1px dashed @xf-borderColorHeavy;
	border-radius: @xf-borderRadiusSmall;
	background: @xf-contentBg;
	cursor: pointer;

	.colorPickerBox-sample
	{
		display: none;
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
	}

	&.is-unknown
	{
		&:after
		{
			content: \'?\';
			display: block;
			line-height: 1;
			font-size: 120%;
			font-weight: @xf-fontWeightHeavy;
			color: @xf-textColorMuted;
		}
	}

	&.is-active
	{
		border-style: solid;
		.m-checkerboardBackground();

		&:after
		{
			display: none;
		}

		.colorPickerBox-sample
		{
			display: block;
			background: red;
		}
	}
}

@_colorPicker-borderColor: @xf-borderColorHeavy;
@_colorPicker-arrowSize: 6px;
@_colorPicker-mainHeight: 250px + (@_colorPicker-arrowSize) + ((@xf-paddingLarge) * 2);

/* XF-RTL:disable */

// HSV gradients from Spectrum (https://github.com/bgrins/spectrum), MIT licensed
.colorPicker-sliders
{
	height: @_colorPicker-mainHeight;
	padding: @xf-paddingLarge;
	.m-clearFix();
}

.colorPicker-colorGrad
{
	width: 220px;
	height: 220px;
}

.colorPicker-colorGrad-color
{
	position: relative;
	width: 100%;
	height: 100%;
	border: 1px solid @_colorPicker-borderColor;
	background: red;
}

.colorPicker-colorGrad-sat,
.colorPicker-colorGrad-val
{
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
}

.colorPicker-colorGrad-sat
{
	background-image: linear-gradient(to right, #fff, rgba(204, 154, 129, 0));
}

.colorPicker-colorGrad-val
{
	background-image: linear-gradient(to top, #000, rgba(204, 154, 129, 0));
}

.colorPicker-colorGrad-indicator
{
	position: absolute;
	top: 0;
	left: 0;
	width: 8px;
	height: 8px;
	margin-left: -4px;
	margin-top: -4px;
	border-radius: 50%;
	border: 1px solid #666;
	box-shadow: 0 0 0 1px white;
	pointer-events: none;
}

.colorPicker-hue
{
	float: right;
	margin-left: 10px;
	height: 250px;
	width: 20px;
}

.colorPicker-hue-bar
{
	position: relative;
	width: 100%;
	height: 100%;
	background: linear-gradient(to bottom, #ff0000 0%, #ffff00 17%, #00ff00 33%, #00ffff 50%, #0000ff 67%, #ff00ff 83%, #ff0000 100%);
	border: 1px solid @_colorPicker-borderColor;
}

.colorPicker-hue-indicator
{
	position: absolute;
	height: 1px;
	background: #666;
	top: 0;
	left: -1px;
	right: -1px;
	z-index: @zIndex-1;
	pointer-events: none;

	&:before
	{
		content: \'\';
		position: absolute;
		left: -@_colorPicker-arrowSize;
		top: -(@_colorPicker-arrowSize - 1px);
		.m-triangleRight(#666, @_colorPicker-arrowSize);
	}

	&:after
	{
		content: \'\';
		position: absolute;
		right: -@_colorPicker-arrowSize;
		top: -(@_colorPicker-arrowSize - 1px);
		.m-triangleLeft(#666, @_colorPicker-arrowSize);
	}
}

.colorPicker-alpha
{
	margin-top: 10px;
	height: 20px;
	width: 220px;
	background-clip: content-box;
	.m-checkerboardBackground();
}

.colorPicker-alpha-bar
{
	position: relative;
	width: 100%;
	height: 100%;
	border: 1px solid @_colorPicker-borderColor;
}

.colorPicker-alpha-indicator
{
	position: absolute;
	width: 1px;
	background: #666;
	left: 0;
	top: -1px;
	bottom: -1px;
	z-index: @zIndex-1;
	pointer-events: none;

	&:before
	{
		content: \'\';
		position: absolute;
		left: -@_colorPicker-arrowSize;
		top: -(@_colorPicker-arrowSize - 1px);
		.m-triangleDown(#666, @_colorPicker-arrowSize);
	}

	&:after
	{
		content: \'\';
		position: absolute;
		left: -@_colorPicker-arrowSize;
		bottom: -(@_colorPicker-arrowSize - 1px);
		.m-triangleUp(#666, @_colorPicker-arrowSize);
	}
}

/* XF-RTL:enable */

.colorPicker-inputs
{
	.m-clearFix();
	padding: @xf-paddingMedium @xf-paddingLarge;
}

.colorPicker-preview
{
	float: left;
	position: relative;
	width: 40px;
	height: 70px;
	border: 1px solid @_colorPicker-borderColor;
	background-clip: content-box;
	.m-checkerboardBackground()
}

.colorPicker-preview-current
{
	position: absolute;
	left: 0;
	right: 0;
	top: 0;
	bottom: 50%;
}

.colorPicker-preview-original
{
	position: absolute;
	left: 0;
	right: 0;
	top: 50%;
	bottom: 0;
}

.colorPicker-inputContainer
{
	margin-left: 46px;
}

.colorPicker-saveContainer
{
	margin-left: 46px;
	margin-top: 6px;
	text-align: right;
}

.colorPicker-propertyContainer
{
	height: @_colorPicker-mainHeight;
	overflow: auto;
	border-bottom: 1px solid @xf-borderColor;
}

.colorPicker-property
{
	position: relative;
	padding: @xf-paddingSmall @xf-paddingMedium;
	padding-left: 2.9em;
	cursor: pointer;

	&.is-active
	{
		background: @xf-contentHighlightBg;
		font-weight: @xf-fontWeightHeavy;
		color: @xf-textColorEmphasized;
	}

	&:hover
	{
		background: @xf-contentHighlightBg;
	}
}

.colorPicker-propName
{
	display: block;
	color: fade(@xf-textColorMuted, 75%);
	font-size: @xf-fontSizeSmallest;
}

.colorPicker-property-preview
{
	position: absolute;
	top: .2em;
	left: .3em;
	bottom: .2em;
	width: 2.4em;
	border: 1px solid @xf-borderColorHeavy;

	.colorPicker-property.is-unknown &
	{
		border-style: dashed;
		display: flex;
		align-items: center;

		&:after
		{
			content: \'?\';
			display: block;
			width: 100%;
			text-align: center;
			line-height: 1;
			font-size: 1.6em;
			font-weight: @xf-fontWeightHeavy;
			color: @xf-textColorMuted;
		}
	}
}

.menu.menu--colorPicker
{
	width: 294px;
	max-width: 100%;
}

.inputGroup.inputGroup--colorSmall
{
	width: 180px;
}

.m-checkerboardBackground(@color: #D8D8D8; @size: 12px)
{
	background-image: linear-gradient(45deg, @color 25%, transparent 25%),
		linear-gradient(-45deg, @color 25%, transparent 25%),
		linear-gradient(45deg, transparent 75%, @color 75%),
		linear-gradient(-45deg, transparent 75%, @color 75%);
	background-size: @size @size;
	background-position: 0 0, 0 ((@size) / 2), ((@size) / 2) -((@size) / 2), -((@size) / 2) 0px;
}';
	return $__finalCompiled;
});