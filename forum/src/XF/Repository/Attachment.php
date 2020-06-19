<?php

namespace XF\Repository;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;
use XF\Moderator\AbstractModerator;

class Attachment extends Repository
{
	public function getEditorData($contentType, Entity $entity = null, $tempHash = null, array $extraContext = [])
	{
		$handler = $this->getAttachmentHandler($contentType);
		if (!$handler)
		{
			throw new \InvalidArgumentException("No attachment handler found for content type '$contentType'");
		}
		$context = $handler->getContext($entity, $extraContext);

		$constraints = $handler->getConstraints($context);

		if (!$tempHash)
		{
			$tempHash = md5(microtime(true) . \XF::generateRandomString(8, true));
			$hashAttachments = [];
		}
		else
		{
			$hashAttachments = $this->findAttachmentsByTempHash($tempHash)->fetch()->toArray();
		}

		$containerId = $handler->getContainerIdFromContext($context);
		if ($containerId)
		{
			$contentAttachments = $this->findAttachmentsByContent($contentType, $containerId)->fetch()->toArray();
		}
		else
		{
			$contentAttachments = [];
		}

		return [
			'type' => $contentType,
			'hash' => $tempHash,
			'context' => $context,
			'constraints' => $constraints,
			'attachments' => $contentAttachments + $hashAttachments,
		];
	}

	/**
	 * @return Finder
	 */
	public function findAttachmentsForList()
	{
		return $this->finder('XF:Attachment')
			->with('Data', true)
			->setDefaultOrder('attach_date', 'DESC');
	}

	/**
	 * @param string $hash
	 *
	 * @return Finder
	 */
	public function findAttachmentsByTempHash($hash)
	{
		return $this->finder('XF:Attachment')
			->where('temp_hash', $hash)
			->order('attach_date');
	}

	/**
	 * @param string $contentType
	 * @param int $contentId
	 *
	 * @return Finder
	 */
	public function findAttachmentsByContent($contentType, $contentId)
	{
		return $this->finder('XF:Attachment')
			->where('content_type', $contentType)
			->where('content_id', $contentId)
			->order('attach_date');
	}

	/**
	 * @return \XF\Attachment\AbstractHandler[]
	 */
	public function getAttachmentHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('attachment_handler_class') AS $contentType => $handlerClass)
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
	 *
	 * @return \XF\Attachment\AbstractHandler|null
	 */
	public function getAttachmentHandler($type)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'attachment_handler_class');
		if (!$handlerClass)
		{
			return null;
		}

		if (!class_exists($handlerClass))
		{
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}

	public function getDefaultAttachmentConstraints()
	{
		$options = $this->options();

		return [
			'extensions' => preg_split('/\s+/', trim($options->attachmentExtensions), -1, PREG_SPLIT_NO_EMPTY),
			'size' => $options->attachmentMaxFileSize * 1024,
			'width' => $options->attachmentMaxDimensions['width'],
			'height' => $options->attachmentMaxDimensions['height'],
			'count' => $options->attachmentMaxPerMessage
		];
	}

	public function applyVideoAttachmentConstraints(array $constraints)
	{
		$options = $this->options();

		if (!empty($options->allowVideoUploads['enabled']))
		{
			$constraints['extensions'] = array_unique(array_merge(
				$constraints['extensions'], $this->getVideoAttachmentExtensions()
			));

			$size = $options->allowVideoUploads['size'];
			$constraints['video_size'] = $size * 1024;
		}

		return $constraints;
	}

	public function getVideoAttachmentExtensions()
	{
		return array_keys($this->app()->inlineVideoTypes);
	}

	public function logAttachmentView(\XF\Entity\Attachment $attachment)
	{
		$this->db()->query("
			INSERT INTO xf_attachment_view
				(attachment_id, total)
			VALUES
				(? , 1)
			ON DUPLICATE KEY UPDATE
				total = total + 1
		", $attachment->attachment_id);
	}

	public function batchUpdateAttachmentViews()
	{
		$db = $this->db();
		$db->query("
			UPDATE xf_attachment AS a
			INNER JOIN xf_attachment_view AS av ON (a.attachment_id = av.attachment_id)
			SET a.view_count = a.view_count + av.total
		");
		$db->emptyTable('xf_attachment_view');
	}

	/**
	 * @param \XF\Mvc\Entity\ArrayCollection|\XF\Mvc\Entity\Entity[] $content
	 * @param $contentType
	 * @param string $countKey
	 * @param string $relationKey
	 *
	 * @return mixed
	 */
	public function addAttachmentsToContent($content, $contentType, $countKey = 'attach_count', $relationKey = 'Attachments')
	{
		$ids = [];
		foreach ($content AS $item)
		{
			if ($item->{$countKey})
			{
				$ids[] = $item->getEntityId();
			}
		}

		if ($ids)
		{
			$attachments = $this->finder('XF:Attachment')
				->where([
					'content_type' => $contentType,
					'content_id' => $ids
				])
				->order('attach_date')
				->fetch()
				->groupBy('content_id');

			foreach ($content AS $item)
			{
				$contentId = $item->getEntityId();

				$contentAttachments = isset($attachments[$contentId])
					? $this->em->getBasicCollection($attachments[$contentId])
					: $this->em->getEmptyCollection();

				$item->hydrateRelation($relationKey, $contentAttachments);
			}
		}

		return $content;
	}

	public function deleteUnassociatedAttachments($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time - 86400;
		}

		$pairs = $this->db()->fetchPairs("
			SELECT attachment_id, data_id
			FROM xf_attachment
			WHERE unassociated = 1
				AND attach_date < ?
		", $cutOff);

		return $this->fastDeleteAttachmentsFromPairs($pairs);
	}

	public function fastDeleteContentAttachments($contentType, $contentIds)
	{
		if (!is_array($contentIds))
		{
			$contentIds = [$contentIds];
		}

		if (!$contentIds)
		{
			return 0;
		}

		$db = $this->db();
		$pairs = $db->fetchPairs('
			SELECT attachment_id, data_id
			FROM xf_attachment
			WHERE content_type = ?
				AND content_id IN (' . $db->quote($contentIds) . ')
		', $contentType);

		return $this->fastDeleteAttachmentsFromPairs($pairs);
	}

	protected function fastDeleteAttachmentsFromPairs(array $pairs)
	{
		if (!$pairs)
		{
			return 0;
		}

		$dataCount = [];
		foreach ($pairs AS $dataId)
		{
			if (isset($dataCount[$dataId]))
			{
				$dataCount[$dataId]++;
			}
			else
			{
				$dataCount[$dataId] = 1;
			}
		}

		$db = $this->db();
		$db->beginTransaction();

		$total = $db->delete('xf_attachment',
			'attachment_id IN (' . $db->quote(array_keys($pairs)) . ')'
		);

		foreach ($dataCount AS $dataId => $delta)
		{
			$db->query('
				UPDATE xf_attachment_data
				SET attach_count = IF(attach_count > ?, attach_count - ?, 0)
				WHERE data_id = ?
			', [$delta, $delta, $dataId]);
		}

		$db->commit();

		return $total;
	}

	public function deleteUnusedAttachmentData()
	{
		$attachments = $this->finder('XF:AttachmentData')
			->where('attach_count', 0)
			->fetch(1000);
		foreach ($attachments AS $attachment)
		{
			$attachment->delete();
		}
	}
}