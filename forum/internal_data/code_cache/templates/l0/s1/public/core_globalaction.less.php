<?php
// FROM HASH: ff96bdf79f89b433a64fcc23ff11c774
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '// #################################### GLOBAL ACTION INDICATOR ##########################

.globalAction
{
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	z-index: @zIndex-9;
	opacity: 0;
	.m-transition(opacity);
	pointer-events: none;

	&.is-active
	{
		opacity: 1;
	}
}

.globalAction-bar
{
	position: absolute;
	left: 0;
	top: 0;
	width: 33%;
	height: 3px;
	background: @xf-globalActionColor;
	pointer-events: none;

	-ltr-transform: translateX(-33vw);
	-rtl-transform: translateX(33vw);

	.m-animation(1.5s ease-in-out infinite globalActionBar);

	&:before
	{
		content: \'\';
		position: absolute;
		right: 0;
		height: 100%;
		width: 100px;
		box-shadow: 0 0 10px 2px @xf-globalActionColor;

		-ltr-transform: rotate(2deg) translate(0px, -3px);
		-rtl-transform: rotate(-2deg) translate(0px, -3px);
	}
}

.m-keyframes(globalActionBar, {
	from
	{
		-ltr-transform: translateX(-33vw);
		-rtl-transform: translateX(33vw);
	}
	to
	{
		-ltr-transform: translateX(100vw);
		-rtl-transform: translateX(-100vw);
	}
});

.globalAction-block
{
	position: fixed;
	top: 20px;
	right: 10px;
	pointer-events: none;

	i
	{
		display: inline-block;
		content: \' \';
		font-size: 10px;
		width: 1em;
		height: 2.5em;
		margin-right: .3em;
		background-color: @xf-globalActionColor;
		box-shadow: 1px 1px 2px rgba(0, 0, 0, .5);
		.m-animation(1s ease infinite globalActionPulse);
		opacity: .35;

		&:last-child
		{
			margin-right: 0;
		}

		&:nth-child(2)
		{
			animation-delay: .2s;
		}

		&:nth-child(3)
		{
			animation-delay: .4s;
		}
	}

	@media (max-width: @xf-responsiveNarrow)
	{
		display: none;
	}
}

.m-keyframes(globalActionPulse, {
	from
	{
		.m-transform(scaleY(1.5));
		opacity: 1;
	}
	to
	{
		.m-transform(scaleY(1));
		opacity: .35;
	}
});';
	return $__finalCompiled;
});