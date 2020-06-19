<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property string content_type
 * @property int content_id
 * @property int content_date
 *
 * GETTERS
 * @property Entity|null Content
 * @property array|null SpamDetails
 */
class ApprovalQueue extends Entity
{
	public function isInvalid()
	{
		return ($this->getHandler() && !$this->Content);
	}

	public function getHandler()
	{
		return $this->getApprovalQueueRepo()->getApprovalQueueHandler($this->content_type);
	}

	public function getDefaultActions()
	{
		$handler = $this->getHandler();

		if (!$handler)
		{
			return [];
		}

		$content = $this->getContent();
		$actions = $handler->getDefaultActions();

		if (method_exists($handler, 'actionSpamClean') && $content)
		{
			$user = null;
			if ($content instanceof \XF\Entity\User)
			{
				$user = $content;
			}
			else
			{
				if ($content->User)
				{
					$user = $content->User;
				}
			}

			if ($user)
			{
				if ($user->isPossibleSpammer())
				{
					$actions['spam_clean'] = \XF::phrase('spam_clean');
				}
			}
		}

		return $actions;
	}

	/**
	 * @return Entity|null
	 */
	public function getContent()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContent($this->content_id) : null;
	}

	/**
	 * @return array|null
	 */
	public function getSpamDetails()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getSpamDetails($this->content_id) : null;
	}

	public function setContent(Entity $content = null)
	{
		$this->_getterCache['Content'] = $content;
	}

	public function setSpamDetails($spamDetails)
	{
		$this->_getterCache['SpamDetails'] = $spamDetails;
	}

	protected function _postSave()
	{
		$this->rebuildUnapprovedCounts();
	}

	protected function _postDelete()
	{
		$this->rebuildUnapprovedCounts();
	}

	protected function rebuildUnapprovedCounts()
	{
		\XF::runOnce('unapprovedCountsRebuild', function()
		{
			$this->getApprovalQueueRepo()->rebuildUnapprovedCounts();
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_approval_queue';
		$structure->shortName = 'XF:ApprovalQueue';
		$structure->primaryKey = ['content_type', 'content_id'];
		$structure->columns = [
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'content_date' => ['type' => self::UINT, 'default' => \XF::$time, 'required' => true]
		];
		$structure->getters = [
			'Content' => true,
			'SpamDetails' => true
		];
		$structure->relations = [];

		return $structure;
	}

	/**
	 * @return \XF\Repository\ApprovalQueue
	 */
	protected function getApprovalQueueRepo()
	{
		return $this->repository('XF:ApprovalQueue');
	}
}