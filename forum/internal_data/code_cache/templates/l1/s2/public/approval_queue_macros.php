<?php
// FROM HASH: bb8be75cf195a993fc4fead106ae3342
return array('macros' => array('spam_log' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'spamDetails' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['spamDetails']) {
		$__finalCompiled .= '
		' . $__templater->formRow('
			' . $__templater->escape($__vars['spamDetails']) . '
		', array(
			'label' => 'Spam log',
		)) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'action_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'unapprovedItem' => '!',
		'handler' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formRow('
		' . $__templater->callMacro(null, 'action_radio', array(
		'unapprovedItem' => $__vars['unapprovedItem'],
		'handler' => $__vars['handler'],
	), $__vars) . '
	', array(
		'label' => 'Action',
		'class' => 'js-approvalQueue-itemControls',
	)) . '
';
	return $__finalCompiled;
},
'action_radio' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'unapprovedItem' => '!',
		'handler' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="js-approvalQueue-itemControls">
		';
	$__compilerTemp1 = array();
	$__compilerTemp2 = $__templater->method($__vars['unapprovedItem'], 'getDefaultActions', array());
	if ($__templater->isTraversable($__compilerTemp2)) {
		foreach ($__compilerTemp2 AS $__vars['action'] => $__vars['label']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['action'],
				'checked' => ((!$__vars['action']) ? 'checked' : ''),
				'data-xf-click' => 'approval-control',
				'label' => $__templater->escape($__vars['label']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->formRadio(array(
		'name' => 'queue[' . $__vars['unapprovedItem']['content_type'] . '][' . $__vars['unapprovedItem']['content_id'] . ']',
	), $__compilerTemp1) . '
	</div>
';
	return $__finalCompiled;
},
'item_message_type' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'content' => '!',
		'contentDate' => '',
		'user' => '!',
		'typePhraseHtml' => '!',
		'headerPhraseHtml' => '',
		'spamDetails' => '',
		'messageHtml' => '!',
		'unapprovedItem' => '',
		'handler' => '',
		'actionsHtml' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<div class="message">
		';
	$__templater->includeCss('message.less');
	$__finalCompiled .= '
		<div class="message-inner">
			<div class="message-cell message-cell--user">

				<section itemscope itemtype="https://schema.org/Person" class="message-user">
					<div class="message-avatar">
						<div class="message-avatar-wrapper">
							' . $__templater->func('avatar', array($__vars['user'], 'm', false, array(
		'itemprop' => 'image',
	))) . '
						</div>
					</div>
					<div class="message-userDetails">
						<h4 class="message-name">' . $__templater->func('username_link', array($__vars['user'], true, array(
		'itemprop' => 'name',
	))) . '</h4>
					</div>

					<span class="message-userArrow"></span>
				</section>

			</div>
			<div class="message-cell message-cell--main">
				<div class="message-main">

					<header class="message-attribution">
						<span class="message-attribution-main">' . $__templater->func('date_dynamic', array(($__vars['contentDate'] ?: $__vars['content']['post_date']), array(
	))) . '</span>
						<span class="message-attribution-opposite">' . $__templater->filter($__vars['typePhraseHtml'], array(array('raw', array()),), true) . '</span>
					</header>

					<div class="message-content">

						';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
								';
	if (!$__templater->test($__vars['headerPhraseHtml'], 'empty', array())) {
		$__compilerTemp1 .= '
									<div class="messageNotice messageNotice--highlighted messageNotice--moderated">
										' . $__templater->filter($__vars['headerPhraseHtml'], array(array('raw', array()),), true) . '
									</div>
								';
	}
	$__compilerTemp1 .= '

								';
	if (!$__templater->test($__vars['spamDetails'], 'empty', array())) {
		$__compilerTemp1 .= '
									<div class="messageNotice messageNotice--warning">
										' . $__templater->escape($__vars['spamDetails']) . '
									</div>
								';
	}
	$__compilerTemp1 .= '

								';
	if (!$__templater->test($__vars['messageHtml'], 'empty', array())) {
		$__compilerTemp1 .= '
									<div class="message-userContent">
										<article class="message-body">' . $__templater->filter($__vars['messageHtml'], array(array('raw', array()),), true) . '</article>
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
							<div class="message-userContent">
								<article class="message-body">
									<i>' . 'No additional information available.' . '</i>
								</article>
							</div>
						';
	}
	$__finalCompiled .= '
					</div>

				</div>
			</div>
			<div class="message-cell message-cell--extra">
				';
	if ($__vars['unapprovedItem'] AND $__vars['handler']) {
		$__finalCompiled .= '
					' . $__templater->callMacro('approval_queue_macros', 'action_radio', array(
			'unapprovedItem' => $__vars['unapprovedItem'],
			'handler' => $__vars['handler'],
		), $__vars) . '
				';
	} else {
		$__finalCompiled .= '
					' . $__templater->filter($__vars['actionsHtml'], array(array('raw', array()),), true) . '
				';
	}
	$__finalCompiled .= '
			</div>
		</div>
	</div>
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

';
	return $__finalCompiled;
});