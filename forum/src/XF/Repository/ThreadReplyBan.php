<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;
use XF\Mvc\Entity\Finder;

class ThreadReplyBan extends Repository
{
	/**
	 * @return Finder
	 */
	public function findReplyBansForList()
	{
		$finder = $this->finder('XF:ThreadReplyBan');
		$finder->setDefaultOrder('ban_date', 'DESC')
			->with('Thread', true);
		return $finder;
	}

	/**
	 * @return Finder
	 */
	public function findReplyBansForThread(\XF\Entity\Thread $thread)
	{
		$finder = $this->findReplyBansForList();
		$finder->where('thread_id', $thread->thread_id)
			->with(['User', 'BannedBy']);
		return $finder;
	}

	public function cleanUpExpiredBans($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = time();
		}
		$this->db()->delete('xf_thread_reply_ban', 'expiry_date > 0 AND expiry_date < ?', $cutOff);
	}
}