<?php

namespace XF\Admin\Controller;

use XF\Entity\OptionGroup;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;
use XF\Util\Arr;

class Option extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('option');
	}

	public function actionIndex()
	{
		$optionRepo = $this->getOptionRepo();

		$viewParams = [
			'groups' => $optionRepo->findOptionGroupList()->fetch(),
			'canAdd' => $optionRepo->canAddOption()
		];
		return $this->view('XF:Option\GroupList', 'option_group_list', $viewParams);
	}

	public function actionMenu()
	{
		$optionRepo = $this->getOptionRepo();

		$viewParams = [
			'groups' => $optionRepo->findOptionGroupList()->fetch()
		];
		return $this->view('XF:Option\GroupMenu', 'option_group_menu', $viewParams);
	}

	public function actionGroup(ParameterBag $params)
	{
		$group = $this->assertGroupExists($params['group_id']);

		if ($group->AddOn && !$group->AddOn->active)
		{
			return $this->error(\XF::phrase('option_group_belongs_to_disabled_addon', [
				'addon' => $group->AddOn->title,
				'link' => $this->buildLink('add-ons')
			]));
		}

		$optionRepo = $this->getOptionRepo();

		$viewParams = [
			'group' => $group,
			'groups' => $optionRepo->findOptionGroupList()->fetch(),
			'canAdd' => $optionRepo->canAddOption()
		];
		return $this->view('XF:Option\Listing', 'option_list', $viewParams);
	}

	public function actionUpdate()
	{
		$this->assertPostOnly();

		$input = $this->filter([
			'options_listed' => 'array-str',
			'options' => 'array'
		]);

		$options = [];
		foreach ($input['options_listed'] AS $optionId)
		{
			if (!isset($input['options'][$optionId]))
			{
				$options[$optionId] = false;
			}
			else
			{
				$options[$optionId] = $input['options'][$optionId];
			}
		}

		$this->getOptionRepo()->updateOptions($options);

		return $this->redirect($this->getDynamicRedirect());
	}

	protected function groupAddEdit(OptionGroup $group)
	{
		$viewParams = [
			'group' => $group
		];

		return $this->view('XF:Option\GroupEdit', 'option_group_edit', $viewParams);
	}

	public function actionGroupAdd()
	{
		if (!$this->getOptionRepo()->canAddOption())
		{
			return $this->noPermission();
		}

		$group = $this->em()->create('XF:OptionGroup');
		return $this->groupAddEdit($group);
	}

	public function actionGroupEdit(ParameterBag $params)
	{
		$group = $this->assertGroupExists($params['group_id'], ['MasterTitle', 'MasterDescription']);
		if (!$group->canEdit())
		{
			return $this->noPermission();
		}

		return $this->groupAddEdit($group);
	}

	protected function groupSaveProcess(OptionGroup $group)
	{
		$entityInput = $this->filter([
			'group_id' => 'str',
			'icon' => 'str',
			'display_order' => 'uint',
			'debug_only' => 'bool',
			'addon_id' => 'str'
		]);

		$form = $this->formAction();
		$form->basicEntitySave($group, $entityInput);

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
		$form->apply(function() use ($phraseInput, $group)
		{
			$title = $group->getMasterPhrase(true);
			$title->phrase_text = $phraseInput['title'];
			$title->save();

			$description = $group->getMasterPhrase(false);
			$description->phrase_text = $phraseInput['description'];
			$description->save();
		});

		return $form;
	}

	public function actionGroupSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['group_id'])
		{
			$group = $this->assertGroupExists($params['group_id']);
			if (!$group->canEdit())
			{
				return $this->noPermission();
			}
		}
		else
		{
			if (!$this->getOptionRepo()->canAddOption())
			{
				return $this->noPermission();
			}

			$group = $this->em()->create('XF:OptionGroup');
		}

		$form = $this->groupSaveProcess($group);
		$form->run();

		return $this->redirect($this->buildLink('options/groups', $group) . $this->buildLinkHash($group->group_id));
	}

	public function actionGroupDelete(ParameterBag $params)
	{
		$group = $this->assertGroupExists($params['group_id']);
		if (!$group->canEdit())
		{
			return $this->noPermission();
		}

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$group,
			$this->buildLink('options/groups/delete', $group),
			$this->buildLink('options/groups/edit', $group),
			$this->buildLink('options/groups'),
			$group->title
		);
	}

	protected function optionAddEdit(\XF\Entity\Option $option, $baseRelations = [])
	{
		$relations = $baseRelations;
		$group = null;
		if ($option->exists() && $option->Relations)
		{
			foreach ($option->Relations AS $relation)
			{
				$relations[$relation->group_id] = $relation->display_order;
			}
			$groupId = $this->filter('group_id', 'str');
			if (isset($option->Relations[$groupId]))
			{
				$group = $option->Relations[$groupId]->OptionGroup;
			}
			else
			{
				$group = $option->Relations->first()->OptionGroup;
			}
		}

		$optionRepo = $this->getOptionRepo();

		$viewParams = [
			'option' => $option,
			'group' => $group,
			'groups' => $optionRepo->findAllGroups()->fetch(),
			'relations' => $relations,
			'redirect' => $this->app->getDynamicRedirect()
		];

		return $this->view('XF:Option\Edit', 'option_edit', $viewParams);
	}

	public function actionAdd()
	{
		if (!$this->getOptionRepo()->canAddOption())
		{
			return $this->noPermission();
		}

		$option = $this->em()->create('XF:Option');

		$baseRelations = [];
		$groupId = $this->filter('group_id', 'str');
		if ($groupId)
		{
			$baseRelations[$groupId] = 1;
		}

		return $this->optionAddEdit($option, $baseRelations);
	}

	public function actionEdit(ParameterBag $params)
	{
		$option = $this->assertOptionExists($params['option_id']);
		if (!$option->canEdit())
		{
			return $this->noPermission();
		}

		return $this->optionAddEdit($option);
	}

	protected function optionSaveProcess(\XF\Entity\Option $option)
	{
		$entityInput = $this->filter([
			'option_id' => 'str',
			'default_value' => 'str',
			'edit_format' => 'str',
			'edit_format_params' => 'str',
			'data_type' => 'str',
			'validation_class' => 'str',
			'validation_method' => 'str',
			'addon_id' => 'str'
		]);
		$subOptions = Arr::stringToArray($this->filter('sub_options', 'str'),'/\r?\n/');

		$form = $this->formAction();

		$form->basicEntitySave($option, $entityInput)
			->setup(function() use ($option, $subOptions)
			{
				$option->sub_options = $subOptions;
			});

		$phraseInput = $this->filter([
			'title' => 'str',
			'explain' => 'str'
		]);
		$form->validate(function(FormAction $form) use ($phraseInput)
		{
			if ($phraseInput['title'] === '')
			{
				$form->logError(\XF::phrase('please_enter_valid_title'), 'title');
			}
		});
		$form->apply(function() use ($phraseInput, $option)
		{
			$title = $option->getMasterPhrase(true);
			$title->phrase_text = $phraseInput['title'];
			$title->save();

			$explain = $option->getMasterPhrase(false);
			$explain->phrase_text = $phraseInput['explain'];
			$explain->save();
		});

		$groups = $this->getOptionRepo()->findAllGroups()->fetch();
		$relationMap = [];

		foreach ($this->filter('relations', 'array') AS $groupId => $relation)
		{
			if (is_array($relation)
				&& !empty($relation['selected'])
				&& isset($relation['display_order'])
				&& isset($groups[$groupId])
			)
			{
				$relationMap[$groupId] = $this->app->inputFilterer()->filter($relation['display_order'], 'uint');
			}
		}

		$form->validate(function(FormAction $form) use ($option, $groups, $relationMap)
		{
			if (!count($relationMap))
			{
				$form->logError(\XF::phrase('this_option_must_belong_to_at_least_one_group'), 'relations');
			}
		});
		$form->apply(function() use ($option, $relationMap)
		{
			$option->updateRelations($relationMap);
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['option_id'])
		{
			$option = $this->assertOptionExists($params['option_id']);
			if (!$option->canEdit())
			{
				return $this->noPermission();
			}
		}
		else
		{
			if (!$this->getOptionRepo()->canAddOption())
			{
				return $this->noPermission();
			}

			$option = $this->em()->create('XF:Option');
		}

		$this->optionSaveProcess($option)->run();

		return $this->redirect(
			$this->getDynamicRedirect($this->buildLink('options'), false)
		);
	}

	public function actionDelete(ParameterBag $params)
	{
		$option = $this->assertOptionExists($params['option_id']);
		if (!$option->canEdit())
		{
			return $this->noPermission();
		}

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$option,
			$this->buildLink('options/delete', $option),
			$this->buildLink('options/edit', $option),
			$this->getDynamicRedirect(
				$this->buildLink('options'), false
			),
			$option->title
		);
	}

	public function actionView(ParameterBag $params)
	{
		$option = $this->assertOptionExists($params['option_id']);
		$relation = $option->Relations->first();
		$group = $relation ? $relation->OptionGroup : null;

		return $this->redirect(
			$group ? $this->buildLink('options/groups', $group) . '#' . $option->option_id : $this->buildLink('options')
		);
	}

	/**
	 * @param string $groupId
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return OptionGroup
	 */
	protected function assertGroupExists($groupId, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:OptionGroup', $groupId, $with, $phraseKey);
	}

	/**
	 * @param string $optionId
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Option
	 */
	protected function assertOptionExists($optionId, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Option', $optionId, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Option
	 */
	protected function getOptionRepo()
	{
		return $this->repository('XF:Option');
	}
}