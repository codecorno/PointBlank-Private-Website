<?php

namespace XF\Pub\View\Member;

use XF\Mvc\View;

class WarnFill extends View
{
	public function renderJson()
	{
		/** @var \XF\Entity\WarningDefinition|null $definition */
		$definition = $this->params['definition'];

		if ($definition)
		{
			$hasConversation = $this->params['conversationTitle'] && $this->params['conversationMessage'];

			$formValues = [
				'#WarningEditableInput' => $definition->is_editable ? 1 : 0,
				'input[name=filled_warning_definition_id]' => $definition->warning_definition_id,
				'input[name=points_enable]' => $definition->points_default ? 1 : 0,
				'input[name=points]' => $definition->points_default ?: 1,
				'input[name=expiry_enable]' => $definition->expiry_type == 'never' ? 0 : 1,
				'input[name=expiry_value]' => $definition->expiry_default ?: 1,
				'select[name=expiry_unit]' => $definition->expiry_type == 'never' ? 'months' : $definition->expiry_type,
				'input[name=start_conversation]' => $hasConversation ? 1 : 0,
				'input[name=conversation_title]' => $this->params['conversationTitle'],
				'textarea[name=conversation_message]' => $this->params['conversationMessage'],
			];
		}
		else
		{
			$formValues = [
				'#WarningEditableInput' => 1,
				'input[name=filled_warning_definition_id]' => 0,
				'input[name=points_enable]' => 1,
				'input[name=points]' => 1,
				'input[name=expiry_enable]' => 1,
				'input[name=expiry_value]' => 1,
				'select[name=expiry_unit]' => 'months',
				'input[name=start_conversation]' => 0,
				'input[name=conversation_title]' => '',
				'textarea[name=conversation_message]' => '',
			];
		}

		return [
			'formValues' => $formValues
		];
	}
}