<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class ConversationUser extends Finder
{
	public function forUser(\XF\Entity\User $user, $forList = true)
	{
		$this->where('owner_user_id', $user->user_id);

		if ($forList)
		{
			$this->forList($user);
		}

		return $this;
	}

	public function forList(\XF\Entity\User $user)
	{
		$this->with(['Master.Starter']);

		return $this;
	}

	public function orderForUser(\XF\Entity\User $user, $orderBy, $orderDir = 'desc')
	{
		if ($orderBy == 'last_message_date')
		{
			$this->order('Users|' . $user->user_id . '.last_message_date', $orderDir);
		}
		else
		{
			$this->order($orderBy, $orderDir);
		}

		return $this;
	}
}