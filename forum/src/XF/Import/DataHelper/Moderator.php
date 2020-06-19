<?php

namespace XF\Import\DataHelper;

class Moderator extends AbstractHelper
{
	public function importModerator($userId, $isSuperMod, array $extraGroupIds = [], array $permissionsGrouped = [])
	{
		$db = $this->db();
		$db->beginTransaction();

		$db->insert('xf_moderator', [
			'user_id' => $userId,
			'is_super_moderator' => $isSuperMod ? 1 : 0,
			'extra_user_group_ids' => implode(',', $extraGroupIds)
		], false, 'is_super_moderator = VALUES(is_super_moderator), extra_user_group_ids = VALUES(extra_user_group_ids)');

		if ($extraGroupIds)
		{
			$db->insert('xf_user_group_change', [
				'user_id' => $userId,
				'change_key' => 'moderator',
				'group_ids' => implode(',', $extraGroupIds)
			], false, 'group_ids = VALUES(group_ids)');
		}

		$db->update('xf_user', ['is_moderator' => 1], 'user_id = ?', $userId);

		if ($permissionsGrouped)
		{
			/** @var \XF\Import\DataHelper\Permission $permHelper */
			$permHelper = $this->dataManager->helper('XF:Permission');
			$permHelper->insertUserPermissions($userId, $permissionsGrouped);
		}

		$db->commit();
	}

	public function importContentModerator($userId, $contentType, $contentId, array $permissionsGrouped = [])
	{
		$db = $this->db();
		$db->beginTransaction();

		$rowsInserted = $db->insert('xf_moderator_content', [
			'content_type' => $contentType,
			'content_id' => $contentId,
			'user_id' => $userId
		], false, false, 'IGNORE');

		if ($rowsInserted)
		{
			$this->db()->insert('xf_moderator', [
				'user_id' => $userId,
				'is_super_moderator' => 0,
				'extra_user_group_ids' => ''
			], false, false, 'IGNORE');

			$db->update('xf_user', ['is_moderator' => 1], 'user_id = ?', $userId);
		}

		if ($permissionsGrouped)
		{
			/** @var \XF\Import\DataHelper\Permission $permHelper */
			$permHelper = $this->dataManager->helper('XF:Permission');
			$permHelper->insertContentUserPermissions($contentType, $contentId, $userId, $permissionsGrouped);
		}

		$db->commit();
	}

	public function importContentModeratorsRaw($userId, $contentType, $contentIds)
	{
		if (!is_array($contentIds))
		{
			$contentIds = [];
		}

		$insert = [];
		foreach ($contentIds AS $contentId)
		{
			$insert[] = [
				'content_type' => $contentType,
				'content_id' => $contentId,
				'user_id' => $userId
			];
		}

		if ($insert)
		{
			$this->db()->insertBulk('xf_moderator_content', $insert, false, false, 'IGNORE');
		}
	}
}