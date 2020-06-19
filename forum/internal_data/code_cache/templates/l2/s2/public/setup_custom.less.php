<?php
// FROM HASH: e699ae7fff20bf30043c399d89c9f29a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// This should be used for additional LESS setup code (that does not output anything).
// setup.less customizations should be avoided when possible.

.m-dotControls()
{
	.xf-nlDotControl();
}
.m-dotControlsHover()
{
	.xf-nlDotControlHover();
}
.m-stripElement()
{
	background: transparent none;
	padding: 0;
	border: none;
	box-shadow: none !important;
}
.m-buttonReset()
{
    .m-stripElement();
    color: inherit;
    text-transform: capitalize;
    font-weight: normal;
    padding: 0;
    display: inline;
}
.m-lineHeightNormal()
{
	line-height: normal;
}
.m-lineHeightDefault()
{
	line-height: 1.4;
}
.m-flexAlignCenter()
{
	display: flex;
	align-items: center;
}

.m-fullTriangleLeft(@color; @size; @offset: 50%) {
	position: relative;
	
	&:before {
		content: "";
		position: absolute;
		left: -@size;
		top: @offset;
		transform: translateY(-50%);
	}
	&:before {
		.m-triangleLeft(@color, @size)
	}
}
.m-fullTriangleUp(@color; @size; @offset: 50%) {
	position: relative;
	
	&:before {
		content: "";
		position: absolute;
		top: -@size;
		margin: 0 auto;
		left: @offset;
		transform: translateX(-50%);
	}
	&:before {
		.m-triangleUp(@color, @size)
	}
}
.m-fullTriangleRight(@color; @size; @offset: 50%) {
	position: relative;
	
	&:after {
		content: "";
		position: absolute;
		right: -@size;
		top: @offset;
		transform: translateY(-50%);
	}
	&:after {
		.m-triangleRight(@color, @size)
	}
}
.m-fullTriangleDown(@color; @size; @offset: 50%) {
	position: relative;
	
	&:before {
		content: "";
		position: absolute;
		bottom: -@size;
		margin: 0 auto;
		left: @offset;
		transform: translateX(-50%);
	}
	&:before {
		.m-triangleDown(@color, @size)
	}
}

.m-dropShadow(@x: @xf-nlBoxShadowX; @y: @xf-nlBoxShadowY; @blur: @xf-nlBoxShadowBlur; @spread: @xf-nlBoxShadowSpread; @alpha: @xf-nlBoxShadowAlpha)
{
	box-shadow: @x @y @blur @spread fade(@xf-nlBoxShadowColor, (@alpha * 100));
}

/* Footer width here */

/* Gradients */

';
	if (($__templater->func('property', array('gradientTop', ), false) != null) AND ($__templater->func('property', array('gradientBottom', ), false) != null)) {
		$__finalCompiled .= '
.m-primaryGradient(@direction: to bottom; @startColor: @xf-gradientTop; @stopColor: @xf-gradientBottom;) {
	background: linear-gradient(@direction, @startColor, @stopColor);
}
';
	}
	$__finalCompiled .= '
';
	if (($__templater->func('property', array('secondaryGradientTop', ), false) != null) AND ($__templater->func('property', array('secondaryGradientBottom', ), false) != null)) {
		$__finalCompiled .= '
.m-secondaryGradient(@direction: to bottom; @startColor: @xf-secondaryGradientTop; @stopColor: @xf-secondaryGradientBottom;) {
	background: linear-gradient(@direction, @startColor, @stopColor);
}
';
	}
	$__finalCompiled .= '
';
	if (($__templater->func('property', array('lightGradientTop', ), false) != null) AND ($__templater->func('property', array('lightGradientBottom', ), false) != null)) {
		$__finalCompiled .= '
.m-lightGradient(@direction: to bottom; @startColor: @xf-lightGradientTop; @stopColor: @xf-lightGradientBottom;) {
	background: linear-gradient(@direction, @startColor, @stopColor);
}
';
	}
	$__finalCompiled .= '
';
	if (($__templater->func('property', array('darkGradientTop', ), false) != null) AND ($__templater->func('property', array('darkGradientBottom', ), false) != null)) {
		$__finalCompiled .= '
.m-darkGradient(@direction: to bottom; @startColor: @xf-darkGradientTop; @stopColor: @xf-darkGradientBottom;) {
	background: linear-gradient(@direction, @startColor, @stopColor);
}
';
	}
	return $__finalCompiled;
});