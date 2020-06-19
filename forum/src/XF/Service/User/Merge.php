<?php

namespace XF\Service\User;

use XF\Entity\User;

class Merge extends \XF\Service\AbstractService
{
	use \XF\MultiPartRunnerTrait;

	/**
	 * @var User
	 */
	protected $source;

	/**
	 * @var User
	 */
	protected $target;

	protected $steps = [
		'stepPreMerge',
		'stepMergeUserData',
		'stepReassignContent',
		'stepFinalizeMerge'
	];

	public function __construct(\XF\App $app)
	{
		parent::__construct($app);

		// not passing in the source/target here to make the code explicit as to which is which
	}

	public function setSource(User $source)
	{
		$this->source = $source;

		return $this;
	}

	/**
	 * @return User
	 */
	public function getSource()
	{
		return $this->source;
	}

	public function setTarget(User $target)
	{
		$this->target = $target;

		return $this;
	}

	/**
	 * @return User
	 */
	public function getTarget()
	{
		return $this->target;
	}

	protected function getSteps()
	{
		return $this->steps;
	}

	public function merge($maxRunTime = 0)
	{
		if (!$this->source)
		{
			throw new \LogicException("No source user provided");
		}
		if (!$this->target)
		{
			throw new \LogicException("No target user provided");
		}

		if ($this->source->user_id == $this->target->user_id)
		{
			// no work to do
			return \XF\ContinuationResult::completed();
		}

		$this->db()->beginTransaction();
		$result = $this->runLoop($maxRunTime);
		$this->db()->commit();

		return $result;
	}

	protected function stepPreMerge()
	{
		// this would end up liking your own content
		$this->db()->delete(
			'xf_reaction_content',
			'reaction_user_id = ? AND content_user_id = ?',
			[$this->source->user_id, $this->target->user_id]
		);
		// TODO: count differences? rebuild?
	}

	protected function stepMergeUserData()
	{
		$this->combineData();

		$this->target->save();
	}

	protected function combineData()
	{
		$this->target->message_count += $this->source->message_count;
		$this->target->reaction_score += $this->source->reaction_score;
		$this->target->conversations_unread += $this->source->conversations_unread;
		$this->target->alerts_unread += $this->source->alerts_unread;
		$this->target->warning_points += $this->source->warning_points;
		$this->target->register_date = min($this->target->register_date, $this->source->register_date);
		$this->target->last_activity = max($this->target->last_activity, $this->source->last_activity);

		$this->app->fire('user_merge_combine', [$this->target, $this->source, $this]);
	}

	protected function stepReassignContent($lastOffset, $maxRunTime)
	{
		/** @var \XF\Service\User\ContentChange $contentChanger */
		$contentChanger = $this->service('XF:User\ContentChange', $this->source);
		$contentChanger->setupForMerge($this->target);

		if (is_array($lastOffset))
		{
			list($changeStep, $changeLastOffset) = $lastOffset;
			$contentChanger->restoreState($changeStep, $changeLastOffset);
		}

		$result = $contentChanger->apply($maxRunTime);
		if ($result->isCompleted())
		{
			return null;
		}
		else
		{
			$continueData = $result->getContinueData();
			return [$continueData['currentStep'], $continueData['lastOffset']];
		}
	}

	protected function stepFinalizeMerge()
	{
		$this->source->delete();

		$this->postMergeCleanUp();
	}

	protected function postMergeCleanUp()
	{
		/** @var \XF\Repository\Trophy $trophyRepo */
		$trophyRepo = $this->repository('XF:Trophy');
		$trophyRepo->recalculateUserTrophyPoints($this->target);

		// anything left over is where both users were in the same conversation so we can remove the old records
		$this->db()->delete('xf_conversation_recipient', 'user_id = ?', $this->source->user_id);

		// prevent situations where a user can be following/ignoring themselves
		$this->db()->delete(
			'xf_user_follow',
			'user_id = ? AND (follow_user_id = ? OR follow_user_id = ?)',
			[$this->target->user_id, $this->target->user_id, $this->source->user_id]
		);
		$this->db()->delete(
			'xf_user_ignored',
			'user_id = ? AND (ignored_user_id = ? OR ignored_user_id = ?)',
			[$this->target->user_id, $this->target->user_id, $this->source->user_id]
		);

		$this->repository('XF:UserFollow')->rebuildFollowingCache($this->target->user_id);
		$this->repository('XF:UserIgnored')->rebuildIgnoredCache($this->target->user_id);
	}
}