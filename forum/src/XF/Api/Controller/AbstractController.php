<?php

namespace XF\Api\Controller;

use XF\Mvc\Controller;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\RouteMatch;

abstract class AbstractController extends Controller
{
	protected $requestedVersion;

	protected function preDispatchType($action, ParameterBag $params)
	{
		$this->assertCorrectVersion($action);
		// TODO: check IP ban?
		$this->assertUserState($action);
		$this->assertViewingPermissions($action);
		$this->assertBoardActive($action);

		$this->preDispatchController($action, $params);
	}

	protected function preDispatchController($action, ParameterBag $params)
	{
	}

	public function setupFromMatch(RouteMatch $match)
	{
		$this->setResponseType('api');

		if ($match instanceof \XF\Api\Mvc\RouteMatch)
		{
			$this->requestedVersion = $match->getVersion();
		}
		else
		{
			$this->requestedVersion = \XF::API_VERSION;
		}
	}

	public function applyReplyChanges($action, ParameterBag $params, AbstractReply &$reply)
	{
		$reply->setViewOption('requestedApiVersion', $this->requestedVersion);

		if ($this->responseType && !$reply->getResponseType())
		{
			$reply->setResponseType($this->responseType);
		}
	}

	public function setupFromReply(AbstractReply $reply)
	{
	}

	public function setSectionContext($sectionContext)
	{
	}

	public function setDefaultSectionContext($sectionContext)
	{
	}

	public function setContainerKey($containerKey)
	{
	}

	public function setContentKey($contentKey)
	{
	}

	public function setViewOption($option, $value)
	{
	}

	public function assertApiScope($scope)
	{
		$key = \XF::apiKey();

		if (!$key->hasScope($scope))
		{
			throw $this->exception(
				$this->error(\XF::phrase('api_error.missing_scope', ['scope' => $scope]), 403)
			);
		}
	}

	public function assertApiScopeByRequestMethod($scope, array $extraMapping = [])
	{
		$modifier = 'write';
		$method = $this->request->getRequestMethod();

		if (isset($extraMapping[$method]))
		{
			$modifier = $extraMapping[$method];
		}
		else if ($method === 'get')
		{
			$modifier = 'read';
		}

		$this->assertApiScope("$scope:$modifier");
	}

