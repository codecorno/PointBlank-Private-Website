<?php

namespace XF\Spam\Checker;

interface UserCheckerInterface
{
	public function check(\XF\Entity\User $user, array $extraParams = []);

	public function submit(\XF\Entity\User $user, array $extraParams = []);
}