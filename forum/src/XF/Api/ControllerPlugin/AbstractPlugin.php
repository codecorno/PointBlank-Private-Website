<?php

namespace XF\Api\ControllerPlugin;

/**
 * @method void assertApiScope($scope)
 * @method void assertApiScopeByRequestMethod($scope, array $extraMapping = [])
 * @method void assertRequiredApiInput($inputKeys)
 * @method \XF\Mvc\Entity\Entity assertViewableApiRecord($identifier, $id, $with = null, $phraseKey = null)
 * @method array getPaginationData($results, $page, $perPage, $total)
 * @method \XF\Mvc\Reply\Error apiError($errorMessage, $errorCode, array $params = null, $httpCode = 400)
 * @method \XF\Api\Mvc\Reply\ApiResult apiResult($result)
 * @method \XF\Api\Mvc\Reply\ApiResult apiSuccess(array $extra = [])
 */
abstract class AbstractPlugin extends \XF\ControllerPlugin\AbstractPlugin
{
	public function __construct(\XF\Mvc\Controller $controller)
	{
		if (!($controller instanceof \XF\Api\Controller\AbstractController))
		{
			throw new \LogicException("API controller plugins only work with API controllers");
		}

		parent::__construct($controller);
	}
}