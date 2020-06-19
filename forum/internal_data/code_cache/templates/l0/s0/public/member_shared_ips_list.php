<?php
// FROM HASH: 8de8471304b9a836de7c0f58da66ef97
return array('macros' => array('shared_block' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'user' => '!',
		'shared' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			';
	if ($__vars['shared']) {
		$__finalCompiled .= '
				<ol class="block-body">
					';
		if ($__templater->isTraversable($__vars['shared'])) {
			foreach ($__vars['shared'] AS $__vars['share']) {
				$__finalCompiled .= '
						<li class="block-row block-row--separated">
							<div class="contentRow">
								<div class="contentRow-figure">
									' . $__templater->func('avatar', array($__vars['share']['user'], 's', false, array(
					'notooltip' => 'true',
				))) . '
								</div>
								<div class="contentRow-main">
									';
				if ($__templater->method($__vars['share']['user'], 'isPossibleSpammer', array())) {
					$__finalCompiled .= '
										<span class="contentRow-extra">
											' . $__templater->button('
												' . 'Spam' . '
											', array(
						'href' => $__templater->func('link', array('spam-cleaner', $__vars['share']['user'], ), false),
						'class' => 'button--link',
						'overlay' => 'true',
					), '', array(
					)) . '
										</span>
									';
				}
				$__finalCompiled .= '

									<h3 class="contentRow-header">' . $__templater->func('username_link', array($__vars['share']['user'], true, array(
					'notooltip' => 'true',
				))) . '</h3>

									' . $__templater->func('user_blurb', array($__vars['share']['user'], array(
					'class' => 'contentRow-lesser',
				))) . '

									<div class="contentRow-minor">
										<ul class="listInline listInline--bullet">
											<li><dl class="pairs pairs--inline">
												<dt>' . 'Joined' . '</dt>
												<dd>' . $__templater->func('date_dynamic', array($__vars['share']['user']['register_date'], array(
				))) . '</dd>
											</dl></li>
											<li><dl class="pairs pairs--inline">
												<dt>' . 'Messages' . '</dt>
												<dd>' . $__templater->filter($__vars['share']['user']['message_count'], array(array('number', array()),), true) . '</dd>
											</dl></li>
											<li><dl class="pairs pairs--inline">
												<dt>' . 'Reaction score' . '</dt>
												<dd>' . $__templater->filter($__vars['share']['user']['reaction_score'], array(array('number', array()),), true) . '</dd>
											</dl></li>
										</ul>
									</div>

									<div class="contentRow-lesser">
										<ol>
											';
				if ($__templater->isTraversable($__vars['share']['ips'])) {
					foreach ($__vars['share']['ips'] AS $__vars['ip']) {
						$__finalCompiled .= '
												<li>
													<ul class="listInline listInline--bullet">
														<li><a href="' . $__templater->func('link', array('misc/ip-info', '', array('ip' => $__templater->filter($__vars['ip']['ip'], array(array('ip', array()),), false), ), ), true) . '" target="_blank">' . $__templater->filter($__vars['ip']['ip'], array(array('ip', array()),), true) . '</a></li>
														';
						if ($__vars['ip']['total'] > 1) {
							$__finalCompiled .= '
															<li>' . '' . $__templater->filter($__vars['ip']['total'], array(array('number', array()),), true) . ' times' . '</li>
															<li>' . $__templater->func('date_dynamic', array($__vars['ip']['first_date'], array(
							))) . ' - ' . $__templater->func('date_dynamic', array($__vars['ip']['last_date'], array(
							))) . '</li>
														';
						} else {
							$__finalCompiled .= '
															<li>' . '1 time' . '</li>
															<li>' . $__templater->func('date_dynamic', array($__vars['ip']['first_date'], array(
							))) . '</li>
														';
						}
						$__finalCompiled .= '
													</ul>
												</li>
											';
					}
				}
				$__finalCompiled .= '
										</ol>
									</div>
								</div>
							</div>
						</li>
					';
			}
		}
		$__finalCompiled .= '
				</ol>
				<div class="block-footer">
					<span class="block-footer-counter">' . 'Matched ' . $__templater->func('count', array($__vars['shared'], ), true) . ' users.' . '</span>
				</div>
			';
	} else {
		$__finalCompiled .= '
				<div class="block-body block-row">' . 'No matching users were found.' . '</div>
			';
	}
	$__finalCompiled .= '
		</div>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Shared IPs for ' . $__templater->escape($__vars['user']['username']) . '');
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['user']['username'])), $__templater->func('link', array('full:members', $__vars['user'], ), false), array(
	));
	$__finalCompiled .= '

' . $__templater->callMacro(null, 'shared_block', array(
		'user' => $__vars['user'],
		'shared' => $__vars['shared'],
	), $__vars) . '

';
	return $__finalCompiled;
});