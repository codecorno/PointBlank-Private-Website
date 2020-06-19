<?php
// FROM HASH: 98a26995359dd541c093c16f02f67b41
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeCss('member_tooltip.less');
	$__finalCompiled .= '

<div class="tooltip-content-inner">
	<div class="memberTooltip">
		<div class="memberTooltip-header">
			<span class="memberTooltip-avatar">
				' . $__templater->func('avatar', array($__vars['user'], 'm', false, array(
		'notooltip' => 'true',
	))) . '
			</span>
			<div class="memberTooltip-headerInfo">
				';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
						' . '
						';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
										' . $__templater->callMacro('member_macros', 'moderator_menu_actions', array(
		'user' => $__vars['user'],
		'context' => 'tooltip',
	), $__vars) . '
									';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__compilerTemp1 .= '
							' . $__templater->button('
								' . $__templater->fontAwesome('fa-cog', array(
		)) . '
							', array(
			'class' => 'button--link button--small menuTrigger',
			'data-xf-click' => 'menu',
			'aria-label' => 'More options',
			'aria-expanded' => 'false',
			'aria-haspopup' => 'true',
		), '', array(
		)) . '

							<div class="menu" data-menu="menu" aria-hidden="true">
								<div class="menu-content">
									<h3 class="menu-header">' . 'Moderator tools' . '</h3>
									' . $__compilerTemp2 . '
								</div>
							</div>
						';
	}
	$__compilerTemp1 .= '
						' . '
						';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
					<div class="memberTooltip-headerAction">
						' . $__compilerTemp1 . '
					</div>
				';
	}
	$__finalCompiled .= '

				<h4 class="memberTooltip-name">' . $__templater->func('username_link', array($__vars['user'], true, array(
		'notooltip' => 'true',
	))) . '</h4>

				';
	$__compilerTemp3 = '';
	$__compilerTemp3 .= $__templater->func('user_banners', array($__vars['user'], array(
	)));
	if (strlen(trim($__compilerTemp3)) > 0) {
		$__finalCompiled .= '
					<div class="memberTooltip-banners">
						' . $__compilerTemp3 . '
					</div>
				';
	}
	$__finalCompiled .= '

				';
	$__compilerTemp4 = '';
	$__compilerTemp4 .= '
						' . $__templater->func('user_blurb', array($__vars['user'], array(
		'tag' => 'div',
	))) . '
					';
	if (strlen(trim($__compilerTemp4)) > 0) {
		$__finalCompiled .= '
					<div class="memberTooltip-blurb">
					' . $__compilerTemp4 . '
					</div>
				';
	}
	$__finalCompiled .= '

				<div class="memberTooltip-blurb">
					<dl class="pairs pairs--inline">
						<dt>' . 'Joined' . '</dt>
						<dd>' . $__templater->func('date_dynamic', array($__vars['user']['register_date'], array(
	))) . '</dd>
					</dl>
				</div>

				';
	$__compilerTemp5 = '';
	$__compilerTemp5 .= $__templater->func('user_activity', array($__vars['user']));
	if (strlen(trim($__compilerTemp5)) > 0) {
		$__finalCompiled .= '
					<div class="memberTooltip-blurb">
						<dl class="pairs pairs--inline">
							<dt>' . 'Last seen' . '</dt>
							<dd dir="auto">
								' . $__compilerTemp5 . '
							</dd>
						</dl>
					</div>
				';
	}
	$__finalCompiled .= '
			</div>
		</div>
		<div class="memberTooltip-info">
			<div class="memberTooltip-stats">
				<div class="pairJustifier">
					' . $__templater->callMacro('member_macros', 'member_stat_pairs', array(
		'user' => $__vars['user'],
		'context' => 'tooltip',
	), $__vars) . '
				</div>
			</div>
		</div>

		';
	$__compilerTemp6 = '';
	$__compilerTemp6 .= '
				' . $__templater->callMacro('member_macros', 'member_action_buttons', array(
		'user' => $__vars['user'],
		'context' => 'tooltip',
	), $__vars) . '
			';
	if (strlen(trim($__compilerTemp6)) > 0) {
		$__finalCompiled .= '
			<hr class="memberTooltip-separator" />

			<div class="memberTooltip-actions">
			' . $__compilerTemp6 . '
			</div>
		';
	}
	$__finalCompiled .= '
	</div>
</div>';
	return $__finalCompiled;
});