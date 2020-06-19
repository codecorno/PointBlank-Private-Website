<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Trophy extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('trophy');
	}

	public function actionIndex()
	{
		$options = $this->em()->findByIds('XF:Option', ['enableTrophies', 'userTitleLadderField']);

		$viewParams = [
			'trophies' => $this->getTrophyRepo()->findTrophiesForList()->fetch(),
			'options' => $options
		];
		return $this->view('XF:Trophy\Listing', 'trophy_list', $viewParams);
	}

	protected function trophyAddEdit(\XF\Entity\Trophy $trophy)
	{
		$userCriteria = $this->app->criteria('XF:User', $trophy->user_criteria);

		$viewParams = [
			'trophy' => $trophy,
			'userCriteria' => $userCriteria
		];
		return $this->view('XF:Trophy\Edit', 'trophy_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$trophy = $this->assertTrophyExists($params->trophy_id);
		return $this->trophyAddEdit($trophy);
	}

	public function actionAdd()
	{
		$trophy = $this->em()->create('XF:Trophy');
		return $this->trophyAddEdit($trophy);
	}

	protected function trophySaveProcess(\XF\Entity\Trophy $trophy)
	{
		$form = $this->formAction();

		$trophyInput = $this->filter([
			'trophy_points' => 'uint',
			'user_criteria' => 'array'
		]);
		$form->basicEntitySave($trophy, $trophyInput);

		$phraseInput = $this->filter([
			'title' => 'str',
			'description' => 'str'
		]);
		$form->validate(function(FormAction $form) use ($phraseInput)
		{
			if ($phraseInput['title'] === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($phraseInput, $trophy)
		{
			$masterTitle = $trophy->getMasterPhrase(true);
			$masterTitle->phrase_text = $phraseInput['title'];
			$masterTitle->save();

			$masterDescription = $trophy->getMasterPhrase(false);
			$masterDescription->phrase_text = $phraseInput['description'];
			$masterDescription->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->trophy_id)
		{
			$trophy = $this->assertTrophyExists($params->trophy_id);
		}
		else
		{
			$trophy = $this->em()->create('XF:Trophy');
		}

		$this->trophySaveProcess($trophy)->run();

		return $this->redirect($this->buildLink('trophies') . $this->buildLinkHash($trophy->trophy_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$trophy = $this->assertTrophyExists($params->trophy_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$trophy,
			$this->buildLink('trophies/delete', $trophy),
			$this->buildLink('trophies/edit', $trophy),
			$this->buildLink('trophies'),
			$trophy->title
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Trophy
	 */
	protected function assertTrophyExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Trophy', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Trophy
	 */
	protected function getTrophyRepo()
	{
		return $this->repository('XF:Trophy');
	}
}