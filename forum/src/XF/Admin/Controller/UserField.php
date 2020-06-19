<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;
use XF\Mvc\FormAction;

class UserField extends AbstractField
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('userField');
	}

	protected function getClassIdentifier()
	{
		return 'XF:UserField';
	}

	protected function getLinkPrefix()
	{
		return 'custom-user-fields';
	}

	protected function getTemplatePrefix()
	{
		return 'user_field';
	}

	protected function saveAdditionalData(FormAction $form, \XF\Entity\AbstractField $field)
	{
		$input = $this->filter([
			'show_registration' => 'bool',
			'viewable_profile' => 'bool',
			'viewable_message' => 'bool'
		]);

		$form->basicEntitySave($field, $input);

		return $form;
	}
}