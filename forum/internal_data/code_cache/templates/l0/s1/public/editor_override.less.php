<?php
// FROM HASH: 4ce0ba67e290a9a0f141ae1317070303
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '@font-family: @xf-fontFamilyUi;
@border-radius: @xf-borderRadiusSmall;
@arrow-size: 4px;
@transition-timing: @xf-animationSpeed ease 0s;

@ui-color: @xf-editorToolbarActiveColor;
@ui-text: @xf-textColor;
@ui-hover-color: fade(@xf-textColor, 6%);
@ui-focused-color: fade(@xf-textColor, 12%);
@ui-disabled-color: mix(@xf-textColor, @xf-editorToolbarBg, 20%);
@ui-bg: @xf-editorToolbarBg;
@ui-font-size: @xf-fontSizeNormal;
@ui-border-color: @xf-editorToolbarBorderColor;
@ui-border-top: @xf-borderSizeFeature solid @xf-editorToolbarBorderColor;

@input-label-color: @xf-textColorMuted;

@tooltip-bg: xf-default(@xf-tooltip--background-color, black);
@tooltip-text: xf-default(@xf-tooltip--color, white);
@tooltip-font-size: @xf-fontSizeSmaller;
@tooltip-line-height: ((@xf-fontSizeSmaller) * 2);

@editor-padding: @xf-paddingLarge;
@editor-bg: xf-default(@xf-input--background-color, white);
@editor-text: xf-default(@xf-input--color, black);
@editor-shadow-level: 0;
@editor-border: @xf-borderSize solid @xf-borderColorHeavy;

@selection-bg: @xf-editorSelectedBg;
@selection-text: @xf-editorSelectedColor;

@btn-width: 32px;
@btn-height: 32px;
@btn-margin: 1px;
@btn-font-size: 16px;
@btn-text: @xf-editorToolbarColor;
@btn-hover-text: @xf-textColor;
@btn-hover-bg: fade(@xf-textColor, 6%);

@separator-color: @xf-borderColorLight;';
	return $__finalCompiled;
});