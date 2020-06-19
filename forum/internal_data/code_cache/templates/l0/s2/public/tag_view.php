<?php
// FROM HASH: f254a63adae9a614b7599d774f8966af
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['tag']['tag']));
	$__templater->pageParams['pageNumber'] = $__vars['page'];
	$__finalCompiled .= '

';
	$__templater->breadcrumb($__templater->preEscaped('Tags'), $__templater->func('link', array('tags', ), false), array(
	));
	$__finalCompiled .= '

';
	if ($__vars['activeModType']) {
		$__finalCompiled .= '
	';
		$__templater->includeJs(array(
			'src' => 'xf/inline_mod.js',
			'min' => '1',
		));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

<div class="block" data-xf-init="' . ($__vars['activeModType'] ? 'inline-mod' : '') . '" data-type="' . $__templater->escape($__vars['activeModType']) . '" data-href="' . $__templater->func('link', array('inline-mod', ), true) . '">
	';
	if ($__vars['modTypes']) {
		$__finalCompiled .= '
		<div class="block-outer">
			<div class="block-outer-opposite">
				<div class="buttonGroup">
					';
		if ($__vars['activeModType']) {
			$__finalCompiled .= '
						' . $__templater->callMacro('inline_mod_macros', 'button', array(), $__vars) . '
					';
		}
		$__finalCompiled .= '
					<div class="buttonGroup-buttonWrapper">
						' . $__templater->button('Enable moderation', array(
			'class' => 'button--link menuTrigger',
			'data-xf-click' => 'menu',
			'aria-expanded' => 'false',
			'aria-haspopup' => 'true',
		), '', array(
		)) . '
						<div class="menu" data-menu="menu" aria-hidden="true">
							<div class="menu-content">
								<h3 class="menu-header">' . 'Enable moderation' . '</h3>
								';
		if ($__templater->isTraversable($__vars['modTypes'])) {
			foreach ($__vars['modTypes'] AS $__vars['type'] => $__vars['text']) {
				$__finalCompiled .= '
									<a href="' . $__templater->func('link', array('tags', $__vars['tag'], array('mod' => $__vars['type'], 'page' => $__vars['page'], ), ), true) . '" class="menu-linkRow ' . (($__vars['activeModType'] == $__vars['type']) ? 'is-selected' : '') . '">' . $__templater->escape($__vars['text']) . '</a>
								';
			}
		}
		$__finalCompiled .= '
								';
		if ($__vars['activeModType']) {
			$__finalCompiled .= '
									<hr class="menu-separator" />
									<a href="' . $__templater->func('link', array('tags', $__vars['tag'], array('page' => $__vars['page'], ), ), true) . '" class="menu-linkRow">' . 'Disable' . '</a>
								';
		}
		$__finalCompiled .= '
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	';
	}
	$__finalCompiled .= '

	<div class="block-container">
		<ol class="block-body">
			';
	if ($__templater->isTraversable($__vars['results'])) {
		foreach ($__vars['results'] AS $__vars['result']) {
			$__finalCompiled .= '
				' . $__templater->filter($__templater->method($__vars['result'], 'render', array(array('mod' => $__vars['activeModType'], ), )), array(array('raw', array()),), true) . '
			';
		}
	}
	$__finalCompiled .= '
		</ol>
	</div>
	<div class="block-outer block-outer--after">
		' . $__templater->func('page_nav', array(array(
		'page' => $__vars['page'],
		'total' => $__vars['totalResults'],
		'link' => 'tags',
		'data' => $__vars['tag'],
		'params' => array('mod' => $__vars['activeModType'], ),
		'wrapperclass' => 'block-outer-main',
		'perPage' => $__vars['perPage'],
	))) . '
		' . $__templater->func('show_ignored', array(array(
		'wrapperclass' => 'block-outer-opposite',
	))) . '
	</div>
</div>';
	return $__finalCompiled;
});