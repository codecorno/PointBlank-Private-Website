<?php

namespace XF\Admin\Controller;

use XF\Mvc\ParameterBag;

class ContentType extends AbstractController
{
	/**
	 * @param $action
	 * @param ParameterBag $params
	 * @throws \XF\Mvc\Reply\Exception
	 */
	protected function preDispatchController($action, ParameterBag $params)
	{
		$this->assertDevelopmentMode();
	}

	public function actionIndex()
	{
		/** @var \XF\Repository\ContentTypeField $fieldRepo */
		$fieldRepo = $this->repository('XF:ContentTypeField');

		$fields = $fieldRepo->findContentTypeFieldsForList()->fetch();
		$fieldsGrouped = $fields->groupBy('content_type');

		$viewParams = [
			'fieldsGrouped' => $fieldsGrouped
		];
		return $this->view('XF:ContentType\Listing', 'content_type_list', $viewParams);
	}

	protected function fieldAddEdit(\XF\Entity\ContentTypeField $field)
	{
		$viewParams = [
			'field' => $field
		];
		return $this->view('XF:ContentType\Edit', 'content_type_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$field = $this->assertFieldExists($params['content_type'], $params['field_name']);
		return $this->fieldAddEdit($field);
	}

	public function actionAdd()
	{
		$field = $this->em()->create('XF:ContentTypeField');
		return $this->fieldAddEdit($field);
	}

	protected function fieldSaveProcess(\XF\Entity\ContentTypeField $field)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'content_type' => 'str',
			'field_name' => 'str',
			'field_value' => 'str',
			'addon_id' => 'str'
		]);

		$form->basicEntitySave($field, $input);

		return $form;
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['content_type'] || $params['field_name'])
		{
			$field = $this->assertFieldExists($params['content_type'], $params['field_name']);
		}
		else
		{
			$field = $this->em()->create('XF:ContentTypeField');
		}

		$this->fieldSaveProcess($field)->run();

		return $this->redirect(
			$this->buildLink('content-types')
			. $this->buildLinkHash("{$field->content_type}_{$field->field_name}")
		);
	}

	public function actionDelete(ParameterBag $params)
	{
		$field = $this->assertFieldExists($params['content_type'], $params['field_name']);

		/** @var \XF\ControllerPlugin\Delete $plugin */
		$plugin = $this->plugin('XF:Delete');
		return $plugin->actionDelete(
			$field,
			$this->buildLink('content-types/delete', $field),
			$this->buildLink('content-types/edit', $field),
			$this->buildLink('content-types'),
			"{$field->content_type} - {$field->field_name}"
		);
	}

	/**
	 * @param string $contentType
	 * @param string $fieldName
	 * @param array|string|null $with
	 * @param null|string $phraseKey
	 *
	 * @return \XF\Entity\ContentTypeField
	 */
	protected function assertFieldExists($contentType, $fieldName, $with = null, $phraseKey = null)
	{
		$id = ['content_type' => $contentType, 'field_name' => $fieldName];
		return $this->assertRecordExists('XF:ContentTypeField', $id, $with, $phraseKey);
	}
}