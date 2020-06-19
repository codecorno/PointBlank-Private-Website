<?php

namespace XF\Repository;

use XF\Mvc\Entity\Repository;

class Poll extends Repository
{
	public function resetPollVotes(\XF\Entity\Poll $poll)
	{
		$db = $this->db();

		$db->beginTransaction();

		$db->delete('xf_poll_vote', 'poll_id = ?', $poll->poll_id);
		$this->rebuildPollData($poll->poll_id);

		$db->commit();
	}

	public function voteOnPoll(\XF\Entity\Poll $poll, $votes, \XF\Entity\User $voter = null)
	{
		$voter = $voter ?: \XF::visitor();
		$responses = $poll->Responses;

		if (!is_array($votes))
		{
			$votes = [$votes];
		}

		foreach ($votes AS $k => $responseId)
		{
			if (!isset($responses[$responseId]))
			{
				unset($votes[$k]);
			}
		}

		if (!$votes)
		{
			return false;
		}

		$db = $this->db();

		$db->beginTransaction();

		$rawPoll = $db->fetchRow("
			SELECT *
			FROM xf_poll
			WHERE poll_id = ?
			FOR UPDATE
		", $poll->poll_id);

		$previousVotes = $db->delete('xf_poll_vote', 'poll_id = ? AND user_id = ?', [$poll->poll_id, $voter->user_id]);
		$newVoter = ($previousVotes == 0);

		foreach ($votes AS $responseId)
		{
			// votes have been validated against this poll

			/** @var \XF\Entity\PollResponse $response */
			$response = $responses[$responseId];

			$inserted = $db->insert('xf_poll_vote', [
				'user_id' => $voter->user_id,
				'poll_response_id' => $responseId,
				'poll_id' => $poll->poll_id,
				'vote_date' => \XF::$time
			], false, false, 'IGNORE');
			if ($newVoter && $inserted)
			{
				// can take a short cut as we know they haven't voted before
				// otherwise we need to do a rebuild
				$response->updateForNewVote($voter);
			}
		}

		$poll->clearCache('Votes');

		if ($newVoter)
		{
			$poll->voter_count = $rawPoll['voter_count'] + 1;
			$poll->save(); // triggers the response cache rebuild
		}
		else
		{
			$this->rebuildPollData($poll->poll_id);
		}

		$db->commit();

		return true;
	}

	public function rebuildPollData($pollId)
	{
		$poll = $this->em->find('XF:Poll', $pollId);
		if (!$poll)
		{
			return false;
		}

		$db = $this->db();

		$results = $db->fetchAll("
			SELECT *
			FROM xf_poll_vote
			WHERE poll_id = ?
		", $poll->poll_id);

		$votes = [];
		$voters = [];

		foreach ($results AS $vote)
		{
			$votes[$vote['poll_response_id']][$vote['user_id']] = true;
			$voters[$vote['user_id']] = true;
		}

		$db->beginTransaction();

		/** @var \XF\Entity\PollResponse $response */
		foreach ($poll->Responses AS $response)
		{
			$responseId = $response->poll_response_id;
			$responseVotes = isset($votes[$responseId]) ? $votes[$responseId] : [];

			$response->fastUpdate([
				'response_vote_count' => count($responseVotes),
				'voters' => $responseVotes
			]);
		}

		$voters = count($voters);
		$responseCache = $this->getResponseCacheData($poll->poll_id);

		$poll->fastUpdate([
			'voter_count' => $voters,
			'responses' => $responseCache
		]);

		$db->commit();

		return true;
	}

	public function getResponseCacheData($pollId)
	{
		$cache = [];

		$responses = $this->finder('XF:PollResponse')
			->where('poll_id', $pollId)
			->order('poll_response_id');

		foreach ($responses->fetch() AS $response)
		{
			$cache[$response->poll_response_id] = [
				'response' => $response->response,
				'response_vote_count' => $response->response_vote_count,
				'voters' => $response->voters
			];
		}

		return $cache;
	}

	/**
	 * @return \XF\Poll\AbstractHandler[]
	 */
	public function getPollHandlers()
	{
		$handlers = [];

		foreach (\XF::app()->getContentTypeField('poll_handler_class') AS $contentType => $handlerClass)
		{
			if (class_exists($handlerClass))
			{
				$handlerClass = \XF::extendClass($handlerClass);
				$handlers[$contentType] = new $handlerClass($contentType);
			}
		}

		return $handlers;
	}

	/**
	 * @param string $type
	 * @param bool $throw
	 *
	 * @return \XF\Poll\AbstractHandler|null
	 */
	public function getPollHandler($type, $throw = false)
	{
		$handlerClass = \XF::app()->getContentTypeFieldValue($type, 'poll_handler_class');
		if (!$handlerClass)
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("No poll handler for '$type'");
			}
			return null;
		}

		if (!class_exists($handlerClass))
		{
			if ($throw)
			{
				throw new \InvalidArgumentException("Poll handler for '$type' does not exist: $handlerClass");
			}
			return null;
		}

		$handlerClass = \XF::extendClass($handlerClass);
		return new $handlerClass($type);
	}
}