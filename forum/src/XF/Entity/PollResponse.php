<?php

namespace XF\Entity;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

/**
 * COLUMNS
 * @property int|null poll_response_id
 * @property int poll_id
 * @property string response
 * @property int response_vote_count
 * @property array voters
 *
 * RELATIONS
 * @property \XF\Mvc\Entity\AbstractCollection|\XF\Entity\PollVote[] Votes
 */
class PollResponse extends Entity
{
	public function updateForNewVote(User $user)
	{
		$voters = $this->voters;
		$voters[$user->user_id] = true;

		$this->fastUpdate([
			'response_vote_count' => $this->response_vote_count + 1,
			'voters' => $voters
		]);
	}

	protected function _postSave()
	{
		$this->rebuildPollData();
	}


	protected function _postDelete()
	{
		$this->db()->delete('xf_poll_vote', 'poll_response_id = ?', $this->poll_response_id);

		$this->rebuildPollData();
	}

	protected function rebuildPollData()
	{
		$pollRepo = $this->getPollRepo();
		\XF::runOnce('rebuildPollData' . $this->poll_id, function() use ($pollRepo)
		{
			$pollRepo->rebuildPollData($this->poll_id);
		});
	}

	public static function getStructure(Structure $structure)
	{
		$structure->table = 'xf_poll_response';
		$structure->shortName = 'XF:PollResponse';
		$structure->primaryKey = 'poll_response_id';
		$structure->columns = [
			'poll_response_id' => ['type' => self::UINT, 'autoIncrement' => true, 'nullable' => true],
			'poll_id' => ['type' => self::UINT, 'required' => true],
			'response' => ['type' => self::STR, 'maxLength' => 100,
				'required' => true,
				'censor' => true
			],
			'response_vote_count' => ['type' => self::UINT, 'default' => 0],
			'voters' => ['type' => self::JSON_ARRAY, 'default' => []]
		];
		$structure->getters = [];
		$structure->relations = [
			'Votes' => [
				'entity' => 'XF:PollVote',
				'type' => self::TO_MANY,
				'conditions' => [
					['poll_response_id', '=', '$poll_response_id'],
					['poll_id', '=', '$poll_id']
				]
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