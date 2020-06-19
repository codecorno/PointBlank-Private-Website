<?php

namespace XF\Admin\Controller;

use XF\Entity\WidgetDefinition;
use XF\Entity\WidgetPosition;
use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

class Widget extends AbstractController
{
	/**
	 * @param $action
	 * @param ParameterBag $params
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function preDispatchController($action, ParameterBag $params)
	{
		if (preg_match('/^(definition|position)/i', $action))
		{
			$this->assertDevelopmentMode();
		}
		else
		{
			$this->assertAdminPermission('widget');
		}
	}

	public function actionIndex()
	{
		$widgetRepo = $this->getWidgetRepo();

		$widgetsFinder = $widgetRepo->findWidgetsForList();
		$widgets = $widgetsFinder->fetch();

		$groupedWidgets = $widgetRepo->groupWidgetsByPositions($widgets, $totalWidgets);

		$positionsFinder = $widgetRepo->findWidgetPositionsForList();
		$positions = $positionsFinder->fetch();
		$positions[''] = null;

		$viewParams = [
			'groupedWidgets' => $groupedWidgets,
			'positions' => $positions,
			'totalWidgets' => $totalWidgets
		];
		return $this->view('XF:Widget\Listing', 'widget_list', $viewParams);
	}

	protected function widgetAddEdit(\XF\Entity\Widget $widget)
	{
		$widgetRepo = $this->getWidgetRepo();
		$widgetPositions = $widgetRepo
			->findWidgetPositionsForList(true)
			->fetch();

		$viewParams = [
			'widget' => $widget,
			'handler' => $widget->handler,
			'widgetDefinition' => $widget->WidgetDefinition,
			'widgetPositions' => $widgetPositions
		];
		return $this->view('XF:Widget\Edit', 'widget_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$widget = $this->assertWidgetExists($params->widget_id);
		return $this->widgetAddEdit($widget);
	}

	public function actionAdd()
	{
		$definitionId = $this->filter('definition_id', 'str');
		if (!$definitionId)
		{
			if (!$this->isPost())
			{
				$widgetRepo = $this->getWidgetRepo();
				$widgetDefinitions = $widgetRepo->getWidgetDefinitionTitlePairs(true);

				$viewParams = [
					'widgetDefinitions' => $widgetDefinitions
				];
				return $this->view('XF:Widget\Add', 'widget_definition_chooser', $viewParams);
			}
		}
		if ($this->isPost())
		{
			if ($definitionId)
			{
				return $this->redirect($this->buildLink('widgets/add', [], ['definition_id' => $definitionId]), '');
			}
			else
			{
				return $this->error(\XF::phrase('you_must_select_widget_definition_to_use_for_your_new_widget'));
			}
		}
		$widget = $this->em()->create('XF:Widget');
		$widget->definition_id = $definitionId;

		return $this->widgetAddEdit($widget);
	}

	protected function widgetSaveProcess(\XF\Entity\Widget $widget)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'definition_id' => 'str',
			'widget_key' => 'str',
			'positions' => 'array-str',
			'display_condition' => 'str',
		]);

		$form->validate(function(FormAction $form) use ($widget)
		{
			$options = $this->filter('options', 'array');
			$request = new \XF\Http\Request($this->app->inputFilterer(), $options, [], []);
			$handler = $widget->getHandler();
			if ($handler && !$handler->verifyOptions($request, $options, $error))
			{
				if ($error)
				{
					$form->logError($error);
				}
			}
			$widget->options = $options;
		});

		$form->basicEntitySave($widget, $input);

		$extraInput = $this->filter([
			'title' => 'str'
		]);
		$form->apply(function() use ($extraInput, $widget)
		{
			$title = $widget->getMasterPhrase();
			$title->phrase_text = $extraInput['title'];
			$title->save();
		});

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		if ($params->widget_id)
		{
			$widget = $this->assertWidgetExists($params->widget_id);
		}
		else
		{
			$widget = $this->em()->create('XF:Widget');
		}

		$this->widgetSaveProcess($widget)->run();

		return $this->redirect($this->buildLink('widgets') . $this->buildLinkHash($widget->widget_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$widget = $this->assertWidgetExists($params->widget_id);
		$positionId = $this->filter('position_id', 'str');

		if ($positionId)
		{
			if (isset($widget->positions[$positionId]))
			{
				if ($this->isPost())
				{
					$positions = $widget->positions;
					unset($positions[$positionId]);

					if ($positions || !$this->filter('delete', 'bool'))
					{
						$widget->positions = $positions;
						$widget->save();
					}
					else
					{
						if (!$widget->preDelete())
						{
							return $this->error($widget->getErrors());
						}

						$widget->delete();
					}

					return $this->redirect($this->buildLink('widgets'));
				}
			}
			else
			{
				return $this->redirect($this->buildLink('widgets'));
			}
		}
		else
		{
			if (!$widget->preDelete())
			{
				return $this->error($widget->getErrors());
			}

			if ($this->isPost())
			{
				$widget->delete();
				return $this->redirect($this->buildLink('widgets'));
			}
		}

		$viewParams = [
			'widget' => $widget,
			'positionId' => $positionId
		];
		return $this->view('XF:Widget\Delete', 'widget_delete', $viewParams);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Widget
	 */
	protected function assertWidgetExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:Widget', $id, $with, $phraseKey);
	}

	public function actionDefinition()
	{
		$widgetRepo = $this->getWidgetRepo();
		$widgetDefinitionsFinder = $widgetRepo->findWidgetDefinitionsForList();

		$viewParams = [
			'widgetDefinitions' => $widgetDefinitionsFinder->fetch()
		];
		return $this->view('XF:Widget\Definition\Listing', 'widget_definition_list', $viewParams);
	}

	protected function definitionAddEdit(WidgetDefinition $widgetDefinition)
	{
		$viewParams = [
			'widgetDefinition' => $widgetDefinition
		];
		return $this->view('XF:Widget\Definition\Edit', 'widget_definition_edit', $viewParams);
	}

	public function actionDefinitionEdit(ParameterBag $params)
	{
		$widgetDefinition = $this->assertWidgetDefinitionExists($params->definition_id);
		return $this->definitionAddEdit($widgetDefinition);
	}

	public function actionDefinitionAdd()
	{
		$widgetDefinition = $this->em()->create('XF:WidgetDefinition');
		return $this->definitionAddEdit($widgetDefinition);
	}

	protected function widgetDefinitionSaveProcess(WidgetDefinition $widgetDefinition)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'definition_id' => 'str',
			'definition_class' => 'str',
			'addon_id' => 'str'
		]);

		$form->basicEntitySave($widgetDefinition, $input);

		$extraInput = $this->filter([
			'title' => 'str',
			'description' => 'str'
		]);
		$form->apply(function() use ($extraInput, $widgetDefinition)
		{
			$title = $widgetDefinition->getMasterTitlePhrase();
			$title->phrase_text = $extraInput['title'];
			$title->save();

			$description = $widgetDefinition->getMasterDescriptionPhrase();
			$description->phrase_text = $extraInput['description'];
			$description->save();
		});

		return $form;
	}

	public function actionDefinitionSave(ParameterBag $params)
	{
		if ($params->definition_id)
		{
			$widgetDefinition = $this->assertWidgetDefinitionExists($params->definition_id);
		}
		else
		{
			$widgetDefinition = $this->em()->create('XF:WidgetDefinition');
		}

		$this->widgetDefinitionSaveProcess($widgetDefinition)->run();

		return $this->redirect($this->buildLink('widgets/definitions') . $this->buildLinkHash($widgetDefinition->definition_id));
	}

	public function actionDefinitionDelete(ParameterBag $params)
	{
		$widgetDefinition = $this->assertWidgetDefinitionExists($params->definition_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$widgetDefinition,
			$this->buildLink('widgets/definitions/delete', $widgetDefinition),
			$this->buildLink('widgets/definitions/edit', $widgetDefinition),
			$this->buildLink('widgets/definitions'),
			$widgetDefinition->title
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return WidgetDefinition
	 */
	protected function assertWidgetDefinitionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:WidgetDefinition', $id, $with, $phraseKey);
	}

	public function actionPosition()
	{
		$widgetRepo = $this->getWidgetRepo();
		$widgetPositionsFinder = $widgetRepo->findWidgetPositionsForList();

		$viewParams = [
			'widgetPositions' => $widgetPositionsFinder->fetch()
		];
		return $this->view('XF:Widget\Position\Listing', 'widget_position_list', $viewParams);
	}

	protected function positionAddEdit(WidgetPosition $widgetPosition)
	{
		$viewParams = [
			'widgetPosition' => $widgetPosition
		];
		return $this->view('XF:Widget\Position\Edit', 'widget_position_edit', $viewParams);
	}

	public function actionPositionEdit(ParameterBag $params)
	{
		$widgetPosition = $this->assertWidgetPositionExists($params->position_id);
		return $this->positionAddEdit($widgetPosition);
	}

	public function actionPositionAdd()
	{
		$widgetPosition = $this->em()->create('XF:WidgetPosition');
		return $this->positionAddEdit($widgetPosition);
	}

	protected function widgetPositionSaveProcess(WidgetPosition $widgetPosition)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'position_id' => 'str',
			'active' => 'bool',
			'addon_id' => 'str'
		]);

		$form->basicEntitySave($widgetPosition, $input);

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
		$form->apply(function() use ($extraInput, $widgetPosition)
		{
			$title = $widgetPosition->getMasterTitlePhrase();
			$title->phrase_text = $extraInput['title'];
			$title->save();

			$description = $widgetPosition->getMasterDescriptionPhrase();
			$description->phrase_text = $extraInput['description'];
			$description->save();
		});

		return $form;
	}

	public function actionPositionSave(ParameterBag $params)
	{
		if ($params->position_id)
		{
			$widgetPosition = $this->assertWidgetPositionExists($params->position_id);
		}
		else
		{
			$widgetPosition = $this->em()->create('XF:WidgetPosition');
		}

		$this->widgetPositionSaveProcess($widgetPosition)->run();

		return $this->redirect($this->buildLink('widgets/positions') . $this->buildLinkHash($widgetPosition->position_id));
	}

	public function actionPositionDelete(ParameterBag $params)
	{
		$widgetPosition = $this->assertWidgetPositionExists($params->position_id);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$widgetPosition,
			$this->buildLink('widgets/positions/delete', $widgetPosition),
			$this->buildLink('widgets/positions/edit', $widgetPosition),
			$this->buildLink('widgets/positions'),
			$widgetPosition->title
		);
	}

	public function actionPositionToggle()
	{
		/** @var \XF\ControllerPlugin\Toggle $plugin */
		$plugin = $this->plugin('XF:Toggle');
		return $plugin->actionToggle('XF:WidgetPosition');
	}

	public function actionGetPositionDescription()
	{
		/** @var \XF\ControllerPlugin\DescLoader $plugin */
		$plugin = $this->plugin('XF:DescLoader');
		return $plugin->actionLoadDescription('XF:WidgetPosition');
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return WidgetPosition
	 */
	protected function assertWidgetPositionExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:WidgetPosition', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Widget
	 */
	protected function getWidgetRepo()
	{
		return $this->repository('XF:Widget');
	}
}