<?php

namespace XF\Mvc\Reply;

use XF\Mvc\RouteMatch;

class Reroute extends AbstractReply
{
	/**
	 * @var \XF\Mvc\RouteMatch
	 */
	protected $match;

	public function __construct(RouteMatch $match)
	{
		$this->match = $match;
	}

	public function setMatch(RouteMatch $match)
	{
		$this->match = $match;
	}

	public function getMatch()
	{
		return $this->match;
	}
}