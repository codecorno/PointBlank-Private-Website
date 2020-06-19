<?php

namespace XF\Import\Importer;

use XF\Import\StepState;

trait XenForoSourceTrait
{
	protected function getBaseConfigDefault()
	{
		return [
			'db' => [
				'host' => '',
				'username' => '',
				'password' => '',
				'dbname' => '',
				'port' => 3306
			],
			'data_dir' => '',
			'internal_data_dir' => '',
			'forum_import_log' => ''
		];
	}

	abstract protected function validateVersion(\XF\Db\AbstractAdapter $db, &$error);

	protected function requiresForumImportLog()
	{
		return false;
	}

	protected function requiresDataPath()
	{
		return true;
	}

	protected function requiresInternalDataPath()
	{
		return true;
	}

	public function validateBaseConfig(array &$baseConfig, array &$errors)
	{
		$fullConfig = array_replace_recursive($this->getBaseConfigDefault(), $baseConfig);
		$missingFields = false;

		if ($fullConfig['db']['host'])
		{
			$validDbConnection = false;

			try
			{
				$db = new \XF\Db\Mysqli\Adapter($fullConfig['db'], false);
				$db->getConnection();
				$validDbConnection = true;
			}
			catch (\XF\Db\Exception $e)
			{
				$errors[] = \XF::phrase('source_database_connection_details_not_correct_x', ['message' => $e->getMessage()]);
			}

			if ($validDbConnection)
			{
				try
				{
					if (!$this->validateVersion($db, $versionError))
					{
						$errors[] = $versionError;
					}

					if ($this->requiresForumImportLog())
					{
						if ($fullConfig['forum_import_log'])
						{
							$logExists = $this->app->db()->getSchemaManager()->tableExists($fullConfig['forum_import_log']);
							if (!$logExists)
							{
								$errors[] = \XF::phrase('forum_import_log_cannot_be_found');
							}
						}
						else
						{
							$missingFields = true;
						}
					}
				}
				catch (\XF\Db\Exception $e)
				{
					if ($fullConfig['db']['dbname'] === '')
					{
						$errors[] = \XF::phrase('please_enter_database_name');
					}
					else
					{
						$errors[] = \XF::phrase('table_prefix_or_database_name_is_not_correct');
					}
				}
			}
		}
		else
		{
			$missingFields = true;
		}

		if ($this->requiresDataPath())
		{
			if ($fullConfig['data_dir'])
			{
				$data = rtrim($fullConfig['data_dir'], '/\\ ');

				if (!file_exists($data) || !is_dir($data))
				{
					$errors[] = \XF::phrase('directory_x_not_found_is_not_readable', ['dir' => $data]);
				}
				else if (!file_exists("$data/avatars") || !file_exists("$data/attachments"))
				{
					$errors[] = \XF::phrase('directory_x_does_not_contain_expected_contents', ['dir' => $data]);
				}

				$baseConfig['data_dir'] = $data; // to make sure it takes the format we expect
			}
			else
			{
				$missingFields = true;
			}
		}

		if ($this->requiresInternalDataPath())
		{
			if ($fullConfig['internal_data_dir'])
			{
				$internalData = rtrim($fullConfig['internal_data_dir'], '/\\ ');

				if (!file_exists($internalData) || !is_dir($internalData))
				{
					$errors[] = \XF::phrase('directory_x_not_found_is_not_readable', ['dir' => $internalData]);
				}
				else if (!file_exists("$internalData/install-lock.php"))
				{
					$errors[] = \XF::phrase('directory_x_does_not_contain_expected_contents', ['dir' => $internalData]);
				}

				$baseConfig['internal_data_dir'] = $internalData; // to make sure it takes the format we expect
			}
			else
			{
				$missingFields = true;
			}
		}

		if ($missingFields)
		{
			$errors[] = \XF::phrase('please_complete_required_fields');
		}

		return $errors ? false : true;
	}

