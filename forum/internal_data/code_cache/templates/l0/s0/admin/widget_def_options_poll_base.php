<?php
// FROM HASH: d7c714c06134cd903c803ddcbce2e054
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__vars['options']['poll_id']) {
		$__finalCompiled .= '

	' . $__templater->formTextBoxRow(array(
			'name' => 'options[content_url]',
			'type' => 'url',
		), array(
			'label' => 'Content URL',
			'explain' => 'Enter the URL of the content which currently has a poll attached to it.',
		)) . '

';
	} else if ($__vars['poll']) {
		$__finalCompiled .= '

	' . $__templater->formRow('
		<a href="' . $__templater->escape($__vars['contentLink']) . '" target="_blank">' . $__templater->escape($__vars['contentTitle']) . '</a>
	', array(
			'label' => 'Poll for content',
		)) . '

	' . $__templater->formHiddenVal('options[poll_id]', $__vars['poll']['poll_id'], array(
		)) . '

	' . $__templater->formCheckBoxRow(array(
		), array(array(
			'name' => 'options[change_url]',
			'label' => 'Change content URL' . $__vars['xf']['language']['label_separator'],
			'_dependent' => array('
				' . $__templater->formTextBox(array(
			'name' => 'options[content_url]',
			'type' => 'url',
		)) . '
			'),
			'_type' => 'option',
		)), array(
			'explain' => 'If you wish to change the current poll enter the URL of the content which currently has a poll attached to it.',
		)) . '

';
	} else {
		$__finalCompiled .= '

	' . $__templater->formRow('
		<div class="blockMessage blockMessage--error blockMessage--iconic">' . 'The poll for this content cannot be found. It may have been deleted. Please provide a new content URL below.' . '</div>
	', array(
		)) . '

	' . $__templater->formTextBoxRow(array(
			'name' => 'options[content_url]',
			'type' => 'url',
		), array(
			'label' => 'Content URL',
			'explain' => 'Enter the URL of the content which currently has a poll attached to it.',
		)) . '

';
	}
	return $__finalCompiled;
});