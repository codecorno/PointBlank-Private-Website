<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class UserTitleLadder extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('user');
	}

	public function actionIndex()
	{
		$options = $this->em()->findByIds('XF:Option', ['enableTrophies', 'userTitleLadderField']);

		$viewParams = [
			'ladder' => $this->getLadderRepo()->findLadder()->fetch(),
			'options' => $options
		];

		return $this->view('XF:UserTitleLadder\Listing', 'user_title_ladder_list', $viewParams);
	}

	public function actionUpdate()
	{
		$this->assertPostOnly();

		$update = $this->filter('update', 'array');

		foreach ($this->filter('delete', 'array-uint') AS $delete)
		{
			unset($update[$delete]);
		}

		$new = $this->filter([
			'minimum_level' => 'uint',
			'title' => 'str'
		]);
		if ($new['title'] !== '')
		{
			$update[] = $new;
		}

		$this->getLadderRepo()->recreateLadder($update);

		return $this->redirect($this->buildLink('user-title-ladder'));
	}

	/**
	 * @return \XF\Repository\UserTitleLadder
	 */
	protected function getLadderRepo()
	{
		return $this->repository('XF:UserTitleLadder');
	}
}