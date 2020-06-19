<?php
// FROM HASH: 539b30ba797311e195709c4446f2803d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '.smilie
{
	vertical-align: text-bottom;
	max-width: none;

	&.smilie--emoji
	{
		width: 1.467em;
	}

	&.is-clicked
	{
		transform: rotate(45deg);
		transition: all 0.25s;
	}
}

.contentRow-figure--emoji
{
	.smilie.smilie--emoji
	{
		width: 22px;
	}

	img
	{
		max-width: 24px;
		max-height: 24px;
		vertical-align: top;
	}
}

';
	if ($__vars['smilieSprites']) {
		$__finalCompiled .= '
	';
		if ($__templater->isTraversable($__vars['smilieSprites'])) {
			foreach ($__vars['smilieSprites'] AS $__vars['smilieId'] => $__vars['smilieSprite']) {
				$__finalCompiled .= '
		';
				if ($__vars['smilieSprite']['sprite_css']) {
					$__finalCompiled .= '
			.smilie--sprite.smilie--sprite' . $__templater->escape($__vars['smilieId']) . '
			{
				' . $__templater->filter($__vars['smilieSprite']['sprite_css'], array(array('raw', array()),), true) . '
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