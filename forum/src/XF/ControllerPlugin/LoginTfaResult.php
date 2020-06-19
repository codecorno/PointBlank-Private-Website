<?php

namespace XF\ControllerPlugin;

class LoginTfaResult
{
	const RESULT_ERROR = 1;
	const RESULT_FORM = 2;
	const RESULT_SKIPPED = 3;
	const RESULT_SUCCESS = 4;

	protected $result;

	protected $error;
	protected $formParams;
	protected $redirect;

	/**
	 * @var null|\XF\Entity\User
	 */
	protected $user;

	public function getResult()
	{
		return $this->result;
	}

	public static function newError($error)
	{
		$res = new self();
		$res->result = self::RESULT_ERROR;
		$res->error = $error;
		return $res;
	}

	public function getError()
	{
		return $this->error;
	}

	public static function newForm(array $params)
	{
		$res = new self();
		$res->result = self::RESULT_FORM;
		$res->formParams = $params;
		return $res;
	}

	public function getFormParams()
	{
		return $this->formParams;
	}

	public static function newSkipped($redirect)
	{
		$res = new self();
		$res->result = self::RESULT_SKIPPED;
		$res->redirect = $redirect;
		return $res;
	}

	public function getRedirect()
	{
		return $this->redirect;
	}

	public static function newSuccess(\XF\Entity\User $user, $redirect)
	{
		$res = new self();
		$res->result = self::RESULT_SUCCESS;
		$res->user = $user;
		$res->redirect = $redirect;
		return $res;
	}

	/**
	 * @return null|\XF\Entity\User
	 */
	public function getUser()
	{
		return $this->user;
	}
}