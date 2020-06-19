<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int user_id
 * @property int poll_response_id
 * @property int poll_id
 * @property int vote_date
 *
 * RELATIONS
 * @property \XF\Entity\User User
 * @property \XF\Entity\PollResponse Response
 * @property \XF\Entity\Poll Poll
 */
class PollVote extends Entity
{
	protected function _preSave()
	{
		if ($this->isUpdate())
		{
			throw new \LogicException("Poll votes cannot be updated");
		}
	}

	protected function _postDelete()
	{
		$pollRepo = $this->getPollRepo();

		\XF::runOnce('rebuildPollData' . $this->poll_id, function() use ($pollRepo)
		{
			$pollRepo->rebuildPollData($this->poll_id);
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_poll_vote';
		$structure->shortName = 'XF:PollVote';
		$structure->primaryKey = ['poll_response_id', 'user_id'];
		$structure->columns = [
			'user_id' => ['type' => self::UINT, 'required' => true],
			'poll_response_id' => ['type' => self::UINT, 'required' => true],
			'poll_id' => ['type' => self::UINT, 'required' => true],
			'vote_date' => ['type' => self::UINT, 'default' => \XF::$time]
		];
		$structure->getters = [];
		$structure->relations = [
			'User' => [
				'entity' => 'XF:User',
				'type' => self::TO_ONE,
				'conditions' => 'user_id',
				'primary' => true
			],
			'Response' => [
				'entity' => 'XF:PollResponse',
				'type' => self::TO_ONE,
				'conditions' => 'poll_response_id',
				'primary' => true
			],
			'Poll' => [
				'entity' => 'XF:Poll',
				'type' => self::TO_ONE,
				'conditions' => 'poll_id',
				'primary' => true
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