	public function renderBaseConfigOptions(array $vars)
	{
		$vars['requiresDataPath'] = $this->requiresDataPath();
		$vars['requiresInternalDataPath'] = $this->requiresInternalDataPath();

		$vars['requiresForumImportLog'] = $this->requiresForumImportLog();

		return $this->app->templater()->renderTemplate('admin:import_config_xenforo_source', $vars);
	}

	protected function mapUserGroupList($userGroups)
	{
		return $this->getHelper()->mapUserGroupList($userGroups);
	}

	protected function mapCustomFields($importType, array $fieldValues)
	{
		$this->typeMap($importType);

		$importFields = [];
		foreach ($fieldValues AS $oldFieldId => $fieldValue)
		{
			$newFieldId = $this->lookupId($importType, $oldFieldId);
			if ($newFieldId)
			{
				$importFields[$newFieldId] = $fieldValue;
			}
		}

		return $importFields;
	}

	protected function setupCustomFieldImport($fieldType, array $sourceData)
	{
		$data = $this->mapKeys($sourceData, [
			'field_id',
			'display_group',
			'display_order',
			'field_type',
			'match_type',
			'max_length',
			'required',
			'user_editable',
			'moderator_editable',
			'display_template'
		], true);

		/** @var \XF\Import\Data\AbstractField $import */
		$import = $this->newHandler($fieldType);
		$import->bulkSet($data);

		if (isset($sourceData['editable_user_group_ids']))
		{
			if ($import->editable_user_group_ids == '-1')
			{
				$import->editable_user_group_ids = [-1];
			}
			else
			{
				$import->editable_user_group_ids = $this->mapUserGroupList($sourceData['editable_user_group_ids']);
			}
		}

		$import->match_params = $this->decodeValue($sourceData['match_params'], 'json-array');
		$import->field_choices = $this->decodeValue($sourceData['field_choices'], 'serialized-json-array');

		$description = isset($sourceData['description']) ? $sourceData['description'] : null;
		if (isset($sourceData['title']))
		{
			$import->setTitle($sourceData['title'], $description);
		}

		return $import;
	}

