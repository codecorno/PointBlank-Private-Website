<?php
// FROM HASH: 634029c9c43777cafe348db13b76a886
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Import data');
	$__finalCompiled .= '

';
	if (!$__vars['xf']['addOns']['XFI']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important">
		' . 'XenForo importers are now installed as a separate add-on, downloadable from your <a href="https://xenforo.com/customers" target="_blank">Customer area</a>.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	if ($__vars['complete']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--success blockMessage--iconic">
		' . 'Your import has been completed successfully.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	if ($__vars['importersGrouped']) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = array();
		if ($__templater->isTraversable($__vars['importersGrouped'])) {
			foreach ($__vars['importersGrouped'] AS $__vars['target'] => $__vars['importers']) {
				$__compilerTemp1[] = array(
					'label' => 'Import target' . $__vars['xf']['language']['label_separator'] . ' ' . $__vars['target'],
					'_type' => 'optgroup',
					'options' => array(),
				);
				end($__compilerTemp1); $__compilerTemp2 = key($__compilerTemp1);
				if ($__templater->isTraversable($__vars['importers'])) {
					foreach ($__vars['importers'] AS $__vars['importer'] => $__vars['info']) {
						$__compilerTemp1[$__compilerTemp2]['options'][] = array(
							'value' => $__vars['importer'],
							'label' => $__templater->escape($__vars['info']['source']),
							'_type' => 'option',
						);
					}
				}
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-container">
			<div class="block-body">
				' . $__templater->formSelectRow(array(
			'name' => 'importer',
			'size' => '10',
		), $__compilerTemp1, array(
			'label' => 'Import from',
		)) . '
			</div>
			' . $__templater->formSubmitRow(array(
			'submit' => 'Continue' . $__vars['xf']['language']['ellipsis'],
		), array(
		)) . '
		</div>
	', array(
			'action' => $__templater->func('link', array('import/config', ), false),
			'class' => 'block',
		)) . '
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No importers are currently available.' . '</div>
';
	}
	return $__finalCompiled;
});