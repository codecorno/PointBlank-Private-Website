<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;
use XF\Diff;

class TemplateModification extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('style');
	}

	public function actionIndex()
	{
		$templateModRepo = $this->getTemplateModificationRepo();

		$type = $this->filter('type', 'str');
		if (!$type)
		{
			$type = 'public';
		}
		$types = $templateModRepo->getModificationTypes();
		if (!isset($types[$type]))
		{
			return $this->error(\XF::phrase('cannot_view_template_modifications_of_this_type'));
		}

		$modifications = $templateModRepo->findTemplateModificationsForList($type)->fetch();
		$modifications = $templateModRepo->addLogsToModifications($modifications);

		$addOns = $modifications->pluckNamed('AddOn', 'addon_id');

		$viewParams = [
			'canCreateModification' => $templateModRepo->canCreateTemplateModification(),

			'groupedModifications' => $modifications->groupBy('addon_id'),
			'addOns' => $addOns,

			'modificationCount' => $modifications->count(),

			'type' => $type,
			'types' => $types
		];

		return $this->view('XF:TemplateModification\Listing', 'template_modification_list', $viewParams);
	}

	protected function templateModificationAddEdit(\XF\Entity\TemplateModification $modification)
	{
		$types = $this->getTemplateModificationRepo()->getModificationTypes();
		if (!isset($types[$modification->type]))
		{
			return $this->error(\XF::phrase('cannot_edit_template_modifications_of_this_type'));
		}

		$viewParams = [
			'modification' => $modification,
			'types' => $types
		];
		return $this->view('XF:TemplateModification\Edit', 'template_modification_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$modification = $this->assertTemplateModificationExists($params['modification_id']);
		return $this->templateModificationAddEdit($modification);
	}

	public function actionAdd()
	{
		$modification = $this->em()->create('XF:TemplateModification');
		$modification->type = $this->filter('type', 'str', 'public');
		return $this->templateModificationAddEdit($modification);
	}

	protected function modificationSaveProcess(\XF\Entity\TemplateModification $modification)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'type' => 'str',
			'template' => 'str',
			'modification_key' => 'str',
			'description' => 'str',
			'action' => 'str',
			'find' => 'str,no-trim',
			'replace' => 'str,no-trim',
			'execution_order' => 'uint',
			'enabled' => 'bool',
			'addon_id' => 'str'
		]);

		$form->basicEntitySave($modification, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($this->filter('test', 'bool'))
		{
			return $this->rerouteController(__CLASS__, 'test', $params);
		}

		if ($params['modification_id'])
		{
			$modification = $this->assertTemplateModificationExists($params['modification_id']);
		}
		else
		{
			$modification = $this->em()->create('XF:TemplateModification');
		}

		$this->modificationSaveProcess($modification)->run();

		return $this->redirect($this->buildLink('template-modifications', '', ['type' => $modification->type]));
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:TemplateModification', 'enabled');
	}

	public function actionDelete(ParameterBag $params)
	{
		$modification = $this->assertTemplateModificationExists($params['modification_id']);
		if (!$modification->canEdit())
		{
			return $this->error(\XF::phrase('item_cannot_be_deleted_associated_with_addon_explain'));
		}

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$modification,
			$this->buildLink('template-modifications/delete', $modification),
			$this->buildLink('template-modifications/edit', $modification),
			$this->buildLink('template-modifications'),
			$modification->template
		);
	}

	public function actionTest(ParameterBag $params)
	{
		if ($params['modification_id'])
		{
			$modification = $this->assertTemplateModificationExists($params['modification_id']);
		}
		else
		{
			$modification = $this->em()->create('XF:TemplateModification');
		}

		$input = $this->filter([
			'type' => 'str',
			'template' => 'str',
			'modification_key' => 'str',
			'description' => 'str',
			'action' => 'str',
			'find' => 'str,no-trim',
			'replace' => 'str,no-trim',
			'execution_order' => 'uint',
			'enabled' => 'bool',
			'addon_id' => 'str'
		]);

		$modification->bulkSet($input);
		$modification->preSave();

		$errors = $modification->getErrors();
		if (isset($errors['template']))
		{
			return $this->error($errors['template']);
		}
		if (isset($errors['find']))
		{
			return $this->error($errors['find']);
		}

		$template = $this->finder('XF:Template')
			->where([
				'style_id' => 0,
				'title' => $input['template'],
				'type' => $input['type']
			])
			->fetchOne();
		if (!$template)
		{
			return $this->error(\XF::phrase('requested_template_not_found'));
		}

		$content = $template->template;
		$contentModified = $this->getTemplateModificationRepo()->applyTemplateModifications($content, [$modification]);

		$diff = new Diff();
		$diffs = $diff->findDifferences($content, $contentModified);

		$viewParams = [
			'modification' => $modification,
			'content' => $content,
			'contentModified' => $contentModified,
			'diffs' => $diffs
		];

		return $this->view('XF:TemplateModification\Test', 'template_modification_test', $viewParams);
	}

	public function actionLog(ParameterBag $params)
	{
		$modification = $this->assertTemplateModificationExists($params['modification_id']);

		$viewParams = [
			'modification' => $modification
		];

		return $this->view('XF:TemplateModification\Log', 'template_modification_log', $viewParams);
	}

	public function actionAutoComplete()
	{
		$type = $this->filter('type', 'str');

		$types = $this->getTemplateModificationRepo()->getModificationTypes();
		if (empty($types[$type]))
		{
			$type = 'public';
		}

		$q = $this->filter('q', 'str');

		$finder = $this->finder('XF:Template');
		$finder->where('type', $type)
			->where('style_id', 0)
			->where(
				$finder->columnUtf8('title'),
				'LIKE', $finder->escapeLike($q, '?%'))
			->limit(10);

		$results = [];
		foreach ($finder->fetch() AS $templateMap)
		{
			$results[] = [
				'id' => $templateMap->title,
				'text' => $templateMap->title
			];
		}

		$view = $this->view();
		$view->setJsonParam('results', $results);

		return $view;
	}

	public function actionContents()
	{
		$type = $this->filter('type', 'str');

		$types = $this->getTemplateModificationRepo()->getModificationTypes();
		if (empty($types[$type]))
		{
			$type = 'public';
		}

		$templateTitle = $this->filter('template', 'str');

		$template = $this->finder('XF:Template')
			->where([
				'style_id' => 0,
				'title' => $templateTitle,
				'type' => $type
			])
			->fetchOne();

		$view = $this->view('XF:TemplateModification\Contents', '');
		$view->setJsonParam('template', $template ? $template->template : false);
		return $view;
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\TemplateModification
	 */
	protected function assertTemplateModificationExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:TemplateModification', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\TemplateModification
	 */
	protected function getTemplateModificationRepo()
	{
		return $this->repository('XF:TemplateModification');
	}
}