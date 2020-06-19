<?php
// FROM HASH: 3cdbe7cab5b2b6f737037e6a27ee6a50
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->isTraversable($__vars['displayStyles'])) {
		foreach ($__vars['displayStyles'] AS $__vars['id'] => $__vars['style']) {
			if ($__vars['style']['username_css']) {
				$__finalCompiled .= '
	.username--style' . $__templater->escape($__vars['id']) . '
	{
		' . $__templater->filter($__vars['style']['username_css'], array(array('raw', array()),), true) . '
	}
';
			}
		}
	}
	$__finalCompiled .= '

.m-usernameIcon()
{
	.m-faBase();
	margin-left: .33em;
	font-size: smaller;
}

.username--invisible
{
	color: @xf-textColorMuted;

	/*&:after {
		.m-usernameIcon();
		.m-faContent(@fa-var-eye-slash);
	}*/
}

.username--banned
{
	text-decoration: line-through;

	/*&:after {
		.m-usernameIcon();
		.m-faContent(@fa-var-ban);
	}*/
}

/*
.username--staff:after
{
	.m-usernameIcon();
	.m-faContent(@fa-var-address-card-o);
}

.username--moderator:after
{
	.m-usernameIcon();
	.m-faContent(@fa-var-shield);
}

.username--admin:after
{
	.m-usernameIcon();
	.m-faContent(@fa-var-id-badge);
}*/
';
	return $__finalCompiled;
});