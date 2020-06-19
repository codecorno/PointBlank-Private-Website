<?php
// FROM HASH: d9cf5f0de0eca2572f67666fd7a8d21f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Reported content' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['report']['title']));
	$__finalCompiled .= '
';
	$__templater->pageParams['pageH1'] = $__templater->preEscaped($__templater->escape($__vars['report']['title']));
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Reports'), $__templater->func('link', array('reports', ), false), array(
	));
	$__finalCompiled .= '
';
	$__templater->includeCss('message.less');
	$__finalCompiled .= '
';
	$__templater->includeCss('report.less');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			<div class="block-row block-row--separated block-row--alt">
				' . $__templater->callMacro('member_list_macros', 'item', array(
		'user' => $__vars['report']['User'],
	), $__vars) . '
			</div>
			' . $__templater->filter($__templater->method($__vars['handler'], 'render', array($__vars['report'], )), array(array('raw', array()),), true) . '
		</div>
		';
	if ($__vars['report']['link']) {
		$__finalCompiled .= '
			<div class="block-footer">
				<span class="block-footer-controls">' . $__templater->button('Go to content', array(
			'href' => $__vars['report']['link'],
		), '', array(
		)) . '</span>
			</div>
		';
	}
	$__finalCompiled .= '
	</div>
</div>

<div class="blockMessage">
	<div class="pairJustifier">
		<dl class="pairs pairs--rows">
			<dt>' . 'Created' . '</dt>
			<dd>' . $__templater->func('date_dynamic', array($__vars['report']['first_report_date'], array(
	))) . '</dd>
		</dl>
		<dl class="pairs pairs--rows">
			<dt>' . 'Reports' . '</dt>
			<dd>' . $__templater->filter($__vars['report']['report_count'], array(array('number', array()),), true) . '</dd>
		</dl>
		<dl class="pairs pairs--rows">
			<dt>' . 'Comments' . '</dt>
			<dd>' . $__templater->filter($__vars['report']['comment_count'], array(array('number', array()),), true) . '</dd>
		</dl>
		<dl class="pairs pairs--rows">
			<dt>' . 'Status' . '</dt>
			<dd>
				' . $__templater->escape($__templater->method($__vars['report'], 'getReportState', array())) . '
				';
	if ($__vars['report']['report_state'] == 'assigned') {
		$__finalCompiled .= '
					' . $__templater->escape($__vars['xf']['language']['parenthesis_open']) . $__templater->func('username_link', array($__vars['report']['AssignedUser'], false, array(
		))) . $__templater->escape($__vars['xf']['language']['parenthesis_close']) . '
				';
	}
	$__finalCompiled .= '
			</dd>
		</dl>
		<dl class="pairs pairs--rows">
			<dt>' . 'Updated' . '</dt>
			<dd>' . $__templater->func('date_dynamic', array($__vars['report']['last_modified_date'], array(
	))) . '</dd>
		</dl>
	</div>
</div>

<div class="block">
	<div class="block-container">
		<h3 class="block-header">' . 'Comments' . '</h3>

		<div class="block-body">
			';
	if ($__templater->isTraversable($__vars['comments'])) {
		foreach ($__vars['comments'] AS $__vars['comment']) {
			$__finalCompiled .= '
				<article class="message message--simple">
					<div class="message-inner">
						<div class="message-cell message-cell--user">
							' . $__templater->callMacro('message_macros', 'user_info_simple', array(
				'user' => $__vars['comment']['User'],
				'fallbackName' => $__vars['comment']['username'],
			), $__vars) . '
						</div>
						<div class="message-cell message-cell--main">
							<span class="u-anchorTarget" id="report-comment-' . $__templater->escape($__vars['comment']['report_comment_id']) . '"></span>
							<div class="message-content">
								<header class="message-attribution message-attribution--plain">
									<ul class="listInline listInline--bullet">
										<li class="message-attribution-user">
											' . $__templater->func('avatar', array($__vars['comment']['User'], 'xxs', false, array(
			))) . '
											<h4 class="attribution">' . $__templater->func('username_link', array($__vars['comment']['User'], true, array(
			))) . '</h4>
										</li>
										<li><a href="#report-comment-' . $__templater->escape($__vars['comment']['report_comment_id']) . '" class="u-concealed">' . $__templater->func('date_dynamic', array($__vars['comment']['comment_date'], array(
			))) . '</a></li>
										';
			if ($__vars['comment']['is_report']) {
				$__finalCompiled .= '
											<li><span class="label label--accent">' . 'Report' . '</span></li>
										';
			}
			$__finalCompiled .= '
									</ul>
								</header>

								';
			if ($__vars['comment']['state_change']) {
				$__finalCompiled .= '
									<div class="message-minorHighlight">' . 'Status changed' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__templater->method($__vars['report'], 'getReportState', array($__vars['comment']['state_change'], ))) . '</div>
								';
			}
			$__finalCompiled .= '

								';
			if ($__vars['comment']['message']) {
				$__finalCompiled .= '
									<article class="message-body">
										' . $__templater->func('bb_code', array($__vars['comment']['message'], 'report_comment', $__vars['comment'], ), true) . '
									</article>
								';
			}
			$__finalCompiled .= '
							</div>
						</div>
					</div>
				</article>
			';
		}
	}
	$__finalCompiled .= '
		</div>
	</div>
