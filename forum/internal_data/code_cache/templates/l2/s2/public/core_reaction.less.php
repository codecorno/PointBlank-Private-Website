<?php
// FROM HASH: 4e13fdd38bbc49e3ec051ed3ea982007
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.reaction
{
	display: flex;
	-webkit-touch-callout: none;

	&.reaction--imageHidden
	{
		img
		{
			display: none;
		}

		&.reaction--1
		{
			i
			{
				.m-faBefore(@fa-var-thumbs-up);
				.m-faBase();
			}
		}
	}

	&.reaction--small,
	&.reaction--medium
	{
		display: inline-block;

		.reaction-text
		{
			margin-left: 0;
		}
	}

	&.reaction--right
	{
		justify-content: flex-end;
	}

	&.reaction--inline
	{
		display: inline-flex;;
	}

	a&:hover,
	a&:focus
	{
		text-decoration: none;

		.reaction-text
		{
			text-decoration: underline;
		}
	}
}

.reactionScore
{
	display: inline-block;
	padding: 2px 3px;
	min-width: 16px;
	text-align: center;
	vertical-align: text-bottom;
	border-radius: @xf-borderRadiusMedium;
	font-size: @xf-fontSizeSmallest;
	.xf-chip();

	&:hover
	{
		.xf-chipHover();
	}
}

.reaction-image
{
	vertical-align: text-bottom;

	.reaction--small &
	{
		max-width: 16px;
		max-height: 16px;
	}

	.reaction--medium &
	{
		max-width: 21px;
		max-height: 21px;
	}
}

.reaction-sprite
{
	vertical-align: text-bottom;

	';
	if ($__vars['reactionSprites']) {
		$__finalCompiled .= '
		';
		if ($__templater->isTraversable($__vars['reactionSprites'])) {
			foreach ($__vars['reactionSprites'] AS $__vars['reactionId'] => $__vars['reactionSprite']) {
				$__finalCompiled .= '
			';
				if ($__vars['reactionSprite']['sprite_css']) {
					$__finalCompiled .= '
				.reaction--' . $__templater->escape($__vars['reactionId']) . ' &
				{
					' . $__templater->filter($__vars['reactionSprite']['sprite_css'], array(array('raw', array()),), true) . '
				}
			';
				}
				$__finalCompiled .= '
			';
				if ($__vars['reactionSprite']['small_sprite_css']) {
					$__finalCompiled .= '
				.reaction--small.reaction--' . $__templater->escape($__vars['reactionId']) . ' &
				{
					' . $__templater->filter($__vars['reactionSprite']['small_sprite_css'], array(array('raw', array()),), true) . '
				}
			';
				}
				$__finalCompiled .= '
			';
				if ($__vars['reactionSprite']['medium_sprite_css']) {
					$__finalCompiled .= '
				.reaction--medium.reaction--' . $__templater->escape($__vars['reactionId']) . ' &
				{
					' . $__templater->filter($__vars['reactionSprite']['medium_sprite_css'], array(array('raw', array()),), true) . '
				}
			';
				}
				$__finalCompiled .= '
		';
			}
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
}

.reaction-text
{
	margin-left: 10px;
	align-self: center;

	// note: when we add a reaction we make the text bold
	// the below approach sets the space for that to ensure
	// the text doesn\'t jump when the bold happens.
	&::after
	{
		content: " ";
		font-weight: @xf-fontWeightHeavy;
		height: 1px;
		color: transparent;
		overflow: hidden;
		visibility: hidden;
	}

	.has-reaction &
	{
		font-weight: @xf-fontWeightHeavy;
	}

	.reaction--small &
	{
		margin-left: 0;
	}

	';
	if ($__vars['reactionColors']) {
		$__finalCompiled .= '
		';
		if ($__templater->isTraversable($__vars['reactionColors'])) {
			foreach ($__vars['reactionColors'] AS $__vars['reactionId'] => $__vars['reactionColor']) {
				$__finalCompiled .= '
			';
				if ($__vars['reactionColor']) {
					$__finalCompiled .= '
				.reaction--' . $__templater->escape($__vars['reactionId']) . ' &
				{
					color: ' . $__templater->escape($__vars['reactionColor']) . ';
				}
			';
				}
				$__finalCompiled .= '
		';
			}
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
}

';
	if ($__vars['reactionColors']) {
		$__finalCompiled .= '
	';
		if ($__templater->isTraversable($__vars['reactionColors'])) {
			foreach ($__vars['reactionColors'] AS $__vars['reactionId'] => $__vars['reactionColor']) {
				$__finalCompiled .= '
		';
				if ($__vars['reactionColor']) {
					$__finalCompiled .= '
			.is-active.tabs-tab--reaction' . $__templater->escape($__vars['reactionId']) . '
			{
				border-color: ' . $__templater->escape($__vars['reactionColor']) . ' !important;
			}
		';
				}
				$__finalCompiled .= '
	';
			}
		}
		$__finalCompiled .= '
';
	}
	return $__finalCompiled;
});