<?php

namespace XF\ControllerPlugin;

class Attachment extends AbstractPlugin
{
	public function displayAttachment(\XF\Entity\Attachment $attachment, $allowViewLog = true)
	{
		if (!$attachment->Data || !$attachment->Data->isDataAvailable())
		{
			return $this->error(\XF::phrase('attachment_cannot_be_shown_at_this_time'));
		}

		$this->setResponseType('raw');

		$eTag = $this->request->getServer('HTTP_IF_NONE_MATCH');
		if ($eTag && $eTag == '"' . $attachment['attach_date'] . '"')
		{
			$return304 = true;
		}
		else
		{
			if (!$attachment->temp_hash && $allowViewLog)
			{
				// if not associated, don't log it
				$this->getAttachmentRepo()->logAttachmentView($attachment);
			}

			$return304 = false;
		}

		$viewParams = [
			'attachment' => $attachment,
			'return304' => $return304
		];
		return $this->view('XF:Attachment\View', '', $viewParams);
	}

	/**
	 * @return \XF\Repository\Attachment
	 */
	protected function getAttachmentRepo()
	{
		return $this->repository('XF:Attachment');
	}
}