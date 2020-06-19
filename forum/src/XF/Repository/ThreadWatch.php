<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class ThreadWatch extends Repository
{
	public function autoWatchThread(\XF\Entity\Thread $thread, \XF\Entity\User $user, $onCreation = false)
	{
		$userField = $onCreation ? 'creation_watch_state' : 'interaction_watch_state';

		if (!$thread->thread_id || !$user->user_id || !$user->Option->getValue($userField))
		{
			return null;
		}

		$watch = $this->em->find('XF:ThreadWatch', [
			'thread_id' => $thread->thread_id,
			'user_id' => $user->user_id
		]);
		if ($watch)
		{
			return null;
		}

		$watch = $this->em->create('XF:ThreadWatch');
		$watch->thread_id = $thread->thread_id;
		$watch->user_id = $user->user_id;
		$watch->email_subscribe = ($user->Option->getValue($userField) == 'watch_email');

		try
		{
			$watch->save();
		}
		catch (\XF\Db\DuplicateKeyException $e)
		{
			return null;
		}

		return $watch;
	}

	public function setWatchState(\XF\Entity\Thread $thread, \XF\Entity\User $user, $state)
	{
		if (!$thread->thread_id || !$user->user_id)
		{
			throw new \InvalidArgumentException("Invalid thread or user");
		}

		$watch = $this->em->find('XF:ThreadWatch', [
			'thread_id' => $thread->thread_id,
			'user_id' => $user->user_id
		]);

		switch ($state)
		{
			case 'watch_email':
			case 'watch_no_email':
			case 'no_email':
				if (!$watch)
				{
					$watch = $this->em->create('XF:ThreadWatch');
					$watch->thread_id = $thread->thread_id;
					$watch->user_id = $user->user_id;
				}
				$watch->email_subscribe = ($state == 'watch_email');
				try
				{
					$watch->save();
				}
				catch (\XF\Db\DuplicateKeyException $e) {}
				break;

			case 'delete':
			case 'stop':
			case '':
				if ($watch)
				{
					$watch->delete();
				}
				break;

			default:
				throw new \InvalidArgumentException("Unknown state '$state'");
		}
	}

	public function setWatchStateForAll(\XF\Entity\User $user, $state)
	{
		if (!$user->user_id)
		{
			throw new \InvalidArgumentException("Invalid user");
		}

		$db = $this->db();

		switch ($state)
		{
			case 'watch_email':
				return $db->update('xf_thread_watch', ['email_subscribe' => 1], 'user_id = ?', $user->user_id);

			case 'watch_no_email':
			case 'no_email':
				return $db->update('xf_thread_watch', ['email_subscribe' => 0], 'user_id = ?', $user->user_id);

			case 'delete':
			case 'stop':
			case '':
				return $db->delete('xf_thread_watch', 'user_id = ?', $user->user_id);

			default:
				throw new \InvalidArgumentException("Unknown state '$state'");
		}
	}

	public function isValidWatchState($state)
	{
		switch ($state)
		{
			case 'watch_email':
			case 'watch_no_email':
			case 'no_email':
			case 'delete':
			case 'stop':
			case '':
				return true;

			default:
				return false;
		}
	}
}