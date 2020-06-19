<?php

namespace XF\Admin\Controller;

use XF\Entity\AdvertisingPosition;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Advertising extends AbstractController
{
	/**
	 * @param $action
	 * @param ParameterBag $params
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function preDispatchController($action, ParameterBag $params)
	{
		if (preg_match('/^(position)/i', $action))
		{
			$this->assertDevelopmentMode();
		}
		else
		{
			$this->assertAdminPermission('advertising');
		}
	}

	public function actionIndex()
	{
		$advertisingRepo = $this->getAdvertisingRepo();

		$options = $this->em()->find('XF:Option', 'adsDisallowedTemplates');

		$adsFinder = $advertisingRepo->findAdsForList();
		$ads = $adsFinder->fetch()->groupBy('position_id');

		$positionsFinder = $advertisingRepo->findAdvertisingPositionsForList();
		$positions = $positionsFinder->fetch();

		$viewParams = [
			'ads' => $ads,
			'options' => [$options],
			'positions' => $positions,
			'totalAds' => $advertisingRepo->getTotalGroupedAds($ads)
		];
		return $this->view('XF:Advertising\Listing', 'advertising_list', $viewParams);
	}

	protected function advertisingAddEdit(\XF\Entity\Advertising $ad)
	{
		$advertisingRepo = $this->getAdvertisingRepo();
		$advertisingPositions = $advertisingRepo
			->findAdvertisingPositionsForList(true)
			->fetch()
			->pluckNamed('title', 'position_id');

		/** @var \XF\Repository\UserGroup $userGroupRepo */
		$userGroupRepo = $this->app->repository('XF:UserGroup');
		$userGroups = $userGroupRepo->getUserGroupTitlePairs();

		$viewParams = [
			'ad' => $ad,
			'advertisingPositions' => $advertisingPositions,
			'userGroups' => $userGroups
		];
		return $this->view('XF:Advertising\Edit', 'advertising_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$ad = $this->assertAdExists($params->ad_id);
		return $this->advertisingAddEdit($ad);
	}

	public function actionAdd()
	{
		$ad = $this->em()->create('XF:Advertising');
		return $this->advertisingAddEdit($ad);
	}

	protected function adSaveProcess(\XF\Entity\Advertising $ad)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'title' => 'str',
			'position_id' => 'str',
			'ad_html' => 'str',
			'display_criteria' => 'array',
			'display_order' => 'uint',
			'active' => 'bool'
		]);

		$form->basicEntitySave($ad, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		if ($params->ad_id)
		{
			$ad = $this->assertAdExists($params->ad_id);
		}
		else
		{
			$ad = $this->em()->create('XF:Advertising');
		}

		$this->adSaveProcess($ad)->run();

		return $this->redirect($this->buildLink('advertising') . $this->buildLinkHash($ad->ad_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$ad = $this->assertAdExists($params->ad_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$ad,
			$this->buildLink('advertising/delete', $ad),
			$this->buildLink('advertising/edit', $ad),
			$this->buildLink('advertising'),
			$ad->title
		);
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:Advertising');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Advertising
	 */
	protected function assertAdExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Advertising', $id, $with, $phraseKey);
	}

	public function actionPosition()
	{
		$advertisingRepo = $this->getAdvertisingRepo();
		$advertisingPositionsFinder = $advertisingRepo->findAdvertisingPositionsForList();

		$viewParams = [
			'advertisingPositions' => $advertisingPositionsFinder->fetch()
		];
		return $this->view('XF:Advertising\Position\Listing', 'advertising_position_list', $viewParams);
	}

	protected function positionAddEdit(AdvertisingPosition $advertisingPosition)
	{
		$viewParams = [
			'advertisingPosition' => $advertisingPosition,
			'nextCounter' => count($advertisingPosition->arguments)
		];
		return $this->view('XF:Advertising\Position\Edit', 'advertising_position_edit', $viewParams);
	}

	public function actionPositionEdit(ParameterBag $params)
	{
		$advertisingPosition = $this->assertAdvertisingPositionExists($params->position_id);
		return $this->positionAddEdit($advertisingPosition);
	}

	public function actionPositionAdd()
	{
		$advertisingPosition = $this->em()->create('XF:AdvertisingPosition');
		return $this->positionAddEdit($advertisingPosition);
	}

	protected function advertisingPositionSaveProcess(AdvertisingPosition $advertisingPosition)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'position_id' => 'str',
			'active' => 'bool',
			'addon_id' => 'str'
		]);

		$input['arguments'] = [];
		$args = $this->filter('arguments', 'array');
		foreach ($args AS $arg)
		{
			if (!$arg['argument'])
			{
				continue;
			}
			$input['arguments'][] = $this->filterArray($arg, [
				'argument' => 'str',
				'required' => 'bool'
			]);
		}

		$form->basicEntitySave($advertisingPosition, $input);

		$extraInput = $this->filter([
			'title' => 'str',
			'description' => 'str'
		]);
		$form->validate(function(FormAction $form) use ($extraInput)
		{
			if ($extraInput['title'] === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($extraInput, $advertisingPosition)
		{
			$title = $advertisingPosition->getMasterTitlePhrase();
			$title->phrase_text = $extraInput['title'];
			$title->save();

			$description = $advertisingPosition->getMasterDescriptionPhrase();
			$description->phrase_text = $extraInput['description'];
			$description->save();
		});

		return $form;
	}

	public function actionPositionSave(ParameterBag $params)
	{
		if ($params->position_id)
		{
			$advertisingPosition = $this->assertAdvertisingPositionExists($params->position_id);
		}
		else
		{
			$advertisingPosition = $this->em()->create('XF:AdvertisingPosition');
		}

		$this->advertisingPositionSaveProcess($advertisingPosition)->run();

		return $this->redirect($this->buildLink('advertising/positions') . $this->buildLinkHash($advertisingPosition->position_id));
	}

	public function actionPositionDelete(ParameterBag $params)
	{
		$advertisingPosition = $this->assertAdvertisingPositionExists($params->position_id);
		
		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$advertisingPosition,
			$this->buildLink('advertising/positions/delete', $advertisingPosition),
			$this->buildLink('advertising/positions/edit', $advertisingPosition),
			$this->buildLink('advertising/positions'),
			$advertisingPosition->title
		);
	}

	public function actionPositionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:AdvertisingPosition');
	}

	public function actionGetPositionDescription()
	{
		/** @var \XF\ControllerPlugin\DescLoader $plugin */
		$plugin = $this->plugin('XF:DescLoader');
		return $plugin->actionLoadDescription('XF:AdvertisingPosition');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return AdvertisingPosition
	 */
	protected function assertAdvertisingPositionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:AdvertisingPosition', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Advertising
	 */
	protected function getAdvertisingRepo()
	{
		return $this->repository('XF:Advertising');
	}
}