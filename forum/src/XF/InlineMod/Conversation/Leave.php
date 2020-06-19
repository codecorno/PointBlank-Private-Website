<?php

namespace XF\InlineMod\Conversation;

use XF\Http\Request;
use XF\InlineMod\AbstractAction;
use XF\Mvc\Entity\AbstractCollection;
use XF\Mvc\Entity\Entity;

class Leave extends AbstractAction
{
	public function getTitle()
	{
		return \XF::phrase('leave_conversations...');
	}

	protected function canApplyToEntity(Entity $entity, array $options, &$error = null)
	{
		return true;
	}

	protected function applyToEntity(Entity $entity, array $options)
	{
		/** @var \XF\Entity\ConversationMaster $entity */
		$recipient = $entity->Recipients[\XF::visitor()->user_id];
		if ($recipient)
		{
			$recipient->recipient_state = $options['recipient_state'];
			$recipient->save();
		}
	}

	public function getBaseOptions()
	{
		return [
			'recipient_state' => 'deleted'
		];
	}

	public function renderForm(AbstractCollection $entities, \XF\Mvc\Controller $controller)
	{
		$viewParams = [
			'conversations' => $entities,
			'total' => count($entities),
		];
		return $controller->view('XF:Public:InlineMod\Conversation\Leave', 'inline_mod_conversation_leave', $viewParams);
	}

	public function getFormOptions(AbstractCollection $entities, Request $request)
	{
		return [
			'recipient_state' => $request->filter('recipient_state', 'str')
		];
	}
}