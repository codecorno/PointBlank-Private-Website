<?php

namespace XF\Admin\Controller;

use XF\Diff;
use XF\Diff3;
use XF\Mvc\ParameterBag;

class StyleProperty extends AbstractController
{
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertAdminPermission('style');
	}

	public function actionIndex()
	{
		$style = $this->plugin('XF:Style')->getActiveEditStyle();

		return $this->redirect($this->buildLink('styles/style-properties', $style));
	}

	public function actionView(ParameterBag $params)
	{
		$property = $this->assertPropertyExists($params->property_id);

		$styleId = $this->filter('style_id', 'uint');
		$style = $this->assertStyleExists($styleId);

		return $this->redirect($this->buildLink(
			'styles/style-properties/group',
			$style,
			['group' => $property->group_name]
		) . '#sp-' . $property->property_name);
	}

	protected function propertyAddEdit(\XF\Entity\StyleProperty $property)
	{
		if ($property->exists() && !$this->request->exists('style_id'))
		{
			$styleId = $property->style_id;
		}
		else
		{
			$styleId = $this->filter('style_id', 'uint');
		}

		$style = $this->assertStyleExists($styleId);
		if (!$style->canEdit() || !$style->canEditStylePropertyDefinitions())
		{
			return $this->error(\XF::phrase('style_properties_in_style_can_not_be_modified'));
		}

		if (!$property->exists() && $style->style_id)
		{
			$property->addon_id = '';
		}

		$viewParams = [
			'property' => $property,
			'style' => $style,
			'groups' => $this->getPropertyRepo()->getEffectivePropertyGroupsInStyle($style)
		];
		return $this->view('XF:StyleProperty\Edit', 'style_property_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$property = $this->assertPropertyExists($params->property_id);
		return $this->propertyAddEdit($property);
	}

	public function actionAdd()
	{
		$property = $this->em()->create('XF:StyleProperty');
		$group = $this->filter('group', 'str');
		if ($group)
		{
			$property->group_name = $group;
		}
		return $this->propertyAddEdit($property);
	}

	protected function propertySaveProcess(\XF\Entity\StyleProperty $property)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'style_id' => 'uint',
			'property_name' => 'str',
			'title' => 'str',
			'description' => 'str',
			'property_type' => 'str',
			'css_components' => 'array-str',
			'value_type' => 'str',
			'value_parameters' => 'str',
			'group_name' => 'str',
			'display_order' => 'uint',
			'depends_on' => 'str',
			'value_group' => 'str',
			'addon_id' => 'str'
		]);

		$form->basicEntitySave($property, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->property_id)
		{
			$property = $this->assertPropertyExists($params->property_id);
		}
		else
		{
			$property = $this->em()->create('XF:StyleProperty');
		}

		$this->propertySaveProcess($property)->run();

		return $this->redirect($this->buildLink(
			'styles/style-properties/group',
			$property->Style,
			['group' => $property->group_name]
		));
	}

	public function actionDelete(ParameterBag $params)
	{
		$property = $this->assertPropertyExists($params->property_id);
		if (!$property->Style || !$property->Style->canEdit() || !$property->Style->canEditStylePropertyDefinitions())
		{
			return $this->error(\XF::phrase('style_properties_in_style_can_not_be_modified'));
		}

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$property,
			$this->buildLink('style-properties/delete', $property),
			$this->buildLink('style-properties/edit', $property),
			$this->buildLink(
				'styles/style-properties/group',
				$property->Style,
				['group' => $property->group_name]
			),
			$property->title
		);
	}

	protected function groupAddEdit(\XF\Entity\StylePropertyGroup $group)
	{
		if ($group->exists() && !$this->request->exists('style_id'))
		{
			$styleId = $group->style_id;
		}
		else
		{
			$styleId = $this->filter('style_id', 'uint');
		}

		$style = $this->assertStyleExists($styleId);
		if (!$style->canEdit() || !$style->canEditStylePropertyDefinitions())
		{
			return $this->error(\XF::phrase('style_properties_in_style_can_not_be_modified'));
		}

		if (!$group->exists() && $style->style_id)
		{
			$group->addon_id = '';
		}

		$viewParams = [
			'group' => $group,
			'style' => $style
		];
		return $this->view('XF:StyleProperty\GroupEdit', 'style_property_group_edit', $viewParams);
	}

	public function actionGroupEdit(ParameterBag $params)
	{
		$group = $this->assertGroupExists($params->property_group_id);
		return $this->groupAddEdit($group);
	}

	public function actionGroupAdd()
	{
		$group = $this->em()->create('XF:StylePropertyGroup');
		return $this->groupAddEdit($group);
	}

	protected function groupSaveProcess(\XF\Entity\StylePropertyGroup $group)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'style_id' => 'uint',
			'group_name' => 'str',
			'title' => 'str',
			'description' => 'str',
			'display_order' => 'uint',
			'addon_id' => 'str'
		]);

		$form->basicEntitySave($group, $input);

		return $form;
	}

	public function actionGroupSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params->property_group_id)
		{
			$group = $this->assertGroupExists($params->property_group_id);
		}
		else
		{
			$group = $this->em()->create('XF:StylePropertyGroup');
		}

		$this->groupSaveProcess($group)->run();

		return $this->redirect($this->buildLink('styles/style-properties', $group->Style));
	}

	public function actionGroupDelete(ParameterBag $params)
	{
		$group = $this->assertGroupExists($params->property_group_id);
		if (!$group->Style || !$group->Style->canEdit() || !$group->Style->canEditStylePropertyDefinitions())
		{
			return $this->error(\XF::phrase('style_properties_in_style_can_not_be_modified'));
		}

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$group,
			$this->buildLink('style-properties/groups/delete', $group),
			$this->buildLink('style-properties/groups/edit', $group),
			$this->buildLink('styles/style-properties', $group->Style),
			$group->title
		);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\Style
	 */
	protected function assertStyleExists($id, $with = null, $phraseKey = null)
	{
		return $this->plugin('XF:Style')->assertStyleExists($id, $with, $phraseKey);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\StyleProperty
	 */
	protected function assertPropertyExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:StyleProperty', $id, $with, $phraseKey);
	}

	/**
	 * @param string $id
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\StylePropertyGroup
	 */
	protected function assertGroupExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists('XF:StylePropertyGroup', $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Repository\Style
	 */
	protected function getStyleRepo()
	{
		return $this->repository('XF:Style');
	}

	/**
	 * @return \XF\Repository\StyleProperty
	 */
	protected function getPropertyRepo()
	{
		return $this->repository('XF:StyleProperty');
	}
}