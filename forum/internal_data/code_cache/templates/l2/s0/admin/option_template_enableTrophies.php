<?php
// FROM HASH: 3818d0d15c591cc66940e45de535ec36
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'],
		'selected' => $__vars['option']['option_value'],
		'class' => 'js-enableTrophies',
		'label' => $__templater->escape($__vars['option']['title']),
		'_type' => 'option',
	)), array(
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	)) . '

';
	$__templater->inlineJs('
	$(document).ready(function()
	{
		processCheckBox();

		$(\'.js-enableTrophies\').click(function()
		{
			processCheckBox();
		});

		function processCheckBox()
		{
			var $userTitle = $("input[name=\'options[userTitleLadderField]\']:checked"),
				$trophyOption = $(\'.js-trophy_points\'),
				$messageOption = $(\'.js-messages\');

			if ($(\'.js-enableTrophies\').is(\':checked\'))
			{
				$trophyOption.attr(\'disabled\', false);
			}
			else
			{
				$trophyOption.attr(\'disabled\', true);

				if ($userTitle.val() == \'trophy_points\')
				{
					$messageOption.prop(\'checked\', true);
				}
			}
		}
	});
');
	return $__finalCompiled;
});