<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class ThreadRedirect extends Repository
{
	public function createThreadRedirectionDouble(\XF\Entity\Thread $thread, \XF\Entity\Forum $target, $expiryLength = 0)
	{
		if ($thread->discussion_state != 'visible')
		{
			return null;
		}

		$data = $thread->toArray(false);
		$data = $this->cleanUpThreadRedirectionDouble($data);

		$data['discussion_type'] = 'redirect';
		$data['node_id'] = $target->node_id;

		/** @var \XF\Entity\Thread $double */
		$double = $this->em->create('XF:Thread');
		$double->bulkSet($data);

		$this->createRedirectionRecordForThread($double, $thread, $expiryLength);

		if (!$double->save())
		{
			return null;
		}

		return $double;
	}

	protected function cleanUpThreadRedirectionDouble(array $data)
	{
		unset($data['thread_id'], $data['node_id']);

		if (!$data['first_post_reactions'])
		{
			unset($data['first_post_reactions']);
		}

		$data['first_post_id'] = 0;

		return $data;
	}

	public function createRedirectionRecordForThread(
		\XF\Entity\Thread $thread, \XF\Entity\Thread $targetThread, $expiryLength = 0, $nodeId = null, $saveNow = true
	)
	{
		$nodeId = intval($nodeId);
		if (!$nodeId)
		{
			$thread->node_id;
		}

		$saveNow = ($saveNow && $thread->thread_id > 0);

		/** @var \XF\Entity\ThreadRedirect $redirect */
		$redirect = $thread->getRelationOrDefault('Redirect', $saveNow ? false : true);
		$redirect->target_url = $this->app()->router('public')->buildLink('nopath:threads', $targetThread);
		$redirect->redirect_key = "thread-{$targetThread->thread_id}-{$thread->node_id}-";
		$redirect->expiry_date = $expiryLength ? \XF::$time + $expiryLength : 0;

		if ($saveNow)
		{
			$redirect->save();
		}

		return $redirect;
	}

	public function deleteRedirectsByKey($key)
	{
		$redirects = $this->finder('XF:ThreadRedirect')
			->where('redirect_key', 'like', $key)
			->with('Thread')
			->fetch();

		$db = $this->db();
		$db->beginTransaction();

		foreach ($redirects AS $redirect)
		{
			$this->deleteRedirect($redirect, false, false);
		}

		$db->commit();

		return $redirects;
	}

	protected function deleteRedirect(\XF\Entity\ThreadRedirect $redirect, $throw = true, $newTransaction = true)
	{
		if ($redirect->Thread)
		{
			$redirect->Thread->delete($throw, $newTransaction);
		}
		else
		{
			$redirect->delete($throw, $newTransaction);
		}
	}

	public function deleteRedirectsToThread(\XF\Entity\Thread $thread)
	{
		$key = 'thread-' . $thread->thread_id . '-%';
		return $this->deleteRedirectsByKey($key);
	}

	public function deleteRedirectsToThreadInForum(\XF\Entity\Thread $thread, \XF\Entity\Forum $forum)
	{
		$key = 'thread-' . $thread->thread_id . '-' . $forum->node_id . '-';
		return $this->deleteRedirectsByKey($key);
	}

	public function rebuildThreadRedirectKey(\XF\Entity\Thread $thread)
	{
		/** @var \XF\Entity\ThreadRedirect $redirect */
		$redirect = $thread->Redirect;
		if ($redirect)
		{
			$key = preg_replace('/^(thread-\d+)-\d+-$/', '$1-' . $thread->node_id . '-', $redirect->redirect_key);
			if ($key != $redirect->redirect_key)
			{
				$redirect->redirect_key = $key;
				$redirect->save();
			}
		}
	}

	public function pruneThreadRedirects($cutOff = null)
	{
		if ($cutOff === null)
		{
			$cutOff = \XF::$time;
		}

		$redirects = $this->finder('XF:ThreadRedirect')
			->where('expiry_date', '>', 0)
			->where('expiry_date', '<=', $cutOff)
			->with('Thread')
			->fetch();

		$db = $this->db();
		$db->beginTransaction();

		foreach ($redirects AS $redirect)
		{
			$this->deleteRedirect($redirect, false, false);
		}

		$db->commit();
	}
}