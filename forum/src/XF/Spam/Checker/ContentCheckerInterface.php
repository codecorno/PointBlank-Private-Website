<?php

namespace XF\Spam\Checker;

interface ContentCheckerInterface
{
	public function check(\XF\Entity\User $user, $message, array $extraParams = []);

	public function submitSpam($contentType, $contentIds);

	public function submitHam($contentType, $contentIds);
}