<?php

namespace XF\NewsFeed;

class ProfilePost extends AbstractHandler
{
	public function getEntityWith()
	{
		return ['ProfileUser', 'ProfileUser.Privacy'];
	}
}