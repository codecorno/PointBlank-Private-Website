<?php

namespace XF\Service\User;

class TempChange extends \XF\Service\AbstractService
{
	protected $validChangeRelations = ['Auth', 'Option', 'Profile', 'Privacy'];

	public function applyFieldChange(\XF\Entity\User $user, $changeKey, $field, $newValue, $expiryDate = null)
	{
		return $this->applyChange(
			$user, $changeKey, $expiryDate, function(\XF\Entity\User $user) use ($field, $newValue)
			{
				$changeEntField = $this->identifyFieldChangeEntity($user, $field);
				if (!$changeEntField)
				{
					throw new \InvalidArgumentException("Cannot change $field");
				}

				/** @var \XF\Mvc\Entity\Entity $changeEnt */
				list($changeEnt, $finalField) = $changeEntField;

				$oldestChange = $this->finder('XF:UserChangeTemp')->where([
					'user_id' => $user->user_id,
					'action_type' => 'field',
					'action_modifier' => $field
				])->order('create_date')->fetchOne();
				$oldValue = $oldestChange ? $oldestChange->old_value : $changeEnt->getValueSourceEncoded($finalField);

				$changeEnt->set($finalField, $newValue);
				if ($changeEnt->isChanged($finalField))
				{
					$changeEnt->save(false, false);
				}

				$newValue = $changeEnt->getValueSourceEncoded($finalField);

				return [
					'action_type' => 'field',
					'action_modifier' => $field,
					'new_value' => $newValue,
					'old_value' => $oldValue
				];
			}
		);
	}

	public function applyGroupChange(\XF\Entity\User $user, $changeKey, array $addGroups, $groupChangeKey = null, $expiryDate = null)
	{
		return $this->applyChange(
			$user, $changeKey, $expiryDate, function(\XF\Entity\User $user) use ($groupChangeKey, $addGroups)
			{
				if (!$groupChangeKey)
				{
					$groupChangeKey = 'user_change_' . substr(md5(uniqid()), 0, 16);
				}

				/** @var \XF\Service\User\UserGroupChange $changeService */
				$changeService = $this->service('XF:User\UserGroupChange');
				$changeService->addUserGroupChange($user->user_id, $groupChangeKey, $addGroups);

				return [
					'action_type' => 'groups',
					'action_modifier' => $groupChangeKey
				];
			}
		);
	}

	protected function applyChange(\XF\Entity\User $user, $changeKey, $expiryDate, \Closure $applier)
	{
		$this->db()->beginTransaction();

		if ($changeKey !== null)
		{
			$this->expireUserChangeByKey($user, $changeKey);
		}

		try
		{
			$applyResult = $applier($user);
		}
		catch (\Exception $e)
		{
			$this->db()->rollback();
			throw $e;
		}

		$values = array_merge([
			'user_id' => $user->user_id,
			'change_key' => $changeKey,
			'new_value' => null,
			'old_value' => null,
			'expiry_date' => $expiryDate
		], $applyResult);

		$change = $this->em()->instantiateEntity('XF:UserChangeTemp');
		$change->bulkSet($values);
		$change->save(true, false);

		$this->db()->commit();

		return $change;
	}

	public function expireUserChangeByKey(\XF\Entity\User $user, $changeKey)
	{
		/** @var \XF\Entity\UserChangeTemp|null $change */
		$change = $this->em()->findOne('XF:UserChangeTemp', ['user_id' => $user->user_id, 'change_key' => $changeKey]);
		if ($change)
		{
			return $this->expireChange($change);
		}

		return false;
	}

	public function expireChange(\XF\Entity\UserChangeTemp $change)
	{
		/** @var \XF\Entity\User $user */
		$user = $change->User;

		$this->em()->detachEntity($change);
		$success = $this->db()->delete('xf_user_change_temp', 'user_change_temp_id = ? ', $change->user_change_temp_id);
		if (!$success || !$user)
		{
			return false;
		}

		$this->db()->beginTransaction();

		switch ($change->action_type)
		{
			case 'groups':
				$groupChangeKey = $change->action_modifier;

				/** @var \XF\Service\User\UserGroupChange $changeService */
				$changeService = $this->service('XF:User\UserGroupChange');
				$changeService->removeUserGroupChange($user->user_id, $groupChangeKey);
				break;

			case 'field':
				$field = $change->action_modifier;

				$changeEntField = $this->identifyFieldChangeEntity($user, $field);
				if (!$changeEntField)
				{
					// We successfully inserted this, but it's not working now. This is probably related to
					// a field from an add-on. We need to just ignore it and carry on.
					break;
				}

				/** @var \XF\Mvc\Entity\Entity $changeEnt */
				list($changeEnt, $finalField) = $changeEntField;

				if ((string)$change->new_value !== (string)$changeEnt->getValueSourceEncoded($finalField))
				{
					// the field value has been changed from our programmatic change, can't do anything.
					// note we need to force string comparison as new_value is a blob field; might be comparing to int field
					break;
				}

				// we're either going to revert to the latest remaining programmatic change or back to what we
				// were at if that doesn't exist
				$newestChange = $this->finder('XF:UserChangeTemp')->where([
					'user_id' => $user->user_id,
					'action_type' => 'field',
					'action_modifier' => $field
				])->order('create_date', 'desc')->fetchOne();
				$revertValue = $newestChange ? $newestChange->new_value : $change->old_value;
				$changeEnt->setFromEncoded($finalField, $revertValue);
				if ($changeEnt->isChanged($finalField))
				{
					$changeEnt->save(false, false);
				}
				break;
		}

		$this->db()->commit();

		return true;
	}

	protected function identifyFieldChangeEntity(\XF\Entity\User $user, $changeField)
	{
		$parts = explode('.', $changeField);
		if (count($parts) == 1)
		{
			if ($user->isValidColumn($changeField))
			{
				return [$user, $changeField];
			}

			foreach ($this->validChangeRelations AS $relation)
			{
				$key = "{$relation}.{$changeField}";
				$valid = $this->identifyFieldChangeEntity($user, $key);
				if ($valid)
				{
					return $valid;
				}
			}

			return null;
		}
		else if (count($parts) == 2)
		{
			list($relation, $field) = $parts;
			if (!in_array($relation, $this->validChangeRelations))
			{
				throw new \InvalidArgumentException("Relation $relation is not a valid user change relation");
			}

			/** @var \XF\Mvc\Entity\Entity $subEnt */
			$subEnt = $user->{$relation};
			if ($subEnt && $subEnt->isValidColumn($field))
			{
				return [$subEnt, $field];
			}

			return null;
		}
		else
		{
			throw new \InvalidArgumentException("Change field $changeField is not valid, may only reference one relation");
		}
	}
}