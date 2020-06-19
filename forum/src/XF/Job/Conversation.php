<?php

namespace XF\Job;

class Conversation extends AbstractRebuildJob
{
	protected function getNextIds($start, $batch)
	{
		$db = $this->app->db();

		return $db->fetchAllColumn($db->limit(
			"
				SELECT conversation_id
				FROM xf_conversation_master
				WHERE conversation_id > ?
				ORDER BY conversation_id
			", $batch
		), $start);
	}

	protected function rebuildById($id)
	{
		/** @var \XF\Entity\ConversationMaster $conversation */
		$conversation = $this->app->em()->find('XF:ConversationMaster', $id);
		if ($conversation)
		{
			$conversation->rebuildCounters();
			$conversation->save();

		}
	}

	protected function getStatusType()
	{
		return \XF::phrase('conversations');
	}
}