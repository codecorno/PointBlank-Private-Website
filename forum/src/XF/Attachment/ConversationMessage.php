<?php

namespace XF\Attachment;

use XF\Entity\Attachment;
use XF\Mvc\Entity\Entity;

class ConversationMessage extends AbstractHandler
{
	public function canView(Attachment $attachment, Entity $container, &$error = null)
	{
		/** @var \XF\Entity\ConversationMessage $container */
		if (!$container->canView())
		{
			return false;
		}

		/** @var \XF\Entity\ConversationMaster $conversation */
		$conversation = $container->Conversation;
		return $conversation->canView($error);
	}

	public function canManageAttachments(array $context, &$error = null)
	{
		$conversation = $this->getConversationFromContext($context);
		return ($conversation && $conversation->canUploadAndManageAttachments());
	}

	public function onAttachmentDelete(Attachment $attachment, Entity $container = null)
	{
		if (!$container)
		{
			return;
		}

		/** @var \XF\Entity\ConversationMessage $container */
		$container->attach_count--;
		$container->save();
	}

	public function getConstraints(array $context)
	{
		/** @var \XF\Repository\Attachment $attachRepo */
		$attachRepo = \XF::repository('XF:Attachment');

		$constraints = $attachRepo->getDefaultAttachmentConstraints();

		$conversation = $this->getConversationFromContext($context);
		if ($conversation && $conversation->canUploadVideos())
		{
			$constraints = $attachRepo->applyVideoAttachmentConstraints($constraints);
		}

		return $constraints;
	}

	public function getContainerIdFromContext(array $context)
	{
		return isset($context['message_id']) ? intval($context['message_id']) : null;
	}

	public function getContainerLink(Entity $container, array $extraParams = [])
	{
		return \XF::app()->router('public')->buildLink('conversations/messages', $container, $extraParams);
	}

	public function getContext(Entity $entity = null, array $extraContext = [])
	{
		if ($entity instanceof \XF\Entity\ConversationMessage)
		{
			$extraContext['message_id'] = $entity->message_id;
		}
		else if ($entity instanceof \XF\Entity\ConversationMaster)
		{
			$extraContext['conversation_id'] = $entity->conversation_id;
		}
		else if (!$entity)
		{
			// need nothing
		}
		else
		{
			throw new \InvalidArgumentException("Entity must be conversation or conversation message");
		}

		return $extraContext;
	}

	protected function getConversationFromContext(array $context)
	{
		$em = \XF::em();

		if (!empty($context['message_id']))
		{
			/** @var \XF\Entity\ConversationMessage $message */
			$message = $em->find('XF:ConversationMessage', intval($context['message_id']), ['Conversation']);
			if (!$message || !$message->canView() || !$message->canEdit())
			{
				return null;
			}

			$conversation = $message->Conversation;
		}
		else if (!empty($context['conversation_id']))
		{
			/** @var \XF\Entity\ConversationMaster $conversation */
			$conversation = $em->find('XF:ConversationMaster', intval($context['conversation_id']));
			if (!$conversation || !$conversation->canView())
			{
				return null;
			}
		}
		else
		{
			$conversation = $em->create('XF:ConversationMaster');
		}

		return $conversation;
	}
}