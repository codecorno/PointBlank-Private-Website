<?php

namespace XF\Spam\Cleaner;

class Conversation extends AbstractHandler
{
	public function canCleanUp(array $options = [])
	{
		return !empty($options['delete_conversations']);
	}

	public function cleanUp(array &$log, &$error = null)
	{
		$conversationsFinder = \XF::app()->finder('XF:ConversationMaster');
		$conversations = $conversationsFinder->where('user_id', $this->user->user_id)->fetch();

		foreach ($conversations AS $conversation)
		{
			$conversation->delete();
		}

		$log['conversation'] = [
			'count' => $conversations->count()
		];

		return true;
	}

	public function restore(array $log, &$error = null)
	{
		return true;
	}
}