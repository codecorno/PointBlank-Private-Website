<?php

namespace XF\Import;

use XF\Import\Importer\AbstractImporter;

class Helper
{
	/**
	 * @var AbstractImporter
	 */
	protected $importer;

	public function __construct(AbstractImporter $importer)
	{
		$this->importer = $importer;
	}

	public function importUser($oldId, \XF\Import\Data\User $user, array $stepConfig)
	{
		$db = $this->importer->getDataManager()->db();

		$originalEmail = $user->email;

		if ($user->email && !strpos($user->email, '@'))
		{
			// if the email doesn't have a @, it isn't valid anyway, but this will break our conflict resolution
			$user->email = '';
		}

		if ($user->email)
		{
			$matchByEmail = $db->fetchOne("SELECT user_id FROM xf_user WHERE email = ?", $user->email);
			if ($matchByEmail)
			{
				if (!empty($stepConfig['merge_email']))
				{
					return $this->mergeUser($oldId, $user, $matchByEmail);
				}
				else
				{
					// not merging, so change the email and log it
					$i = 0;

					do
					{
						$i++;
						$replace = $i > 1 ? "+xf{$i}@" : '+xf@';
						$testEmail = str_replace('@', $replace, $originalEmail);

						$hasConflict = $db->fetchOne("SELECT user_id FROM xf_user WHERE email = ?", $testEmail);
					}
					while ($hasConflict);

					$user->email = $testEmail;
				}
			}
		}
		else
		{
			$matchByEmail = null;
		}

		$originalUsername = $user->username;

		if (strpos($user->username, ',') !== false)
		{
			$user->username = preg_replace('#,\s+#', ' ', $user->username);
			$user->username = str_replace(',', '', $user->username);
			if (!strlen($user->username))
			{
				$user->username = 'comma';
			}

			$hasComma = true;
		}
		else
		{
			$hasComma = false;
		}

		$matchByName = $db->fetchOne("SELECT user_id FROM xf_user WHERE username = ?", $user->username);
		if ($matchByName)
		{
			if (!empty($stepConfig['merge_name']))
			{
				return $this->mergeUser($oldId, $user, $matchByName);
			}
			else
			{
				// not merging, so change the name and log it
				$i = 0;

				do
				{
					$i++;
					$testUsername = $user->username . $i;

					$hasConflict = $db->fetchOne("SELECT user_id FROM xf_user WHERE username = ?", $testUsername);
				}
				while ($hasConflict);

				$user->username = $testUsername;
			}
		}

		$newId = $user->save($oldId);

		$session = $this->importer->getSession();
		if ($matchByEmail)
		{
			$session->notes['userEmailConflict'][$newId] = $originalEmail;
		}
		if ($matchByName)
		{
			$session->notes['userNameConflict'][$newId] = $originalUsername;
		}
		else if ($hasComma)
		{
			$session->notes['userNameConflict'][$newId] = $originalUsername;
		}

		return $newId;
	}

	protected function mergeUser($oldId, \XF\Import\Data\User $user, $targetUserId)
	{
		$mergedInto = $user->mergeFromInto($oldId, $targetUserId);

		return $mergedInto;
	}

	public function mapUserGroupList($userGroups)
	{
		if (is_string($userGroups))
		{
			if (!strlen($userGroups))
			{
				return [];
			}

			$userGroups = explode(',', $userGroups);
		}
		if (is_numeric($userGroups))
		{
			$userGroups = [$userGroups];
		}
		if (!is_array($userGroups))
		{
			throw new \InvalidArgumentException("Could not get user group array list");
		}

		$ids = $this->importer->lookup('user_group', $userGroups);
		foreach ($ids AS $k => $v)
		{
			if ($v === false)
			{
				unset($ids[$k]);
			}
		}
		return array_values($ids);
	}

	protected function getQuotesFromBbCode($content)
	{
		$quotes = [];

		// look for attributed quotes
		if (stripos($content, '[quote=') !== false)
		{
			if (preg_match_all('/\[quote=([^"\'\]]+|"[^"]*"|\'[^\']*\')+\]/siU', $content, $foundQuotes))
			{
				$quotes = $foundQuotes[0];
			}
		}

		return $quotes;
	}

