<?php
// FROM HASH: c5cd3c023cd8a7b8e327e36865376488
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['user']['username']));
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
		';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
			';
	if ($__vars['user']['Profile']['about']) {
		$__compilerTemp1 .= '
				<div class="block-row block-row--separated">
					' . $__templater->func('bb_code', array($__vars['user']['Profile']['about'], 'user:about', $__vars['user'], ), true) . '
				</div>
			';
	}
	$__compilerTemp1 .= '

			';
	$__compilerTemp2 = '';
	$__compilerTemp2 .= '
					';
	$__compilerTemp3 = '';
	$__compilerTemp3 .= '
									';
	if ($__vars['user']['Profile']['birthday']['timeStamp']) {
		$__compilerTemp3 .= '
										' . $__templater->func('date', array($__vars['user']['Profile']['birthday']['timeStamp'], $__vars['user']['Profile']['birthday']['format'], ), true) . '
										';
		if ($__vars['user']['Profile']['birthday']['age']) {
			$__compilerTemp3 .= '
											' . $__templater->func('parens', array(('Age' . $__vars['xf']['language']['label_separator'] . ' ') . $__vars['user']['Profile']['birthday']['age'], ), true) . '
										';
		}
		$__compilerTemp3 .= '
									';
	}
	$__compilerTemp3 .= '
								';
	if (strlen(trim($__compilerTemp3)) > 0) {
		$__compilerTemp2 .= '
						<dl class="pairs pairs--columns pairs--fixedSmall">
							<dt>' . 'Birthday' . '</dt>
							<dd>
								' . $__compilerTemp3 . '
							</dd>
						</dl>
					';
	}
	$__compilerTemp2 .= '

					';
	if ($__vars['user']['Profile']['website']) {
		$__compilerTemp2 .= '
						<dl class="pairs pairs--columns pairs--fixedSmall">
							<dt>' . 'Website' . '</dt>
							<dd>
								<a href="' . $__templater->escape($__vars['user']['Profile']['website']) . '" rel="nofollow" target="_blank">' . $__templater->escape($__vars['user']['Profile']['website']) . '</a>
							</dd>
						</dl>
					';
	}
	$__compilerTemp2 .= '

					';
	if ($__vars['user']['Profile']['location']) {
		$__compilerTemp2 .= '
						<dl class="pairs pairs--columns pairs--fixedSmall">
							<dt>' . 'Location' . '</dt>
							<dd>
								';
		if ($__vars['xf']['options']['geoLocationUrl']) {
			$__compilerTemp2 .= '
									<a href="' . $__templater->func('link', array('misc/location-info', '', array('location' => $__vars['user']['Profile']['location'], ), ), true) . '" rel="nofollow noreferrer" target="_blank" class="u-concealed">' . $__templater->escape($__vars['user']['Profile']['location']) . '</a>
								';
		} else {
			$__compilerTemp2 .= '
									' . $__templater->escape($__vars['user']['Profile']['location']) . '
								';
		}
		$__compilerTemp2 .= '
							</dd>
						</dl>
					';
	}
	$__compilerTemp2 .= '

					' . $__templater->callMacro('custom_fields_macros', 'custom_fields_view', array(
		'type' => 'users',
		'group' => 'personal',
		'set' => $__vars['user']['Profile']['custom_fields'],
		'additionalFilters' => array('profile', ),
	), $__vars) . '
				';
	if (strlen(trim($__compilerTemp2)) > 0) {
		$__compilerTemp1 .= '
				<div class="block-row block-row--separated">
				' . $__compilerTemp2 . '
				</div>
			';
	}
	$__compilerTemp1 .= '

			';
	if ($__templater->method($__vars['user'], 'canViewIdentities', array())) {
		$__compilerTemp1 .= '
				';
		$__compilerTemp4 = '';
		$__compilerTemp4 .= '
							';
		if ($__templater->method($__vars['xf']['visitor'], 'canStartConversationWith', array($__vars['user'], ))) {
			$__compilerTemp4 .= '
								<dl class="pairs pairs--columns pairs--fixedSmall">
									<dt>' . 'Conversation' . '</dt>
									<dd><a href="' . $__templater->func('link', array('conversations/add', '', array('to' => $__vars['user']['username'], ), ), true) . '">' . 'Start conversation' . '</a></dd>
								</dl>
							';
		}
		$__compilerTemp4 .= '

							' . $__templater->callMacro('custom_fields_macros', 'custom_fields_view', array(
			'type' => 'users',
			'group' => 'contact',
			'set' => $__vars['user']['Profile']['custom_fields'],
			'additionalFilters' => array('profile', ),
		), $__vars) . '
						';
		if (strlen(trim($__compilerTemp4)) > 0) {
			$__compilerTemp1 .= '
					<div class="block-row block-row--separated">
						<h4 class="block-textHeader">' . 'Contact' . '</h4>
						' . $__compilerTemp4 . '
					</div>
				';
		}
		$__compilerTemp1 .= '
			';
	}
	$__compilerTemp1 .= '

			';
	if ($__vars['user']['Profile']['signature']) {
		$__compilerTemp1 .= '
				<div class="block-row block-row--separated">
					<h4 class="block-textHeader">' . 'Signature' . '</h4>
					' . $__templater->func('bb_code', array($__vars['user']['Profile']['signature'], 'user:signature', $__vars['user'], ), true) . '
				</div>
			';
	}
	$__compilerTemp1 .= '

			';
	if ($__vars['followingCount']) {
		$__compilerTemp1 .= '
				';
		$__compilerTemp5 = '';
		$__compilerTemp5 .= '
								';
		if ($__templater->isTraversable($__vars['following'])) {
			foreach ($__vars['following'] AS $__vars['followingUser']) {
				if (!$__templater->method($__vars['xf']['visitor'], 'isIgnoring', array($__vars['followingUser']['user_id'], ))) {
					$__compilerTemp5 .= '
									<li>
										' . $__templater->func('avatar', array($__vars['followingUser'], 's', false, array(
					))) . '
									</li>
								';
				}
			}
		}
		$__compilerTemp5 .= '
							';
		if (strlen(trim($__compilerTemp5)) > 0) {
			$__compilerTemp1 .= '
					<div class="block-row block-row--separated">
						<h4 class="block-textHeader">' . 'Following' . '</h4>
						<ul class="listHeap">
							' . $__compilerTemp5 . '
						</ul>
						';
			if ($__vars['followingCount'] > $__templater->func('count', array($__vars['following'], ), false)) {
				$__compilerTemp1 .= '
							<a href="' . $__templater->func('link', array('members/following', $__vars['user'], ), true) . '" data-xf-click="overlay">' . '... and ' . ($__vars['followingCount'] - $__templater->func('count', array($__vars['following'], ), false)) . ' more.' . '</a>
						';
			}
			$__compilerTemp1 .= '
					</div>
				';
		}
		$__compilerTemp1 .= '
			';
	}
	$__compilerTemp1 .= '

			';
	if ($__vars['followersCount']) {
		$__compilerTemp1 .= '
				';
		$__compilerTemp6 = '';
		$__compilerTemp6 .= '
								';
		if ($__templater->isTraversable($__vars['followers'])) {
			foreach ($__vars['followers'] AS $__vars['followerUser']) {
				if (!$__templater->method($__vars['xf']['visitor'], 'isIgnoring', array($__vars['followerUser']['user_id'], ))) {
					$__compilerTemp6 .= '
									<li>
										' . $__templater->func('avatar', array($__vars['followerUser'], 's', false, array(
					))) . '
									</li>
								';
				}
			}
		}
		$__compilerTemp6 .= '
							';
		if (strlen(trim($__compilerTemp6)) > 0) {
			$__compilerTemp1 .= '
					<div class="block-row block-row--separated">
						<h4 class="block-textHeader">' . 'Followers' . '</h4>
						<ul class="listHeap">
							' . $__compilerTemp6 . '
						</ul>
						';
			if ($__vars['followersCount'] > $__templater->func('count', array($__vars['followers'], ), false)) {
				$__compilerTemp1 .= '
							<a href="' . $__templater->func('link', array('members/followers', $__vars['user'], ), true) . '" data-xf-click="overlay">' . '... and ' . ($__vars['followersCount'] - $__templater->func('count', array($__vars['followers'], ), false)) . ' more.' . '</a>
						';
			}
			$__compilerTemp1 .= '
					</div>
				';
		}
		$__compilerTemp1 .= '
			';
	}
	$__compilerTemp1 .= '

			';
	if (!$__templater->test($__vars['trophies'], 'empty', array()) AND $__vars['xf']['options']['enableTrophies']) {
		$__compilerTemp1 .= '
				<div class="block-row block-row--separated">
					<h4 class="block-textHeader">' . 'Trophies' . '</h4>
					<ol class="listPlain">
						';
		if ($__templater->isTraversable($__vars['trophies'])) {
			foreach ($__vars['trophies'] AS $__vars['trophy']) {
				$__compilerTemp1 .= '
							<li class="block-row">
								<div class="contentRow">
									<span class="contentRow-figure contentRow-figure--text contentRow-figure--fixedSmall">' . $__templater->escape($__vars['trophy']['Trophy']['trophy_points']) . '</span>
									<div class="contentRow-main">
										<span class="contentRow-extra">' . $__templater->func('date_dynamic', array($__vars['trophy']['award_date'], array(
				))) . '</span>
										<h2 class="contentRow-header">' . $__templater->escape($__vars['trophy']['Trophy']['title']) . '</h2>
										<div class="contentRow-minor">' . $__templater->filter($__vars['trophy']['Trophy']['description'], array(array('raw', array()),), true) . '</div>
									</div>
								</div>
							</li>
						';
			}
		}
		$__compilerTemp1 .= '
					</ol>
				</div>
			';
	}
	$__compilerTemp1 .= '
		';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		' . $__compilerTemp1 . '
		';
	} else {
		$__finalCompiled .= '
			<div class="block-row block-row--separated">' . '' . $__templater->escape($__vars['user']['username']) . ' has not provided any additional information.' . '</div>
		';
	}
	$__finalCompiled .= '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});