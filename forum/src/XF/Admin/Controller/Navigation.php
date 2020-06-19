<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Navigation extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('navigation');
	}

	public function actionIndex()
	{
		$viewParams = [
			'tree' => $this->getNavigationRepo()->createNavigationTree(),
			'defaultNavigationId' => $this->app()->get('defaultNavigationId')
		];
		return $this->view('XF:Navigation\Listing', 'navigation_list', $viewParams);
	}

	protected function navigationAddEdit(\XF\Entity\Navigation $navigation)
	{
		$navRepo = $this->getNavigationRepo();

		$navigation->setOption('user_edit', true);

		$typeHandlers = $navRepo->getTypeHandlers();
		$selectedType = $navigation->navigation_type_id;
		if (!$selectedType || !isset($typeHandlers[$selectedType]))
		{
			reset($typeHandlers);
			$selectedType = key($typeHandlers);
		}

		$viewParams = [
			'navigation' => $navigation,
			'navigationTree' => $navRepo->createNavigationTree(),
			'selectedType' => $selectedType,
			'typeHandlers' => $typeHandlers
		];
		return $this->view('XF:Navigation\Edit', 'navigation_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$navigation = $this->assertNavigationExists($params['navigation_id']);
		return $this->navigationAddEdit($navigation);
	}

	public function actionAdd()
	{
		$navigation = $this->em()->create('XF:Navigation');
		return $this->navigationAddEdit($navigation);
	}

	protected function navigationSaveProcess(\XF\Entity\Navigation $navigation)
	{
		// since this is where we expose editing, this may be considered a user edit.
		// note that there's additional logic in the entity which is used
		$navigation->setOption('user_edit', true);

		$form = $this->formAction();

		$input = $this->filter([
			'navigation_id' => 'str',
			'parent_navigation_id' => 'str',
			'display_order' => 'uint',
			'enabled' => 'bool',
			'addon_id' => 'str'
		]);

		$typeId = $this->filter('navigation_type_id', 'str');

		$config = $this->filter('config', 'array');
		if (isset($config[$typeId]) && is_array($config[$typeId]))
		{
			$typeConfig = $config[$typeId];
		}
		else
		{
			$typeConfig = [];
		}

		$form->basicEntitySave($navigation, $input);

		$form->setup(function(FormAction $form) use ($navigation, $typeId, $typeConfig)
		{
			$navigation->setTypeFromInput($typeId, $typeConfig);
		});

		$phraseInput = $this->filter([
			'title' => 'str'
		]);
		$form->validate(function(FormAction $form) use ($phraseInput)
		{
			if ($phraseInput['title'] === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($phraseInput, $navigation)
		{
			$title = $navigation->getMasterPhrase();
			$title->phrase_text = $phraseInput['title'];
			$title->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['navigation_id'])
		{
			$navigation = $this->assertNavigationExists($params['navigation_id']);
		}
		else
		{
			$navigation = $this->em()->create('XF:Navigation');
		}

		$this->navigationSaveProcess($navigation)->run();

		return $this->redirect($this->buildLink('navigation') . $this->buildLinkHash($navigation->navigation_id));
	}

	public function actionRevert(ParameterBag $params)
	{
		$navigation = $this->assertNavigationExists($params['navigation_id']);
		$returnTarget = $this->buildLink('navigation') . $this->buildLinkHash($navigation->navigation_id);

		if (!$navigation->is_customized)
		{
			return $this->redirect($returnTarget);
		}

		if ($this->isPost())
		{
			$navigation->revertToDefault();
			$navigation->save();

			return $this->redirect($returnTarget);
		}
		else
		{
			$viewParams = [
				'navigation' => $navigation
			];
			return $this->view('XF:Navigation\Revert', 'navigation_revert', $viewParams);
		}
	}

	public function actionDelete(ParameterBag $params)
	{
		$navigation = $this->assertNavigationExists($params['navigation_id']);

		if (!$navigation->canDelete())
		{
			return $this->error(\XF::phrase('item_cannot_be_deleted_associated_with_addon_explain'));
		}

		if (!$navigation->preDelete())
		{
			return $this->error($navigation->getErrors());
		}

		if ($this->isPost())
		{
			$navigation->delete();

			return $this->redirect($this->buildLink('navigation'));
		}
		else
		{
			$navRepo = $this->getNavigationRepo();

			$navTree = $navRepo->createNavigationTree();
			$children = $navTree->children($navigation->navigation_id);

			$viewParams = [
				'navigation' => $navigation,
				'hasChildren' => count($children) > 0
			];
			return $this->view('XF:Navigation\Delete', 'navigation_delete', $viewParams);
		}
	}

	public function actionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:Navigation', 'enabled', [
			'preSaveCallback' => function(\XF\Entity\Navigation $nav)
			{
				$nav->setOption('user_edit', true);
			}
		]);
	}

	public function actionSort()
	{
		$navTree = $this->getNavigationRepo()->createNavigationTree();

		if ($this->isPost())
		{
			/** @var \XF\ControllerPlugin\Sort $sorter */
			$sorter = $this->plugin('XF:Sort');
			$sortTree = $sorter->buildSortTree($this->filter('navigation', 'json-array'), '');
			$sorter->sortTree($sortTree, $navTree->getAllData(), 'parent_navigation_id', [
				'preSaveCallback' => function(\XF\Entity\Navigation $nav)
				{
					$nav->setOption('user_edit', true);
				}
			]);

			return $this->redirect($this->buildLink('navigation'));
		}
		else
		{
			$viewParams = [
				'navTree' => $navTree
			];
			return $this->view('XF:Navigation\Sort', 'navigation_sort', $viewParams);
		}
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Navigation
	 */
	protected function assertNavigationExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Navigation', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Navigation
	 */
	protected function getNavigationRepo()
	{
		return $this->repository('XF:Navigation');
	}
}