<?php
// FROM HASH: 4da89406c0e434c149d88d82702f8991
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['options']['staffOnline']) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		$__compilerTemp1 .= '
					';
		if ($__templater->isTraversable($__vars['online']['users'])) {
			foreach ($__vars['online']['users'] AS $__vars['user']) {
				$__compilerTemp1 .= '
						';
				if ($__vars['user']['is_staff']) {
					$__compilerTemp1 .= '
							<li class="block-row">
								<div class="contentRow">
									<div class="contentRow-figure">
										' . $__templater->func('avatar', array($__vars['user'], 'xs', false, array(
					))) . '
									</div>
									<div class="contentRow-main contentRow-main--close">
										' . $__templater->func('username_link', array($__vars['user'], true, array(
					))) . '
										<div class="contentRow-minor">
											' . $__templater->func('user_title', array($__vars['user'], false, array(
					))) . '
										</div>
									</div>
								</div>
							</li>
						';
				}
				$__compilerTemp1 .= '
					';
			}
		}
		$__compilerTemp1 .= '
				';
		if (strlen(trim($__compilerTemp1)) > 0) {
			$__finalCompiled .= '
		<div class="block" data-widget-section="staffMembers"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
			<div class="block-container">
				<h3 class="block-minorHeader"><a href="' . $__templater->func('link', array('members', null, array('key' => 'staff_members', ), ), true) . '">' . 'Staff online' . '</a></h3>
				<ul class="block-body">
				' . $__compilerTemp1 . '
				</ul>
			</div>
		</div>
	';
		}
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

<div class="block" data-widget-section="onlineNow"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
	<div class="block-container">
		<h3 class="block-minorHeader"><a href="' . $__templater->func('link', array('online', ), true) . '">' . $__templater->escape($__vars['title']) . '</a></h3>
		<div class="block-body">
			';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
							';
	if ($__vars['options']['followedOnline']) {
		$__compilerTemp2 .= '
								';
		if ($__templater->isTraversable($__vars['online']['users'])) {
			foreach ($__vars['online']['users'] AS $__vars['user']) {
				$__compilerTemp2 .= '
									';
				if ($__templater->func('in_array', array($__vars['user']['user_id'], $__vars['xf']['visitor']['Profile']['following'], ), false)) {
					$__compilerTemp2 .= '
										<li>
											' . $__templater->func('avatar', array($__vars['user'], 'xxs', false, array(
						'img' => 'true',
					))) . '
										</li>
									';
				}
				$__compilerTemp2 .= '
								';
			}
		}
		$__compilerTemp2 .= '
							';
	}
	$__compilerTemp2 .= '
						';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__finalCompiled .= '
				<div class="block-row block-row--minor">
					<h4 class="block-textHeader block-textHeader--scaled">
						' . 'People you follow' . '
					</h4>
					<ul class="listHeap">
						' . $__compilerTemp2 . '
					</ul>
				</div>

				<div class="block-row block-row--minor">
					<h4 class="block-textHeader block-textHeader--scaled">
						' . 'Members' . '
					</h4>
			';
	} else {
		$__finalCompiled .= '
				<div class="block-row block-row--minor">
			';
	}
	$__finalCompiled .= '

				';
	if (!$__templater->test($__vars['online']['users'], 'empty', array())) {
		$__finalCompiled .= '
					<ul class="listInline listInline--comma">
						';
		if ($__templater->isTraversable($__vars['online']['users'])) {
			foreach ($__vars['online']['users'] AS $__vars['user']) {
				$__finalCompiled .= trim('
							<li>' . $__templater->func('username_link', array($__vars['user'], true, array(
					'class' => ((!$__vars['user']['visible']) ? 'username--invisible' : ''),
				))) . '</li>
						');
			}
		}
		$__finalCompiled .= '
					</ul>
					';
		if ($__vars['online']['counts']['unseen']) {
			$__finalCompiled .= '
						<a href="' . $__templater->func('link', array('online', ), true) . '">' . '... and ' . $__templater->escape($__vars['online']['counts']['unseen']) . ' more.' . '</a>
					';
		}
		$__finalCompiled .= '
				';
	} else {
		$__finalCompiled .= '
					' . 'No members online now.' . '
				';
	}
	$__finalCompiled .= '
			</div>
		</div>
		<div class="block-footer">
			<span class="block-footer-counter">' . 'Total:&nbsp;' . $__templater->func('number', array($__vars['online']['counts']['total'], ), true) . ' (members:&nbsp;' . $__templater->func('number', array($__vars['online']['counts']['members'], ), true) . ', guests:&nbsp;' . $__templater->func('number', array($__vars['online']['counts']['guests'], ), true) . ')' . '</span>
		</div>
	</div>
</div>';
	return $__finalCompiled;
});