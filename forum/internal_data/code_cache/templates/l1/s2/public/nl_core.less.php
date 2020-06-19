<?php
// FROM HASH: b33605936f95748481b27af328dad574
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '/* core_setup.less */

body
{
	overflow-x: hidden;
}

/* core_avatar.less */

.avatar
{
	.xf-nlAvatar();
}

/* core_blocklink.less */

.blockLink
{
	.xf-blockLinkExtended();

	&.is-selected
	{
		.xf-blockLinkSelected();
		.xf-blockLinkSelectedExtended();
	}

	&:hover
	{
		.xf-blockLinkSelected();
		.xf-blockLinkSelectedExtended();
	}
}

/* core_blockmessage.less */

.blockMessage
{
	&.blockMessage--none
	{
	}
}

/* core_datalist.less */

.dataList-table
{
	background: @xf-nlStructItemListContainer--background-color;
}
.dataList-row
{
	.xf-nlDataListRow();

	.dataListAltRows &:nth-of-type(even)
	{
		.xf-nlDataListRowAlternate();
	}
	&:hover:not(.dataList-row--noHover):not(.dataList-row--header):not(.is-spHovered),
	.is-spActive &.is-spChecked
	{
		.xf-nlDataListRowHover();
	}

	.is-spActive &.is-spHovered
	{
		.xf-nlDataListRowHover();
	}
}
.dataList-cell {
	&.dataList-cell--alt,
	&.dataList-cell--action {
		.xf-nlDataListActionItem();
	}

	&.dataList-cell--action,
	&.dataList-cell--link {
		cursor: pointer;
		text-decoration: none;

		&.dataList-cell--alt:hover,
		&.dataList-cell--action:hover {
			.xf-nlDataListActionItemHover();
		}
	}
}

/* core_contentrow.less */

.contentRow-lesser
{
	color: @xf-textColorMuted;
}
.contentRow-header,
.contentRow-title {
	.xf-nlContentRowTitle();
	
	a {
		color: inherit;
		
		&:hover {
			color: @xf-linkHoverColor;
		}
	}
}
.contentRow-extra.contentRow-extra--large {
    font-size: inherit;
    line-height: normal;
}

/* core_input.less */

.inputGroup
{
	&.inputGroup--joined
	{
		.inputGroup-text
		{
			.xf-nlButtonLink();
			
			&:hover,
			&:active,
			&:focus {
				.xf-nlButtonLinkHover();
			}
		}
	}
}

.inputNumber-button
{
	.inputGroup.inputGroup--joined &
	{
		&:hover,
		&:active,
		&:focus
		{
			.xf-nlButtonLinkHover();
		}
	}
}

@_input-elementSpacer: 10px;
@_input-checkBoxSpacer: 1.2em;

.inputChoices
{

	> .inputChoices-choice
	{
		padding-left: @_input-checkBoxSpacer;
		margin-right: @_input-elementSpacer;
	}
}

@controlColor: @xf-nlControlColor;
@controlColor--hover: @xf-nlControlColorActive;

// Don\'t apply to off-canvas controls
.p-body-main input[type="checkbox"],
.p-body-main input[type="radio"] {
	color: @controlColor;
	
	~ * {
		transition: .2s color;
	}
	~ span.iconic-label {
		color: @xf-nlControlLabelColor;
	}
	&:checked ~ span.iconic-label {
		color: @xf-nlControlLabelColorActive;
	}
	&:checked + i {
		color: @controlColor--hover;
	}
}
.formRow,
.inputGroup,
.inputChoices,
.block-footer,
.dataList-cell,
.message-cell--extra,
.structItem-extraInfo
{
	.has-pointerControls & .iconic:hover {
		cursor: pointer;
	}
}
.iconic > input[type=checkbox] + i:after {
    font-weight: @xf-nlCheckboxStyle;
}

/* core_pagenav.less */

.pageNav-main
{
	border-spacing: 1px;
}

/* core_labels.less */

.label
{
	&.label--primary
	{
		.xf-nlLabelPrimary();
	}

	&.label--accent
	{
		.xf-nlLabelAccent();
	}
	&.label--subtle
	{
		.xf-nlLabelSubtle();
	}
}
.use-label--accent .label.label--primary
{
		.xf-nlLabelAccent();
}

/* core_menu.less */

@_menu-paddingV: @xf-nlMenuRowPaddingV;
@_menu-paddingH: @xf-nlMenuRowPaddingH;

