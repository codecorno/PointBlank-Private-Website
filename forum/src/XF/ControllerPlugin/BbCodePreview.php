<?php

namespace XF\ControllerPlugin;

use XF\Mvc\Entity\AbstractCollection;

class BbCodePreview extends AbstractPlugin
{
	public function actionPreview($message, $context, \XF\Entity\User $user = null, $attachments = null, $canViewAttachments = true)
	{
		$user = $user ?: $this->repository('XF:User')->getGuestUser();

		$viewParams = [
			'message' => $message,
			'context' => $context,
			'user' => $user,
			'attachments' => $attachments,
			'canViewAttachments' => $canViewAttachments
		];
		return $this->view('XF:BbCodePreview\Preview', 'bb_code_preview', $viewParams);
	}
}