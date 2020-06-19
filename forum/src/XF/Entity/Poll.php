<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null poll_id
 * @property string content_type
 * @property int content_id
 * @property string question
 * @property array responses
 * @property int voter_count
 * @property bool public_votes
 * @property int max_votes
 * @property int close_date
 * @property bool change_vote
 * @property bool view_results_unvoted
 *
 * GETTERS
 * @property \XF\Poll\AbstractHandler|null Handler
 * @property Entity|null Content
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\PollResponse[] Responses
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\PollVote[] Votes
 */
class Poll extends Entity
{
	public function isClosed()
	{
		return ($this->close_date && $this->close_date < \XF::$time);
	}

	public function canVote(&$error = null)
	{
		if ($this->isClosed())
		{
			return false;
		}

		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			return false;
		}

		$handler = $this->Handler;
		$content = $this->Content;
		if (!$handler || !$content || !$handler->canVote($content, $this, $error))
		{
			return false;
		}

		if ($this->change_vote)
		{
			return true;
		}
		else
		{
			return !$this->hasVoted();
		}
	}

	public function canViewResults(&$error = null)
	{
		return ($this->view_results_unvoted || $this->isClosed() || $this->hasVoted());
	}

	public function canViewContent(&$error = null)
	{
		$handler = $this->getHandler();
		$content = $this->getContent();
		return ($content && $handler) ? $handler->canViewContent($content, $error) : false;
	}

	public function canEdit(&$error = null)
	{
		$handler = $this->Handler;
		$content = $this->Content;
		return ($handler && $content && $handler->canEdit($content, $this, $error));
	}

	public function canDelete(&$error = null)
	{
		$handler = $this->Handler;
		$content = $this->Content;
		return ($handler && $content && $handler->canDelete($content, $this, $error));
	}

	public function canEditDetails(&$error = null)
	{
		$handler = $this->Handler;
		$content = $this->Content;
		if ($handler && $content && $handler->canAlwaysEditDetails($content, $this, $error))
		{
			return true;
		}

		return ($this->voter_count == 0);
	}

	public function canEditMaxVotes(&$error = null)
	{
		return ($this->max_votes != 0 || !$this->voter_count);
	}

	public function canChangePollVisibility(&$error = null)
	{
		return ($this->public_votes || !$this->voter_count);
	}

	public function hasVoted($responseId = null, User $user = null)
	{
		$user = $user ?: \XF::visitor();
		$userId = $user->user_id;

		if (!$userId)
		{
			return false;
		}

		$responses = $this->responses;
		if ($responseId !== null)
		{
			return isset($responses[$responseId]['voters'][$userId]);
		}
		else
		{
			foreach ($responses AS $response)
			{
				if (isset($response['voters'][$userId]))
				{
					return true;
				}
			}

			return false;
		}
	}

	public function getVotePercentage($totalVotes)
	{
		if ($this->voter_count)
		{
			return ($totalVotes / $this->voter_count) * 100;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * @return \XF\Poll\ResponseEditor
	 */
	public function getResponseEditor()
	{
		return new \XF\Poll\ResponseEditor($this);
	}

	/**
	 * @return \XF\Poll\AbstractHandler|null
	 */
	public function getHandler()
	{
		return $this->getPollRepo()->getPollHandler($this->content_type);
	}

	/**
	 * @return Entity|null
	 */
	public function getContent()
	{
		$handler = $this->getHandler();
		return $handler ? $handler->getContent($this->content_id) : null;
	}

	public function setContent(Entity $content = null)
	{
		$this->_getterCache['Content'] = $content;
	}

	public function getLink($action, $extraParams = [])
	{
		$handler = $this->getHandler();
		$content = $this->getContent();
		return $content ? $handler->getPollLink($action, $content, $extraParams) : null;
	}

	public function setMaxVotes($type, $value = null)
	{
		if (is_int($type))
		{
			$value = $type;
			$type = 'number';
		}

		switch ($type)
		{
			case 'single':
				$this->max_votes = 1;
				break;

			case 'unlimited':
				$this->max_votes = 0;
				break;

			default:
				if ($value === null)
				{
					$value = $type;
				}
				$this->max_votes = max(0, intval($value));
				break;
		}
	}

	public function setCloseDateRelative($value, $unit)
	{
		$value = max(0, intval($value));
		if (!$value)
		{
			$this->error(\XF::phraseDeferred('close_poll_after_value_nonsense'), 'close_length');
			return;
		}

		$this->close_date = min(
			pow(2,32) - 1, strtotime("+$value $unit")
		);
	}

	protected function _preSave()
	{
		if ($this->isUpdate()
			&& $this->isChanged('max_votes')
			&& $this->voter_count
			&& $this->max_votes != 0 // unlimited is always ok
		)
		{
			$newMax = $this->max_votes;
			$oldMax = $this->getExistingValue('max_votes');
			if ($oldMax == 0 || $newMax < $oldMax)
			{
				$this->error(\XF::phraseDeferred('maximum_selectable_responses_value_may_not_be_reduced'), 'max_votes');
			}
		}

		if ($this->isUpdate() && $this->isChanged('voter_count'))
		{
			$this->responses = $this->getPollRepo()->getResponseCacheData($this->poll_id);
		}
	}

	protected function _postSave()
	{
		if ($this->isInsert())
		{
			$content = $this->Content;
			if ($content)
			{
				$this->Handler->finalizeCreation($content, $this);
			}
		}
	}

	protected function _postDelete()
	{
		$id = $this->poll_id;

		$this->db()->delete('xf_poll_response', 'poll_id = ?', $id);
		$this->db()->delete('xf_poll_vote', 'poll_id = ?', $id);

		$content = $this->Content;
		if ($content && $content->exists())
		{
			$this->Handler->finalizeDeletion($content, $this);
		}
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_poll';
		$structure->shortName = 'XF:Poll';
		$structure->contentType = 'poll';
		$structure->primaryKey = 'poll_id';
		$structure->columns = [
			'poll_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'content_type' => ['type' => self::STR, 'maxLength' => 25, 'required' => true],
			'content_id' => ['type' => self::UINT, 'required' => true],
			'question' => ['type' => self::STR, 'maxLength' => 100,
				'required' => 'please_enter_poll_question',
				'censor' => true
			],
			'responses' => ['type' => self::JSON_ARRAY, 'default' => []],
			'voter_count' => ['type' => self::UINT, 'default' => 0],
			'public_votes' => ['type' => self::BOOL, 'default' => false],
			'max_votes' => ['type' => self::UINT, 'default' => 1, 'forced' => true, 'max' => 255],
			'close_date' => ['type' => self::UINT, 'default' => 0],
			'change_vote' => ['type' => self::BOOL, 'default' => false],
			'view_results_unvoted' => ['type' => self::BOOL, 'default' => true]
		];
		$structure->getters = [
			'Handler' => true,
			'Content' => true
		];
		$structure->relations = [
			'Responses' => [
				'entity' => 'XF:PollResponse',
				'type' => self::TO_MANY,
				'conditions' => 'poll_id'
			],
			'Votes' => [
				'entity' => 'XF:PollVote',
				'type' => self::TO_MANY,
				'conditions' => 'poll_id'
			]
		];

		return $structure;
	}

	/**
	 * @return \XF\Repository\Poll
	 */
	protected function getPollRepo()
	{
		return $this->repository('XF:Poll');
	}
}