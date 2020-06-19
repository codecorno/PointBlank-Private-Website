<?php

namespace XF\Job;

use XF\Mvc\Entity\Entity;

class ConversationEmbedMetadata extends AbstractEmbedMetadataJob
{
	protected function getIdsToRebuild(array $types)
	{
		// See XF Bug #153298 as to why we are not using the xf_conversation_message table right now
		return $this->getIdsBug153298Workaround('conversation_message');

		$db = $this->app->db();

		// Note: only attachments are supported currently, so we filter based on attach count for efficiency.
		// If other types become available, this condition will need to change.
//		return $db->fetchAllColumn($db->limit(
//			"
//				SELECT message_id
//				FROM xf_conversation_message
//				WHERE message_id > ?
//					AND attach_count > 0
//				ORDER BY message_id
//			", $this->data['batch']
//		), $this->data['start']);
	}

	protected function getRecordToRebuild($id)
	{
		return $this->app->em()->find('XF:ConversationMessage', $id);
	}

	protected function getPreparerContext()
	{
		return 'conversation';
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
		$type = \XF::phrase('conversations');
		return sprintf('%s... %s', $rebuildPhrase, $type);
	}
}