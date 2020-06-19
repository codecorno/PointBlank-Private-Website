<?php

namespace XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Warning extends AbstractController
{
	public function actionIndex(ParameterBag $params)
	{
		$warning = $this->assertViewableWarning($params->warning_id, ['User']);

		if (!$warning->User)
		{
			return $this->error(\XF::phrase('user_who_received_this_warning_no_longer_exists'));
		}

		$handler = $warning->getHandler();
		$content = $warning->Content;
		if ($handler && $content)
		{
			$canViewContent = $handler->canViewContent($content);
			$contentUrl = $handler->getContentUrl($content);
		}
		else
		{
			$canViewContent = false;
			$contentUrl = '';
		}

		$viewParams = [
			'warning' => $warning,
			'user' => $warning->User,
			'canViewContent' => $canViewContent,
			'contentUrl' => $contentUrl
		];
		return $this->view('XF:Warning\Info', 'warning_info', $viewParams);
	}

	public function actionExpire(ParameterBag $params)
	{
		$this->assertPostOnly();

		$warning = $this->assertViewableWarning($params->warning_id);
		if (!$warning->canEditExpiry($error))
		{
			return $this->noPermission($error);
		}


		if ($this->filter('expire', 'str') == 'now')
		{
			$expiryDate = \XF::$time;
		}
		else
		{
			$expiryLength = $this->filter('expiry_value', 'uint');
			$expiryUnit = $this->filter('expiry_unit', 'str');

			$expiryDate = strtotime("+$expiryLength $expiryUnit");
			if ($expiryDate >= pow(2, 32) - 1)
			{
				$expiryDate = 0;
			}
		}

		$warning->expiry_date = $expiryDate;
		$warning->save();

		return $this->redirect($this->getDynamicRedirect());
	}

	public function actionDelete(ParameterBag $params)
	{
		$this->assertPostOnly();

		$warning = $this->assertViewableWarning($params->warning_id);
		if (!$warning->canDelete($error))
		{
			return $this->noPermission($error);
		}

		if ($this->filter('confirm', 'bool'))
		{
			$warning->delete();

			return $this->redirect($this->getDynamicRedirect());
		}
		else
		{
			return $this->rerouteController('XF:Warning', 'index', $params->params());
		}
	}

	/**
	 * @param $id
	 * @param array $extraWith
	 *
	 * @return \XF\Entity\Warning
	 *
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function assertViewableWarning($id, array $extraWith = [])
	{
		/** @var \XF\Entity\Warning $warning */
		$warning = $this->em()->find('XF:Warning', $id, $extraWith);
		if (!$warning)
		{
			throw $this->exception($this->notFound(\XF::phrase('requested_warning_not_found')));
		}

		if (!$warning->canView($error))
		{
			throw $this->exception($this->noPermission($error));
		}

		return $warning;
	}

	public static function getActivityDetails(array $activities)
	{
		return \XF::phrase('performing_moderation_duties');
	}

}