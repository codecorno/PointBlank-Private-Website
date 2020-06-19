<?php

namespace XF\Job;

use XF\Mvc\Entity\Entity;

class PostEmbedMetadata extends AbstractEmbedMetadataJob
{
	protected function getIdsToRebuild(array $types)
	{
		return $this->getIdsBug153298Workaround('post');

		$db = $this->app->db();

		// Note: only attachments are supported currently, so we filter based on attach count for efficiency.
		// If other types become available, this condition will need to change.
//		return $db->fetchAllColumn($db->limit(
//			"
//				SELECT post_id
//				FROM xf_post
//				WHERE post_id > ?
//					AND attach_count > 0
//				ORDER BY post_id
//			", $this->data['batch']
//		), $this->data['start']);
	}

	protected function getRecordToRebuild($id)
	{
		return $this->app->em()->find('XF:Post', $id);
	}

	protected function getPreparerContext()
	{
		return 'post';
	}

	protected function getMessageContent(Entity $record)
	{
		return $record->message;
	}

	protected function rebuildAttachments(Entity $record, \XF\Service\Message\Preparer $preparer, array &$embedMetadata)
	{
		$embedMetadata['attachments'] = $preparer->getEmbeddedAttachments();
	}

	protected function getActionDescription()
	{
		$rebuildPhrase = \XF::phrase('rebuilding');
		$type = \XF::phrase('posts');
		return sprintf('%s... %s', $rebuildPhrase, $type);
	}
}