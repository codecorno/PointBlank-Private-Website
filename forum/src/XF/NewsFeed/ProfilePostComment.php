<?php

namespace XF\NewsFeed;

class ProfilePostComment extends AbstractHandler
{
	public function getEntityWith()
	{
		return ['ProfilePost', 'ProfilePost.ProfileUser', 'ProfilePost.ProfileUser.Privacy'];
	}
}