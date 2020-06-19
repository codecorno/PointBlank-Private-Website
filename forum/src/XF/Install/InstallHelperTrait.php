<?php

namespace XF\Install;

use XF\Db\Schema\Alter;

/**
 * Class InstallHelperTrait
 *
 * The methods here are designed to be called by upgrade steps for various uses. This is a trait so it can be shared
 * with both the core software installer and add-on setup classes.
 *
 * @package XF\Install
 */
trait InstallHelperTrait
{
	/**
	 * @var \XF\App
	 */
	protected $app;

	/**
	 * Convert all SERIALIZED data controlled by the entity named in $entityName to JSON.
	 * Serialized columns are identified in $columns.
	 *
	 * @param       $entityName
	 * @param array $columns
	 * @param       $position
	 * @param array $stepData
	 * @param bool $singlePass
	 *
	 * @return array
	 */
	public function entityColumnsToJson($entityName, array $columns, $position, array $stepData, $singlePass = false, $perPage = 1000)
	{
		$class = \XF::stringToClass($entityName, '%s\Entity\%s');

		$structure = $class::getStructure(new \XF\Mvc\Entity\Structure());

		$primaryKeys = is_array($structure->primaryKey) ? $structure->primaryKey : [$structure->primaryKey];

		$regex = '^([abCdioOsS]:|N;$)';
		return $this->tableColumnsToJson($structure->table, $primaryKeys, $columns, $position, $stepData, $singlePass, $regex, $perPage);
	}

