<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class SmilieCategory extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('bbCodeSmilie');
	}

	public function actionIndex()
	{
		return $this->redirectPermanently($this->buildLink('smilies'));
	}

	public function smilieCategoryAddEdit(\XF\Entity\SmilieCategory $smilieCategory)
	{
		$viewParams = [
			'smilieCategory' => $smilieCategory
		];
		return $this->view('XF:SmilieCategory\Edit', 'smilie_category_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$smilieCategory = $this->assertSmilieCategoryExists($params['smilie_category_id']);
		return $this->smilieCategoryAddEdit($smilieCategory);
	}

	public function actionAdd()
	{
		$smilieCategory = $this->em()->create('XF:SmilieCategory');
		return $this->smilieCategoryAddEdit($smilieCategory);
	}

	protected function smilieSaveProcess(\XF\Entity\SmilieCategory $smilieCategory)
	{
		$entityInput = $this->filter([
			'display_order' => 'uint'
		]);

		$form = $this->formAction();
		$form->basicEntitySave($smilieCategory, $entityInput);

		$titlePhrase = $this->filter('title', 'str');

		$form->validate(function(FormAction $form) use ($titlePhrase)
		{
			if ($titlePhrase === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($titlePhrase, $smilieCategory)
		{
			$masterTitle = $smilieCategory->getMasterPhrase();
			$masterTitle->phrase_text = $titlePhrase;
			$masterTitle->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['smilie_category_id'])
		{
			$smilieCategory = $this->assertSmilieCategoryExists($params['smilie_category_id']);
		}
		else
		{
			$smilieCategory = $this->em()->create('XF:SmilieCategory');
		}

		$this->smilieSaveProcess($smilieCategory)->run();

		return $this->redirect($this->buildLink('smilies'));
	}

	public function actionDelete(ParameterBag $params)
	{
		$smilieCategory = $this->assertSmilieCategoryExists($params['smilie_category_id']);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$smilieCategory,
			$this->buildLink('smilie-categories/delete', $smilieCategory),
			$this->buildLink('smilie-categories/edit', $smilieCategory),
			$this->buildLink('smilies'),
			$smilieCategory->title,
			'smilie_category_delete'
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\SmilieCategory
	 */
	protected function assertSmilieCategoryExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:SmilieCategory', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\SmilieCategory
	 */
	protected function getSmilieCategoryRepo()
	{
		return $this->repository('XF:SmilieCategory');
	}
}