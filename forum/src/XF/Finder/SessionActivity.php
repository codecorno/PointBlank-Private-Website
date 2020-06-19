<?php

namespace XF\Finder;

use XF\Mvc\Entity\Finder;

class SessionActivity extends Finder
{
	public function restrictType($type, $applyVisibilityRestriction = true)
	{
		switch ($type)
		{
			case '':
				// no fetch limits
				if ($applyVisibilityRestriction)
				{
					$this->applyMemberVisibilityRestriction();
				}
				break;

			case 'member':
				$this->where('user_id', '>', 0)->exists('User');
				if ($applyVisibilityRestriction)
				{
					$this->applyMemberVisibilityRestriction();
				}
				break;

			case 'guest':
				$this->where([
					'user_id' => 0,
					'robot_key' => ''
				]);
				break;

			case 'robot':
				$this->where('user_id', 0)
					->where('robot_key', '<>', '');
				break;

			default:
				throw new \InvalidArgumentException("Unknown session activity type '$type'");
		}

		return $this;
	}

	public function applyMemberVisibilityRestriction()
	{
		$visitor = \XF::visitor();

		if (!$visitor->canBypassUserPrivacy())
		{
			$constraints = [
				['user_id' => 0],
				['User.visible' => 1, 'User.user_state' => 'valid']
			];

			if ($visitor->user_id)
			{
				$constraints[] = ['user_id' => $visitor->user_id];
			}

			$this->whereOr($constraints);
		}

		return $this;
	}

	public function activeOnly()
	{
		$cutOff = \XF::$time - $this->app()->options()->onlineStatusTimeout * 60;
		$this->where('view_date', '>=', $cutOff);

		return $this;
	}

	public function withFullUser()
	{
		$this->with(['User', 'User.Option', 'User.Profile']);

		return $this;
	}
}