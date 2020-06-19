<?php

namespace XF\Repository;

use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class Draft extends Repository
{
	/**
	 * @param $draftKey
	 * @param \XF\Entity\User $user
	 *
	 * @return null|\XF\Entity\Draft
	 */
	public function getDraftByKeyAndUser($draftKey, \XF\Entity\User $user)
	{
		if (!$user->user_id)
		{
			return null;
		}

		return $this->finder('XF:Draft')->where([
			'draft_key' => $draftKey,
			'user_id' => $user->user_id
		])->fetchOne();
	}

	public function deleteDraft($draftKey, \XF\Entity\User $user)
	{
		if (!$user->user_id)
		{
			return false;
		}

		$draft = $this->getDraftByKeyAndUser($draftKey, $user);
		if ($draft)
		{
			$draft->delete();
			return true;
		}
		else
		{
			return false;
		}
	}

	public function pruneDrafts($cutOff = null)
	{
		if ($cutOff === null)
		{
			if (!$this->options()->saveDrafts['enabled'])
			{
				return 0;
			}

			$cutOff = \XF::$time - 3600 * $this->options()->saveDrafts['lifetime'];
		}

		return $this->db()->delete('xf_draft', 'last_update < ?', $cutOff);
	}
}