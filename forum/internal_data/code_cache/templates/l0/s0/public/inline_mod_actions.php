<?php
// FROM HASH: a964e3ba84aabcceabb7d2de57d18d0a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="inlineModBar">
	<div class="inlineModBar-inner">
		<div class="inlineModBar-close">
			' . $__templater->button('X', array(
		'class' => 'js-inlineModClose',
	), '', array(
	)) . '
		</div>

		<ul class="inlineModBar-controls">
			<li class="inlineModBar-title">' . $__templater->escape($__vars['title']) . ' ' . $__templater->filter($__vars['total'], array(array('number', array()),array('parens', array()),), true) . '</li>
			<li>' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'labelclass' => 'iconic--inherit-color',
		'class' => 'js-inlineModSelectAll',
		'label' => 'Select all',
		'_type' => 'option',
	))) . '</li>
			<li>
				<select class="input input--inline js-inlineModAction">
					<optgroup>
						<option value="" hidden="hidden">' . 'Choose action' . $__vars['xf']['language']['ellipsis'] . '</option>
						';
	if ($__templater->isTraversable($__vars['actions'])) {
		foreach ($__vars['actions'] AS $__vars['id'] => $__vars['action']) {
			$__finalCompiled .= '
							<option value="' . $__templater->escape($__vars['id']) . '">' . $__templater->escape($__vars['action']) . '</option>
						';
		}
	}
	$__finalCompiled .= '
					</optgroup>
					';
	if (!$__vars['actions']['deselect']) {
		$__finalCompiled .= '
						<optgroup>
							<option value="deselect">' . 'Deselect all' . '</option>
						</optgroup>
					';
	}
	$__finalCompiled .= '
				</select>
				' . $__templater->button('Go', array(
		'type' => 'submit',
		'class' => 'button--primary inlineModBar-goButton',
	), '', array(
	)) . '
			</li>
		</ul>
	</div>
</div>';
	return $__finalCompiled;
});