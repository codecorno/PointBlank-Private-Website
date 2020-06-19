<?php

namespace XF\Service\User;

use XF\Service\AbstractXmlImport;

class Import extends AbstractXmlImport
{
	public function import(\SimpleXMLElement $xml)
	{
		$xmlUser = $xml->user[0];

		$username = (string)$xmlUser['username'];
		$email = (string)$xmlUser['email'];

		if ($this->finder('XF:User')->where('username', $username)->fetchOne()
			|| $this->finder('XF:User')->where('email', $email)->fetchOne()
		)
		{
			throw new \XF\PrintableException(\XF::phrase('user_with_this_username_and_or_email_already_exists'));
		}

		$inputFilterer = $this->app->inputFilterer();

		/** @var \XF\Entity\User $user */
		$user = $this->repository('XF:User')->setupBaseUser();

		$input = [
			'user' => [
				'username' => $username,
				'email' => $email
			],
			'profile' => []
		];

		foreach ($this->getUserFields() AS $field)
		{
			if (isset($xmlUser[$field]))
			{
				$input['user'][$field] = (string)$xmlUser[$field];
			}
		}

		$user->bulkSet($inputFilterer->cleanArrayStrings($input['user']));

		$profile = $user->Profile;

		foreach ($this->getUserProfileFields() AS $field)
		{
			if (isset($xmlUser[$field]))
			{
				$input['profile'][$field] = (string)$xmlUser[$field];
			}
		}

		foreach ($this->getUserProfileElements() AS $field)
		{
			$input['profile'][$field] = (string)$xmlUser->$field;
		}

		$profile->bulkSet($inputFilterer->cleanArrayStrings($input['profile']));

		$auth = $user->Auth;
		$auth->resetPassword();

		/** @var \XF\CustomField\DefinitionSet $definitionSet */
		$definitionSet = $this->app->container('customFields.users');
		$fieldDefinitions = $definitionSet->getFieldDefinitions();

		$xmlCustomFields = $xmlUser->custom_fields;

		$customFields = [];
		foreach (array_keys($fieldDefinitions) AS $fieldId)
		{
			if (preg_match('#^\d#', $fieldId))
			{
				$xmlFieldId = '__' . $fieldId;
			}
			else
			{
				$xmlFieldId = $fieldId;
			}

			if (isset($xmlCustomFields->$xmlFieldId))
			{
				$customField = $xmlCustomFields->$xmlFieldId;

				if ((string)$customField['array'])
				{
					$customFields[$fieldId] = json_decode((string)$customField, true);
				}
				else
				{
					$customFields[$fieldId] = (string)$customField;
				}
			}
		}

		$profile->custom_fields = $inputFilterer->cleanArrayStrings($customFields);

		if (!$user->preSave())
		{
			throw new \XF\PrintableException($user->getErrors());
		}

		if ($user->save(false))
		{
			/** @var \XF\Service\User\PasswordReset $passwordReset */
			$passwordReset = $this->service('XF:User\PasswordReset', $user);
			$passwordReset->setAdminReset(true);
			$passwordReset->triggerConfirmation();
		}
	}

	protected function getUserFields()
	{
		return [
			'gravatar', 'timezone'
		];
	}

	protected function getUserProfileFields()
	{
		return [
			'dob_day', 'dob_month', 'dob_year',
			'website', 'location'
		];
	}

	protected function getUserProfileElements()
	{
		return [
			'signature', 'about'
		];
	}
}