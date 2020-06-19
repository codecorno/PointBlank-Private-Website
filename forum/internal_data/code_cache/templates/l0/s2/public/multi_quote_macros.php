<?php
// FROM HASH: bf037c100f22f57b2a131d83b02119c7
return array('macros' => array('block' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'quotes' => '!',
		'messages' => '!',
		'containerRelation' => '!',
		'dateKey' => '!',
		'bbCodeContext' => '!',
		'titleKey' => 'title',
		'userRelation' => 'User',
		'usernameKey' => 'username',
		'messageKey' => 'message',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro('nestable_macros', 'setup', array(
		'includeLess' => false,
	), $__vars) . '

	';
	$__templater->includeCss('message.less');
	$__finalCompiled .= '
	';
	$__templater->includeCss('multi_quote_sort.less');
	$__finalCompiled .= '

	';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Review selected messages');
	$__finalCompiled .= '

	<div class="block">
		<div class="block-container">
			<div class="block-row block-row--minor">
				' . 'Drag the icon to rearrange the messages.' . '
			</div>
			<div class="block-body nestable-container" data-xf-init="nestable" data-max-depth="1" data-value-function="serialize">
				<ol class="nestable-list">
					';
	$__compilerTemp1 = $__templater->func('array_keys', array($__vars['quotes'], ), false);
	if ($__templater->isTraversable($__compilerTemp1)) {
		foreach ($__compilerTemp1 AS $__vars['messageId']) {
			$__finalCompiled .= '
						';
			$__vars['message'] = $__vars['messages'][$__vars['messageId']];
			$__finalCompiled .= '
						';
			if ($__vars['message']) {
				$__finalCompiled .= '
							';
				if ($__templater->isTraversable($__vars['quotes'][$__vars['messageId']])) {
					foreach ($__vars['quotes'][$__vars['messageId']] AS $__vars['key'] => $__vars['quote']) {
						$__finalCompiled .= '
								<li class="nestable-item" data-id="' . $__templater->escape($__vars['messageId']) . '-' . $__templater->escape($__vars['key']) . '">
									<div class="message message--simple message--bordered message--multiQuoteList message--forceColumns">
										<div class="message-inner">
											<div class="message-cell message-cell--closer message-cell--action">
												<div class="nestable-handle" aria-label="' . $__templater->filter('Drag handle', array(array('for_attr', array()),), true) . '">
													<span class="u-muted">' . $__templater->fontAwesome('fa-bars fa-2x', array(
						)) . '</span>
												</div>
											</div>
											<div class="message-cell message-cell--closer message-cell--main">
												<div class="message-content">
													<div class="message-attribution">
														<div class="contentRow contentRow--alignMiddle">
															<div class="contentRow-figure">
																' . $__templater->func('avatar', array($__vars['message'][$__vars['userRelation']], 'xxs', false, array(
							'defaultname' => $__vars['message'][$__vars['usernameKey']],
							'href' => '',
						))) . '
															</div>
															<div class="contentRow-main contentRow-main--close">
																<ul class="listInline listInline--bullet">
																	<li>' . $__templater->func('username_link', array($__vars['message'][$__vars['userRelation']], false, array(
							'defaultname' => $__vars['message'][$__vars['usernameKey']],
							'href' => '',
						))) . '</li>
																	<li>' . $__templater->func('date_dynamic', array($__vars['message'][$__vars['dateKey']], array(
						))) . '</li>
																	<li>' . $__templater->escape($__vars['message'][$__vars['containerRelation']][$__vars['titleKey']]) . '</li>
																</ul>
															</div>
															<span class="contentRow-extra">
																' . $__templater->button('
																	' . 'Remove' . '
																', array(
							'href' => 'javascript:',
							'class' => 'button--small button--link js-removeMessage',
						), '', array(
						)) . '
															</span>
														</div>
													</div>

													<article class="message-body">
														';
						if ($__vars['quote'] === true) {
							$__finalCompiled .= '
															' . $__templater->func('bb_code', array($__vars['message'][$__vars['messageKey']], $__vars['bbCodeContext'], $__vars['message'], ), true) . '
														';
						} else {
							$__finalCompiled .= '
															' . $__templater->func('bb_code', array($__vars['quote'], $__vars['bbCodeContext'], $__vars['message'], ), true) . '
														';
						}
						$__finalCompiled .= '
													</article>

													<div class="message-gradient"></div>
												</div>
											</div>
										</div>
									</div>
								</li>
							';
					}
				}
				$__finalCompiled .= '
						';
			}
			$__finalCompiled .= '
					';
		}
	}
	$__finalCompiled .= '
				</ol>
				' . $__templater->formHiddenVal('message_ids', '', array(
	)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-controls">' . $__templater->button('
					' . 'Quote messages' . '
				', array(
		'class' => 'button--primary button--small u-pullRight js-quoteMessages',
		'icon' => 'quote',
	), '', array(
	)) . '</span>
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},
'button' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'href' => '!',
		'messageSelector' => '!',
		'storageKey' => '!',
		'addMessage' => 'Message added to multi-quote.',
		'removeMessage' => 'Message removed from multi-quote.',
		'row' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__templater->includeJs(array(
		'src' => 'xf/message.js',
		'min' => '1',
	));
	$__finalCompiled .= '
	';
	$__vars['button'] = $__templater->preEscaped('
		' . $__templater->button('
			' . 'Insert quotes' . $__vars['xf']['language']['ellipsis'] . '
		', array(
		'class' => 'button--link button--multiQuote',
		'style' => 'display: none;',
		'data-xf-init' => 'multi-quote',
		'data-href' => $__vars['href'],
		'data-message-selector' => $__vars['messageSelector'],
		'data-storage-key' => $__vars['storageKey'],
		'data-add-message' => 'Message added to multi-quote.',
		'data-remove-message' => 'Message removed from multi-quote.',
		'icon' => 'quote',
	), '', array(
	)) . '
	');
	$__finalCompiled .= '
	';
	if ($__vars['row']) {
		$__finalCompiled .= '
		' . $__templater->formRow('
			' . $__templater->filter($__vars['button'], array(array('raw', array()),), true) . '
		', array(
			'label' => '',
		)) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->filter($__vars['button'], array(array('raw', array()),), true) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});