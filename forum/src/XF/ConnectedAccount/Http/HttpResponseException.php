<?php

namespace XF\ConnectedAccount\Http;

use OAuth\Common\Http\Exception\TokenResponseException;

class HttpResponseException extends TokenResponseException
{
	protected $responseContent;

	public function setResponseContent($responseContent)
	{
		$this->responseContent = $responseContent;
	}

	public function getResponseContent()
	{
		return $this->responseContent;
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}
}