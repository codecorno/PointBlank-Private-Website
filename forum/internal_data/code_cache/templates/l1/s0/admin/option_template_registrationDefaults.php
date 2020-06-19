<?php
// FROM HASH: 5f291ba1c70b00403c031136023cef24
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formRow('

	' . $__templater->formCheckBox(array(
	), array(array(
		'name' => $__vars['inputName'] . '[visible]',
		'selected' => $__vars['option']['option_value']['visible'],
		'label' => 'Show online status',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[activity_visible]',
		'selected' => $__vars['option']['option_value']['activity_visible'],
		'label' => 'Show current activity',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[content_show_signature]',
		'selected' => $__vars['option']['option_value']['content_show_signature'],
		'label' => 'Show signatures with messages',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[show_dob_date]',
		'selected' => $__vars['option']['option_value']['show_dob_date'],
		'label' => 'Show day and month of birth',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[show_dob_year]',
		'selected' => $__vars['option']['option_value']['show_dob_year'],
		'label' => 'Show year of birth',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[receive_admin_email]',
		'selected' => $__vars['option']['option_value']['receive_admin_email'],
		'label' => 'Receive news and update emails',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[email_on_conversation]',
		'selected' => $__vars['option']['option_value']['email_on_conversation'],
		'label' => 'Receive email when a new conversation message is received',
		'_type' => 'option',
	))) . '
	<div class="u-inputSpacer">
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_dws">' . 'Watch content on creation' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[creation_watch_state]',
		'value' => $__vars['option']['option_value']['creation_watch_state'],
		'id' => $__vars['inputName'] . '_dws',
	), array(array(
		'value' => 'watch_no_email',
		'label' => 'Yes',
		'_type' => 'option',
	),
	array(
		'value' => 'watch_email',
		'label' => 'Yes, with email',
		'_type' => 'option',
	),
	array(
		'label' => 'No',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_dws">' . 'Watch content on interaction' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[interaction_watch_state]',
		'value' => $__vars['option']['option_value']['interaction_watch_state'],
		'id' => $__vars['inputName'] . '_dws',
	), array(array(
		'value' => 'watch_no_email',
		'label' => 'Yes',
		'_type' => 'option',
	),
	array(
		'value' => 'watch_email',
		'label' => 'Yes, with email',
		'_type' => 'option',
	),
	array(
		'label' => 'No',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_avp">' . 'View this user\'s profile page details' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_view_profile]',
		'value' => $__vars['option']['option_value']['allow_view_profile'],
		'id' => $__vars['inputName'] . '_avp',
	), array(array(
		'value' => 'everyone',
		'label' => 'All visitors',
		'_type' => 'option',
	),
	array(
		'value' => 'members',
		'label' => 'Members only',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Followed members only',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Nobody',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_app">' . 'Post messages on this user\'s profile page' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_post_profile]',
		'value' => $__vars['option']['option_value']['allow_post_profile'],
		'id' => $__vars['inputName'] . '_app',
	), array(array(
		'value' => 'members',
		'label' => 'Members only',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Followed members only',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Nobody',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_arnf">' . 'Receive this user\'s news feed' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_receive_news_feed]',
		'value' => $__vars['option']['option_value']['allow_receive_news_feed'],
		'id' => $__vars['inputName'] . '_arnf',
	), array(array(
		'value' => 'everyone',
		'label' => 'All visitors',
		'_type' => 'option',
	),
	array(
		'value' => 'members',
		'label' => 'Members only',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Followed members only',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Nobody',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_aspc">' . 'Initiate conversations with this user' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_send_personal_conversation]',
		'value' => $__vars['option']['option_value']['allow_send_personal_conversation'],
		'id' => $__vars['inputName'] . '_aspc',
	), array(array(
		'value' => 'members',
		'label' => 'Members only',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Followed members only',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Nobody',
		'_type' => 'option',
	))) . '</dd>
		</dl>
		<dl class="inputLabelPair">
			<dt><label for="' . $__templater->escape($__vars['inputName']) . '_avi">' . 'View this user\'s identities' . '</label></dt>
			<dd>' . $__templater->formSelect(array(
		'name' => $__vars['inputName'] . '[allow_view_identities]',
		'id' => $__vars['inputName'] . '_avi',
		'value' => $__vars['option']['option_value']['allow_view_identities'],
	), array(array(
		'value' => 'everyone',
		'label' => 'All visitors',
		'_type' => 'option',
	),
	array(
		'value' => 'members',
		'label' => 'Members only',
		'_type' => 'option',
	),
	array(
		'value' => 'followed',
		'label' => 'Followed members only',
		'_type' => 'option',
	),
	array(
		'value' => 'none',
		'label' => 'Nobody',
		'_type' => 'option',
	))) . '</dd>
		</dl>
	</div>
', array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});