	public function getQuotedContentIds($content, $quoteType)
	{
		$quotedPostIds = [];
		$quotes = $this->getQuotesFromBbCode($content);

		foreach ($quotes AS $quote)
		{
			// extract the post id
			if (preg_match("/\s{$quoteType}:\s+(\d+)/si", $quote, $postIdMatch))
			{
				$quotedPostIds[] = intval($postIdMatch[1]);
			}
		}

		return $quotedPostIds;
	}

	public function rewriteQuotesInBbCode($content, $quoteType, $contentType = null)
	{
		$replacements = [];
		$quotes = $this->getQuotesFromBbCode($content);

		foreach ($quotes AS $quote)
		{
			if (preg_match('/\[quote=(\'|"|)([^,]+),[^\]]+\1\]/si', $quote, $nameMatches))
			{
				$quotedName = $nameMatches[2];

				if (preg_match("/\s{$quoteType}:\s+(\d+)/si", $quote, $postIdMatch))
				{
					$contentId = $this->importer->lookup($contentType ?: $quoteType, $postIdMatch[1]);

					if ($contentId)
					{
						$replacements[$quote] = sprintf("[QUOTE='%s, {$quoteType}: %d']", $quotedName, $contentId);
					}
					else
					{
						$replacements[$quote] = sprintf('[QUOTE="%s"]', $quotedName);
					}
				}
				else
				{
					$replacements[$quote] = sprintf('[QUOTE="%s"]', $quotedName);
				}
			}
			else
			{
				$replacements[$quote] = '[QUOTE]';
			}
		}

		if ($replacements)
		{
			$content = str_replace(array_keys($replacements), $replacements, $content);
		}

		return $content;
	}

	protected function getMentionsFromBbCode($content)
	{
		$mentions = [];

		// look for mentions
		if (stripos($content, '[USER=') !== false)
		{
			if (preg_match_all('/\[USER=\d+\]/siU', $content, $foundMentions))
			{
				$mentions = $foundMentions[0];
			}
		}

		return $mentions;
	}

	public function getMentionedUserIds($content)
	{
		$mentionedUserIds = [];
		$mentions = $this->getMentionsFromBbCode($content);

		foreach ($mentions AS $mention)
		{
			// extract the user ID
			if (preg_match('/\[USER=(\d+)\]/si', $mention, $match))
			{
				$mentionedUserIds[] = intval($match[1]);
			}
		}

		return $mentionedUserIds;
	}

	public function rewriteMentionsInBbCode($content)
	{
		$replacements = [];
		$mentions = $this->getMentionsFromBbCode($content);

		foreach ($mentions AS $mention)
		{
			if (preg_match('/\[USER=(\d+)\]/si', $mention, $userIdMatch))
			{
				$userId = $this->importer->lookupId('user', $userIdMatch[1]);

				if (!$userId)
				{
					continue;
				}

				$replacements[$mention] = sprintf('[USER=%d]', $userId);
			}
		}

		if ($replacements)
		{
			$content = str_replace(array_keys($replacements), $replacements, $content);
		}

		return $content;
	}

	/**
	 * @deprecated
	 * @see \XF\Import\Importer\XenForoSourceTrait::setupCustomFieldImport()
	 *
	 * @param       $fieldType
	 * @param array $sourceData
	 *
	 * @return Data\AbstractField
	 */
	public function setupXfCustomFieldImport($fieldType, array $sourceData)
	{
		$data = $this->importer->mapKeys($sourceData, [
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
		$import = $this->importer->newHandler($fieldType);
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

		$import->match_params = $this->importer->decodeValue($sourceData['match_params'], 'json-array');
		$import->field_choices = $this->importer->decodeValue($sourceData['field_choices'], 'serialized-json-array');

		$description = isset($sourceData['description']) ? $sourceData['description'] : null;
		if (isset($sourceData['title']))
		{
			$import->setTitle($sourceData['title'], $description);
		}

		return $import;
	}
}