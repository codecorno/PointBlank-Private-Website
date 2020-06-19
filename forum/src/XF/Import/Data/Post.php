<?php

namespace XF\Import\Data;

class Post extends AbstractEmulatedData
{
	use HasDeletionLogTrait;

	protected $loggedIp;

	public function getImportType()
	{
		return 'post';
	}

	public function getEntityShortName()
	{
		return 'XF:Post';
	}

	public function setLoggedIp($loggedIp)
	{
		$this->loggedIp = $loggedIp;
	}

	protected function preSave($oldId)
	{
		$this->forceNotEmpty('username', $oldId);
		$this->forceNotEmpty('message', $oldId);
	}

	protected function postSave($oldId, $newId)
	{
		$this->logIp($this->loggedIp, $this->post_date);
		$this->insertStateRecord($this->message_state, $this->post_date);

		if ($this->message_state == 'visible' && $this->user_id)
		{
			$this->db()->insert('xf_thread_user_post', [
				'thread_id' => $this->thread_id,
				'user_id' => $this->user_id,
				'post_count' => 1
			], false, 'post_count = post_count + VALUES(post_count)');
		}
	}
}