<?php
// FROM HASH: c99506f9711278ec19474845e56f2c5c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.diffListContainer
{
	overflow: auto;
	max-height: 400px;
	max-height: 80vh;
}

.diffList
{
	margin: 0;
	padding: 0;
	list-style: none;

	&.diffList--code
	{
		display: table;
		width: 100%;
		direction: ltr;

		.diffList-line
		{
			font-family: @xf-fontFamilyCode;
			white-space: pre;
			word-wrap: normal;
		}

		textarea
		{
			font-family: @xf-fontFamilyCode;
		}

		&.diffList--wrapped .diffList-line
		{
			white-space: pre-wrap;
			word-wrap: break-word;
		}
	}

	&.diffList--editable
	{
		.diffList-line--u,
		.diffList-conflict.is-resolved .is-chosen
		{
			cursor: pointer;
		}
	}
}

.diffList-line
{
	line-height: 1.5;
	min-height: 8px;
	padding: 0 @xf-paddingSmall;

	&.diffList-line--u
	{
		background-color: #F0F8FF;
		border: 1px solid #D0D8DF;
		color: #4040A0;
	}

	&.diffList-line--d
	{
		background-color: #FAE4E4;
		border: 1px solid #C86060;
		color: #882020;
	}

	&.diffList-line--i
	{
		background-color: #E4FBE4;
		border: 1px solid #60C860;
		color: #208820;
	}

	&.diffList-line--d + .diffList-line--i,
	&.diffList-line--i + .diffList-line--d
	{
		border-top: none;
	}

	textarea
	{
		box-sizing: border-box;
		width: 100%;
	}
}

.diffList-conflict
{
	background-color: #FFFADD;
	border: 1px solid #ddcaad;
	color: #4F4A2D;
}

.diffList-resolve
{
	border-bottom: 1px solid #ddcaad;
	border-top: 1px solid #ddcaad;
	padding: 5px 0;
	text-align: center;
}

.diffList
{
	.diffLine
	{
		line-height: 1.5;
		min-height: 8px;
		padding: 0 @xf-paddingSmall 0 @xf-paddingSmall;
	}
	
	.diff_d
	{
		background-color: #FAE4E4;
		border: 1px solid #C86060;
		color: #882020;
	}
	
	.diff_d+.diff_i
	{
		border-top: none;
	}
	
	.diff_i+.diff_d
	{
		border-top: none;
	}
	
	.diff_i
	{
		background-color: #E4FBE4;
		border: 1px solid #60C860;
		color: #208820;
	}
	
	.diff_u
	{
		background-color: #F0F8FF;
		border: 1px solid #D0D8DF;
		color: #4040A0;
	}
	
	.conflict
	{
		background-color: #FFFADD;
		border: 1px solid #ddcaad;
		color: #4F4A2D;
		
		.resolve
		{
			border-bottom: 1px solid #ddcaad;
			border-top: 1px solid #ddcaad;
			padding: 5px 0;
			text-align: center;
		}
	}
	
	textarea.editor
	{
		box-sizing: border-box;
		margin-top: 5px;
		margin: 0;
		width: 100%;
	}
}
';
	return $__finalCompiled;
});