.menu
{
	.xf-nlMenuOverlayWrapper();

	&.menu--structural
	{
		&.menu--left
		{
			border-top-left-radius: @xf-menuBorderRadius;
		}
		&.menu--right
		{
			border-top-right-radius: @xf-menuBorderRadius;
		}
	}
}
.menu-content
{
	.menu--structural.menu--left &
	{
		border-top-left-radius: @xf-menuBorderRadius;
	}
	.menu--structural.menu--right &
	{
		border-top-right-radius: @xf-menuBorderRadius;
	}
}

.menu-row
{
	.xf-nlMenuRow();

	&.menu-row--alt
	{
		.xf-nlMenuRowAlt();
	}

	&.menu-row--highlighted
	{
		.xf-nlMenuRowHighlighted();
	}

	&.menu-row--clickable:hover
	{
		.xf-nlMenuRowHover();;
	}
	&.menu-row--separated
	{
		+ .menu-row
		{
			border-top: @xf-borderSize solid @xf-nlMenuDividerBorderColor;
		}
	}
}

.menu-linkRow
{
	.xf-menuLinkRowExtended();

	&.is-selected,
	&:hover,
	&:focus
	{
		.xf-menuLinkRowSelectedExtended();

		&.is-selected
		{

		}
	}
}

.menu-tabHeader
{
	.tabs-tab
	{
		&:hover:not(.is-active)
		{
			.xf-nlMenuTabHeaderHover();
		}
	}
}

/* core_offcanvas.less */

.offCanvasMenu-content
{

	& when(@ltr) or 
	{
		.m-dropShadow(2px, 0, 5px, 0, .25);
		.m-transform(translateX(-300px));
	}

	& when(@rtl)
	{
		.m-dropShadow(-2px, 0, 5px, 0, .25);
		.m-transform(translateX(280px));
	}
}


/* core_tab.less */

.tabs--standalone
{

	.tabs-tab
	{
		.xf-standaloneTabTab();

		&:hover:not(.is-active)
		{
			.xf-standaloneTabHover();
		}
	}
}

@media (max-width: @xf-responsiveEdgeSpacerRemoval)
{
	.tabs--standalone
	{
		margin: 0;
	}
}

/* core_blockend.less */

@media (max-width: @xf-responsiveEdgeSpacerRemoval)
{
	.block-container,
	.blockMessage
	{
		margin: 0;
	}
}

/* core_block.less */

.blocks
{
	.block
	{
		.xf-nlBlockWrapper();
	}
}
/* Currently present in customized template
.block-tabHeader
{
	.tabs-tab
	{
		&:hover
		{
			.xf-blockTabHeaderSelected();
		}
	}
}
*/

.block-filterBar
{
	padding: @xf-blockHeadPaddingV @xf-blockHeadPaddingH;
	line-height: normal;

	&.block-filterBar--standalone
	{
		padding: @xf-blockHeadPaddingV @xf-blockHeadPaddingH;
	}

	.filterBar-filterToggle,
	.filterBar-menuTrigger
	{
		&:hover
		{
			.xf-nlBlockMenuTriggerHover();
		}
	}

	.filterBar-menuTrigger
	{
		margin-right: 0;
	}
}

/* core_button.less */

.button,
a.button // needed for specificity over a:link
{
	&:hover,
	&:active,
	&:focus
	{
		text-decoration: none;
		.xf-nlButtonDefaultHover();
	}

	&.button--primary
	{
		&:hover,
		&:active,
		&:focus
		{
			text-decoration: none;
			.xf-nlButtonPrimaryHover();
		}
	}

	&.button--cta
	{
		&:hover,
		&:active,
		&:focus
		{
			text-decoration: none;
			.xf-nlButtonCtaHover();
		}
	}
	
	&.button--link
	{
		.xf-nlButtonLink();

		&:hover,
		&:active,
		&:focus
		{
			text-decoration: none;
			background: @xf-contentHighlightBg;
			.xf-nlButtonLinkHover();
		}
	}
	
	&.button--small, &.button--s
	{
		font-size: @xf-fontSizeSmall;
		padding: 3px 6px;
		.xf-nlButtonSmall();
		
		&:hover,
		&:active,
		&:focus
		{
			text-decoration: none;
			.xf-nlButtonSmallHover();
		}
	}
	
	&.button--medium, &.button--m
	{
		.xf-nlButtonMedium();
	}
	
	&.button--large, &.button--l
	{
		.xf-nlButtonLarge();
	}

	&.button--icon
	{

		.button-icon
		{
		}

		// &--xx          { .m-buttonIcon(@fa-var-plus-square, .79em); }
	}
}

/* core_tooltip.less */

.tooltip-content
{
	.tooltip--bookmark &,
	.tooltip--member &,
	.tooltip--share &
	{
		.xf-nlTooltipMenu();
		
		.block-row + .block-row {
			padding-top: 0;
		}
	}
}';
	return $__finalCompiled;
});