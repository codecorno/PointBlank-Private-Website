<?php
// FROM HASH: 7b712b0fcc7c6b291957d734d557fdb7
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeCss('help_page_smilies.less');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['smilieCategories'])) {
		foreach ($__vars['smilieCategories'] AS $__vars['smilieCategoryId'] => $__vars['smilieCategory']) {
			$__compilerTemp1 .= '
					';
			if ($__vars['smilieCategoryId']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'rowclass' => 'dataList-row--subSection dataList-row--noHover',
				), array(array(
					'colspan' => '3',
					'_type' => 'cell',
					'html' => $__templater->escape($__vars['smilieCategory']['title']),
				))) . '
					';
			}
			$__compilerTemp1 .= '
					';
			if ($__templater->isTraversable($__vars['smilieCategory']['smilies'])) {
				foreach ($__vars['smilieCategory']['smilies'] AS $__vars['smilie']) {
					$__compilerTemp1 .= '
						';
					$__compilerTemp2 = '';
					if ($__templater->isTraversable($__vars['smilie']['smilie_text_options'])) {
						foreach ($__vars['smilie']['smilie_text_options'] AS $__vars['smilieText']) {
							$__compilerTemp2 .= '
									<span class="smilieText">' . $__templater->escape($__vars['smilieText']) . '</span>
								';
						}
					}
					$__compilerTemp1 .= $__templater->dataRow(array(
						'rowclass' => 'dataList-row--noHover',
					), array(array(
						'class' => 'dataList-cell--min dataList-cell--alt',
						'_type' => 'cell',
						'html' => '
								' . $__templater->func('smilie', array($__vars['smilie']['smilie_text_options']['0'], ), true) . '
							',
					),
					array(
						'label' => $__templater->escape($__vars['smilie']['title']),
						'_type' => 'main',
						'html' => '',
					),
					array(
						'_type' => 'cell',
						'html' => '
								<span>
								' . $__compilerTemp2 . '
								</span>
							',
					))) . '
					';
				}
			}
			$__compilerTemp1 .= '
				';
		}
	}
	$__finalCompiled .= $__templater->dataList('
				' . $__templater->dataRow(array(
		'rowtype' => 'header',
	), array(array(
		'class' => 'dataList-cell--min',
		'_type' => 'cell',
		'html' => 'Image',
	),
	array(
		'_type' => 'cell',
		'html' => 'Title',
	),
	array(
		'_type' => 'cell',
		'html' => 'Text',
	))) . '
				' . $__compilerTemp1 . '
			', array(
		'data-xf-init' => 'responsive-data-list',
	)) . '
		</div>
		<div class="block-footer">
			' . 'Emoji provided by <a href="https://www.joypixels.com" target="_blank">JoyPixels</a>.' . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});