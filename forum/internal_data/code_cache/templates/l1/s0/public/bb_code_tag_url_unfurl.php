<?php
// FROM HASH: 70ab1dd7e3dfbcaa82193c5045c6cefe
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['result'], 'isBasicLink', array())) {
		$__finalCompiled .= '
	<div>
		<a href="' . $__templater->escape($__vars['result']['url']) . '"
			class="' . ($__templater->escape($__vars['linkInfo']['class']) ?: '') . '"
			target="' . ($__templater->escape($__vars['linkInfo']['target']) ?: '') . '"
			rel="' . ($__vars['rels'] ? $__templater->filter($__vars['rels'], array(array('join', array(' ', )),), true) : '') . '"
			data-proxy-href="' . ($__templater->escape($__vars['proxyUrl']) ?: '') . '">
			' . ($__templater->escape($__vars['result']['title']) ?: $__templater->escape($__vars['result']['url'])) . '
		</a>
	</div>
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->includeCss('bb_code.less');
		$__finalCompiled .= '

	<div class="bbCodeBlock bbCodeBlock--unfurl ' . ($__vars['result']['pending'] ? 'is-pending' : '') . ' ' . ($__vars['result']['is_recrawl'] ? 'is-recrawl' : '') . ' ' . ($__vars['simple'] ? 'is-simple' : '') . ' js-unfurl fauxBlockLink"
		data-unfurl="true" data-result-id="' . $__templater->escape($__vars['result']['result_id']) . '" data-url="' . $__templater->escape($__vars['result']['url']) . '" data-host="' . $__templater->escape($__vars['result']['host']) . '" data-pending="' . ($__vars['result']['pending'] ? 'true' : 'false') . '">
		<div class="contentRow">
			';
		if (($__vars['result']['pending'] OR $__vars['imageUrl']) AND (!$__vars['simple'])) {
			$__finalCompiled .= '
				<div class="contentRow-figure contentRow-figure--fixedSmall js-unfurl-figure">
					';
			if ($__vars['imageUrl']) {
				$__finalCompiled .= '
						<img src="' . $__templater->escape($__vars['imageUrl']) . '" alt="' . $__templater->escape($__vars['result']['host']) . '" data-onerror="hide-parent"/>
					';
			} else {
				$__finalCompiled .= '
						<span class="fa-2x u-muted">
							' . $__templater->fontAwesome('fa-spinner fa-pulse', array(
				)) . '
						</span>
					';
			}
			$__finalCompiled .= '
				</div>
			';
		}
		$__finalCompiled .= '
			<div class="contentRow-main">
				<h3 class="contentRow-header js-unfurl-title">
					<a href="' . $__templater->escape($__vars['result']['url']) . '"
						class="' . ($__templater->escape($__vars['linkInfo']['class']) ?: '') . ' fauxBlockLink-blockLink"
						target="' . ($__templater->escape($__vars['linkInfo']['target']) ?: '') . '"
						rel="' . ($__vars['rels'] ? $__templater->filter($__vars['rels'], array(array('join', array(' ', )),), true) : '') . '"
						data-proxy-href="' . ($__templater->escape($__vars['proxyUrl']) ?: '') . '">
						' . ($__templater->escape($__vars['result']['title']) ?: 'Loading' . $__vars['xf']['language']['ellipsis']) . '
					</a>
				</h3>

				<div class="contentRow-snippet js-unfurl-desc">' . $__templater->func('snippet', array($__vars['result']['description'], ($__vars['simple'] ? 50 : 300), ), true) . '</div>

				<div class="contentRow-minor contentRow-minor--hideLinks">
					<span class="js-unfurl-favicon">
						';
		if ($__vars['faviconUrl']) {
			$__finalCompiled .= '
							<img src="' . $__templater->escape($__vars['faviconUrl']) . '" alt="' . $__templater->escape($__vars['result']['host']) . '" class="bbCodeBlockUnfurl-icon"
								data-onerror="hide-parent"/>
						';
		}
		$__finalCompiled .= '
					</span>
					' . $__templater->escape($__vars['result']['host']) . '
				</div>
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});