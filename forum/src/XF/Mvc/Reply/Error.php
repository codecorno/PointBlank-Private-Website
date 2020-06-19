<?php

namespace XF\Mvc\Reply;

class Error extends AbstractReply
{
	protected $errors = [];

	public function __construct($errors, $responseCode = 200)
	{
		$this->setErrors($errors, false);
		$this->setResponseCode($responseCode);
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function setErrors($errors, $append = true)
	{
		if (!is_array($errors))
		{
			$errors = [$errors];
		}

		if ($append)
		{
			$this->errors = array_merge($this->errors, $errors);
		}
		else
		{
			$this->errors = $errors;
		}
	}

	public function addError($error)
	{
		$this->errors[] = $error;
	}
}