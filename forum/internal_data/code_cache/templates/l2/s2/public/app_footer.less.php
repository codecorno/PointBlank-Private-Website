<?php
// FROM HASH: 81b1cb4d507747cd0339b4f76f7ff83d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.p-footer
{
	.m-clearFix();
	.xf-publicFooter();
	
	.p-footer-wrapper {
		.m-clearFix();
		.xf-nlPublicFooterWrapper();
	}

	a
	{
		.xf-publicFooterLink();
	}
}

.p-footer-row
{
	.m-clearFix();

	// margin-bottom: -@xf-paddingLarge;
	.xf-nlFooterRow();
}
.p-footer-inner
{

	// padding-top: @xf-paddingMedium;
	// padding-bottom: @xf-paddingLarge;
	
	.m-footerWidth();
	.xf-nlFooterRowInner();
	.m-clearFix();
}

.p-footer-row.p-footer-links {
	.m-clearFix();
	.xf-nlFooterLinksBar();
}
/*
.footerFixed .p-footer-links {
	.m-clearFix();
	.xf-nlFooterLinksBar();
}
*/
.p-footer-row-main
{
	float: left;
	// margin-bottom: @xf-paddingLarge;
}

.p-footer-row-opposite
{
	float: right;
	// margin-bottom: @xf-paddingLarge;
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
			.xf-nlFooterLinksBarLink();

			&:hover
			{
				text-decoration: none;
				.xf-nlFooterLinksBarLinkHover();
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

.p-footer-row.p-footer-copyright {
	.m-clearFix();
	.xf-nlFooterCopyright();
}
/*
.footerFixed .p-footer-copyright
{
	.m-clearFix();
	.xf-nlFooterCopyright();
}
*/

.p-footer-row.p-footer-debug
{
	.m-clearFix();
	.xf-nlFooterDebug();
	.pairs > dt { color: inherit; }
}

@media (max-width: @xf-responsiveMedium)
{
	.p-footer-row-main,
	.p-footer-row-opposite
	{
		float: none;
		margin-bottom: @xf-paddingLarge;
	}

	.p-footer-copyright
	{
		text-align: left;
		padding: 0 4px; // aligns with other links
	}
}';
	return $__finalCompiled;
});