<?php

namespace XF\Import\Data;

class Poll extends AbstractEmulatedData
{
	/**
	 * @var PollResponse[]
	 */
	protected $responses = [];

	public function getImportType()
	{
		return 'poll';
	}

	public function getEntityShortName()
	{
		return 'XF:Poll';
	}

	public function addResponse($oldResponseId, PollResponse $response)
	{
		$this->responses[$oldResponseId] = $response;
	}

	protected function preSave($oldId)
	{
		if (count($this->responses) < 2)
		{
			return false;
		}

		return null;
	}

	protected function postSave($oldId, $newId)
	{
		foreach ($this->responses AS $oldResponseId => $response)
		{
			$response->poll_id = $newId;
			$response->log(false);
			$response->checkExisting(false);
			$response->useTransaction(false);

			$response->save($oldResponseId);
		}

		if ($this->content_type == 'thread')
		{
			$this->db()->update('xf_thread',
				['discussion_type' => 'poll'],
				'thread_id = ?', $this->content_id);
		}

		// let the job rebuild this data
	}
}