	protected function loadSourcePermissions($userGroupId, $userId)
	{
		$output = [];
		$results = $this->sourceDb->fetchAll("
			SELECT *
			FROM xf_permission_entry
			WHERE user_group_id = ?
				AND user_id = ?
		", [$userGroupId, $userId]);
		foreach ($results AS $result)
		{
			$value = $result['permission_value'];
			if ($value == 'use_int')
			{
				$value = $result['permission_value_int'];
			}

			$output[$result['permission_group_id']][$result['permission_id']] = $value;
		}

		return $output;
	}

	protected function extractDeletionLogData(array $data)
	{
		$deletionLog = [];
		foreach ($data AS $k => $v)
		{
			if ($v === null)
			{
				continue;
			}

			switch ($k)
			{
				case 'delete_date': $deletionLog['date'] = $v; break;
				case 'delete_user_id': $deletionLog['user_id'] = $v ? $this->lookupId('user', $v, 0) : 0; break;
				case 'delete_username': $deletionLog['username'] = $v; break;
				case 'delete_reason': $deletionLog['reason'] = $v; break;
			}
		}

		return $deletionLog;
	}

	protected function getSourceAttachmentDataPath($dataId, $filePath, $fileHash)
	{
		$group = floor($dataId / 1000);

		if ($filePath)
		{
			$placeholders = [
				'%INTERNAL%' => 'internal-data://', // for legacy
				'%DATA%' => 'data://', // for legacy
				'%DATA_ID%' => $dataId,
				'%FLOOR%' => $group,
				'%HASH%' => $fileHash
			];
			$path = strtr($filePath, $placeholders);
			$path = str_replace(':///', '://', $path); // writing %INTERNAL%/path would cause this
		}
		else
		{
			$path = sprintf('internal-data://attachments/%d/%d-%s.data',
				$group,
				$dataId,
				$fileHash
			);
		}

		return strtr($path, [
			'internal-data://' => $this->baseConfig['internal_data_dir'] . '/',
			'data://' => $this->baseConfig['data_dir'] . '/'
		]);
	}

	protected function rewriteQuotes($text, $importType = 'post')
	{
		if (stripos($text, '[quote=') === false)
		{
			return $text;
		}

		return preg_replace_callback(
			'/\[quote=("|\'|)(?P<username>[^,]*)\s*,\s*(?P<content_type>[^:]*):\s*(?P<content_id>\d+)\s*,\s*member:\s*(?P<user_id>\d+)\s*\1\]/siU',
			function ($match) use ($importType)
			{
				return sprintf('[QUOTE="%s, %s: %d, member: %d"]',
					$match['username'],
					$match['content_type'],
					$this->lookupId($importType, $match['content_id'], 0),
					$this->lookupId('user', $match['user_id'], 0)
				);
			},
			$text
		);
	}

	protected function getMaxLikeIdForContentTypes($contentTypes)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$contentTypesQuoted = $this->sourceDb->quote($contentTypes);

		$max = $this->sourceDb->fetchOne("
			SELECT MAX(like_id) FROM xf_liked_content WHERE content_type IN($contentTypesQuoted)
		");

		return intval($max);
	}

	protected function getLikesStepStateForContentTypes($contentTypes, StepState $state, array $stepConfig, $maxTime)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$contentTypesQuoted = $this->sourceDb->quote($contentTypes);

		$limit = 1000;
		$timer = new \XF\Timer($maxTime);

		$likes = $this->sourceDb->fetchAllKeyed("
			SELECT *
			FROM xf_liked_content
			WHERE like_id > ? AND like_id <= ?
				AND content_type IN ($contentTypesQuoted)
			ORDER BY like_id
			LIMIT {$limit}
		", 'like_id', [$state->startAfter, $state->end]);
		if (!$likes)
		{
			return $state->complete();
		}

		$mapUserIds = [];
		$mapContentIds = [];
		foreach ($likes AS $like)
		{
			$mapUserIds[] = $like['like_user_id'];
			$mapUserIds[] = $like['content_user_id'];
			$mapContentIds[$like['content_type']][] = $like['content_id'];
		}

		$this->lookup('user', array_unique($mapUserIds));

		foreach ($mapContentIds AS $contentType => $contentIds)
		{
			$this->lookup($contentType, array_unique($contentIds));
		}

		foreach ($likes AS $oldId => $like)
		{
			$state->startAfter = $oldId;

			$contentId = $this->lookupId($like['content_type'], $like['content_id']);
			if (!$contentId)
			{
				continue;
			}

			$likeUserId = $this->lookupId('user', $like['like_user_id']);
			if (!$likeUserId)
			{
				continue;
			}

			/** @var \XF\Import\Data\ReactionContent $import */
			$import = $this->newHandler('XF:ReactionContent');
			$import->bulkSet($this->mapKeys($like, [
				'content_type',
				'like_date' => 'reaction_date',
				'is_counted'
			]));
			$import->setReactionId(1);
			$import->content_id = $contentId;
			$import->reaction_user_id = $likeUserId;
			$import->content_user_id = $this->lookupId('user', $like['content_user_id'], 0);

			$newId = $import->save($oldId);
			if ($newId)
			{
				$state->imported++;
			}

			if ($timer->limitExceeded())
			{
				break;
			}
		}

		return $state->resumeIfNeeded();
	}

	protected function getMaxReactionContentIdForContentTypes($contentTypes)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$contentTypesQuoted = $this->sourceDb->quote($contentTypes);

		$max = $this->sourceDb->fetchOne("
			SELECT MAX(reaction_content_id) FROM xf_reaction_content WHERE content_type IN($contentTypesQuoted)
		");

		return intval($max);
	}

	protected function getReactionContentStepStateForContentTypes($contentTypes, StepState $state, array $stepConfig, $maxTime)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$contentTypesQuoted = $this->sourceDb->quote($contentTypes);

		$limit = 1000;
		$timer = new \XF\Timer($maxTime);

		$reactions = $this->sourceDb->fetchAllKeyed("
			SELECT *
			FROM xf_reaction_content
			WHERE reaction_content_id > ? AND reaction_content_id <= ?
				AND content_type IN ($contentTypesQuoted)
			ORDER BY reaction_content_id
			LIMIT {$limit}
		", 'reaction_content_id', [$state->startAfter, $state->end]);
		if (!$reactions)
		{
			return $state->complete();
		}

		$mapUserIds = [];
		$mapReactionIds = [];
		$mapContentIds = [];
		foreach ($reactions AS $reaction)
		{
			$mapUserIds[] = $reaction['reaction_user_id'];
			$mapUserIds[] = $reaction['content_user_id'];
			$mapReactionIds[] = $reaction['reaction_id'];
			$mapContentIds[$reaction['content_type']][] = $reaction['content_id'];
		}

		$this->lookup('user', array_unique($mapUserIds));
		$this->lookup('reaction', array_unique($mapReactionIds));

		foreach ($mapContentIds AS $contentType => $contentIds)
		{
			$this->lookup($contentType, array_unique($contentIds));
		}

		foreach ($reactions AS $oldId => $reaction)
		{
			$state->startAfter = $oldId;

			$contentId = $this->lookupId($reaction['content_type'], $reaction['content_id']);
			if (!$contentId)
			{
				continue;
			}

			$reactionUserId = $this->lookupId('user', $reaction['reaction_user_id']);
			if (!$reactionUserId)
			{
				continue;
			}

			$reactionId = $this->lookupId('reaction', $reaction['reaction_id']);
			if (!$reactionId)
			{
				continue;
			}

			/** @var \XF\Import\Data\ReactionContent $import */
			$import = $this->newHandler('XF:ReactionContent');
			$import->bulkSet($this->mapKeys($reaction, [
				'content_type',
				'reaction_date',
				'is_counted'
			]));
			$import->setReactionId($reactionId);
			$import->content_id = $contentId;
			$import->reaction_user_id = $reactionUserId;
			$import->content_user_id = $this->lookupId('user', $reaction['content_user_id'], 0);

			$newId = $import->save($oldId);
			if ($newId)
			{
				$state->imported++;
			}

			if ($timer->limitExceeded())
			{
				break;
			}
		}

		return $state->resumeIfNeeded();
	}

	protected function getMaxTagContentIdForContentTypes($contentTypes)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$contentTypesQuoted = $this->sourceDb->quote($contentTypes);

		$max = $this->sourceDb->fetchOne("
			SELECT MAX(tag_content_id) FROM xf_tag_content WHERE content_type IN($contentTypesQuoted)
		");

		return intval($max);
	}

	protected function getTagsStepStateForContentTypes($contentTypes, StepState $state, array $stepConfig, $maxTime)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$contentTypesQuoted = $this->sourceDb->quote($contentTypes);

		$limit = 1000;
		$timer = new \XF\Timer($maxTime);

		$tags = $this->sourceDb->fetchAllKeyed("
			SELECT tc.*,
				t.*
			FROM xf_tag_content AS tc
			INNER JOIN xf_tag AS t ON (tc.tag_id = t.tag_id)
			WHERE tc.tag_content_id > ? AND tc.tag_content_id <= ?
				AND tc.content_type IN ($contentTypesQuoted)
			ORDER BY tc.tag_content_id
			LIMIT {$limit}
		", 'tag_content_id', [$state->startAfter, $state->end]);
		if (!$tags)
		{
			return $state->complete();
		}

		$mapUserIds = [];
		$mapContentIds = [];
		foreach ($tags AS $tag)
		{
			$mapUserIds[] = $tag['add_user_id'];
			$mapContentIds[$tag['content_type']][] = $tag['content_id'];
		}

		$this->lookup('user', array_unique($mapUserIds));

		foreach ($mapContentIds AS $contentType => $contentIds)
		{
			$this->lookup($contentType, array_unique($contentIds));
		}

		/** @var \XF\Import\DataHelper\Tag $tagHelper */
		$tagHelper = $this->getDataHelper('XF:Tag');

		foreach ($tags AS $oldId => $tag)
		{
			$state->startAfter = $oldId;

			$contentId = $this->lookupId($tag['content_type'], $tag['content_id']);
			if (!$contentId)
			{
				continue;
			}

			$contentExtra = [
				'add_user_id' => $this->lookupId('user', $tag['add_user_id'], 0),
				'add_date' => $tag['add_date'],
				'visible' => $tag['visible'],
				'content_date' => $tag['content_date']
			];
			$tagExtra = [
				'tag_url' => $tag['tag_url'],
				'permanent' => $tag['permanent']
			];

			$newId = $tagHelper->importTag($tag['tag'], $tag['content_type'], $contentId, $contentExtra, $tagExtra);
			if ($newId)
			{
				$state->imported++;
			}

			if ($timer->limitExceeded())
			{
				break;
			}
		}

		return $state->resumeIfNeeded();
	}

	protected function getMaxBookmarkIdForContentTypes($contentTypes)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$contentTypesQuoted = $this->sourceDb->quote($contentTypes);

		$max = $this->sourceDb->fetchOne("
			SELECT MAX(bookmark_id) FROM xf_bookmark_item WHERE content_type IN($contentTypesQuoted)
		");

		return intval($max);
	}

	protected function getBookmarksStepStateForContentTypes($contentTypes, StepState $state, array $stepConfig, $maxTime)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$contentTypesQuoted = $this->sourceDb->quote($contentTypes);

		$limit = 1000;
		$timer = new \XF\Timer($maxTime);

		$bookmarks = $this->sourceDb->fetchAllKeyed("
			SELECT *
			FROM xf_bookmark_item
			WHERE bookmark_id > ? AND bookmark_id <= ?
				AND content_type IN ($contentTypesQuoted)
			ORDER BY bookmark_id
			LIMIT {$limit}
		", 'bookmark_id', [$state->startAfter, $state->end]);
		if (!$bookmarks)
		{
			return $state->complete();
		}

		$mapUserIds = [];
		$mapContentIds = [];
		foreach ($bookmarks AS $bookmark)
		{
			$mapUserIds[] = $bookmark['user_id'];
			$mapContentIds[$bookmark['content_type']][] = $bookmark['content_id'];
		}

		$this->lookup('user', array_unique($mapUserIds));

		foreach ($mapContentIds AS $contentType => $contentIds)
		{
			$this->lookup($contentType, array_unique($contentIds));
		}

		/** @var \XF\Import\DataHelper\BookmarkLabel $labelHelper */
		$labelHelper = $this->getDataHelper('XF:BookmarkLabel');

		foreach ($bookmarks AS $oldId => $bookmark)
		{
			$state->startAfter = $oldId;

			$contentId = $this->lookupId($bookmark['content_type'], $bookmark['content_id']);
			if (!$contentId)
			{
				continue;
			}

			$userId = $this->lookupId('user', $bookmark['user_id']);
			if (!$userId)
			{
				continue;
			}

			/** @var \XF\Import\Data\BookmarkItem $import */
			$import = $this->newHandler('XF:BookmarkItem');
			$import->bulkSet($this->mapKeys($bookmark, [
				'content_type',
				'bookmark_date',
				'message'
			]));
			$import->content_id = $contentId;
			$import->user_id = $userId;

			$newId = $import->save($oldId);

			$labels = $this->decodeValue($bookmark['labels'], 'json-array');
			if ($labels)
			{
				foreach (array_keys($labels) AS $labelId)
				{
					$labelUse = $this->sourceDb->fetchRow("
						SELECT lu.*, l.*
						FROM xf_bookmark_label_use AS lu
						INNER JOIN xf_bookmark_label AS l ON
							(lu.label_id = l.label_id)
						WHERE lu.label_id = ?
							AND lu.bookmark_id = ?
						ORDER BY lu.label_id
					", [$labelId, $oldId]);

					$labelUseExtra = [
						'use_date' => $labelUse['use_date']
					];
					$labelExtra = [
						'label_url' => $labelUse['label_url']
					];

					$labelHelper->importLabel($labelUse['label'], $newId, $userId, $labelUseExtra, $labelExtra);
				}
			}

			if ($newId)
			{
				$state->imported++;
			}

			if ($timer->limitExceeded())
			{
				break;
			}
		}

		return $state->resumeIfNeeded();
	}
}