<?php

namespace XF\Import\Importer;

abstract class AbstractCoreImporter extends AbstractImporter
{
	public function canRetainIds()
	{
		$db = $this->app->db();

		$maxUserId = $db->fetchOne("SELECT MAX(user_id) FROM xf_user");
		if ($maxUserId > 1)
		{
			return false;
		}

		$maxUserGroupId = $db->fetchOne("SELECT MAX(user_group_id) FROM xf_user_group");
		if ($maxUserGroupId > 4)
		{
			return false;
		}

		$maxConvId = $db->fetchOne("SELECT MAX(conversation_id) FROM xf_conversation_master");
		if ($maxConvId)
		{
			return false;
		}

		return true;
	}

	public function getFinalizeJobs(array $stepsRun)
	{
		return [
			'XF:User',
			'XF:PermissionRebuild',
			'XF:Conversation',
			'XF:ProfilePost'
		];
	}

	public function getFinalNotes(\XF\Import\Session $session, $context)
	{
		$notes = [];

		$colon = \XF::language()->label_separator;

		if (!empty($session->notes['userEmailConflict']))
		{
			$conflictUserIds = array_keys($session->notes['userEmailConflict']);
			$users = $this->db()->fetchAllKeyed("
				SELECT user_id, username, email
				FROM xf_user
				WHERE user_id IN (" . $this->db()->quote($conflictUserIds) . ")
				ORDER BY username
			", 'user_id');

			$emailConflicts = [];
			foreach ($users AS $userId => $user)
			{
				$emailConflicts[] = "$user[username]$colon " . \XF::phrase('email_changed_to_x', ['email' => $user['email']]);
			}

			if ($emailConflicts)
			{
				$notes[] = [
					'title' => \XF::phrase('email_conflicts'),
					'entries' => $emailConflicts
				];
			}
		}

		if (!empty($session->notes['userNameConflict']))
		{
			$conflictUserIds = array_keys($session->notes['userNameConflict']);
			$users = $this->db()->fetchAllKeyed("
				SELECT user_id, username
				FROM xf_user
				WHERE user_id IN (" . $this->db()->quote($conflictUserIds) . ")
				ORDER BY username
			", 'user_id');

			$nameConflicts = [];
			foreach ($users AS $userId => $user)
			{
				$nameConflicts[] = \XF::phrase('x_user_name_changed_to_x', [
					'oldName' => $session->notes['userNameConflict'][$userId],
					'newName' => $user['username'],
					'colon' => $colon
				]);
			}

			if ($nameConflicts)
			{
				$notes[] = [
					'title' => \XF::phrase('user_name_conflicts'),
					'entries' => $nameConflicts
				];
			}
		}

		return $notes;
	}

	protected function importUser($oldId, \XF\Import\Data\User $user, array $stepConfig)
	{
		return $this->getHelper()->importUser($oldId, $user, $stepConfig);
	}
}