<?php
// FROM HASH: b3be80314d0f10c7f40d940e7a6610a0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__templater->test($__vars['threads'], 'empty', array())) {
		$__finalCompiled .= '
	';
		if ($__vars['style'] != 'expanded') {
			$__finalCompiled .= '
		<div class="block"' . $__templater->func('widget_data', array($__vars['widget'], ), true) . '>
			<div class="block-container">
				';
			if ($__vars['style'] == 'full') {
				$__finalCompiled .= '
					<h3 class="block-header">
						<a href="' . $__templater->escape($__vars['link']) . '" rel="nofollow">' . $__templater->escape($__vars['title']) . '</a>
					</h3>
					<div class="block-body">
						<div class="structItemContainer">
							';
				if ($__templater->isTraversable($__vars['threads'])) {
					foreach ($__vars['threads'] AS $__vars['thread']) {
						$__finalCompiled .= '
								' . $__templater->callMacro('thread_list_macros', 'item', array(
							'allowInlineMod' => false,
							'thread' => $__vars['thread'],
						), $__vars) . '
							';
					}
				}
				$__finalCompiled .= '
						</div>
					</div>
				';
			} else {
				$__finalCompiled .= '
					<h3 class="block-minorHeader">
						<a href="' . $__templater->escape($__vars['link']) . '" rel="nofollow">' . $__templater->escape($__vars['title']) . '</a>
					</h3>
					<ul class="block-body">
						';
				if ($__templater->isTraversable($__vars['threads'])) {
					foreach ($__vars['threads'] AS $__vars['thread']) {
						$__finalCompiled .= '
							<li class="block-row">
								' . $__templater->callMacro('thread_list_macros', 'item_new_threads', array(
							'thread' => $__vars['thread'],
						), $__vars) . '
							</li>
						';
					}
				}
				$__finalCompiled .= '
					</ul>
				';
			}
			$__finalCompiled .= '
			</div>
		</div>
	';
		} else {
			$__finalCompiled .= '
		';
			$__templater->includeCss('message.less');
			$__finalCompiled .= '

		<div class="blocks">
			';
			if ($__vars['showExpandedTitle']) {
				$__finalCompiled .= '
				<h2 class="blocks-header">
					' . $__templater->escape($__vars['title']) . '
				</h2>
			';
			}
			$__finalCompiled .= '

			';
			if ($__templater->isTraversable($__vars['threads'])) {
				foreach ($__vars['threads'] AS $__vars['thread']) {
					$__finalCompiled .= '
				';
					$__vars['post'] = $__vars['thread']['FirstPost'];
					$__finalCompiled .= '

				<div class="block">
					<div class="block-container"
						data-xf-init="lightbox">

						<h4 class="block-header">
							<a href="' . $__templater->func('link', array('threads', $__vars['thread'], ), true) . '">' . $__templater->escape($__vars['thread']['title']) . '</a>
						</h4>
						<div class="block-body">
							<div class="message message--post">
								<div class="message-inner">
									<div class="message-cell message-cell--main">
										<div class="message-content js-messageContent">
											<header class="message-attribution">
												<ul class="listInline listInline--bullet u-muted">
													<li>' . $__templater->func('username_link', array($__vars['thread']['User'], false, array(
						'defaultname' => $__vars['thread']['username'],
					))) . '</li>
													<li>' . $__templater->func('date_dynamic', array($__vars['post']['post_date'], array(
					))) . '</li>
													<li><a href="' . $__templater->func('link', array('forums', $__vars['thread']['Forum'], ), true) . '">' . $__templater->escape($__vars['thread']['Forum']['title']) . '</a></li>
													<li>' . 'Replies' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->filter($__vars['thread']['reply_count'], array(array('number', array()),), true) . '</li>
												</ul>
											</header>
											<div class="message-userContent lbContainer js-lbContainer"
												data-lb-id="post-' . $__templater->escape($__vars['post']['post_id']) . '"
												data-lb-caption-desc="' . ($__vars['post']['User'] ? $__templater->escape($__vars['post']['User']['username']) : $__templater->escape($__vars['post']['username'])) . ' &middot; ' . $__templater->func('date_time', array($__vars['post']['post_date'], ), true) . '">

												<article class="message-body">
													' . $__templater->func('bb_code', array($__vars['post']['message'], 'post', $__vars['post'], ), true) . '
												</article>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			';
				}
			}
			$__finalCompiled .= '
		</div>
	';
		}
		$__finalCompiled .= '
';
	}
	return $__finalCompiled;
});