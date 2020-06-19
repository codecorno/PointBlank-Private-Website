<?php

namespace XF\Import\Data;

class ProfilePostComment extends AbstractEmulatedData
{
	use HasDeletionLogTrait;

	protected $loggedIp;

	public function getImportType()
	{
		return 'profile_post_comment';
	}

	public function getEntityShortName()
	{
		return 'XF:ProfilePostComment';
	}

	public function setLoggedIp($loggedIp)
	{
		$this->loggedIp = $loggedIp;
	}

	protected function preSave($oldId)
	{
		$this->forceNotEmpty('username', $oldId);
	}

	protected function postSave($oldId, $newId)
	{
		$this->logIp($this->loggedIp, $this->comment_date);
		$this->insertStateRecord($this->message_state, $this->comment_date);
	}
}