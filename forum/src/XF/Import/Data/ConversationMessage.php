<?php

namespace XF\Import\Data;

class ConversationMessage extends AbstractEmulatedData
{
	protected $loggedIp;

	public function getImportType()
	{
		return 'conversation_message';
	}

	public function getEntityShortName()
	{
		return 'XF:ConversationMessage';
	}

	public function setLoggedIp($loggedIp)
	{
		$this->loggedIp = $loggedIp;
	}

	protected function preSave($oldId)
	{
		$this->forceNotEmpty('message', $oldId);
	}

	protected function postSave($oldId, $newId)
	{
		$this->logIp($this->loggedIp, $this->message_date);
	}
}