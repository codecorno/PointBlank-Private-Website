<?php

namespace XF\Poll;

class ResponseEditor
{
	/** @var \XF\Entity\Poll  */
	protected $poll;

	/** @var \XF\Entity\PollResponse[] */
	protected $existingResponses = [];

	protected $deleteResponses = [];
	protected $replaceResponses = [];
	protected $addResponses = [];

	public function __construct(\XF\Entity\Poll $poll)
	{
		if ($poll->poll_id)
		{
			$responses = $poll->Responses->toArray();
		}
		else
		{
			$responses = [];
		}

		$this->poll = $poll;
		$this->existingResponses = $responses;
	}

	public function getExistingResponses()
	{
		return $this->existingResponses;
	}

	public function addResponses(array $responses)
	{
		foreach ($responses AS $response)
		{
			$this->addResponse($response);
		}
	}

	public function addResponse($response)
	{
		$response = trim($response);
		if (strlen($response))
		{
			$this->addResponses[] = $response;
			return true;
		}
		else
		{
			return false;
		}
	}

	public function getAddedResponses()
	{
		return $this->addResponses;
	}

	public function deleteResponse($responseId)
	{
		if (!isset($this->existingResponses[$responseId]))
		{
			return false;
		}

		$this->deleteResponses[$responseId] = $responseId;
		return true;
	}

	public function getDeletedResponses()
	{
		return $this->deleteResponses;
	}

	public function replaceResponse($responseId, $newResponse)
	{
		if (!isset($this->existingResponses[$responseId]))
		{
			return false;
		}

		$newResponse = trim($newResponse);
		if (!strlen($newResponse))
		{
			$this->deleteResponse($responseId);
		}
		else
		{
			$this->replaceResponses[$responseId] = $newResponse;
		}

		return true;
	}

	public function getReplacedResponses()
	{
		return $this->replaceResponses;
	}

	public function updateResponses(array $responses)
	{
		foreach ($responses AS $id => $response)
		{
			// this handles deleting if needed
			$this->replaceResponse($id, $response);
		}
	}

	public function countResponses()
	{
		return count($this->existingResponses) + count($this->addResponses) - count($this->deleteResponses);
	}

	public function saveChanges()
	{
		if (!$this->poll->poll_id)
		{
			throw new \LogicException("Poll must be saved before responses can be saved");
		}

		if (!$this->addResponses && !$this->deleteResponses && !$this->replaceResponses)
		{
			return;
		}

		$db = $this->poll->em()->getDb();
		$existingResponses = $this->existingResponses;

		$db->beginTransaction();

		foreach ($this->deleteResponses AS $responseId)
		{
			$response = $existingResponses[$responseId];
			$response->delete(true, false);
		}

		foreach ($this->replaceResponses AS $responseId => $value)
		{
			$response = $existingResponses[$responseId];
			$response->response = utf8_substr($value, 0, 100);
			$response->save(true, false);
		}

		foreach ($this->addResponses AS $value)
		{
			$response = \XF::em()->create('XF:PollResponse');
			$response->poll_id = $this->poll->poll_id;
			$response->response = utf8_substr($value, 0, 100);
			$response->save(true, false);
		}

		$db->commit();

		$this->poll->clearCache('Responses');
	}

	public function getResponseCountErrorMessage($maxResponses = null)
	{
		$count = $this->countResponses();
		if ($count < 2)
		{
			return \XF::phrase('please_enter_at_least_two_poll_responses');
		}

		if ($maxResponses && $count > $maxResponses)
		{
			return \XF::phrase('too_many_poll_responses_have_been_entered');
		}

		return null;
	}
}