	/**
	 * @param string $identifier
	 * @param mixed $id
	 * @param array|string|null $with
	 * @param string|null $phraseKey
	 *
	 * @return \XF\Mvc\Entity\Entity
	 *
	 * @throws \XF\Mvc\Reply\Exception|\LogicException
	 */
	public function assertViewableApiRecord($identifier, $id, $with = null, $phraseKey = null)
	{
		$record = $this->assertRecordExists($identifier, $id, $with, $phraseKey);

		if (!method_exists($record, 'canView'))
		{
			throw new \LogicException("assertViewableApiRecord requires the entity of type $identifier to implement canView()");
		}
		if (\XF::isApiCheckingPermissions() && !$record->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		return $record;
	}

	public function assertValidApiPage($page, $perPage, $total)
	{
		if ($perPage < 1 || $total < 1)
		{
			return;
		}

		$page = max(1, intval($page));
		$maxPage = ceil($total / $perPage);

		if ($page <= $maxPage)
		{
			return; // within the range
		}

		throw $this->exception(
			$this->apiError(
				\XF::phrase('invalid_page_requested'),
				'invalid_page',
				['max' => $maxPage]
			)
		);
	}

	public function assertRequiredApiInput($inputKeys)
	{
		if (!is_array($inputKeys))
		{
			$inputKeys = [$inputKeys];
		}

		$missing = [];

		foreach ($inputKeys AS $key)
		{
			if (!$this->request->exists($key))
			{
				$missing[] = $key;
			}
		}

		if ($missing)
		{
			throw $this->exception($this->requiredInputMissing($missing));
		}
	}

	public function requiredInputMissing($missing)
	{
		if (!is_array($missing))
		{
			$missing = [$missing];
		}

		return $this->apiError(
			\XF::phrase('required_input_missing_x', ['input' => implode(', ', $missing)]),
			'required_input_missing',
			['missing' => $missing]
		);
	}

	public function assertSuperUserKey()
	{
		$key = \XF::apiKey();
		if ($key->key_type !== 'super')
		{
			throw $this->exception($this->noPermission());
		}
	}

	public function assertRegisteredUser()
	{
		$visitor = \XF::visitor();
		if (!$visitor->user_id)
		{
			throw $this->exception($this->noPermission(\XF::phrase('login_required')));
		}
	}

	/**
	 * @throws \XF\Mvc\Reply\Exception
	 */
	public function assertSuperAdmin()
	{
		if (\XF::isApiBypassingPermissions())
		{
			return;
		}

		$visitor = \XF::visitor();
		if (!$visitor->is_admin || !$visitor->Admin || !$visitor->Admin->is_super_admin)
		{
			throw $this->exception($this->noPermission(\XF::phrase('you_must_be_super_admin_to_access_this_page')));
		}
	}

	/**
	 * @param string $permission
	 * @throws \XF\Mvc\Reply\Exception
	 */
	public function assertAdminPermission($permission)
	{
		if (\XF::isApiBypassingPermissions())
		{
			return;
		}

		if (!\XF::visitor()->hasAdminPermission($permission))
		{
			throw $this->exception($this->noPermission());
		}
	}

	public function assertUserState($action)
	{
		if (\XF::isApiBypassingPermissions())
		{
			return;
		}

		$visitor = \XF::visitor();

		if ($visitor->is_banned)
		{
			throw $this->exception(
				$this->error(\XF::phrase('you_have_been_banned'), 403)
			);
		}

		if ($visitor->user_state == 'rejected')
		{
			throw $this->exception(
				$this->error(\XF::phrase('your_account_has_been_rejected'), 403)
			);
		}

		if ($visitor->user_state == 'disabled')
		{
			throw $this->exception(
				$this->error(\XF::phrase('your_account_has_been_disabled'), 403)
			);
		}
	}

	public function assertViewingPermissions($action)
	{
		if (\XF::isApiBypassingPermissions())
		{
			return;
		}

		if (!\XF::visitor()->hasPermission('general', 'view'))
		{
			throw $this->exception($this->noPermission());
		}
	}

	public function assertBoardActive($action)
	{
		$options = $this->options();
		if (!$options->boardActive && !\XF::visitor()->is_admin)
		{
			throw $this->exception($this->message(new \XF\PreEscaped($options->boardInactiveMessage), $this->app->config('serviceUnavailableCode')));
		}
	}

	public function getPaginationData($results, $page, $perPage, $total)
	{
		$page = max(1, intval($page));
		$perPage = max(1, intval($perPage));
		$total = max(0, intval($total));
		$maxPage = ceil($total / $perPage);

		$page = min($page, $maxPage);

		return [
			'current_page' => $page,
			'last_page' => $maxPage,
			'per_page' => $perPage,
			'shown' => count($results),
			'total' => $total
		];
	}

	public function getAttachmentTempHashFromKey(
		$key, $expectedContentType, array $expectedContext, $allowExtraContext = false
	)
	{
		if (!$key)
		{
			return null;
		}

		/** @var \XF\Entity\ApiAttachmentKey $keyEnt */
		$keyEnt = $this->em()->find('XF:ApiAttachmentKey', $key);
		if (!$keyEnt)
		{
			throw $this->exception($this->error(\XF::phrase('api_error.attachment_key_unknown')));
		}

		if ($keyEnt->content_type !== $expectedContentType)
		{
			throw $this->exception($this->apiError(
				\XF::phrase('api_error.attachment_key_type_wrong'),
				'attachment_key_type_wrong',
				['expected' => $expectedContentType]
			));
		}

		if ($keyEnt->user_id !== \XF::visitor()->user_id)
		{
			throw $this->exception($this->apiError(
				\XF::phrase('api_error.attachment_key_user_wrong'),
				'attachment_key_user_wrong',
				['expected' => \XF::visitor()->user_id]
			));
		}

		if (!$keyEnt->hasExpectedContext($expectedContext, $allowExtraContext))
		{
			throw $this->exception($this->apiError(
				\XF::phrase('api_error.attachment_key_context_wrong'),
				'attachment_key_context_wrong',
				['expected' => http_build_query($expectedContext)]
			));
		}

		return $keyEnt->temp_hash;
	}

	public function reroute(RouteMatch $match)
	{
		if ($match instanceof \XF\Api\Mvc\RouteMatch)
		{
			$match->setVersion($this->requestedVersion);
		}

		return parent::reroute($match);
	}

	public function apiError($errorMessage, $errorCode, array $params = null, $httpCode = 400)
	{
		$error = new \XF\Api\ErrorMessage($errorMessage, $errorCode, $params);

		return new \XF\Mvc\Reply\Error($error, $httpCode);
	}

	public function apiResult($result)
	{
		if (is_array($result))
		{
			$result = new \XF\Api\Result\ArrayResult($result);
		}
		elseif ($result instanceof \XF\Mvc\Entity\Entity)
		{
			$result = $result->toApiResult();
		}
		else if (!($result instanceof \XF\Api\Result\ResultInterface))
		{
			throw new \LogicException(
				"Must pass \XF\Api\Result\ResultInterface or array to apiResult; received ". gettype($result)
			);
		}

		return new \XF\Api\Mvc\Reply\ApiResult($result);
	}

	public function apiSuccess(array $extra = [])
	{
		return $this->apiResult(['success' => true] + $extra);
	}

	public function checkCsrfIfNeeded($action, ParameterBag $params)
	{
		// never needed in the API as a custom header is required
	}

	public function allowUnauthenticatedRequest($action)
	{
		return false;
	}
}