</div>

<div class="block">
	<div class="block-container">

		<h2 class="block-tabHeader tabs" data-xf-init="tabs" role="tablist">
			<a class="tabs-tab is-active" role="tab" tabindex="0" aria-controls="' . $__templater->func('unique_id', array('reportUpdate', ), true) . '">' . 'Update' . '</a>
			<a class="tabs-tab" role="tab" tabindex="0" aria-controls="' . $__templater->func('unique_id', array('reportReassign', ), true) . '">' . 'Reassign' . '</a>
		</h2>

		<ul class="tabPanes">
			
			<li class="is-active" role="tabpanel" id="' . $__templater->func('unique_id', array('reportUpdate', ), true) . '">
				';
	$__vars['assignedToOther'] = (($__vars['report']['report_state'] == 'assigned') AND ($__vars['report']['AssignedUser'] AND ($__vars['report']['AssignedUser']['user_id'] != $__vars['xf']['visitor']['user_id'])));
	$__compilerTemp1 = '';
	if ($__vars['assignedToOther']) {
		$__compilerTemp1 .= '
							' . $__templater->formRow('
								' . 'This report is currently assigned to ' . $__templater->escape($__vars['report']['AssignedUser']['username']) . '.' . '
								<div>
									' . $__templater->button('
										' . 'Handle report' . '
									', array(
			'data-xf-click' => 'toggle',
			'data-target' => '.js-handleToggle',
			'data-hide' => '.js-handleToggleTrigger',
		), '', array(
		)) . '
								</div>
							', array(
			'rowclass' => 'js-handleToggleTrigger',
		)) . '
						';
	}
	$__compilerTemp2 = '';
	if ($__templater->method($__vars['report'], 'isClosed', array())) {
		$__compilerTemp2 .= 'Your comment will re-open this report.';
	}
	$__compilerTemp3 = '';
	if (!$__templater->method($__vars['report'], 'isClosed', array())) {
		$__compilerTemp3 .= '
								' . $__templater->formRadioRow(array(
			'name' => 'report_state',
		), array(array(
			'value' => '',
			'label' => 'Do not change',
			'selected' => true,
			'_type' => 'option',
		),
		array(
			'value' => 'assigned',
			'label' => (($__vars['report']['report_state'] == 'assigned') ? 'Unassign' : 'Assigned'),
			'_type' => 'option',
		),
		array(
			'value' => 'resolved',
			'label' => 'Resolved',
			'_type' => 'option',
		),
		array(
			'value' => 'rejected',
			'label' => 'Rejected',
			'_type' => 'option',
		)), array(
			'label' => 'Status',
		)) . '

								' . $__templater->formCheckBoxRow(array(
		), array(array(
			'name' => 'send_alert',
			'label' => 'Send resolution/rejection alert' . $__vars['xf']['language']['label_separator'],
			'_dependent' => array('
											' . $__templater->formTextBox(array(
			'name' => 'alert_comment',
			'maxlength' => '300',
		)) . '
										'),
			'_type' => 'option',
		)), array(
			'explain' => 'Any resolution/rejection alert text will be appended automatically to the current comment.',
		)) . '
							';
	}
	$__finalCompiled .= $__templater->form('
					<div class="block-body">
						' . '' . '

						' . $__compilerTemp1 . '

						<div class="js-handleToggle' . ((!$__vars['assignedToOther']) ? ' is-active' : '') . '">

							' . $__templater->formEditorRow(array(
		'name' => 'message',
		'value' => $__vars['report']['draft_comment']['message'],
		'data-min-height' => '100',
	), array(
		'rowtype' => 'fullWidth noLabel',
		'label' => 'Message',
		'explain' => '
									' . 'Your comment will only be visible to moderators.' . '
									' . $__compilerTemp2 . '
								',
	)) . '

							' . $__compilerTemp3 . '
						</div>
					</div>
					<div class="js-handleToggle' . ((!$__vars['assignedToOther']) ? ' is-active' : '') . '">
						' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
	)) . '
					</div>
				', array(
		'action' => $__templater->func('link', array('reports/update', $__vars['report'], ), false),
		'ajax' => 'true',
		'draft' => $__templater->func('link', array('reports/draft', $__vars['report'], ), false),
	)) . '
			</li>

			<li role="tabpanel" id="' . $__templater->func('unique_id', array('reportReassign', ), true) . '">
				';
	$__compilerTemp4 = array(array(
		'value' => '0',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'None' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['moderators'])) {
		foreach ($__vars['moderators'] AS $__vars['moderator']) {
			$__compilerTemp4[] = array(
				'value' => $__vars['moderator']['User']['user_id'],
				'label' => $__templater->escape($__vars['moderator']['User']['username']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
					<div class="block-body">
						' . $__templater->formSelectRow(array(
		'name' => 'user_id',
		'value' => $__vars['report']['assigned_user_id'],
	), $__compilerTemp4, array(
		'label' => 'Member',
	)) . '
					</div>
					' . $__templater->formSubmitRow(array(
		'submit' => 'Reassign',
	), array(
	)) . '
				', array(
		'action' => $__templater->func('link', array('reports/reassign', $__vars['report'], ), false),
		'ajax' => 'true',
	)) . '
			</li>

		</ul>
	</div>
</div>';
	return $__finalCompiled;
});