<?php
// FROM HASH: 4308adab41f314988995bbf738108051
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.p-footer
{
	.xf-publicFooter();

	a
	{
		.xf-publicFooterLink();
	}
}

.p-footer-inner
{
	.m-pageWidth();
	.m-pageInset();

	padding-top: @xf-paddingMedium;
	padding-bottom: @xf-paddingLarge;
}

.p-footer-row
{
	.m-clearFix();

	margin-bottom: -@xf-paddingLarge;
}

.p-footer-row-main
{
	float: left;
	margin-bottom: @xf-paddingLarge;
}

.p-footer-row-opposite
{
	float: right;
	margin-bottom: @xf-paddingLarge;
}

.p-footer-linkList
{
	.m-listPlain();
	.m-clearFix();

	> li
	{
		float: left;
		margin-right: .5em;

		&:last-child
		{
			margin-right: 0;
		}

		a
		{
			padding: 2px 4px;
			border-radius: @xf-borderRadiusSmall;

			&:hover
			{
				text-decoration: none;
				background-color: fade(@xf-publicFooterLink--color, 10%);
			}
		}
	}
}

.p-footer-rssLink
{
	> span
	{
		position: relative;
		top: -1px;

		display: inline-block;
		width: 1.44em;
		height: 1.44em;
		line-height: 1.44em;
		text-align: center;
		font-size: .8em;
		background-color: #FFA500;
		border-radius: 2px;
	}

	.fa-rss
	{
		color: white;
	}
}

.p-footer-copyright
{
	margin-top: @xf-elementSpacer;
	text-align: center;
	font-size: @xf-fontSizeSmallest;
}

.p-footer-debug
{
	margin-top: @xf-paddingLarge;
	text-align: right;
	font-size: @xf-fontSizeSmallest;

	.pairs > dt { color: inherit; }
}

@media (max-width: @xf-responsiveMedium)
{
	.p-footer-row-main,
	.p-footer-row-opposite
	{
		float: none;
	}

	.p-footer-copyright
	{
		text-align: left;
		padding: 0 4px; // aligns with other links
	}
}';
	return $__finalCompiled;
});