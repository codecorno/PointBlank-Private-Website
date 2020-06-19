<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class ChangeLog extends Repository
{
	/**
	 * @param string $contentType
	 * @param int $contentId
	 *
	 * @return Finder
	 */
	public function findChangeLogsByContent($contentType, $contentId)
	{
		return $this->finder('XF:ChangeLog')
			->where(['content_type' => $contentType, 'content_id' => $contentId])
			->with('EditUser')
			->setDefaultOrder('edit_date', 'DESC');
	}

	/**
	 * @param string $contentType
	 *
	 * @return Finder
	 */
	public function findChangeLogsByContentType($contentType)
	{
		return $this->finder('XF:ChangeLog')
			->where('content_type', $contentType)
			->with('EditUser')
			->setDefaultOrder('edit_date', 'DESC');
	}

	public function countChangeLogsSince($contentType, $contentId, $field, $cutOff)
	{
		return $this->db()->fetchOne("
			SELECT COUNT(*)
			FROM xf_change_log
			WHERE content_type = ?
				AND content_id = ?
				AND field = ?
				AND edit_date >= ?
		", [$contentType, $contentId, $field, $cutOff]);
	}

	public function groupChangeLogs($changes)
	{
		$grouped = [];

		/** @var \XF\Entity\ChangeLog $change */
		foreach ($changes AS $key => $change)
		{
			$groupKey = "{$change->content_type}-{$change->content_id}-{$change->edit_user_id}-{$change->edit_date}";
			if (!isset($grouped[$groupKey]))
			{
				$grouped[$groupKey] = [
					'content_type' => $change->content_type,
					'content_id' => $change->content_id,
					'date' => $change->edit_date,
					'content' => $change->Content,
					'editUser' => $change->EditUser,
					'changes' => [],
					'changesRaw' => []
				];
			}

			$grouped[$groupKey]['changes'][$key] = $change->DisplayEntry;
			$grouped[$groupKey]['changesRaw'][$key] = $change;
		}

		return $grouped;
	}

	public function logChange($contentType, $contentId, $column, $oldValue, $newValue, $editByUserId)
	{
		return $this->logHistoricalChange($contentType, $contentId, $column, $oldValue, $newValue, $editByUserId);
	}

	public function logHistoricalChange($contentType, $contentId, $column, $oldValue, $newValue, $editByUserId, $editDate = null)
	{
		$inserts = $this->logChanges($contentType, $contentId, [$column => [$oldValue, $newValue, $editDate]], $editByUserId);
		return $inserts ? null : reset($inserts);
	}

	/**
	 * @param       $contentType
	 * @param       $contentId
	 * @param array $changes Should be of the form [column => [old, new, (optional)edit_date]
	 * @param       $editByUserId
	 *
	 * @return array
	 * @throws \XF\PrintableException
	 */
	public function logChanges($contentType, $contentId, array $changes, $editByUserId)
	{
		$em = \XF::em();
		$inserted = [];

		\XF::db()->beginTransaction();

		foreach ($changes AS $field => $change)
		{
			list($old, $new) = $change;
			$old = strval($old);
			$new = strval($new);
			if ($old === $new)
			{
				continue;
			}

			$date = empty($change[2]) ? \XF::$time : $change[2];

			/** @var \XF\Entity\ChangeLog $entry */
			$entry = $em->create('XF:ChangeLog');
			$entry->bulkSet([
				'content_type' => $contentType,
				'content_id' => $contentId,
				'edit_user_id' => $editByUserId,
				'edit_date' => $date,
				'field' => $field,
				'old_value' => $old,
				'new_value' => $new
			]);

			$handler = $entry->getHandler();
			if ($handler)
			{
				$entry->protected = $handler->isFieldProtected($field);
			}

			if ($entry->save(true, false))
			{
				$inserted[] = $entry;
			}
		}

		\XF::db()->commit();

		return $inserted;
	}

	public function pruneChangeLogs($cutOff = null)
	{
		if ($cutOff === null)
		{
			$length = $this->options()->changeLogLength;
			if (!$length)
			{
				return 0;
			}

			$cutOff = \XF::$time - 86400 * $length;
		}

		return $this->db()->delete('xf_change_log', 'edit_date < ? AND protected = 0', $cutOff);
	}

	/**
	 * @return \XF\ChangeLog\AbstractHandler[]
	 */
	public function getChangeLogHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('change_log_handler_class') AS $contentType => $handlerClass)
		{
			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$handlers[$contentType] = new $handlerClass($contentType);
			}
		}

		return $handlers;
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\ChangeLog\AbstractHandler|null
	 */
	public function getChangeLogHandler($type, $throw = false)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'change_log_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No change log handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Change log handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}

	/**
	 * @param \XF\Entity\ChangeLog[] $logs
	 */
	public function addDataToLogs($logs)
	{
		$contentMap = [];
		$displayMap = [];
		foreach ($logs AS $key => $log)
		{
			$contentType = $log->content_type;
			if (!isset($contentMap[$contentType]))
			{
				$contentMap[$contentType] = [];
				$displayMap[$contentType] = [];
			}
			$contentMap[$contentType][$key] = $log->content_id;
			$displayMap[$contentType][] = $key;
		}

		foreach ($contentMap AS $contentType => $contentIds)
		{
			$handler = $this->getChangeLogHandler($contentType);
			if (!$handler)
			{
				continue;
			}
			$data = $handler->getContent($contentIds);
			foreach ($contentIds AS $alertId => $contentId)
			{
				$content = isset($data[$contentId]) ? $data[$contentId] : null;
				$logs[$alertId]->setContent($content);
			}

			foreach ($displayMap[$contentType] AS $changeId)
			{
				$log = $logs[$changeId];
				$display = $handler->getDisplayEntry($log);
				$log->setDisplayEntry($display);
			}
		}
	}
}