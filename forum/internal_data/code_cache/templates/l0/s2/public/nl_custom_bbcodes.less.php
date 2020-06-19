<?php
// FROM HASH: 4bcaf0c53339d9d0ec1317182c819a50
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '/* You should customize this template in your child/active style, do not edit in your master style */
/* Use the template merge system when this template becomes outdated */

.bbWrapper {
	
	h1, h2, h3, h4, h5 {
		margin: 0;
	}
	h1 {
		
	}
	h2 {
		
	}
	h3 {
		
	}
	h4 {
		
	}
	h5 {
		
	}
	.contentBox {
		padding: @xf-contentPadding;
		.xf-blockBorder();
		.xf-contentBase();
	}
	.accentBox {
		padding: @xf-contentPadding;
		.xf-contentAccentBase();
		border: @xf-borderSize solid @xf-borderColorAccent;

		a { .xf-contentAccentLink(); }
	}
	.highlightBox {
		padding: @xf-contentPadding;
		.xf-contentHighlightBase();
	}
	.floatLeft {
		float: left;
		padding-right: @xf-contentPadding;
	}
	.floatRight {
		float: right;
		padding-left: @xf-contentPadding;
	}
	.imgBar {
		color: #fff;
		padding: @xf-contentPadding;
		background-repeat: no-repeat;
		background-position: center center;
		background-size: cover;
	}
}';
	return $__finalCompiled;
});