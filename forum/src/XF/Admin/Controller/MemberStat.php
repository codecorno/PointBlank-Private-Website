<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class MemberStat extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('user');
	}

	public function actionIndex(ParameterBag $params)
	{
		$memberStatRepo = $this->getMemberStatRepo();
		$memberStats = $memberStatRepo->findMemberStatsForList()->fetch();

		$viewParams = [
			'memberStats' => $memberStats
		];
		return $this->view('XF:MemberStat\Listing', 'member_stat_list', $viewParams);
	}

	protected function memberStatAddEdit(\XF\Entity\MemberStat $memberStat)
	{
		$searcher = $this->searcher('XF:User');
		$searcher->setCriteria($memberStat->criteria ?: []);

		$permissionRepo = $this->repository('XF:Permission');
		$permissionsData = $permissionRepo->getGlobalPermissionListData();

		$viewParams = [
			'memberStat' => $memberStat,
			'criteria' => $searcher->getFormCriteria(),
			'sortOrders' => $searcher->getOrderOptions(),
			'permissionsData' => $permissionsData
		];
		return $this->view('XF:MemberStat\Edit', 'member_stat_edit', $viewParams + $searcher->getFormData());
	}

	public function actionEdit(ParameterBag $params)
	{
		$notice = $this->assertMemberStatExists($params->member_stat_id);
		return $this->memberStatAddEdit($notice);
	}

	public function actionAdd()
	{
		/** @var \XF\Entity\MemberStat $memberStat */
		$memberStat = $this->em()->create('XF:MemberStat');
		return $this->memberStatAddEdit($memberStat);
	}

	protected function memberStatSaveProcess(\XF\Entity\MemberStat $memberStat)
	{
		$form = $this->formAction();

		$entityInput = $this->filter([
			'member_stat_key' => 'str',
			'criteria' => 'array',
			'sort_order' => 'str',
			'sort_direction' => 'str',
			'callback_class' => 'str',
			'callback_method' => 'str',
			'permission_limit' => 'str',
			'show_value' => 'bool',
			'overview_display' => 'bool',
			'active' => 'bool',
			'user_limit' => 'uint',
			'display_order' => 'uint',
			'addon_id' => 'str',
			'cache_lifetime' => 'uint'
		]);

		$form->basicEntitySave($memberStat, $entityInput);


		$extraInput = $this->filter([
			'title' => 'str'
		]);
		$form->validate(function(FormAction $form) use ($extraInput)
		{
			if ($extraInput['title'] === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($extraInput, $memberStat)
		{
			$title = $memberStat->getMasterPhrase();
			$title->phrase_text = $extraInput['title'];
			$title->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->member_stat_id)
		{
			$memberStat = $this->assertMemberStatExists($params->member_stat_id);
		}
		else
		{
			$memberStat = $this->em()->create('XF:MemberStat');
		}

		$this->memberStatSaveProcess($memberStat)->run();

		return $this->redirect($this->buildLink('member-stats') . $this->buildLinkHash($memberStat->member_stat_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$memberStat = $this->assertMemberStatExists($params->member_stat_id);
		if (!$memberStat->canEdit())
		{
			return $this->error(\XF::phrase('item_cannot_be_deleted_associated_with_addon_explain'));
		}

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$memberStat,
			$this->buildLink('member-stats/delete', $memberStat),
			$this->buildLink('member-stats/edit', $memberStat),
			$this->buildLink('member-stats'),
			$memberStat->title
		);
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:MemberStat');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\MemberStat
	 */
	protected function assertMemberStatExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:MemberStat', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\MemberStat
	 */
	protected function getMemberStatRepo()
	{
		return $this->repository('XF:MemberStat');
	}
}