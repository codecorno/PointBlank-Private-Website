<?php

namespace XF\Admin\Controller;

use XF\Mvc\FormAction;
use XF\Mvc\ParameterBag;

abstract class AbstractField extends AbstractController
{
	abstract protected function getClassIdentifier();

	abstract protected function getLinkPrefix();

	abstract protected function getTemplatePrefix();

	public function actionIndex()
	{
		$repo = $this->getRepo();
		$listData = $repo->getFieldListData();

		$viewParams = [
			'prefix' => $this->getLinkPrefix(),

			'displayGroups' => $listData['displayGroups'],
			'fieldsGrouped' => $listData['fieldsGrouped'],

			'fieldTypes' => $repo->getFieldTypes(true),
			'matchTypes' => $repo->getMatchTypePhrases(true)
		];

		return $this->view($this->getClassIdentifier() . '\Listing', $this->getTemplatePrefix() . '_list', $viewParams);
	}

	protected function fieldAddEditResponse(\XF\Entity\AbstractField $field)
	{
		$repo = $this->getRepo();

		$fieldTypes = $repo->getFieldTypes(true);

		$viewParams = [
			'prefix' => $this->getLinkPrefix(),

			'field' => $field,
			'existingType' => $field->exists() ? $fieldTypes[$field->field_type] : '',

			'displayGroups' => $repo->getDisplayGroups(),
			'fieldTypes' => $fieldTypes,
			'matchTypes' => $repo->getMatchTypePhrases(true)
		];
		return $this->view($this->getClassIdentifier() . '\Edit', $this->getTemplatePrefix() . '_edit', $viewParams);
	}

	public function actionEdit(ParameterBag $params)
	{
		$field = $this->assertFieldExists($params['field_id']);
		return $this->fieldAddEditResponse($field);
	}

	public function actionAdd()
	{
		$field = $this->getEntity();
		return $this->fieldAddEditResponse($field);
	}

	protected function fieldSaveProcess(\XF\Entity\AbstractField $field)
	{
		$form = $this->formAction();

		$input = $this->filter([
			'display_group' => 'str',
			'display_order' => 'uint',
			'field_type' => 'str',
			'field_choices' => 'array',
			'max_length' => 'uint',
			'required' => 'bool',
			'moderator_editable' => 'bool',
			'display_template' => 'str'
		]);
		$input['field_id'] = $this->filter('field_id', 'str', $field->field_id);
		$input['match_type'] = $this->filter('match_type', 'str', 'none');
		$input['user_editable'] = $this->filter('user_editable', 'str', 'never');

		// this input has some values which may not always be present, so make sure we remove those
		$structure = $field->structure();
		foreach ($input AS $key => $null)
		{
			if (!isset($structure->columns[$key]))
			{
				unset($input[$key]);
			}
		}

		if (isset($field->editable_user_group_ids))
		{
			$editableUserGroups = $this->filter('editable_user_group', 'str');
			if ($editableUserGroups == 'all')
			{
				$input['editable_user_group_ids'] = [-1];
			}
			else
			{
				$input['editable_user_group_ids'] = $this->filter('editable_user_group_ids', 'array-uint');
			}
		}

		$input['match_params'] = $this->getMatchParams($input['match_type']);

		$fieldChoices = $this->filter('field_choice', 'array-str');
		$fieldChoicesText = $this->filter('field_choice_text', 'array-str');
		$fieldChoicesCombined = [];

		foreach ($fieldChoices AS $key => $choice)
		{
			if (isset($fieldChoicesText[$key]) && $fieldChoicesText[$key] !== '')
			{
				$fieldChoicesCombined[$choice] = $fieldChoicesText[$key];
			}
		}
		$input['field_choices'] = $fieldChoicesCombined;

		$form->basicEntitySave($field, $input);
		$this->saveAdditionalData($form, $field);

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
		$form->apply(function() use ($phraseInput, $field)
		{
			$title = $field->getMasterPhrase(true);
			$title->phrase_text = $phraseInput['title'];
			$title->save();

			$description = $field->getMasterPhrase(false);
			$description->phrase_text = $phraseInput['description'];
			$description->save();
		});

		return $form;
	}

	protected function getMatchParams($matchType)
	{
		if ($matchType == 'none')
		{
			return [];
		}
		else
		{
			$matchParams = [];

			foreach ($this->filter('match_params', 'array') AS $param => $value)
			{
				if (strpos($param, $matchType, 0) === 0)
				{
					$matchParams[$param] = $value;
				}
			}

			return $matchParams;
		}
	}

	protected function saveAdditionalData(FormAction $form, \XF\Entity\AbstractField $field)
	{
	}

	public function actionSave(ParameterBag $params)
	{
		$this->assertPostOnly();

		if ($params['field_id'])
		{
			$field = $this->assertFieldExists($params['field_id']);
		}
		else
		{
			$field = $this->getEntity();
		}

		$this->fieldSaveProcess($field)->run();

		return $this->redirect($this->buildLink($this->getLinkPrefix()) . $this->buildLinkHash($field->field_id));
	}

	public function actionDelete(ParameterBag $params)
	{
		$field = $this->assertFieldExists($params['field_id']);
		if ($this->isPost())
		{
			$field->delete();
			return $this->redirect($this->buildLink($this->getLinkPrefix()));
		}
		else
		{
			$viewParams = [
				'prefix' => $this->getLinkPrefix(),
				'field' => $field
			];
			return $this->view($this->getClassIdentifier() . '\Delete', $this->getTemplatePrefix() . '_delete', $viewParams);
		}
	}

	/**
	 * @return \XF\Entity\AbstractField
	 */
	protected function assertFieldExists($id, $with = null, $phraseKey = null)
	{
		return $this->assertRecordExists($this->getClassIdentifier(), $id, $with, $phraseKey);
	}

	/**
	 * @return \XF\Mvc\Entity\Entity
	 */
	protected function getEntity()
	{
		return $this->em()->create($this->getClassIdentifier());
	}

	/**
	 * @return \XF\Mvc\Entity\Finder
	 */
	protected function getFinder()
	{
		return $this->finder($this->getClassIdentifier());
	}

	/**
	 * @return \XF\Repository\AbstractField
	 */
	protected function getRepo()
	{
		return $this->repository($this->getClassIdentifier());
	}
}