<?php
// FROM HASH: e496f3646e694414d547931f18501849
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Seletor de estilos');
	$__finalCompiled .= '

';
	$__templater->setPageParam('head.' . 'metaNoindex', $__templater->preEscaped('<meta name="robots" content="noindex" />'));
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		';
	if ($__vars['style']) {
		$__finalCompiled .= '
			<div class="block-body">
				' . $__templater->formInfoRow('
					' . 'Please confirm that you would like to change to the following style' . $__vars['xf']['language']['label_separator'] . '
					<strong>' . $__templater->escape($__vars['style']['title']) . '</strong>
				', array(
			'rowtype' => 'confirm',
		)) . '
			</div>

			' . $__templater->formSubmitRow(array(
		), array(
			'rowtype' => 'simple',
			'html' => '
				' . $__templater->button('', array(
			'href' => $__templater->func('link', array('misc/style', null, array('style_id' => $__vars['style']['style_id'], 't' => $__templater->func('csrf_token', array(), false), '_xfRedirect' => $__vars['redirect'], ), ), false),
			'class' => 'button--primary',
			'icon' => 'save',
		), '', array(
		)) . '
			',
		)) . '
		';
	} else {
		$__finalCompiled .= '
			<a href="' . $__templater->func('link', array('misc/style', null, array('style_id' => 0, '_xfRedirect' => $__vars['redirect'], 't' => $__templater->func('csrf_token', array(), false), ), ), true) . '" class="menu-linkRow menu-linkRow--alt">' . 'Usar estilo padrão' . '</a>
			<div class="block-body">
				<ul class="listPlain listColumns listColumns--together">
					';
		if ($__templater->isTraversable($__vars['styles'])) {
			foreach ($__vars['styles'] AS $__vars['style']) {
				$__finalCompiled .= '
						<li>
							<a href="' . $__templater->func('link', array('misc/style', null, array('style_id' => $__vars['style']['style_id'], '_xfRedirect' => $__vars['redirect'], 't' => $__templater->func('csrf_token', array(), false), ), ), true) . '" class="menu-linkRow">' . $__templater->escape($__vars['style']['title']) . ((!$__vars['style']['user_selectable']) ? ' *' : '') . '</a>
						</li>
					';
			}
		}
		$__finalCompiled .= '
				</ul>
			</div>
		';
	}
	$__finalCompiled .= '
	</div>
</div>';
	return $__finalCompiled;
});