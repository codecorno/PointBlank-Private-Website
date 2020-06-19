<?php

namespace XF\Api\Mvc\Reply;

use XF\Mvc\Reply\AbstractReply;

class ApiResult extends AbstractReply
{
	/**
	 * @var \XF\Api\Result\ResultInterface
	 */
	protected $apiResult;

	public function __construct(\XF\Api\Result\ResultInterface $result)
	{
		$this->setApiResult($result);
	}

	public function setApiResult(\XF\Api\Result\ResultInterface $result)
	{
		$this->apiResult = $result;
	}

	public function getApiResult()
	{
		return $this->apiResult;
	}
}