	/**
	 * Convert all SERIALIZED data in $tableName to JSON.
	 * Serialized fields are identified in $serializedFields.
	 *
	 * @param        $tableName
	 * @param array  $primaryKeys
	 * @param array  $serializedFields
	 * @param        $position
	 * @param array  $stepData
	 * @param bool $singlePass
	 * @param string $regex
	 *
	 * @return array|bool
	 */
	public function tableColumnsToJson(
		$tableName, array $primaryKeys, array $serializedFields, $position, array $stepData, $singlePass = false, $regex = '^([abCdioOsS]:|N;$)', $perPage = 1000
	)
	{
		if (!strlen($regex))
		{
			throw new \LogicException("Must provide a limiting regex");
		}

		$db = \XF::db();
		$db->beginTransaction();

		if (count($primaryKeys) == 1 && isset($stepData['max']))
		{
			// for simple cases, we can do a bit more
			$perPage *= 2;
		}

		if (!isset($stepData['max']))
		{
			$stepData['max'] = $db->fetchOne("SELECT MAX({$primaryKeys[0]}) FROM {$tableName}");

			// basic updates for empty array columns
			foreach ($serializedFields AS $column)
			{
				$db->update($tableName, [$column => '[]'], "{$column} = ?", ['a:0:{}']);
			}
		}

		$whereSql = [];
		foreach ($serializedFields AS $column)
		{
			$whereSql[] = "({$column} <> '' AND {$column} <> '[]' AND {$column} REGEXP '{$regex}')";
		}

		if ($singlePass)
		{
			$perPage = 999999999;
		}

		$results = $db->fetchAll("
			SELECT
				" . implode(", ", $primaryKeys) . ",
				" . implode(", ", $serializedFields) . "
			FROM {$tableName}
			WHERE {$primaryKeys[0]} > ?
			AND (" . implode(' OR ', $whereSql) . ")
			ORDER BY {$primaryKeys[0]}
			LIMIT {$perPage}
		", $position);

		if (!$results)
		{
			$db->commit();
			return true;
		}

		$next = $this->updateResultsToJson($results, $tableName, $primaryKeys, $serializedFields);

		if ($next && count($primaryKeys) > 1)
		{
			// With a multi-column key, we assume the first column is an integer and use that for ordering.
			// But we might not finish all rows with that first column value, so fetch the rest of the last
			// one that we saw and convert them all here. Otherwise, they won't be converted on the next go around.

			$results = $db->fetchAll("
				SELECT
					" . implode(", ", $primaryKeys) . ",
					" . implode(", ", $serializedFields) . "
				FROM {$tableName}
				WHERE {$primaryKeys[0]} = ?
				AND (" . implode(' OR ', $whereSql) . ")
			", $next);

			$this->updateResultsToJson($results, $tableName, $primaryKeys, $serializedFields);
		}

		$db->commit();

		return [
			$next,
			"{$next} / {$stepData['max']}",
			$stepData
		];
	}

	protected function updateResultsToJson(array $results, $tableName, array $primaryKeys, array $serializedFields)
	{
		$db = \XF::db();
		$next = 0;

		foreach ($results AS $result)
		{
			$next = $result[$primaryKeys[0]];
			$newValues = [];

			foreach ($serializedFields AS $column)
			{
				$oldValue = $result[$column];
				if ($oldValue !== '')
				{
					$newValue = \XF\Util\Json::decodeJsonOrSerialized($oldValue);
					if ($newValue !== false || $oldValue === 'b:0;')
					{
						$encodedValue = json_encode($newValue);
						if ($encodedValue === false && $newValue !== false)
						{
							$pk = $primaryKeys[0];
							$pkValue = $result[$pk];

							$errorMessage = "Error doing JSON conversion $tableName.$column ($pk=$pkValue): "
								. json_last_error_msg() . '. Forcing partial conversion.';

							if (intval($pkValue))
							{
								try
								{
									$db->insert('xf_json_convert_error', [
										'table_name' => substr($tableName, 0, 100),
										'column_name' => substr($column, 0, 100),
										'pk_id' => intval($pkValue),
										'original_value' => $oldValue
									], false, 'original_value = VALUES(original_value)');

									$errorMessage .= ' (Original value logged into xf_json_convert_error table)';
								}
								catch (\XF\Db\Exception $e) {}
							}

							\XF::logError($errorMessage, true);

							$encodedValue = json_encode($newValue, JSON_PARTIAL_OUTPUT_ON_ERROR);
						}

						$newValues[$column] = $encodedValue;
					}
				}
			}

			if (!empty($newValues))
			{
				$pkConditions = [];
				$pkValues = [];

				foreach ($primaryKeys AS $pk)
				{
					$pkConditions[] = "$pk = ?";
					$pkValues[] = $result[$pk];
				}

				$db->update($tableName, $newValues, implode(' AND ', $pkConditions), $pkValues);
			}
		}

		return $next;
	}

	/**
	 * @param $sql
	 * @param string|array $bind
	 * @param bool $suppressAll
	 *
	 * @return bool|\XF\Db\AbstractStatement
	 * @throws \XF\Db\Exception|\Exception
	 */
	public function executeUpgradeQuery($sql, $bind = [], $suppressAll = false)
	{
		try
		{
			return $this->db()->query($sql, $bind);
		}
		catch (\XF\Db\Exception $e)
		{
			if ($suppressAll)
			{
				return false;
			}

			$message = $e->getMessage();
			if (preg_match('/(have an error in your SQL syntax|table \'.*\' doesn\'t exist|Unknown column|doesn\'t have a default value|Data truncated)/i', $message))
			{
				// we don't want to suppress errors in the query that should generally be corrected
				throw $e;
			}

			return false;
		}
	}

	public function migrateTableToReactions($tableName, $likesColumn = 'likes', $likeUsersColumn = 'like_users')
	{
		if (!$this->schemaManager()->columnExists($tableName, $likesColumn))
		{
			// likes column is gone, so assume this has been run
			return;
		}

		$this->alterTable($tableName, function (Alter $table) use ($likesColumn, $likeUsersColumn)
		{
			$table->changeColumn($likesColumn)->type('int')->unsigned(false)->renameTo('reaction_score');
			$table->addColumn('reactions', 'blob')->nullable()->after('reaction_score');
			$table->renameColumn($likeUsersColumn, 'reaction_users');
		});

		$this->executeUpgradeQuery('UPDATE `' . $tableName . '` SET reactions = CONCAT(\'{"1":\', reaction_score, \'}\') WHERE reaction_score > 0');
	}

	public function renameLikeAlertOptionsToReactions($contentTypes, $oldAction = 'like')
	{
		foreach ((array)$contentTypes AS $contentType)
		{
			$old = "{$contentType}_{$oldAction}";
			$new = "{$contentType}_reaction";

			$this->executeUpgradeQuery('UPDATE xf_user_alert_optout SET alert = ? WHERE alert = ?', [$new, $old]);
			$this->executeUpgradeQuery('UPDATE xf_user_push_optout SET push = ? WHERE push = ?', [$new, $old]);

			$this->executeUpgradeQuery('UPDATE xf_user_option SET alert_optout = REPLACE(alert_optout, ?, ?) WHERE alert_optout LIKE ?', [$old, $new, $this->db()->escapeLike($old, '%?%')]);
			$this->executeUpgradeQuery('UPDATE xf_user_option SET push_optout = REPLACE(push_optout, ?, ?) WHERE push_optout LIKE ?', [$old, $new, $this->db()->escapeLike($old, '%?%')]);
		}
	}

	public function renameLikeAlertsToReactions($contentTypes, $renameNewsFeed = true, $oldAction = 'like')
	{
		$db = $this->db();
		$contentTypesQuoted = $db->quote((array)$contentTypes);

		$this->executeUpgradeQuery("
			UPDATE xf_user_alert
			SET action = 'reaction',
				extra_data = '{\"reaction_id\":1}'
			WHERE content_type IN({$contentTypesQuoted})
			AND action = ?
		", $oldAction);

		if ($renameNewsFeed)
		{
			$this->executeUpgradeQuery("
				UPDATE xf_news_feed
				SET action = 'reaction',
					extra_data = '{\"reaction_id\":1}'
				WHERE content_type IN({$contentTypesQuoted})
				AND action = ?
			", $oldAction);
		}
	}

	public function renameLikeCriteriaToReactions($criteriaTable, $primaryKey, $criteriaField = 'user_criteria')
	{
		$db = $this->db();

		$items = $this->db()->fetchPairs("
			SELECT `$primaryKey`, `$criteriaField`
			FROM `$criteriaTable`
			WHERE `$criteriaField` LIKE '%\"like_count\"%'
				OR `$criteriaField` LIKE '%\"like_ratio\"%'
			ORDER BY `$primaryKey`
		");

		if (!$items)
		{
			return;
		}

		foreach ($items AS $key => $criteria)
		{
			$encodeF = 'json_encode';
			$rules = @json_decode($criteria, true);

			if (!$rules)
			{
				$encodeF = 'serialize';
				$rules = @unserialize($criteria);
			}

			if (!$rules)
			{
				continue;
			}

			foreach ($rules AS &$rule)
			{
				if ($rule['rule'] == 'like_count')
				{
					$rule['rule'] = 'reaction_score';
					$rule['data']['reactions'] = $rule['data']['likes'];
					unset($rule['data']['likes']);
				}
				else if ($rule['rule'] == 'like_ratio')
				{
					$rule['rule'] = 'reaction_ratio';
				}
			}

			$db->update($criteriaTable, [
				$criteriaField => $encodeF($rules)
			], "$primaryKey = ?", $key, 'IGNORE');
		}
	}

	public function renameLikePermissionsToReactions(array $permissionGroupIds, $likePermissionId = 'like', $reactPermissionId = 'react')
	{
		$globalPermissions = [];
		$contentPermissions = [];

		foreach ($permissionGroupIds AS $permissionGroupId => $contentPermission)
		{
			$globalPermissions[] = $permissionGroupId;
			if ($contentPermission)
			{
				$contentPermissions[] = $permissionGroupId;
			}
		}

		$db = $this->db();

		$globalPermissionsQuoted = $db->quote($globalPermissions);

		$this->executeUpgradeQuery("
			UPDATE xf_permission_entry
			SET permission_id = '{$reactPermissionId}'
			WHERE permission_id = ?
			AND permission_group_id IN({$globalPermissionsQuoted})
		", $likePermissionId);

		if ($contentPermissions)
		{
			$contentPermissionsQuoted = $db->quote($contentPermissions);

			$this->executeUpgradeQuery("
				UPDATE xf_permission_entry_content
				SET permission_id = 'react'
				WHERE permission_id = ?
				AND permission_group_id IN({$contentPermissionsQuoted})
			", $likePermissionId);
		}
	}

	public function renameLikeStatsToReactions($contentTypes)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		foreach ($contentTypes AS $contentType)
		{
			$this->executeUpgradeQuery("
				UPDATE xf_stats_daily
				SET stats_type = ?
				WHERE stats_type = ?
			", ["{$contentType}_reaction", "{$contentType}_like"]);
		}
	}

	public function createWidget($widgetKey, $definitionId, array $config, $title = '')
	{
		/** @var \XF\Entity\Widget $widget */
		$widget = $this->app->em()->create('XF:Widget');
		$widget->widget_key = $widgetKey;
		$widget->definition_id = $definitionId;
		$widget->bulkSet($config);
		$success = $widget->save(false);

		if ($success)
		{
			$masterTitle = $widget->getMasterPhrase();
			$masterTitle->phrase_text = $title;
			$masterTitle->save(false);
		}
	}

	public function deleteWidget($widgetKey)
	{
		$widget = $this->app->finder('XF:Widget')->where('widget_key', $widgetKey)->fetchOne();
		if (!$widget)
		{
			return;
		}
		$widget->delete(false);
	}

	public function applyGlobalPermission($applyGroupId, $applyPermissionId, $dependGroupId = null, $dependPermissionId = null)
	{
		$db = $this->db();

		if ($dependGroupId && $dependPermissionId)
		{
			$db->query("
				REPLACE INTO xf_permission_entry
					(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
				SELECT user_group_id, user_id, ?, ?, 'allow', 0
				FROM xf_permission_entry
				WHERE permission_group_id = ?
					AND permission_id = ?
					AND permission_value = 'allow'
			", [$applyGroupId, $applyPermissionId, $dependGroupId, $dependPermissionId]);
		}
		else
		{
			$db->query("
				REPLACE INTO xf_permission_entry
					(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
				SELECT DISTINCT user_group_id, user_id, ?, ?, 'allow', 0
				FROM xf_permission_entry
			", [$applyGroupId, $applyPermissionId]);
		}
	}

	public function applyGlobalPermissionInt($applyGroupId, $applyPermissionId, $applyValue, $dependGroupId = null, $dependPermissionId = null)
	{
		$db = $this->db();

		if ($dependGroupId && $dependPermissionId)
		{
			$db->query("
				REPLACE INTO xf_permission_entry
					(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
				SELECT user_group_id, user_id, ?, ?, 'use_int', ?
				FROM xf_permission_entry
				WHERE permission_group_id = ?
					AND permission_id = ?
					AND permission_value = 'allow'
			", [$applyGroupId, $applyPermissionId, $applyValue, $dependGroupId, $dependPermissionId]);
		}
		else
		{
			$db->query("
				REPLACE INTO xf_permission_entry
					(user_group_id, user_id, permission_group_id, permission_id, permission_value, permission_value_int)
				SELECT DISTINCT user_group_id, user_id, ?, ?, 'use_int', ?
				FROM xf_permission_entry
			", [$applyGroupId, $applyPermissionId, $applyValue]);
		}
	}

	public function applyContentPermission($applyGroupId, $applyPermissionId, $dependGroupId, $dependPermissionId)
	{
		$db = $this->db();

		$db->query("
			REPLACE INTO xf_permission_entry_content
				(content_type, content_id, user_group_id, user_id,
				permission_group_id, permission_id, permission_value, permission_value_int)
			SELECT content_type, content_id, user_group_id, user_id, ?, ?, 'content_allow', 0
			FROM xf_permission_entry_content
			WHERE permission_group_id = ?
				AND permission_id = ?
				AND permission_value = 'content_allow'
		", [$applyGroupId, $applyPermissionId, $dependGroupId, $dependPermissionId]);
	}

	public function applyAdminPermission($applyPermissionId, $dependPermissionId)
	{
		// note: this doesn't rebuild the admin permission cache -- that should happen when the permission
		// is inserted, so this is only safe to use on its own when inserting the permission
		$this->db()->query("
			INSERT IGNORE INTO xf_admin_permission_entry
				(user_id, admin_permission_id)
			SELECT user_id, ?
			FROM xf_admin_permission_entry
			WHERE admin_permission_id = ?
		", [$applyPermissionId, $dependPermissionId]);
	}

	public function uninstallContentTypeData($contentTypes)
	{
		if (!is_array($contentTypes))
		{
			$contentTypes = [$contentTypes];
		}

		$db = $this->db();

		$contentTypesQuoted = $db->quote($contentTypes);

		$db->beginTransaction();

		$contentTypeTables = [
			'xf_approval_queue',
			'xf_bookmark_item',
			'xf_change_log',
			'xf_content_spam_cache',
			'xf_deletion_log',
			'xf_edit_history',
			'xf_moderator_content',
			'xf_moderator_log',
			'xf_news_feed',
			'xf_poll',
			'xf_reaction_content',
			'xf_report',
			'xf_spam_trigger_log',
			'xf_tag_content',
			'xf_user_alert'
		];
		foreach ($contentTypeTables AS $table)
		{
			$db->delete($table, 'content_type IN (' . $contentTypesQuoted . ')');
		}

		// TODO: should try to remove report comments

		// let these be cleaned up over time
		$db->update('xf_attachment', ['unassociated' => 1], 'content_type IN (' . $contentTypesQuoted . ')');

		$db->commit();
	}

	/**
	 * @return \XF\App
	 */
	protected function app()
	{
		return $this->app;
	}

	/**
	 * @return \XF\Db\AbstractAdapter
	 */
	protected function db()
	{
		return $this->app->db();
	}

	/**
	 * @return \XF\Db\SchemaManager
	 */
	protected function schemaManager()
	{
		return $this->db()->getSchemaManager();
	}

	protected function alterTable($tableName, \Closure $toApply)
	{
		$this->schemaManager()->alterTable($tableName, $toApply);
	}

	protected function renameTable($oldTableName, $newTableName)
	{
		$this->schemaManager()->renameTable($oldTableName, $newTableName);
	}

	protected function createTable($tableName, \Closure $toApply)
	{
		$this->schemaManager()->createTable($tableName, $toApply);
	}

	protected function dropTable($tableName, \Closure $toApply = null)
	{
		$this->schemaManager()->dropTable($tableName, $toApply);
	}

	public function tableExists($tableName)
	{
		return $this->schemaManager()->tableExists($tableName);
	}

	public function columnExists($tableName, $column, &$definition = null)
	{
		return $this->schemaManager()->columnExists($tableName, $column, $definition);
	}
}