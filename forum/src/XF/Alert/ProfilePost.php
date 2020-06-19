<?php

namespace XF\Alert;

class ProfilePost extends AbstractHandler
{
	public function getEntityWith()
	{
		return ['ProfileUser', 'ProfileUser.Privacy'];
	}

	public function getOptOutActions()
	{
		$visitor = \XF::visitor();

		if ($visitor->canViewProfilePosts())
		{
			return [
				'insert',
				'mention',
				'reaction'
			];
		}
		else
		{
			return [];
		}
	}

	public function getOptOutDisplayOrder()
	{
		return 20000;
	}
}