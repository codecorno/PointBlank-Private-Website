<?php

namespace XF\EmailStop;

abstract class AbstractHandler
{
	protected $contentType;

	abstract public function getStopOneText(\XF\Entity\User $user, $contentId);
	abstract public function getStopAllText(\XF\Entity\User $user);
	abstract public function stopOne(\XF\Entity\User $user, $contentId);
	abstract public function stopAll(\XF\Entity\User $user);

	public function __construct($contentType)
	{
		$this->contentType = $contentType;
	}
}