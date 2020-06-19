<?php

namespace XF\Import\Data;

class PollResponse extends AbstractEmulatedData
{
	protected $votes = [];

	public function getImportType()
	{
		return 'poll_response';
	}

	public function getEntityShortName()
	{
		return 'XF:PollResponse';
	}

	public function addVote($userId, $voteDate = 0)
	{
		$this->votes[$userId] = $voteDate;
	}

	protected function preSave($oldId)
	{
		$this->forceNotEmpty('response', $oldId);
	}

	protected function postSave($oldId, $newId)
	{
		if ($this->votes)
		{
			$insert = [];
			foreach ($this->votes AS $userId => $voteDate)
			{
				$insert[] = [
					'user_id' => $userId,
					'poll_response_id' => $newId,
					'poll_id' => $this->poll_id,
					'vote_date' => $voteDate
				];
			}

			$this->db()->insertBulk('xf_poll_vote', $insert, false, 'vote_date = VALUES(vote_date)');
		}
	}
}