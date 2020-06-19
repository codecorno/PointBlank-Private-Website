<?php

namespace XF\Alert;

class ProfilePostComment extends AbstractHandler
{
	public function getOptOutActions()
	{
		$visitor = \XF::visitor();

		if ($visitor->canViewProfilePosts())
		{
			return [
				'your_profile',
				'your_post',
				'other_commenter',
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
		return 20005;
	}
}