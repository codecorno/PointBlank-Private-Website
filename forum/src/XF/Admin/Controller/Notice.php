<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class Notice extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('notice');
	}

	public function actionIndex(ParameterBag $params)
	{
		if ($params['notice_id'])
		{
			$notice = $this->assertNoticeExists($params['notice_id']);
			return $this->redirect($this->buildLink('notices/edit', $notice));
		}

		$options = $this->em()->find('XF:Option', 'enableNotices');

		$noticeRepo = $this->getNoticeRepo();
		$noticeList = $noticeRepo->findNoticesForList()->fetch();
		$notices = $noticeList->groupBy('notice_type');

		$invalidNotices = $noticeRepo->getInvalidNotices($noticeList);

		$viewParams = [
			'notices' => $notices,
			'invalidNotices' => $invalidNotices,
			'noticeTypes' => $noticeRepo->getNoticeTypes(),
			'options' => [$options],
			'totalNotices' => $noticeRepo->getTotalGroupedNotices($notices)
		];
		return $this->view('XF:Notice\Listing', 'notice_list', $viewParams);
	}

	protected function noticeAddEdit(\XF\Entity\Notice $notice)
	{
		$userCriteria = $this->app->criteria('XF:User', $notice->user_criteria);
		$pageCriteria = $this->app->criteria('XF:Page', $notice->page_criteria);

		$viewParams = [
			'notice' => $notice,
			'noticeTypes' => $this->getNoticeRepo()->getNoticeTypes(),
			'userCriteria' => $userCriteria,
			'pageCriteria' => $pageCriteria
		];
		return $this->view('XF:Notice\Edit', 'notice_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$notice = $this->assertNoticeExists($params['notice_id']);
		return $this->noticeAddEdit($notice);
	}

	public function actionAdd()
	{
		$notice = $this->em()->create('XF:Notice');
		return $this->noticeAddEdit($notice);
	}

	protected function noticeSaveProcess(\XF\Entity\Notice $notice)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'title' => 'str',
			'message' => 'str',
			'dismissible' => 'bool',
			'active' => 'bool',
			'display_order' => 'uint',
			'display_image' => 'str',
			'image_url' => 'str',
			'visibility' => 'str',
			'notice_type' => 'str',
			'display_style' => 'str',
			'css_class' => 'str',
			'display_duration' => 'uint',
			'delay_duration' => 'uint',
			'auto_dismiss' => 'bool',
			'user_criteria' => 'array',
			'page_criteria' => 'array'
		]);

		$form->basicEntitySave($notice, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->notice_id)
		{
			$notice = $this->assertNoticeExists($params->notice_id);
		}
		else
		{
			$notice = $this->em()->create('XF:Notice');
		}

		$this->noticeSaveProcess($notice)->run();

		return $this->redirect($this->buildLink('notices') . $this->buildLinkHash($notice->notice_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$notice = $this->assertNoticeExists($params->notice_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$notice,
			$this->buildLink('notices/delete', $notice),
			$this->buildLink('notices/edit', $notice),
			$this->buildLink('notices'),
			$notice->title
		);
	}

	public function actionReset(ParameterBag $params)
	{
		$notice = $this->assertNoticeExists($params['notice_id']);

		if ($this->isPost())
		{
			$this->getNoticeRepo()->resetNoticeDismissal($notice);
			return $this->redirect($this->buildLink('notices'));
		}
		else
		{
			$viewParams = [
				'notice' => $notice
			];
			return $this->view('XF:Notice\Reset', 'notice_reset', $viewParams);
		}
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:Notice');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Notice
	 */
	protected function assertNoticeExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Notice', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Notice
	 */
	protected function getNoticeRepo()
	{
		return $this->repository('XF:Notice');
	}
}